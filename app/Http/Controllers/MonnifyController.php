<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\Service;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Monnify\MonnifyLaravel\Facades\Monnify;

class MonnifyController extends Controller
{
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        $userId = null;
        if (! empty($validated['patient_email'])) {
            $user = User::firstOrCreate(
                ['email' => $validated['patient_email']],
                ['name' => $validated['patient_name'], 'password' => Str::random(12)]
            );
            $userId = $user->id;
        }

        $serviceCharge = 120;
        $totalAmount = round((float) $service->price + $serviceCharge, 2);

        $appointment = Appointment::create([
            ...$validated,
            'user_id' => $userId,
            'confirmation_code' => 'APT-' . strtoupper(Str::random(8)),
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        $paymentReference = 'PAY-' . strtoupper(Str::random(12));
        $redirectUrl = url('/monnify/return?paymentReference=' . $paymentReference);
        $payload = [
            'amount' => $totalAmount,
            'customerName' => $validated['patient_name'],
            'customerEmail' => $validated['patient_email'] ?? 'no-reply@example.com',
            'paymentReference' => $paymentReference,
            'paymentDescription' => 'Appointment payment for #' . $appointment->id,
            'currencyCode' => 'NGN',
            'contractCode' => config('monnify.contract_code'),
            'redirectUrl' => $redirectUrl,
        ];

        try {
            $response = Monnify::transactions()->initialise($payload);
        } catch (\Throwable $e) {
            Log::error('Monnify init error', ['error' => $e->getMessage(), 'payload' => $payload]);

            Transaction::create([
                'appointment_id' => $appointment->id,
                'transaction_reference' => $paymentReference,
                'invoice_number' => 'INV-' . $appointment->id,
                'payment_method' => 'monnify',
                'status' => 'pending',
                'amount' => $totalAmount,
                'meta' => ['error' => $e->getMessage(), 'redirect_url' => $redirectUrl],
            ]);

            return response()->json([
                'message' => 'Payment initiation is temporarily unavailable. Please try again in a moment.',
                'payment_reference' => $paymentReference,
                'appointment_id' => $appointment->id,
                'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
            ], 503);
        }

        $body = is_array($response['body'] ?? null) ? ($response['body']['responseBody'] ?? $response['body']) : [];
        $checkoutUrl = $body['checkoutUrl'] ?? $body['checkout_url'] ?? null;

        Transaction::create([
            'appointment_id' => $appointment->id,
            'transaction_reference' => $paymentReference,
            'invoice_number' => 'INV-' . $appointment->id,
            'payment_method' => 'monnify',
            'status' => 'pending',
            'amount' => $totalAmount,
            'meta' => array_merge($body, ['redirect_url' => $redirectUrl, 'init_response_status' => $response['status'] ?? null]),
        ]);

        if (! $checkoutUrl) {
            return response()->json([
                'message' => 'Monnify did not return a checkout URL. Your appointment was saved and you can verify it later.',
                'payment_reference' => $paymentReference,
                'appointment_id' => $appointment->id,
                'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
            ], 502);
        }

        return response()->json([
            'checkout_url' => $checkoutUrl,
            'payment_reference' => $paymentReference,
            'appointment_id' => $appointment->id,
            'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
        ]);
    }

    public function downloadReceipt($transactionId)
    {
        $tx = Transaction::with('appointment.service', 'appointment.staffMember')->findOrFail($transactionId);
        $appt = $tx->appointment;

        $pdf = Pdf::loadView('receipt', ['transaction' => $tx, 'appointment' => $appt]);

        return $pdf->download('receipt-' . $tx->invoice_number . '.pdf');
    }

    public function downloadReceiptByReference($reference)
    {
        $tx = Transaction::with('appointment.service', 'appointment.staffMember')
            ->where('transaction_reference', $reference)
            ->orWhere('invoice_number', $reference)
            ->firstOrFail();

        $pdf = Pdf::loadView('receipt', ['transaction' => $tx, 'appointment' => $tx->appointment]);

        return $pdf->download('receipt-' . $tx->invoice_number . '.pdf');
    }

    public function getByReference($reference)
    {
        $tx = Transaction::with('appointment.service', 'appointment.staffMember')
            ->where('transaction_reference', $reference)
            ->first();

        if (! $tx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $amount = (float) $tx->amount;
        $serviceCharge = 120;
        $serviceAmount = max(0, (int) round($amount - $serviceCharge));

        return response()->json([
            'data' => [
                'transaction' => $tx,
                'appointment' => $tx->appointment,
                'breakdown' => [
                    'service_amount' => $serviceAmount,
                    'service_charge' => $serviceCharge,
                    'total' => $amount,
                ],
            ],
        ]);
    }

    public function paymentStatus(Request $request)
    {
        $paymentReference = $request->query('paymentReference') ?: $request->query('reference') ?: $request->query('transactionReference');
        if (! $paymentReference) {
            return redirect()->route('appointment')->with('error', 'Missing payment reference');
        }

        $paymentReference = is_string($paymentReference) ? trim($paymentReference) : $paymentReference;
        $paymentReference = is_string($paymentReference) ? urldecode($paymentReference) : $paymentReference;
        $paymentReference = is_string($paymentReference) ? str_replace(['?paymentReference=', '?reference=', '?transactionReference='], '', $paymentReference) : $paymentReference;
        $paymentReference = is_string($paymentReference) ? preg_replace('/\s+/', '', $paymentReference) : $paymentReference;

        if (is_string($paymentReference)) {
            $segments = preg_split('/(?=PAY-)/', $paymentReference);
            if (is_array($segments) && isset($segments[1]) && $segments[1] !== '') {
                $paymentReference = $segments[1];
            }

            if (preg_match('/^(PAY-[A-Z0-9-]+)/i', $paymentReference, $matches)) {
                $paymentReference = $matches[1];
            }
        }

        if (! is_string($paymentReference) || $paymentReference === '') {
            return redirect()->route('appointment')->with('error', 'Missing payment reference');
        }

        $tx = Transaction::with('appointment.service', 'appointment.staffMember')
            ->where('transaction_reference', $paymentReference)
            ->first();

        if (! $tx) {
            abort(404);
        }

        $paymentStatus = null;
        $settlementStatus = null;
        $status = $tx->status ?? 'pending';

        try {
            $result = Monnify::transactions()->statusByReference($paymentReference, 'payment');
            $body = is_array($result['body'] ?? null) ? ($result['body']['responseBody'] ?? $result['body']) : [];
            $paymentStatus = $body['paymentStatus'] ?? $body['status'] ?? null;
            $settlementStatus = $body['settlementStatus'] ?? $body['settlement_status'] ?? null;

            if ($paymentStatus === 'PAID' || str_contains(strtolower((string) $paymentStatus), 'paid')) {
                $status = 'paid';
                $tx->update(['status' => 'paid']);
                $tx->appointment?->update(['status' => 'confirmed']);
            } elseif ($paymentStatus === 'FAILED' || str_contains(strtolower((string) $paymentStatus), 'failed')) {
                $status = 'failed';
                $tx->update(['status' => 'failed']);
            }

            $tx->update([
                'meta' => array_merge($tx->meta ?? [], [
                    'last_status_check' => [
                        'paymentStatus' => $paymentStatus,
                        'settlementStatus' => $settlementStatus,
                        'checked_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Monnify status verification skipped', ['error' => $e->getMessage(), 'reference' => $paymentReference]);
        }

        return view('payment-status', [
            'transaction' => $tx,
            'appointment' => $tx->appointment,
            'status' => $status,
            'paymentStatus' => $paymentStatus,
            'settlementStatus' => $settlementStatus,
        ]);
    }

    public function verifyAppointment(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $code = (string) $request->input('code');
        // normalize input: trim whitespace and uppercase for consistent lookup
        $normalized = strtoupper(preg_replace('/\s+/', '', $code));

        // attempt to find appointment by confirmation code (case-insensitive)
        $appointment = Appointment::with('service', 'staffMember')
            ->whereRaw('UPPER(REPLACE(confirmation_code, " ", "")) = ?', [$normalized])
            ->first();

        // if not found, try to find a transaction matching reference or invoice (case-insensitive)
        if (! $appointment) {
            $transaction = Transaction::with('appointment.service', 'appointment.staffMember')
                ->whereRaw('UPPER(REPLACE(transaction_reference, " ", "")) = ?', [$normalized])
                ->orWhereRaw('UPPER(REPLACE(invoice_number, " ", "")) = ?', [$normalized])
                ->first();

            $appointment = $transaction?->appointment;
        }

        if (! $appointment) {
            return response()->json(['found' => false, 'message' => 'No appointment found for that verification code.'], 404);
        }

        $transaction = $appointment?->transaction()->first();

        return response()->json([
            'found' => true,
            'appointment' => [
                'confirmation_code' => $appointment->confirmation_code,
                'patient_name' => $appointment->patient_name,
                'patient_phone' => $appointment->patient_phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'status' => $appointment->status,
                'service' => $appointment->service?->name,
                'doctor' => $appointment->staffMember?->name,
            ],
            'payment' => [
                'status' => $transaction?->status ?? 'pending',
                'transaction_reference' => $transaction?->transaction_reference,
                'invoice_number' => $transaction?->invoice_number,
                'amount' => $transaction?->amount,
                'receipt_url' => $transaction ? route('transactions.receipt', ['transaction' => $transaction->id]) : null,
            ],
        ]);
    }

    public function handleReturn(Request $request)
    {
        $paymentReference = $request->query('paymentReference') ?: $request->query('transactionReference') ?: $request->query('reference');

        if (! $paymentReference) {
            return redirect()->route('appointment')->with('error', 'Missing payment reference');
        }

        $paymentReference = is_string($paymentReference) ? trim($paymentReference) : $paymentReference;
        $paymentReference = str_replace(['?paymentReference=', '?transactionReference=', '?reference='], '', $paymentReference);

        return redirect()->route('payment.status', ['paymentReference' => $paymentReference]);
    }
}

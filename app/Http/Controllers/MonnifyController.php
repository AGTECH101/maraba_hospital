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
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'required|exists:services,id',
            'staff_member_id' => 'nullable|exists:staff_members,id',
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email',
            'patient_phone' => 'required|string|max:20',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $services = Service::whereIntegerInRaw('id', $validated['service_ids'])->get();
        $serviceAmount = round($services->sum('price'), 2);
        $estimatedFee = $this->estimateMonnifyFee($serviceAmount);
        $estimatedVat = $this->calculateVat($estimatedFee);
        $totalAmount = round($serviceAmount + $estimatedFee + $estimatedVat, 2);

        $userId = null;
        if (! empty($validated['patient_email'])) {
            $user = User::firstOrCreate(
                ['email' => $validated['patient_email']],
                ['name' => $validated['patient_name'], 'password' => Str::random(12)]
            );
            $userId = $user->id;
        }

        $breakdown = [
            'service_amount' => $serviceAmount,
            'fee' => $estimatedFee,
            'vat' => $estimatedVat,
            'total' => $totalAmount,
            'breakdown_source' => 'estimated', // becomes 'monnify_confirmed' after payment
        ];

        $appointment = Appointment::create([
            'service_id' => $validated['service_ids'][0] ?? null,
            'service_ids' => $validated['service_ids'],
            'staff_member_id' => $validated['staff_member_id'] ?? null,
            'patient_name' => $validated['patient_name'],
            'patient_email' => $validated['patient_email'] ?? null,
            'patient_phone' => $validated['patient_phone'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'notes' => $validated['notes'] ?? null,
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

        if (config('monnify.hospital_subaccount_code')) {
            $payload['incomeSplitConfig'] = [
                [
                    'subAccountCode' => config('monnify.hospital_subaccount_code'),
                    'splitAmount' => $serviceAmount, // hospital gets exactly the appointment fee
                    'feePercentage' => 0,
                    'feeBearer' => false, // hospital's split should NOT be reduced by Monnify's fee
                ],
            ];
        }

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
                'meta' => ['error' => $e->getMessage(), 'redirect_url' => $redirectUrl, 'breakdown' => $breakdown],
            ]);

            return response()->json([
                'message' => 'Payment initiation is temporarily unavailable. Please try again in a moment.',
                'payment_reference' => $paymentReference,
                'appointment_id' => $appointment->id,
                'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
                'breakdown' => $breakdown,
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
            'meta' => array_merge($body, [
                'redirect_url' => $redirectUrl,
                'init_response_status' => $response['status'] ?? null,
                'breakdown' => $breakdown,
            ]),
        ]);

        if (! $checkoutUrl) {
            return response()->json([
                'message' => 'Monnify did not return a checkout URL. Your appointment was saved and you can verify it later.',
                'payment_reference' => $paymentReference,
                'appointment_id' => $appointment->id,
                'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
                'breakdown' => $breakdown,
            ], 502);
        }

        return response()->json([
            'checkout_url' => $checkoutUrl,
            'payment_reference' => $paymentReference,
            'appointment_id' => $appointment->id,
            'status_url' => route('payment.status', ['paymentReference' => $paymentReference]),
            'breakdown' => $breakdown,
        ]);
    }

    public function downloadReceipt($transactionId)
    {
        try {
            $tx = Transaction::with('appointment.service', 'appointment.staffMember')->findOrFail($transactionId);
            $breakdown = $this->resolveBreakdown($tx);
            $serviceAmount = (float) ($breakdown['service_amount'] ?? 0);
            $fee = (float) ($breakdown['fee'] ?? 0);
            $vat = (float) ($breakdown['vat'] ?? 0);
            $totalAmount = (float) ($breakdown['total'] ?? $tx->amount ?? 0);

            $pdf = Pdf::loadView('receipt', [
                'transaction' => $tx,
                'appointment' => $tx->appointment,
                'serviceAmount' => $serviceAmount,
                'serviceCharge' => $fee + $vat,
                'fee' => $fee,
                'vat' => $vat,
                'totalAmount' => $totalAmount,
            ]);

            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('receipt-' . $tx->invoice_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Receipt download error', ['error' => $e->getMessage(), 'transaction_id' => $transactionId]);
            return response()->json(['error' => 'Failed to generate receipt', 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadReceiptByReference($reference)
    {
        try {
            $tx = Transaction::with('appointment.service', 'appointment.staffMember')
                ->where('transaction_reference', $reference)
                ->orWhere('invoice_number', $reference)
                ->firstOrFail();

            $breakdown = $this->resolveBreakdown($tx);
            $serviceAmount = (float) ($breakdown['service_amount'] ?? 0);
            $fee = (float) ($breakdown['fee'] ?? 0);
            $vat = (float) ($breakdown['vat'] ?? 0);
            $totalAmount = (float) ($breakdown['total'] ?? $tx->amount ?? 0);

            $pdf = Pdf::loadView('receipt', [
                'transaction' => $tx,
                'appointment' => $tx->appointment,
                'serviceAmount' => $serviceAmount,
                'serviceCharge' => $fee + $vat,
                'fee' => $fee,
                'vat' => $vat,
                'totalAmount' => $totalAmount,
            ]);

            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('receipt-' . $tx->invoice_number . '.pdf');
        } catch (\Exception $e) {
            Log::error('Receipt download by reference error', ['error' => $e->getMessage(), 'reference' => $reference]);
            return response()->json(['error' => 'Failed to generate receipt', 'message' => $e->getMessage()], 500);
        }
    }

    public function getByReference($reference)
    {
        $tx = Transaction::with('appointment.service', 'appointment.staffMember')
            ->where('transaction_reference', $reference)
            ->first();

        if (! $tx) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json([
            'data' => [
                'transaction' => $tx,
                'appointment' => $tx->appointment,
                'breakdown' => $this->resolveBreakdown($tx),
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
        $breakdown = $tx->meta['breakdown'] ?? null;

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

            $realFee = isset($body['fee']) ? (float) $body['fee'] : null;

            if ($realFee !== null && $status === 'paid') {
                $vat = $this->calculateVat($realFee);
                $serviceAmount = $breakdown['service_amount'] ?? round(((float) $tx->amount) - $realFee - $vat, 2);

                $breakdown = [
                    'service_amount' => $serviceAmount,
                    'fee' => $realFee,
                    'vat' => $vat,
                    'total' => $tx->amount,
                    'breakdown_source' => 'monnify_confirmed',
                ];
            }

            $tx->update([
                'meta' => array_merge($tx->meta ?? [], [
                    'breakdown' => $breakdown,
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
            'breakdown' => $breakdown,
        ]);
    }

    public function verifyAppointment(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $appointment = $this->resolveAppointment($request->input('code'));

        if (! $appointment) {
            return response()->json([
                'found' => false,
                'state' => 'invalid',
                'message' => 'No appointment found for that verification code.',
            ], 200);
        }

        $transaction = $appointment->transaction()->first();
        $appointmentDateTime = $this->parseAppointmentDateTime($appointment);
        $isExpired = $appointmentDateTime !== null && $appointmentDateTime->isPast();
        $isUsed = ! is_null($appointment->used_at);

        $state = 'valid';
        if ($isUsed) {
            $state = 'used';
        } elseif ($transaction?->status !== 'paid') {
            $state = 'invalid';
        } elseif ($isExpired) {
            $state = 'expired';
        }

        $serviceNames = [];
        if (! empty($appointment->service_ids) && is_array($appointment->service_ids)) {
            $serviceNames = \App\Models\Service::whereIntegerInRaw('id', (array) $appointment->service_ids)->pluck('name')->toArray();
        } elseif ($appointment->service) {
            $serviceNames = [$appointment->service->name];
        }

        return response()->json([
            'found' => true,
            'state' => $state,
            'used_at' => $appointment->used_at?->toISOString(),
            'appointment' => [
                'confirmation_code' => $appointment->confirmation_code,
                'patient_name' => $appointment->patient_name,
                'patient_phone' => $appointment->patient_phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time,
                'status' => $appointment->status,
                'service' => count($serviceNames) ? implode(', ', $serviceNames) : null,
                'services' => $serviceNames,
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

    public function markAppointmentUsed(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $appointment = $this->resolveAppointment($request->input('code'));

        if (! $appointment) {
            return response()->json(['message' => 'Appointment not found.'], 404);
        }

        if (! is_null($appointment->used_at)) {
            return response()->json(['message' => 'Appointment has already been used.'], 409);
        }

        $appointment->update(['used_at' => now()]);

        return response()->json(['message' => 'Appointment marked as used.']);
    }

        protected function estimateMonnifyFee(float $amount): float
    {
        $fee = round($amount * config('monnify.transfer_fee_rate', 0.015), 2);
        return min($fee, config('monnify.transfer_fee_cap', 2000));
    }

    protected function calculateVat(float $fee): float
    {
        return round($fee * config('monnify.vat_rate', 0.075), 2);
    }

    protected function resolveBreakdown(Transaction $tx): array
    {
        if (isset($tx->meta['breakdown'])) {
            return $tx->meta['breakdown'];
        }

        // Fallback for older transactions created before this change
        $amount = (float) $tx->amount;
        $fee = $this->estimateMonnifyFee($amount);
        $vat = $this->calculateVat($fee);

        return [
            'service_amount' => max(0, round($amount - $fee - $vat, 2)),
            'fee' => $fee,
            'vat' => $vat,
            'total' => $amount,
            'breakdown_source' => 'estimated_fallback',
        ];
    }

    protected function resolveAppointment(string $code): ?Appointment
    {
        $normalized = strtoupper(preg_replace('/\s+/', '', $code));

        $appointment = Appointment::with('service', 'staffMember')
            ->whereRaw("UPPER(REPLACE(confirmation_code, ' ', '')) = ?", [$normalized])
            ->first();

        if ($appointment instanceof Appointment) {
            return $appointment;
        }

        $transaction = Transaction::with('appointment.service', 'appointment.staffMember')
            ->whereRaw("UPPER(REPLACE(transaction_reference, ' ', '')) = ?", [$normalized])
            ->orWhereRaw("UPPER(REPLACE(invoice_number, ' ', '')) = ?", [$normalized])
            ->first();

        $appointment = $transaction?->appointment;

        return $appointment instanceof Appointment ? $appointment : null;
    }

    protected function parseAppointmentDateTime(Appointment $appointment): ?\Illuminate\Support\Carbon
    {
        if (! $appointment->appointment_date || ! $appointment->appointment_time) {
            return null;
        }

        $timePart = trim((string) explode('-', (string) $appointment->appointment_time)[0]);

        if ($timePart === '') {
            return null;
        }

        return \Illuminate\Support\Carbon::parse($appointment->appointment_date . ' ' . $timePart);
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

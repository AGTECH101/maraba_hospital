<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonnifyWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('monnify-signature');
        $expected = hash_hmac('sha512', $request->getContent(), config('monnify.secret_key'));
        if (! hash_equals($expected, $signature)) {
            Log::warning('Monnify webhook invalid signature');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $eventType = $request->input('eventType');
        $eventData = $request->input('eventData', []);

        // quick response so Monnify doesn't resend while we process
        try {
            if ($eventType === 'SUCCESSFUL_TRANSACTION') {
                $txRef = $eventData['transactionReference'] ?? ($eventData['paymentReference'] ?? null);
                if ($txRef) {
                    $tx = Transaction::where('transaction_reference', $txRef)->first();
                    if ($tx) {
                        $tx->update(['status' => 'paid', 'meta' => array_merge($tx->meta ?? [], $eventData)]);
                        $appt = $tx->appointment;
                        if ($appt) $appt->update(['status' => 'confirmed']);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Monnify webhook processing error', ['error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #111; }
        .center { text-align: center; }
        .border { border: 1px solid #111; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        td, th { border: 1px solid #aaa; padding: 6px 8px; font-size: 12px; }
        th { background: #f2f2f2; font-weight: bold; }
        .bold { font-weight: bold; }
        .right { text-align: right; }
        .muted { color: #555; font-size: 11px; }
        .boxed { border: 1px solid #111; padding: 10px; margin-top: 12px; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size: 18px; margin-bottom: 12px;">
        MARABA CHARITY HOSPITAL<br>
        <span style="font-size: 12px;">PAYMENT RECEIPT</span>
    </div>

    <div class="muted center" style="margin-bottom: 8px;">Simple receipt generated for your appointment payment.</div>

    <table>
        <tr>
            <td class="bold">Invoice:</td>
            <td>{{ $transaction->invoice_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Reference:</td>
            <td>{{ $transaction->transaction_reference ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Date:</td>
            <td>{{ optional($transaction->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Status:</td>
            <td>{{ strtoupper((string) ($transaction->status ?? 'pending')) }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 12px;">PATIENT INFORMATION</div>
    <table>
        <tr>
            <td class="bold">Patient:</td>
            <td>{{ $appointment->patient_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Email:</td>
            <td>{{ $appointment->patient_email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Phone:</td>
            <td>{{ $appointment->patient_phone ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 12px;">APPOINTMENT DETAILS</div>
    <table>
        <tr>
            <td class="bold">Service(s):</td>
            <td>
                @php
                    $serviceNames = [];
                    if (! empty($appointment->service_ids) && is_array($appointment->service_ids)) {
                        $serviceNames = \App\Models\Service::whereIn('id', $appointment->service_ids)->pluck('name')->toArray();
                    } elseif (! empty($appointment->service_id)) {
                        $serviceNames = [optional($appointment->service)->name ?? 'Service'];
                    }
                @endphp
                {{ count($serviceNames) ? implode(', ', $serviceNames) : 'N/A' }}
            </td>
        </tr>
        <tr>
            <td class="bold">Staff/Doctor:</td>
            <td>{{ optional($appointment->staffMember)->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Date/Time:</td>
            <td>{{ $appointment->appointment_date ?? 'N/A' }} {{ $appointment->appointment_time ?? '' }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 12px;">PAYMENT BREAKDOWN</div>
    <table>
        <tr>
            <th>Description</th>
            <th class="right">Amount (NGN)</th>
        </tr>
        <tr>
            <td>Service Amount</td>
            <td class="right">{{ number_format((float) ($serviceAmount ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td>Service Charge</td>
            <td class="right">{{ number_format((float) ($serviceCharge ?? 0), 2) }}</td>
        </tr>
        <tr class="bold">
            <td>TOTAL PAID</td>
            <td class="right">{{ number_format((float) ($totalAmount ?? $transaction->amount ?? 0), 2) }}</td>
        </tr>
    </table>

    <div class="boxed">
        <div class="bold">VERIFICATION CODE</div>
        <div style="font-family: monospace; font-size: 12px; margin-top: 5px;">{{ $transaction->transaction_reference ?? 'N/A' }}</div>
    </div>

    <div class="center" style="margin-top: 16px; font-size: 11px;">
        Thank you for choosing Maraba Charity Hospital<br>
        This is an electronically generated receipt
    </div>
</body>
</html>

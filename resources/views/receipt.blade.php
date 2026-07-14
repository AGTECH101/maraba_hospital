<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .center { text-align: center; }
        .border { border: 1px solid black; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td, th { border: 1px solid black; padding: 5px; }
        th { background: #ccc; font-weight: bold; }
        .bold { font-weight: bold; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="center bold" style="font-size: 18px; margin-bottom: 20px;">
        MARABA HOSPITAL<br>
        <span style="font-size: 12px;">PAYMENT RECEIPT</span>
    </div>

    <table>
        <tr>
            <td class="bold">Invoice:</td>
            <td>{{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td class="bold">Reference:</td>
            <td>{{ $transaction->transaction_reference }}</td>
        </tr>
        <tr>
            <td class="bold">Date:</td>
            <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="bold">Status:</td>
            <td>{{ strtoupper($transaction->status) }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 15px;">PATIENT INFORMATION</div>
    <table>
        <tr>
            <td class="bold">Patient:</td>
            <td>{{ $appointment->patient_name }}</td>
        </tr>
        <tr>
            <td class="bold">Email:</td>
            <td>{{ $appointment->patient_email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Phone:</td>
            <td>{{ $appointment->patient_phone }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 15px;">APPOINTMENT DETAILS</div>
    <table>
        <tr>
            <td class="bold">Service:</td>
            <td>{{ $appointment->service->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Doctor/Staff:</td>
            <td>{{ $appointment->staffMember->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="bold">Date/Time:</td>
            <td>{{ $appointment->appointment_date ?? 'N/A' }} {{ $appointment->appointment_time ?? '' }}</td>
        </tr>
    </table>

    <div class="bold" style="margin-top: 15px;">PAYMENT BREAKDOWN</div>
    <table>
        <tr>
            <th>Description</th>
            <th class="right">Amount (NGN)</th>
        </tr>
        <tr>
            <td>{{ $appointment->service->name ?? 'Service' }}</td>
            <td class="right">{{ number_format($serviceAmount, 2) }}</td>
        </tr>
        <tr>
            <td>Service Charge</td>
            <td class="right">{{ number_format($serviceCharge, 2) }}</td>
        </tr>
        <tr class="bold">
            <td>TOTAL PAID</td>
            <td class="right">{{ number_format($transaction->amount, 2) }}</td>
        </tr>
    </table>

    <div style="border: 1px solid black; padding: 10px; margin-top: 15px;">
        <div class="bold">VERIFICATION CODE</div>
        <div style="font-family: monospace; font-size: 12px; margin-top: 5px;">{{ $transaction->transaction_reference }}</div>
    </div>

    <div class="center" style="margin-top: 20px; font-size: 11px;">
        Thank you for choosing Maraba Hospital<br>
        This is an electronically generated receipt
    </div>
</body>
</html>

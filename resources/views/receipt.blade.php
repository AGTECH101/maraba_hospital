<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; }
        .header { text-align: center; margin-bottom: 20px; }
        .brand { font-size: 20px; font-weight: bold; }
        .meta { margin-bottom: 16px; }
        .box { border: 1px solid #eee; padding: 12px; border-radius: 6px; }
        table { width:100%; border-collapse: collapse; margin-top:12px }
        th, td { text-align:left; padding:8px; border-bottom:1px solid #f1f1f1 }
        .total { font-weight: bold; text-align:right; }
        .verify { margin-top:18px; padding:10px; background:#f7f7f9; border-left:6px solid #007bff }
        .small { font-size:12px; color:#666 }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Maraba Hospital</div>
        <div class="small">Receipt for appointment payment</div>
    </div>

    <div class="meta box">
        <div><strong>Invoice:</strong> {{ $transaction->invoice_number }}</div>
        <div><strong>Transaction Ref:</strong> {{ $transaction->transaction_reference }}</div>
        <div><strong>Date:</strong> {{ $transaction->created_at->toDayDateTimeString() }}</div>
    </div>

    <h4>Patient & Appointment</h4>
    <div class="box">
        <div><strong>Patient:</strong> {{ $appointment->patient_name }}</div>
        <div><strong>Email:</strong> {{ $appointment->patient_email ?? 'N/A' }}</div>
        <div><strong>Phone:</strong> {{ $appointment->patient_phone }}</div>
        <div><strong>Service:</strong> {{ $appointment->service->name ?? 'N/A' }}</div>
        <div><strong>Doctor:</strong> {{ $appointment->staffMember->name ?? 'N/A' }}</div>
        <div><strong>Date / Time:</strong> {{ $appointment->appointment_date }} {{ $appointment->appointment_time }}</div>
    </div>

    <h4>Payment</h4>
    <table>
        <thead>
            <tr><th>Description</th><th>Amount (NGN)</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Appointment: {{ $appointment->service->name ?? 'Service' }}</td>
                <td>{{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr><td class="total">Total</td><td class="total">{{ number_format($transaction->amount, 2) }}</td></tr>
        </tfoot>
    </table>

    <div class="verify">
        <div><strong>Verification code:</strong> {{ $transaction->transaction_reference }}</div>
        <div class="small">Use this code to verify this payment with hospital staff or support.</div>
    </div>

    <div class="small" style="margin-top:18px">Thank you for choosing Maraba Hospital.</div>
</body>
</html>

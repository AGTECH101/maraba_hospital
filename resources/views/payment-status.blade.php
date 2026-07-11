@extends('layout.base')

@section('page_title', 'Payment Status')

@section('page_content')

<x-banner message="Payment Status" page="Payment Status" />

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        @if($status === 'paid')
                            <i class="bi bi-check-circle-fill text-success display-4"></i>
                            <h2 class="mt-3">Payment Successful</h2>
                        @elseif($status === 'failed')
                            <i class="bi bi-x-circle-fill text-danger display-4"></i>
                            <h2 class="mt-3">Payment Failed</h2>
                        @else
                            <i class="bi bi-hourglass-split text-warning display-4"></i>
                            <h2 class="mt-3">Payment Status Pending</h2>
                        @endif
                    </div>

                    <div class="alert alert-info">
                        <strong>Verification reference:</strong> {{ $transaction->transaction_reference }}<br>
                        <strong>Invoice:</strong> {{ $transaction->invoice_number }}
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="mb-3">Appointment Details</h6>
                                <p class="mb-1"><strong>Patient:</strong> {{ $appointment->patient_name }}</p>
                                <p class="mb-1"><strong>Service:</strong> {{ $appointment->service?->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Doctor:</strong> {{ $appointment->staffMember?->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Date:</strong> {{ $appointment->appointment_date }}</p>
                                <p class="mb-0"><strong>Time:</strong> {{ $appointment->appointment_time }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light p-3 rounded">
                                <h6 class="mb-3">Payment Details</h6>
                                <p class="mb-1"><strong>Status:</strong> {{ ucfirst($status) }}</p>
                                <p class="mb-1"><strong>Payment Status:</strong> {{ $paymentStatus ?? 'Pending' }}</p>
                                <p class="mb-1"><strong>Settlement Status:</strong> {{ $settlementStatus ?? 'Pending' }}</p>
                                <p class="mb-0"><strong>Amount:</strong> ₦{{ number_format($transaction->amount, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        <a href="/verification-portal" class="btn btn-outline-primary">Verify Appointment</a>
                        <a href="/api/transactions/reference/{{ $transaction->transaction_reference }}/receipt" class="btn btn-primary">Download Receipt</a>
                        <a href="/appointment" class="btn btn-outline-secondary">Book Another Appointment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
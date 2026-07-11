@extends('layout.base')

@section('page_title', 'Verification Portal')

@section('page_content')

<x-banner message="Verify Appointment Slots" page="Verification Portal" />

<div class="verification-section">
    <div class="container">
        <div class="verification-card">
            <h3 class="mb-3"><i class="bi bi-shield-check text-primary"></i> Appointment Verification Portal</h3>
            <p class="text-muted">Enter the confirmation code, payment reference, or invoice number from your appointment receipt to confirm the appointment and payment status.</p>

            <form id="verificationForm" onsubmit="verifyCode(event)">
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg" id="verificationCode" placeholder="e.g., APT-2024-ABCDEFGH or PAY-XXXXXX" required>
                    <button class="btn btn-primary" type="submit">Verify</button>
                </div>
            </form>

            <div id="resultContainer" class="verification-result">
                <div id="resultContent"></div>
            </div>

            <hr>
            <p class="text-muted small"><i class="bi bi-info-circle"></i> The portal checks the latest appointment and payment information from the server so you can confirm the booking and download the receipt.</p>
        </div>
    </div>
</div>

<script>
    window.verifyCode = async function (event) {
        event.preventDefault();
        const code = document.getElementById('verificationCode').value.trim();
        const resultContent = document.getElementById('resultContent');
        const resultContainer = document.getElementById('resultContainer');

        resultContainer.classList.add('loading');
        resultContent.innerHTML = '<div class="text-muted">Checking verification code...</div>';

            try {
            const response = await fetch('/api/verification/appointment', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ code })
            });

            let data = {};
            try {
                data = await response.json();
            } catch (e) {
                // ignore parse error
            }

            if (!response.ok || !data.found) {
                const msg = data?.message || 'No appointment or payment record was found for that code.';
                resultContent.innerHTML = `<div class="alert alert-warning">${msg}</div>`;
                return;
            }

            resultContent.innerHTML = `
                <div class="alert alert-success">
                    <h5 class="mb-3">Appointment Verified</h5>
                    <p><strong>Confirmation Code:</strong> ${data.appointment.confirmation_code}</p>
                    <p><strong>Patient:</strong> ${data.appointment.patient_name}</p>
                    <p><strong>Service:</strong> ${data.appointment.service || 'N/A'}</p>
                    <p><strong>Doctor:</strong> ${data.appointment.doctor || 'N/A'}</p>
                    <p><strong>Date / Time:</strong> ${data.appointment.appointment_date || 'N/A'} ${data.appointment.appointment_time || ''}</p>
                    <p><strong>Appointment Status:</strong> ${data.appointment.status}</p>
                    <p><strong>Payment Status:</strong> ${data.payment.status}</p>
                    <p><strong>Transaction Reference:</strong> ${data.payment.transaction_reference || 'N/A'}</p>
                    ${data.payment.receipt_url ? `<a class="btn btn-outline-primary btn-sm mt-2" href="${data.payment.receipt_url}">Download Receipt</a>` : ''}
                </div>`;
        } catch (error) {
            resultContent.innerHTML = '<div class="alert alert-danger">Verification request failed. Please try again.</div>';
        } finally {
            resultContainer.classList.remove('loading');
        }
    };
</script>
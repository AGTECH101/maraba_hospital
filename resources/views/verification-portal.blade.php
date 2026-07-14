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
    window.resolveApiUrl = function (path) {
        const basePath = window.location.pathname.replace(/\/verification-portal\/?$/, '').replace(/\/$/, '');
        const normalizedBase = basePath || '';
        return `${normalizedBase}${path}`;
    };

    window.verifyCode = async function (event) {
        event.preventDefault();
        const code = document.getElementById('verificationCode').value.trim();
        const resultContent = document.getElementById('resultContent');
        const resultContainer = document.getElementById('resultContainer');

        resultContainer.classList.add('loading');
        resultContent.innerHTML = '<div class="text-muted">Checking verification code...</div>';

        try {
            const response = await fetch(window.resolveApiUrl('/api/verification/appointment'), {
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

            const state = data.state || 'invalid';
            let alertClass = 'alert-danger';
            let title = 'Invalid Appointment';
            let icon = 'x-circle-fill';
            let badgeClass = 'bg-danger';
            let badgeLabel = 'Invalid';
            let showMarkUsed = false;

            switch (state) {
                case 'valid':
                    alertClass = 'alert-success';
                    title = 'Appointment Verified';
                    icon = 'check-circle-fill';
                    badgeClass = 'bg-success';
                    badgeLabel = 'Valid';
                    showMarkUsed = true;
                    break;
                case 'expired':
                    alertClass = 'alert-warning';
                    title = 'Appointment Expired';
                    icon = 'clock-history';
                    badgeClass = 'bg-warning text-dark';
                    badgeLabel = 'Expired';
                    break;
                case 'used':
                    alertClass = 'alert-secondary';
                    title = 'Appointment Already Used';
                    icon = 'check-circle-fill';
                    badgeClass = 'bg-secondary';
                    badgeLabel = 'Used';
                    break;
            }

            const usedAt = data.used_at ? new Date(data.used_at).toLocaleString() : '';
            const usedAtMessage = usedAt ? `<p class="mt-2"><strong>Used On:</strong> ${usedAt}</p>` : '';
            const markButton = showMarkUsed ? `<button class="btn btn-outline-primary btn-sm mt-3" type="button" onclick="window.markAppointmentUsed('${code.replace(/'/g, "\\'")}")"><i class="bi bi-check2-circle"></i> Mark as Used</button>` : '';

            resultContent.innerHTML = `
                <div class="alert ${alertClass}">
                    <h5 class="mb-3"><i class="bi bi-${icon}"></i> ${title}</h5>
                    <p><span class="badge ${badgeClass}">${badgeLabel}</span></p>
                    <p><strong>Confirmation Code:</strong> ${data.appointment.confirmation_code}</p>
                    <p><strong>Patient:</strong> ${data.appointment.patient_name}</p>
                    <p><strong>Service:</strong> ${data.appointment.service || 'N/A'}</p>
                    <p><strong>Doctor:</strong> ${data.appointment.doctor || 'N/A'}</p>
                    <p><strong>Date / Time:</strong> ${data.appointment.appointment_date || 'N/A'} ${data.appointment.appointment_time || ''}</p>
                    <p><strong>Appointment Status:</strong> ${data.appointment.status}</p>
                    <p><strong>Payment Status:</strong> ${data.payment.status}</p>
                    <p><strong>Transaction Reference:</strong> ${data.payment.transaction_reference || 'N/A'}</p>
                    ${usedAtMessage}
                    ${data.payment.receipt_url ? `<a class="btn btn-outline-primary btn-sm mt-2" href="${data.payment.receipt_url}">Download Receipt</a>` : ''}
                    ${markButton}
                </div>`;
        } catch (error) {
            resultContent.innerHTML = '<div class="alert alert-danger">Verification request failed. Please try again.</div>';
        } finally {
            resultContainer.classList.remove('loading');
        }
    };

    window.markAppointmentUsed = async function (code) {
        if (!confirm('Mark this appointment as used?')) return;

        const resultContent = document.getElementById('resultContent');
        resultContent.innerHTML = '<div class="text-muted">Marking appointment as used...</div>';

        try {
            const response = await fetch(window.resolveApiUrl('/api/verification/mark-used'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ code })
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                resultContent.innerHTML = `<div class="alert alert-danger">${data.message || 'Unable to mark the appointment as used.'}</div>`;
                return;
            }

            document.getElementById('verificationCode').value = code;
            await window.verifyCode({ preventDefault() {} });
        } catch (error) {
            resultContent.innerHTML = '<div class="alert alert-danger">Unable to mark the appointment as used.</div>';
        }
    };
</script>
// ============================================================
//  SPINNER
// ============================================================
function removeSpinner() {
    var spinner = document.getElementById('spinner');
    if (spinner) {
        spinner.classList.remove('show');
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', removeSpinner);
} else {
    removeSpinner();
}
window.addEventListener('load', removeSpinner);

// ============================================================
//  MAIN INITIALIZATION (jQuery-dependent)
// ============================================================
function initializeWithJQuery() {
    var $ = window.jQuery;
    if (typeof $ === 'undefined') {
        console.warn('jQuery not available yet, will retry...');
        setTimeout(initializeWithJQuery, 100);
        return;
    }
    "use strict";

    // WOW.js
    try {
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }
    } catch (e) {
        console.warn('WOW.js not available:', e);
    }

    // Sticky Navbar
    $(window).on('scroll', function () {
        try {
            if ($(this).scrollTop() > 300) {
                $('.sticky-top').addClass('bg-white shadow-sm').css('top', '-1px');
            } else {
                $('.sticky-top').removeClass('bg-white shadow-sm').css('top', '-100px');
            }
        } catch (e) {
            console.warn('Sticky navbar error:', e);
        }
    });

    // Facts counter
    try {
        if ($('[data-toggle="counter-up"]').length > 0 && typeof jQuery.fn.counterUp !== 'undefined') {
            $('[data-toggle="counter-up"]').counterUp({
                delay: 10,
                time: 2000
            });
        }
    } catch (e) {
        console.warn('Counter-up not available:', e);
    }

    // Back to top
    $(window).on('scroll', function () {
        try {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').fadeIn('slow');
            } else {
                $('.back-to-top').fadeOut('slow');
            }
        } catch (e) {
            console.warn('Back to top button error:', e);
        }
    });
    try {
        $('.back-to-top').on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
            return false;
        });
    } catch (e) {
        console.warn('Back to top click handler error:', e);
    }
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeWithJQuery);
} else {
    initializeWithJQuery();
}

// ============================================================
//  ADMIN DASHBOARD
// ============================================================
(function() {
    if (document.getElementById('staffListBody')) {
        let staffData = [];
        let transactions = [];
        let appointments = [];
        let specializations = [];
        let servicesCatalog = [];

        function formatCurrency(value) {
            return '₦' + Number(value || 0).toLocaleString();
        }

        function computeTransactionSummary() {
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(today.getDate() - 30);
            let lastMonthTotal = 0, paidTotal = 0, pendingTotal = 0;
            transactions.forEach(t => {
                const txDate = new Date(t.created_at || t.date || '');
                if (!Number.isNaN(txDate.getTime()) && txDate >= thirtyDaysAgo && txDate <= today) lastMonthTotal += Number(t.amount || 0);
                if ((t.status || '').toLowerCase() === 'paid') paidTotal += Number(t.amount || 0);
                if ((t.status || '').toLowerCase() === 'pending') pendingTotal += Number(t.amount || 0);
            });
            document.getElementById('totalLastMonth').innerHTML = formatCurrency(lastMonthTotal);
            document.getElementById('totalPaid').innerHTML = formatCurrency(paidTotal);
            document.getElementById('totalPending').innerHTML = formatCurrency(pendingTotal);
        }

        function renderTransactions() {
            const tbody = document.getElementById('transactionsBody');
            tbody.innerHTML = '';
            transactions.forEach(tx => {
                const status = (tx.status || 'pending').toLowerCase();
                const statusClass = status === 'paid' ? 'status-paid' : (status === 'pending' ? 'status-pending' : 'status-failed');
                tbody.innerHTML += `<tr>
                    <td>${tx.transaction_reference || tx.id}</td>
                    <td>${tx.appointment?.patient_name || 'N/A'}</td>
                    <td>${tx.appointment?.service?.name || 'N/A'}</td>
                    <td>${formatCurrency(tx.amount)}</td>
                    <td>${(tx.created_at || tx.date || '').split('T')[0]}</td>
                    <td><span class="status-badge ${statusClass}">${status.toUpperCase()}</span></td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick="viewTransactionDetail('${tx.id}')">View Details</button></td>
                </tr>`;
            });
            if (transactions.length === 0) tbody.innerHTML = '<tr><td colspan="7" class="text-center">No transactions found</td></tr>';
            computeTransactionSummary();
        }

        window.viewTransactionDetail = function(txId) {
            const tx = transactions.find(t => String(t.id) === String(txId));
            if (!tx) return;
            const status = (tx.status || 'pending').toLowerCase();
            const statusClass = status === 'paid' ? 'status-paid' : (status === 'pending' ? 'status-pending' : 'status-failed');
            document.getElementById('transactionDetailBody').innerHTML = `
                <ul class="list-group">
                    <li class="list-group-item"><strong>Transaction ID:</strong> ${tx.transaction_reference || tx.id}</li>
                    <li class="list-group-item"><strong>Invoice Number:</strong> ${tx.invoice_number || 'N/A'}</li>
                    <li class="list-group-item"><strong>Patient:</strong> ${tx.appointment?.patient_name || 'N/A'}</li>
                    <li class="list-group-item"><strong>Service:</strong> ${tx.appointment?.service?.name || 'N/A'}</li>
                    <li class="list-group-item"><strong>Amount:</strong> ${formatCurrency(tx.amount)}</li>
                    <li class="list-group-item"><strong>Date:</strong> ${(tx.created_at || tx.date || '').split('T')[0]}</li>
                    <li class="list-group-item"><strong>Payment Method:</strong> ${tx.payment_method || 'Not specified'}</li>
                    <li class="list-group-item"><strong>Status:</strong> <span class="status-badge ${statusClass}">${status.toUpperCase()}</span></li>
                </ul>
            `;
            new bootstrap.Modal(document.getElementById('transactionDetailModal')).show();
        };

        function renderStaffTable() {
            const tbody = document.getElementById('staffListBody');
            const search = document.getElementById('staffSearch') ? document.getElementById('staffSearch').value.toLowerCase() : '';
            let doctors = 0, techs = 0, scheduled = 0;
            tbody.innerHTML = '';
            staffData.forEach(s => {
                if (s.role === 'doctor') doctors++;
                if (s.role === 'technician') techs++;
                if ((s.appointments || []).length > 0) scheduled++;
                if (!String(s.name || '').toLowerCase().includes(search) && !String(s.email || '').toLowerCase().includes(search) && !String(s.role || '').includes(search)) return;

                const initials = String(s.name || '').split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();
                const avatarHtml = s.image
                    ? `<img src="${s.image}" alt="${s.name}" class="staff-avatar-img" onerror="this.outerHTML='<div class=&quot;staff-avatar&quot;>${initials}</div>'">`
                    : `<div class="staff-avatar">${initials}</div>`;

                tbody.innerHTML += `<tr>
                    <td><div class="staff-name">${avatarHtml}${s.name}</div></td>
                    <td>${s.email}</td>
                    <td><span class="role-badge role-${s.role}">${s.role}</span></td>
                    <td>${formatCurrency(s.salary || 0)}</td>
                    <td><span class="schedule-badge">${(s.appointments || []).length > 0 ? '✓' : '○'}</span> (${(s.appointments || []).length})</td>
                    <td class="table-actions">
                        <button class="btn btn-sm btn-outline-primary action-btn" onclick="viewStaffDetail(${s.id})"><i class="bi bi-eye"></i> View</button>
                        <button class="btn btn-sm btn-outline-warning action-btn" onclick="openEditModal(${s.id})"><i class="bi bi-pencil"></i> Edit</button>
                    </td>
                </tr>`;
            });
            document.getElementById('totalStaff').innerText = staffData.length;
            document.getElementById('totalDoctors').innerText = doctors;
            document.getElementById('totalTechnicians').innerText = techs;
            document.getElementById('scheduledCount').innerText = scheduled;
        }

        window.filterStaffTable = function() { renderStaffTable(); };
        window.viewStaffDetail = function(id) {
            const s = staffData.find(x => String(x.id) === String(id));
            if (!s) return;
            const schedules = Array.isArray(s.appointments) ? s.appointments : [];
            let schedulesHtml = schedules.length === 0 ? '<li class="list-group-item text-muted">No schedules assigned</li>' : schedules.map(sc => `<li class="list-group-item"><strong>${sc.appointment_date} ${sc.appointment_time}</strong> – ${sc.patient_name} (${sc.notes || 'No notes'})</li>`).join('');
            const bioHtml = s.bio && s.bio.trim().length > 0 ? `<div class="bio-text">${s.bio}</div>` : `<div class="bio-text empty-bio">No bio provided</div>`;

            const bigAvatarHtml = s.image
                ? `<img src="${s.image}" alt="${s.name}" width="80" height="80" style="width:80px;height:80px;max-width:100%;border-radius:16px;object-fit:cover;display:block;margin:0 auto;flex-shrink:0;" onerror="this.outerHTML='<div class=&quot;staff-avatar mx-auto&quot; style=&quot;width:80px;height:80px;font-size:32px;&quot;>${String(s.name || '').split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase()}</div>'">`
                : `<div class="staff-avatar mx-auto" style="width:80px;height:80px;font-size:32px;">${String(s.name || '').split(' ').map(n=>n[0]).join('').slice(0,2).toUpperCase()}</div>`;

            document.getElementById('staffDetailBody').innerHTML = `
                <div class="text-center mb-3">
                    ${bigAvatarHtml}
                    <h4>${s.name}</h4>
                    <p class="text-muted">${s.role}</p>
                </div>
                
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Email:</strong> ${s.email}</li>
                    <li class="list-group-item"><strong>Contact:</strong> ${s.phone || 'N/A'}</li>
                    <li class="list-group-item"><strong>Salary:</strong> ${formatCurrency(s.salary || 0)}</li>
                    <li class="list-group-item"><strong>Bio:</strong> ${bioHtml}</li>
                </ul>
                <h6>All Schedules (${schedules.length})</h6>
                <ul class="list-group">${schedulesHtml}</ul>
            `;
            new bootstrap.Modal(document.getElementById('staffDetailModal')).show();
        };

        window.openEditModal = function(id) {
            const staff = staffData.find(s => String(s.id) === String(id));
            if (!staff) return;
            document.getElementById('editStaffId').value = staff.id;
            document.getElementById('editStaffName').value = staff.name;
            document.getElementById('editStaffEmail').value = staff.email;
            document.getElementById('editStaffContact').value = staff.phone || '';
            document.getElementById('editStaffRole').value = staff.role;
            document.getElementById('editStaffSalary').value = staff.salary || 0;
            document.getElementById('editStaffBio').value = staff.bio || '';
            const preview = document.getElementById('editStaffImagePreview');
            const imageInput = document.getElementById('editStaffImage');
            if (preview && imageInput) {
                preview.src = staff.image || '';
                preview.style.display = staff.image ? 'block' : 'none';
                imageInput.value = staff.image || '';
            }
            // populate specializations select
            const editSpecSel = document.getElementById('editStaffSpecializations');
            if (editSpecSel) {
                editSpecSel.innerHTML = '';
                specializations.forEach(spec => {
                    const option = document.createElement('option');
                    option.value = spec.id;
                    option.textContent = spec.name;
                    editSpecSel.appendChild(option);
                });
                const selectedIds = (staff.specialization_ids || []).map(String);
                Array.from(editSpecSel.options).forEach(option => { option.selected = selectedIds.includes(option.value); });
            }
            new bootstrap.Modal(document.getElementById('editStaffModal')).show();
        };

        window.saveEditedStaff = function() {
            const id = document.getElementById('editStaffId').value;
            const formData = new FormData();
            formData.append('_method', 'PATCH');   // ← ADD THIS LINE
            formData.append('name', document.getElementById('editStaffName').value.trim());
            formData.append('email', document.getElementById('editStaffEmail').value.trim());
            formData.append('phone', document.getElementById('editStaffContact').value.trim());
            formData.append('role', document.getElementById('editStaffRole').value);
            formData.append('salary', document.getElementById('editStaffSalary').value);
            formData.append('bio', document.getElementById('editStaffBio').value.trim());
            Array.from(document.getElementById('editStaffSpecializations')?.selectedOptions || []).forEach(option => {
                formData.append('specialization_ids[]', option.value);
            });
            const imageFile = document.getElementById('editStaffImageInput')?.files?.[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }
            if (!formData.get('name') || !formData.get('email') || !formData.get('role') || !formData.get('salary')) {
                alert('Please fill all required fields');
                return;
            }
            fetch(`/api/dashboard/staff/${id}`, {
                method: 'POST',   // ← CHANGED from 'PATCH' to 'POST'
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: formData
            }).then(res => res.json()).then(() => {
                loadDashboardData();
                bootstrap.Modal.getInstance(document.getElementById('editStaffModal')).hide();
            }).catch(() => alert('Unable to update staff member.'));
        };

        window.addNewStaff = function() {
            const formData = new FormData();
            formData.append('name', document.getElementById('newStaffName').value.trim());
            formData.append('email', document.getElementById('newStaffEmail').value.trim());
            formData.append('phone', document.getElementById('newStaffContact').value.trim());
            formData.append('role', document.getElementById('newStaffRole').value);
            formData.append('salary', document.getElementById('newStaffSalary').value);
            formData.append('bio', document.getElementById('newStaffBio').value.trim());
            Array.from(document.getElementById('newStaffSpecializations')?.selectedOptions || []).forEach(option => {
                formData.append('specialization_ids[]', option.value);
            });
            const imageFile = document.getElementById('newStaffImageInput')?.files?.[0];
            if (imageFile) {
                formData.append('image', imageFile);
            }
            if (!formData.get('name') || !formData.get('email') || !formData.get('role') || !formData.get('salary')) {
                alert('Please fill all required fields');
                return;
            }
            fetch('/api/dashboard/staff', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: formData
            }).then(res => res.json()).then(() => {
                loadDashboardData();
                bootstrap.Modal.getInstance(document.getElementById('addStaffModal')).hide();
                document.querySelectorAll('#addStaffModal input, #addStaffModal select, #addStaffModal textarea').forEach(i => i.value = '');
            }).catch(() => alert('Unable to add staff member.'));
        };

        window.previewAdminStaffImage = function(event, previewId) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const hiddenInput = previewId === 'newStaffImagePreview' ? document.getElementById('newStaffImage') : document.getElementById('editStaffImage');
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                if (hiddenInput) {
                    hiddenInput.value = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        };

        window.openAddUserModal = function() {
            document.getElementById('addUserModalTitle').textContent = 'Create New User';
            document.getElementById('addUserId').value = '';
            document.getElementById('addUserName').value = '';
            document.getElementById('addUserEmail').value = '';
            document.getElementById('addUserPhone').value = '';
            document.getElementById('addUserRole').value = 'patient';
            document.getElementById('addUserPassword').value = '';
            document.getElementById('addUserImage').value = '';
            document.getElementById('addUserImagePreview').src = '';
            document.getElementById('addUserImagePreview').style.display = 'none';
            document.getElementById('addUserImagePlaceholder').style.display = 'block';
            new bootstrap.Modal(document.getElementById('addUserModal')).show();
        };

        window.previewNewUserImage = function(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('addUserImagePreview');
                const placeholder = document.getElementById('addUserImagePlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
                document.getElementById('addUserImage').value = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        window.saveNewUser = function() {
            const name = document.getElementById('addUserName').value.trim();
            const email = document.getElementById('addUserEmail').value.trim();
            const phone = document.getElementById('addUserPhone').value.trim();
            const role = document.getElementById('addUserRole').value;
            const password = document.getElementById('addUserPassword').value;
            const imageFile = document.getElementById('addUserImageInput')?.files?.[0] || null;

            if (!name || !email || !role || !password) {
                alert('Please fill all required fields');
                return;
            }

            if (password.length < 8) {
                alert('Password must be at least 8 characters');
                return;
            }

            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', phone);
            formData.append('role', role);
            formData.append('password', password);
            formData.append('is_approved', '1');
            if (imageFile) {
                formData.append('image', imageFile);
            }

            fetch('/api/dashboard/users', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: formData
            })
            .then(res => res.json())
            .then(() => {
                alert('User created successfully');
                loadDashboardData();
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
            })
            .catch(() => alert('Unable to create user'));
        };

        window.previewAdminStaffImage_OLD = function(event, previewId) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const hiddenInput = previewId === 'newStaffImagePreview' ? document.getElementById('newStaffImage') : document.getElementById('editStaffImage');
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                if (hiddenInput) {
                    hiddenInput.value = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        };

        function renderAllSchedules() {
            const tbody = document.getElementById('allSchedulesBody');
            tbody.innerHTML = '';
            appointments.forEach(appt => {
                tbody.innerHTML += `<tr>
                    <td>${appt.staff_member?.name || 'Unassigned'}</td>
                    <td>${appt.staff_member?.role || 'N/A'}</td>
                    <td>${appt.appointment_date} ${appt.appointment_time}</td>
                    <td>${appt.patient_name}</td>
                    <td><button class="btn btn-sm btn-outline-primary" onclick="viewScheduleDetail('${appt.id}')">View Details</button></td>
                </tr>`;
            });
            if (appointments.length === 0) tbody.innerHTML = '<tr><td colspan="5" class="text-center">No schedules found</td></tr>';
        }

        window.viewScheduleDetail = function(id) {
            const appt = appointments.find(a => String(a.id) === String(id));
            if (!appt) return;
            document.getElementById('scheduleDetailBody').innerHTML = `
                <p><strong>Staff:</strong> ${appt.staff_member?.name || 'Unassigned'}</p>
                <p><strong>Appointment Time:</strong> ${appt.appointment_date} ${appt.appointment_time}</p>
                <hr>
                <h6>Patient Information</h6>
                <p><strong>Full Name:</strong> ${appt.patient_name}</p>
                <p><strong>Contact:</strong> ${appt.patient_phone}</p>
                <p><strong>Email:</strong> ${appt.patient_email || 'N/A'}</p>
                <p><strong>Reason for Request:</strong> ${appt.notes || 'No notes'}</p>
            `;
            new bootstrap.Modal(document.getElementById('scheduleDetailModal')).show();
        };

        function populateAvailabilitySelect() {
            const sel = document.getElementById('availabilityStaffSelect');
            if (!sel) return;
            sel.innerHTML = '<option value="">-- Select Staff --</option>';
            staffData.forEach(s => {
                sel.innerHTML += `<option value="${s.id}">${s.name} (${s.role})</option>`;
            });
        }

        if (document.getElementById('availabilityStaffSelect')) {
            document.getElementById('availabilityStaffSelect').addEventListener('change', function() {
                const id = this.value;
                if (!id) { document.getElementById('availabilityContent').style.display = 'none'; return; }
                const staff = staffData.find(s => String(s.id) === String(id));
                if (staff) {
                    const availability = staff.availability || { start: '08:00', end: '17:00', days: [] };
                    document.getElementById('avStartTime').value = availability.start || '08:00';
                    document.getElementById('avEndTime').value = availability.end || '17:00';
                    ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(day => {
                        document.getElementById('av' + day).checked = (availability.days || []).includes(day);
                    });
                    document.getElementById('availabilityContent').style.display = 'block';
                    window.currentAvStaffId = id;
                }
            });
        }

        window.saveStaffAvailability = function() {
            const id = window.currentAvStaffId;
            if (!id) { alert('Please select a staff member first'); return; }
            const days = [];
            ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(day => {
                if (document.getElementById('av' + day).checked) days.push(day);
            });
            const payload = {
                availability: {
                    start: document.getElementById('avStartTime').value,
                    end: document.getElementById('avEndTime').value,
                    days,
                }
            };
            fetch(`/api/staff/${id}/availability`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify(payload)
            }).then(res => res.json()).then(() => {
                loadDashboardData();
                alert('Availability updated successfully.');
            }).catch(() => alert('Unable to update availability.'));
        };

        function renderSpecializations() {
            const tbody = document.getElementById('specializationListBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            specializations.forEach(spec => {
                const serviceNames = (spec.services || []).map(s => s.name).join(', ') || 'None';
                const doctorNames = (spec.staffMembers || []).map(s => s.name).join(', ') || 'None';
tbody.innerHTML += `<tr>
                    <td>${spec.name}</td>
                    <td>${spec.slug}</td>
                    <td>${serviceNames}</td>
                    <td>${doctorNames}</td>
                    <td class="table-actions">
                        <button class="btn btn-sm btn-outline-primary action-btn" onclick="editSpecialization(${spec.id})">Edit</button>
                        <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteSpecialization(${spec.id})">Delete</button>
                    </td>
                </tr>`;
            });
            if (specializations.length === 0) tbody.innerHTML = '<tr><td colspan="5" class="text-center">No specializations found</td></tr>';
        }

        window.openSpecializationModal = function() {
            document.getElementById('specializationModalTitle').textContent = 'Add Specialization';
            document.getElementById('specializationId').value = '';
            document.getElementById('specializationName').value = '';
            document.getElementById('specializationSlug').value = '';
            document.getElementById('specializationDescription').value = '';
            const select = document.getElementById('specializationServiceSelect');
            select.innerHTML = '';
            servicesCatalog.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                select.appendChild(option);
            });
            select.value = '';
            new bootstrap.Modal(document.getElementById('specializationModal')).show();
        };

        window.editSpecialization = function(id) {
            const spec = specializations.find(s => String(s.id) === String(id));
            if (!spec) return;
            document.getElementById('specializationModalTitle').textContent = 'Edit Specialization';
            document.getElementById('specializationId').value = spec.id;
            document.getElementById('specializationName').value = spec.name || '';
            document.getElementById('specializationSlug').value = spec.slug || '';
            document.getElementById('specializationDescription').value = spec.description || '';
            const select = document.getElementById('specializationServiceSelect');
            select.innerHTML = '';
            servicesCatalog.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = service.name;
                select.appendChild(option);
            });
            const selectedIds = (spec.services || []).map(s => String(s.id));
            Array.from(select.options).forEach(option => {
                option.selected = selectedIds.includes(option.value);
            });
            new bootstrap.Modal(document.getElementById('specializationModal')).show();
        };

        window.saveSpecialization = function() {
            const id = document.getElementById('specializationId').value;
            const payload = {
                name: document.getElementById('specializationName').value.trim(),
                slug: document.getElementById('specializationSlug').value.trim(),
                description: document.getElementById('specializationDescription').value.trim(),
                service_ids: Array.from(document.getElementById('specializationServiceSelect').selectedOptions).map(option => Number(option.value)),
            };
            if (!payload.name || !payload.slug) {
                alert('Please enter a name and slug for the specialization.');
                return;
            }
            const method = id ? 'PATCH' : 'POST';
            const url = id ? `/api/dashboard/specializations/${id}` : '/api/dashboard/specializations';
            fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(() => {
                loadDashboardData();
                bootstrap.Modal.getInstance(document.getElementById('specializationModal')).hide();
            })
            .catch(() => alert('Unable to save specialization.'));
        };

        window.deleteSpecialization = function(id) {
            if (!confirm('Delete this specialization?')) return;
            fetch(`/api/dashboard/specializations/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
            })
            .then(res => res.json())
            .then(() => loadDashboardData())
            .catch(() => alert('Unable to delete specialization.'));
        };

        // Simple autocomplete/filtering for specialization selects
        function filterSelectOptions(searchInputId, selectId) {
            const input = document.getElementById(searchInputId);
            const select = document.getElementById(selectId);
            if (!input || !select) return;
            input.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                Array.from(select.options).forEach(opt => {
                    opt.style.display = opt.text.toLowerCase().includes(q) ? '' : 'none';
                });
            });
        }

        filterSelectOptions('newSpecSearch', 'newStaffSpecializations');
        filterSelectOptions('editSpecSearch', 'editStaffSpecializations');

        function renderPendingUsers() {
            const tbody = document.getElementById('pendingUsersBody');
            if (!tbody) return;
            fetch('/api/dashboard/pending-users')
                .then(res => res.json())
                .then(result => {
                    const pending = Array.isArray(result.data) ? result.data : [];
                    tbody.innerHTML = '';
                    if (pending.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No pending approvals</td></tr>';
                        return;
                    }
                    pending.forEach((user) => {
                        tbody.innerHTML += `<tr>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.phone || 'N/A'}</td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                            <td>Pending</td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" onclick="approveUser(${user.id})">Approve</button>
                                <button class="btn btn-sm btn-danger" onclick="declineUser(${user.id})">Decline</button>
                            </td>
                        </tr>`;
                    });
                })
                .catch(() => {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Unable to load pending approvals</td></tr>';
                });
        }

        window.approveUser = function(id) {
            fetch(`/api/dashboard/users/${id}/approve`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
            })
                .then(res => res.json())
                .then(() => {
                    renderPendingUsers();
                    loadDashboardData();
                    alert('User approved. They can now sign in.');
                })
                .catch(() => alert('Unable to approve user.'));
        };

        window.declineUser = function(id) {
            if (!confirm('Decline this user account?')) return;
            fetch(`/api/dashboard/users/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
            })
                .then(res => res.json())
                .then(() => {
                    renderPendingUsers();
                    alert('User declined.');
                })
                .catch(() => alert('Unable to decline user.'));
        };

        function switchToTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-tab') === tabId) link.classList.add('active');
            });
            if (tabId === 'scheduleListTab') renderAllSchedules();
            if (tabId === 'transactionsTab') renderTransactions();
            if (tabId === 'pendingApprovalsTab') renderPendingUsers();
        }

        function loadDashboardData() {
            Promise.all([
                fetch('/api/dashboard/data').then(res => res.json()),
                fetch('/api/services').then(res => res.json())
            ])
                .then(([result, serviceResult]) => {
                    staffData = Array.isArray(result.staff) ? result.staff : [];
                    transactions = Array.isArray(result.transactions) ? result.transactions : [];
                    appointments = Array.isArray(result.appointments) ? result.appointments : [];
                    specializations = Array.isArray(result.specializations) ? result.specializations : [];
                    servicesCatalog = Array.isArray(serviceResult.data) ? serviceResult.data : [];
                    renderStaffTable();
                    populateAvailabilitySelect();
                    renderTransactions();
                    renderAllSchedules();
                    renderSpecializations();
                })
                .catch(() => {
                    renderStaffTable();
                    renderTransactions();
                    renderAllSchedules();
                });
        }

        document.querySelectorAll('.sidebar-nav .nav-link[data-tab]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                if (tabId) switchToTab(tabId);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('staffListBody')) {
                loadDashboardData();
                renderPendingUsers();
            }
            // populate specialization selects when add/edit staff modals are opened
            const addModalEl = document.getElementById('addStaffModal');
            if (addModalEl) {
                addModalEl.addEventListener('show.bs.modal', function() {
                    const sel = document.getElementById('newStaffSpecializations');
                    if (!sel) return;
                    sel.innerHTML = '';
                    specializations.forEach(spec => {
                        const option = document.createElement('option'); option.value = spec.id; option.textContent = spec.name; sel.appendChild(option);
                    });
                });
            }
        });
    }
})();

// ============================================================
//  APPOINTMENT BOOKING
// ============================================================
(function() {
    if (document.getElementById('stepIndicator')) {
        let services = [];
        let staffMembers = [];
        const timeSlots = ['09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '02:00 PM', '03:00 PM', '04:00 PM'];

        const booking = { service: null, patient: { name: '', email: '', phone: '', notes: '' }, doctor: null, date: '', time: '', confirmationCode: '' };
        let currentStep = 1;
        const totalSteps = 6;

        function getToday() { return new Date().toISOString().split('T')[0]; }
        function generateCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let code = 'APT-2024-';
            for (let i = 0; i < 8; i++) code += chars[Math.floor(Math.random() * chars.length)];
            return code;
        }

        function renderStepIndicator() {
            const labels = ['Service', 'Details', 'Doctor', 'Date & Time', 'Review', 'Confirmation'];
            let html = '';
            for (let i = 1; i <= totalSteps; i++) {
                const isActive = i === currentStep, isCompleted = i < currentStep;
                const cls = isCompleted ? 'completed' : (isActive ? 'active' : '');
                html += `<div class="step-dot ${cls}"><div class="circle">${isCompleted ? '<i class="bi bi-check"></i>' : i}</div><span class="label">${labels[i-1]}</span></div>`;
            }
            document.getElementById('stepIndicator').innerHTML = html;
        }

        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
            currentStep = step;
            renderStepIndicator();
            if (step === 5) populateSummary();
            if (step === 6) populateReceipt();
            if (step === 3) renderDoctors();
            if (step === 4) {
                if (!document.getElementById('appointmentDate').value) document.getElementById('appointmentDate').value = getToday();
                renderTimeSlots();
            }
        }

        function renderServices() {
            let html = '';
            services.forEach(s => {
                html += `<div class="col-lg-4 col-md-6"><div class="card service-card p-4 text-center" data-service="${s.id}"><i class="bi ${s.icon || 'bi-heart-pulse'} display-4 text-primary"></i><h6 class="mt-3">${s.name}</h6><p class="text-muted small">${s.description}</p><small class="text-primary">₦${Number(s.price || 0).toLocaleString()}</small></div></div>`;
            });
            document.getElementById('serviceContainer').innerHTML = html;
            document.getElementById('serviceContainer').querySelectorAll('.service-card').forEach(el => {
                el.addEventListener('click', function() {
                    document.getElementById('serviceContainer').querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    booking.service = this.dataset.service;
                    document.querySelector('.step-content[data-step="1"] .next-step').disabled = false;
                });
            });
        }

        function renderDoctors() {
            const serviceId = booking.service;
            if (!serviceId) return;
            const selectedService = services.find(service => String(service.id) === String(serviceId));
            const specializationIds = Array.isArray(selectedService?.specialization_ids) ? selectedService.specialization_ids : [];
            const docList = staffMembers.filter(member => member.role === 'doctor' && (
                specializationIds.length === 0 || member.specialization_ids?.some(id => specializationIds.includes(id))
            ));
            let html = '';
            if (docList.length === 0) html = '<div class="col-12"><p class="text-muted">No doctors available for this service.</p></div>';
            else {
                docList.forEach(d => {
                    const availability = d.availability || { start: '08:00', end: '17:00', days: [] };
                    html += `<div class="col-lg-4 col-md-6"><div class="card doctor-card p-3" data-doctor-id="${d.id}"><img src="${d.image || 'img/team-1.jpg'}" alt="${d.name}" class="img-fluid rounded-circle mx-auto" style="width:80px;height:80px;object-fit:cover;"><h6 class="mt-2">${d.name}</h6><small class="text-primary">${d.specialty || d.role}</small><small class="text-muted">${availability.start || '08:00'} - ${availability.end || '17:00'}</small></div></div>`;
                });
            }
            document.getElementById('doctorContainer').innerHTML = html;
            document.getElementById('doctorContainer').querySelectorAll('.doctor-card').forEach(el => {
                el.addEventListener('click', function() {
                    document.getElementById('doctorContainer').querySelectorAll('.doctor-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    const id = parseInt(this.dataset.doctorId);
                    const doc = docList.find(d => d.id === id);
                    booking.doctor = doc;
                    document.querySelector('.step-content[data-step="3"] .next-step').disabled = false;
                });
            });
        }

        function renderTimeSlots() {
            const selectedDate = document.getElementById('appointmentDate').value;
            if (!selectedDate) { document.getElementById('timeSlotContainer').innerHTML = '<p class="text-muted">Please select a date first.</p>'; return; }
            const doctor = booking.doctor;
            // determine which availability to use
            let availability = null;
            if (doctor && doctor.availability) availability = doctor.availability;
            // default fallback
            if (!availability) availability = { start: '09:00', end: '17:00', days: ['Mon','Tue','Wed','Thu','Fri'] };
            const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
            const dayIndex = new Date(selectedDate).getDay();
            const dayName = dayNames[dayIndex];
            if (Array.isArray(availability.days) && availability.days.length > 0 && !availability.days.includes(dayName)) {
                document.getElementById('timeSlotContainer').innerHTML = '<p class="text-muted">Selected doctor is not available on this date. Choose another date or doctor.</p>';
                return;
            }
            // generate slots between availability.start and availability.end at 60-minute intervals
            function toMinutes(t) { const [hh, mm] = t.split(':').map(Number); return hh * 60 + mm; }
            function formatSlot(mins) { const h = Math.floor(mins/60); const m = mins % 60; const ampm = h >= 12 ? 'PM' : 'AM'; let hr = h % 12; if (hr === 0) hr = 12; return `${String(hr).padStart(2,'0')}:${String(m).padStart(2,'0')} ${ampm}`; }
            const startMin = toMinutes(availability.start || '09:00');
            const endMin = toMinutes(availability.end || '17:00');
            const slotStep = 60; // minutes
            let html = '';
            for (let t = startMin; t + slotStep <= endMin; t += slotStep) {
                const slotLabel = formatSlot(t);
                html += `<div class="col-6 col-md-4"><div class="time-slot" data-time="${slotLabel}">${slotLabel}</div></div>`;
            }
            if (html === '') html = '<p class="text-muted">No time slots available for the selected date.</p>';
            document.getElementById('timeSlotContainer').innerHTML = html;
            document.getElementById('timeSlotContainer').querySelectorAll('.time-slot').forEach(el => {
                el.addEventListener('click', function() {
                    document.getElementById('timeSlotContainer').querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    booking.time = this.dataset.time;
                    document.querySelector('.step-content[data-step="4"] .next-step').disabled = false;
                });
            });
        }

        function populateSummary() {
            document.getElementById('summaryService').textContent = services.find(s => String(s.id) === String(booking.service))?.name || '-';
            document.getElementById('summaryDoctor').textContent = booking.doctor?.name || '-';
            document.getElementById('summaryDate').textContent = booking.date ? new Date(booking.date).toLocaleDateString('en-US', { weekday:'short', year:'numeric', month:'short', day:'numeric' }) : '-';
            document.getElementById('summaryTime').textContent = booking.time || '-';
            document.getElementById('summaryPatient').textContent = booking.patient.name || '-';
            // Payment summary: service price + flat service charge (₦320)
            const service = services.find(s => String(s.id) === String(booking.service));
            const servicePrice = service ? Number(service.price || 0) : 0;
            const serviceCharge = 320;
            const total = servicePrice + serviceCharge;
            booking.amount = total;
            const fmt = v => '₦' + Number(v || 0).toLocaleString();
            const elService = document.getElementById('paymentServiceAmount');
            const elCharge = document.getElementById('paymentServiceFee');
            const elTotal = document.getElementById('paymentTotal');
            if (elService) elService.textContent = fmt(servicePrice);
            if (elCharge) elCharge.textContent = fmt(serviceCharge);
            if (elTotal) elTotal.textContent = fmt(total);
        }

        function populateReceipt() {
            document.getElementById('receiptCode').textContent = booking.confirmationCode;
            document.getElementById('receiptDoctor').textContent = booking.doctor?.name || '-';
            document.getElementById('receiptService').textContent = services.find(s => String(s.id) === String(booking.service))?.name || '-';
            document.getElementById('receiptDate').textContent = booking.date ? new Date(booking.date).toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' }) : '-';
            document.getElementById('receiptTime').textContent = booking.time || '-';
            document.getElementById('receiptPatient').textContent = booking.patient.name || '-';
            document.getElementById('receiptPhone').textContent = booking.patient.phone || '-';
            // receipt amount from booking.amount if available
            const amountEl = document.getElementById('receiptAmount');
            if (amountEl) amountEl.textContent = '₦' + Number(booking.amount || 0).toLocaleString();
        }

        function validateStep(step) {
            switch(step) {
                case 1: if (!booking.service) { alert('Please select a service.'); return false; } return true;
                case 2:
                    const name = document.getElementById('patientName').value.trim();
                    const phone = document.getElementById('patientPhone').value.trim();
                    if (!name) { document.getElementById('patientName').classList.add('is-invalid'); return false; } else { document.getElementById('patientName').classList.remove('is-invalid'); }
                    if (!phone) { document.getElementById('patientPhone').classList.add('is-invalid'); return false; } else { document.getElementById('patientPhone').classList.remove('is-invalid'); }
                    booking.patient.name = name;
                    booking.patient.email = document.getElementById('patientEmail').value.trim();
                    booking.patient.phone = phone;
                    booking.patient.notes = document.getElementById('patientNotes').value.trim();
                    return true;
                case 3: if (!booking.doctor) { alert('Please select a doctor.'); return false; } return true;
                case 4:
                    const date = document.getElementById('appointmentDate').value;
                    if (!date) { alert('Please select a date.'); return false; }
                    if (!booking.time) { alert('Please select a time slot.'); return false; }
                    booking.date = date;
                    return true;
                case 5:
                    if (!document.getElementById('termsCheck').checked) { alert('Please agree to the terms and conditions.'); return false; }
                    return true;
                default: return true;
            }
        }

        function goToStep(step) {
            if (step < 1 || step > totalSteps) return;
            if (step > currentStep && !validateStep(currentStep)) return;
            showStep(step);
        }

        document.addEventListener('click', function(e) {
            if (e.target.closest('.next-step')) {
                const btn = e.target.closest('.next-step');
                const stepEl = btn.closest('.step-content');
                const step = parseInt(stepEl.dataset.step);
                if (validateStep(step)) { goToStep(step === 4 ? 5 : step + 1); }
            }
            if (e.target.closest('.prev-step')) {
                const btn = e.target.closest('.prev-step');
                const stepEl = btn.closest('.step-content');
                goToStep(parseInt(stepEl.dataset.step) - 1);
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.id === 'appointmentDate') { booking.date = e.target.value; renderTimeSlots(); }
        });

        document.getElementById('payBtn').addEventListener('click', function() {
            if (!validateStep(5)) return;
            document.getElementById('paymentOverlay').classList.add('show');
            fetch('/api/appointments/initiate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({
                    service_id: booking.service,
                    staff_member_id: booking.doctor?.id || null,
                    patient_name: booking.patient.name,
                    patient_email: booking.patient.email,
                    patient_phone: booking.patient.phone,
                    appointment_date: booking.date,
                    appointment_time: booking.time,
                    notes: booking.patient.notes,
                })
            })
            .then(response => response.text().then(text => {
                let data = null;
                try { data = JSON.parse(text); } catch (e) { data = { __raw: text }; }
                return { ok: response.ok, status: response.status, data, text };
            }))
            .then(({ ok, status, data, text }) => {
                document.getElementById('paymentOverlay').classList.remove('show');
                if (!ok) {
                    const msg = (data && data.message) ? data.message : `Payment initiation failed (status ${status}).`;
                    console.error('Payment initiation failed response:', text);
                    alert(msg);
                    return;
                }
                if (data && data.checkout_url) {
                    window.open(data.checkout_url, '_blank');
                    alert('A payment window has been opened. Complete payment and return to this page.');

                    // start polling for transaction status by payment reference
                    const paymentRef = data.payment_reference;
                    let pollCount = 0;
                    const pollMax = 60 * 5 / 3; // ~5 minutes at 3s intervals
                    const pollInterval = setInterval(async () => {
                        pollCount++;
                        try {
                            const res = await fetch(`/api/transactions/reference/${encodeURIComponent(paymentRef)}`);
                            if (!res.ok) {
                                if (pollCount > pollMax) clearInterval(pollInterval);
                                return;
                            }
                            const json = await res.json();
                            const tx = json.data.transaction;
                            const appt = json.data.appointment;
                            const status = (tx.status || '').toLowerCase();
                            if (status === 'paid' || status === 'failed') {
                                clearInterval(pollInterval);
                                if (status === 'paid') {
                                    booking.confirmationCode = tx.invoice_number || tx.transaction_reference;
                                    booking.amount = json.data.breakdown.total || tx.amount || booking.amount;
                                    if (appt) {
                                        booking.patient.name = appt.patient_name || booking.patient.name;
                                        booking.patient.email = appt.patient_email || booking.patient.email;
                                        booking.patient.phone = appt.patient_phone || booking.patient.phone;
                                        booking.date = appt.appointment_date || booking.date;
                                        booking.time = appt.appointment_time || booking.time;
                                        booking.doctor = appt.staff_member || booking.doctor;
                                    }
                                    renderReceiptContainer({ transaction: tx, appointment: appt, breakdown: json.data.breakdown });
                                    setTimeout(() => {
                                        window.downloadReceiptPNG(true).then(() => {
                                            window.location.href = `/payment-status?paymentReference=${encodeURIComponent(paymentRef)}`;
                                        }).catch(() => {
                                            window.location.href = `/payment-status?paymentReference=${encodeURIComponent(paymentRef)}`;
                                        });
                                    }, 500);
                                } else {
                                    window.location.href = `/payment-status?paymentReference=${encodeURIComponent(paymentRef)}`;
                                }
                            }
                        } catch (e) {
                            console.error('Polling error', e);
                        }
                        if (pollCount > pollMax) clearInterval(pollInterval);
                    }, 3000);
                } else {
                    console.error('Unexpected payment initiation response:', data, text);
                    alert('Unable to start payment. Please try again or contact support.');
                }
            })
            .catch(error => {
                document.getElementById('paymentOverlay').classList.remove('show');
                console.error('Payment initiation error:', error);
                alert('Payment initiation failed. Please try again.');
            });
        });

        // downloadReceiptPNG(useHidden:Boolean) -> returns Promise
        window.downloadReceiptPNG = function(useHidden = false) {
            return new Promise((resolve, reject) => {
                const receiptId = useHidden ? 'hiddenReceiptContainer' : 'receiptContainer';
                const receipt = document.getElementById(receiptId);
                if (!receipt) return reject(new Error('Receipt element not found'));
                if (typeof html2canvas === 'undefined') return reject(new Error('html2canvas not loaded'));
                html2canvas(receipt, { scale: 2, backgroundColor: '#ffffff', logging: false, allowTaint: true, useCORS: true })
                .then(canvas => {
                    if (canvas.toBlob) {
                        canvas.toBlob(function(blob) {
                            try {
                                const url = URL.createObjectURL(blob);
                                const link = document.createElement('a');
                                link.href = url;
                                link.download = `Maraba-Hospital-Receipt-${booking.confirmationCode || Date.now()}.png`;
                                document.body.appendChild(link);
                                link.click();
                                link.remove();
                                URL.revokeObjectURL(url);
                                resolve();
                            } catch (e) { reject(e); }
                        }, 'image/png');
                    } else {
                        try {
                            const dataUrl = canvas.toDataURL('image/png');
                            const link = document.createElement('a');
                            link.href = dataUrl;
                            link.download = `Maraba-Hospital-Receipt-${booking.confirmationCode || Date.now()}.png`;
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            resolve();
                        } catch (e) { reject(e); }
                    }
                }).catch(err => { console.error(err); reject(err); });
            });
        };

        function renderReceiptContainer(data) {
            try {
                const tx = data.transaction || {};
                const appt = data.appointment || {};
                const breakdown = data.breakdown || { service_amount: 0, service_charge: 0, total: tx.amount || 0 };
                const container = document.getElementById('hiddenReceiptContainer') || document.getElementById('receiptContainer');
                // render off-screen so it doesn't flash on the UI but is available to html2canvas
                container.style.position = 'fixed';
                container.style.left = '-9999px';
                container.style.top = '0';
                container.style.display = 'block';
                container.innerHTML = `
                    <div style="width:800px;padding:24px;font-family:Arial,Helvetica,sans-serif;color:#222;background:#fff;border:1px solid #eee;">
                        <div style="text-align:center;margin-bottom:12px">
                            <h2 style="margin:0">Maraba Hospital</h2>
                            <div style="color:#666">Receipt</div>
                        </div>
                        <div style="margin-bottom:12px">
                            <div><strong>Invoice:</strong> ${tx.invoice_number || ''}</div>
                            <div><strong>Transaction Ref:</strong> ${tx.transaction_reference || ''}</div>
                            <div><strong>Date:</strong> ${new Date(tx.created_at || Date.now()).toLocaleString()}</div>
                        </div>
                        <div style="border:1px solid #f1f1f1;padding:12px;border-radius:6px;margin-bottom:12px">
                            <div><strong>Patient:</strong> ${appt.patient_name || ''}</div>
                            <div><strong>Email:</strong> ${appt.patient_email || 'N/A'}</div>
                            <div><strong>Phone:</strong> ${appt.patient_phone || ''}</div>
                            <div><strong>Service:</strong> ${appt.service?.name || ''}</div>
                            <div><strong>Doctor:</strong> ${appt.staff_member?.name || ''}</div>
                            <div><strong>Date / Time:</strong> ${appt.appointment_date || ''} ${appt.appointment_time || ''}</div>
                        </div>
                        <div>
                            <table style="width:100%;border-collapse:collapse">
                                <tr><td>Service</td><td style="text-align:right">₦${Number(breakdown.service_amount).toLocaleString()}</td></tr>
                                <tr><td>Service Charge</td><td style="text-align:right">₦${Number(breakdown.service_charge).toLocaleString()}</td></tr>
                                <tr style="font-weight:bold;border-top:1px solid #eee"><td style="text-align:right">Total</td><td style="text-align:right">₦${Number(breakdown.total).toLocaleString()}</td></tr>
                            </table>
                        </div>
                        <div style="margin-top:12px;padding:10px;background:#f7f7f9;border-left:6px solid #007bff">
                            <div><strong>Verification code:</strong> ${tx.transaction_reference || ''}</div>
                            <div style="color:#666;font-size:12px">Use this code to verify this payment with hospital staff or support.</div>
                        </div>
                    </div>
                `;
            } catch (err) { console.error('renderReceiptContainer error', err); }
        }

                // fallback: show manual download prompt if auto-download may be blocked
                function showReceiptManualPrompt() {
                        if (typeof bootstrap === 'undefined') {
                                alert('Receipt generated. Click the download button to save it.');
                                return;
                        }
                        const modalHtml = `
                                <div class="modal fade" id="receiptManualModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Receipt Ready</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">Your receipt has been generated. If it did not download automatically, click the button below to download it manually.</div>
                                            <div class="modal-footer"><button type="button" class="btn btn-primary" id="manualDownloadBtn">Download Receipt</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                                        </div>
                                    </div>
                                </div>`;
                        const div = document.createElement('div'); div.innerHTML = modalHtml; document.body.appendChild(div);
                        const modalEl = document.getElementById('receiptManualModal');
                        const bsModal = new bootstrap.Modal(modalEl);
                        document.getElementById('manualDownloadBtn').addEventListener('click', () => { window.downloadReceiptPNG(); });
                        bsModal.show();
                }

        function loadAppointmentData() {
            Promise.all([
                fetch('/api/services').then(res => res.json()),
                fetch('/api/staff').then(res => res.json())
            ])
            .then(([serviceResult, staffResult]) => {
                services = Array.isArray(serviceResult.data) ? serviceResult.data : [];
                staffMembers = Array.isArray(staffResult.data) ? staffResult.data : [];
                renderServices();
                renderStepIndicator();
                showStep(1);
            })
            .catch(() => {
                services = [];
                staffMembers = [];
                renderServices();
                renderStepIndicator();
                showStep(1);
            });
        }

        function initAppointment() {
            document.getElementById('appointmentDate').min = getToday();
            document.getElementById('appointmentDate').value = getToday();
            loadAppointmentData();
        }
        document.addEventListener('DOMContentLoaded', initAppointment);
    }
})();

// ============================================================
//  CONTACT FORM
// ============================================================
(function() {
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const feedback = document.getElementById('formFeedback');
            feedback.className = 'form-feedback';
            feedback.style.display = 'none';
            if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
            const formData = new FormData(form);
            const data = { name: formData.get('name'), email: formData.get('email'), subject: formData.get('subject'), message: formData.get('message') };
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const submitBtn = document.getElementById('submitBtn');
            btnText.textContent = 'Sending...';
            btnSpinner.classList.remove('d-none');
            submitBtn.disabled = true;
            const apiUrl = '/api/contact';
            fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify(data)
            })
            .then(response => { if (!response.ok) { return response.json().then(err => { throw new Error(err.message || 'Server error'); }); } return response.json(); })
            .then(result => {
                feedback.textContent = result.message || 'Your message was sent successfully! We will get back to you shortly.';
                feedback.className = 'form-feedback success';
                feedback.style.display = 'block';
                form.reset();
                form.classList.remove('was-validated');
            })
            .catch(error => {
                feedback.textContent = error.message || 'Something went wrong. Please try again later.';
                feedback.className = 'form-feedback error';
                feedback.style.display = 'block';
            })
            .finally(() => {
                btnText.textContent = 'Send Message';
                btnSpinner.classList.add('d-none');
                submitBtn.disabled = false;
            });
        });
        form.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) this.classList.remove('is-invalid');
                else this.classList.add('is-invalid');
            });
        });
    }
})();

// ============================================================
//  LOGIN & REGISTER
// ============================================================
(function() {
    window.togglePassword = function(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') { input.type = 'text'; icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
        else { input.type = 'password'; icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
    };

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        window.previewProfileImage = function(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('profilePreview');
                const placeholder = document.getElementById('uploadPlaceholder');
                const hiddenInput = document.getElementById('profileImageHidden');
                img.src = e.target.result;
                img.style.display = 'block';
                placeholder.style.display = 'none';
                if (hiddenInput) {
                    hiddenInput.value = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        };
        window.selectRole = function(el) {
            document.querySelectorAll('.role-option').forEach(opt => opt.classList.remove('active'));
            el.classList.add('active');
            el.querySelector('input[type="radio"]').checked = true;
        };
    }
})();

// ============================================================
//  PAGES DIRECTORY
// ============================================================
(function() {
    if (document.getElementById('pagesGrid')) {
        const pages = [
            { icon: '🏠', title: 'Home', path: '/', type: 'public', desc: 'Main landing page with service overview' },
            { icon: '📋', title: 'About', path: '/about', type: 'public', desc: 'Hospital information and background' },
            { icon: '🏥', title: 'Services', path: '/service', type: 'public', desc: 'Complete list of medical services' },
            { icon: '👥', title: 'Team', path: '/team', type: 'public', desc: 'Meet our medical professionals' },
            { icon: '📞', title: 'Contact', path: '/contact', type: 'public', desc: 'Contact information and location' },
            { icon: '📅', title: 'Book Appointment', path: '/appointment', type: 'appointment', desc: 'Single‑page appointment wizard' },
            { icon: '📂', title: 'Pages Directory', path: '/pages-directory', type: 'public', desc: 'Complete pages directory' },
            { icon: '🔐', title: 'Admin Dashboard', path: '/admin-dashboard', type: 'admin', desc: 'Staff management & administration' },
            { icon: '👥', title: 'Staff Dashboard', path: '/staff-dashboard', type: 'staff', desc: 'Personal staff portal' },
            { icon: '🔑', title: 'Login', path: '/login', type: 'public', desc: 'User login page' },
            { icon: '📝', title: 'Register', path: '/signup', type: 'public', desc: 'New user registration' },
            { icon: '🔐', title: 'Forgot Password', path: '/password-reset-email', type: 'public', desc: 'Request password reset' },
            { icon: '🔑', title: 'Reset Password', path: '/password-reset-link', type: 'public', desc: 'Set new password' },
            { icon: '✅', title: 'Reset Confirmation', path: '/password-reset-confirmation', type: 'public', desc: 'Password reset success' },
            { icon: '✅', title: 'Verification Portal', path: '/verification-portal', type: 'public', desc: 'Verify appointment codes' }
        ];
        let currentFilter = 'all';

        function renderPages(pageList) {
            const grid = document.getElementById('pagesGrid');
            grid.innerHTML = '';
            if (pageList.length === 0) {
                grid.style.display = 'none';
                document.getElementById('noResults').style.display = 'block';
                return;
            }
            grid.style.display = 'grid';
            document.getElementById('noResults').style.display = 'none';
            pageList.forEach(p => {
                const card = document.createElement('div');
                card.className = 'page-card';
                card.innerHTML = `<div class="page-icon">${p.icon}</div><div class="page-title">${p.title}</div><div class="page-path">${p.path}</div><div class="page-badge">${p.type.toUpperCase()}</div><div class="page-description">${p.desc}</div><a href="${p.path}" class="page-link">Visit Page →</a>`;
                grid.appendChild(card);
            });
        }

        function updateStats(pageList) {
            const total = pageList.length;
            const publicCount = pageList.filter(p => p.type === 'public').length;
            const appointmentCount = pageList.filter(p => p.type === 'appointment').length;
            const adminCount = pageList.filter(p => p.type === 'admin').length;
            const staffCount = pageList.filter(p => p.type === 'staff').length;
            document.getElementById('statCards').innerHTML = `
                <div class="stat-card"><i class="bi bi-file-earmark" style="font-size:24px;"></i><div class="stat-number">${total}</div><div class="stat-label">Total Pages</div></div>
                <div class="stat-card"><i class="bi bi-globe" style="font-size:24px;"></i><div class="stat-number">${publicCount}</div><div class="stat-label">Public Pages</div></div>
                <div class="stat-card"><i class="bi bi-calendar-check" style="font-size:24px;"></i><div class="stat-number">${appointmentCount}</div><div class="stat-label">Appointment Flow</div></div>
                <div class="stat-card"><i class="bi bi-lock" style="font-size:24px;"></i><div class="stat-number">${adminCount + staffCount}</div><div class="stat-label">Admin & Staff</div></div>
            `;
        }

        window.filterByType = function(type) {
            currentFilter = type;
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            const filtered = type === 'all' ? pages : pages.filter(p => p.type === type);
            renderPages(filtered);
            updateStats(filtered);
        };

        window.filterPages = function() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            let filtered = pages.filter(p => p.title.toLowerCase().includes(search) || p.desc.toLowerCase().includes(search) || p.path.toLowerCase().includes(search));
            if (currentFilter !== 'all') filtered = filtered.filter(p => p.type === currentFilter);
            renderPages(filtered);
            updateStats(filtered);
        };

        function initDirectory() {
            renderPages(pages);
            updateStats(pages);
        }
        document.addEventListener('DOMContentLoaded', initDirectory);
    }
})();


// ============================================================
//  STAFF DASHBOARD
// ============================================================
(function() {
    if (document.getElementById('profileName')) {
        let staffProfile = null;
        let appointments = [];

        function renderBio(bioText) {
            const display = document.getElementById('bioDisplay');
            if (bioText && bioText.trim().length > 0) {
                display.innerHTML = bioText;
                display.classList.remove('empty-bio');
            } else {
                display.innerHTML = 'No bio added yet. Click "Edit" to write your professional biography.';
                display.classList.add('empty-bio');
            }
        }

        function renderTodaySchedules(apps) {
            const container = document.getElementById('todaySchedules');
            if (apps.length === 0) { container.innerHTML = '<li class="text-muted">No appointments today</li>'; return; }
            container.innerHTML = apps.map(a => `<li class="schedule-item"><div class="schedule-time">${a.appointment_time}</div><div>${a.patient_name}</div></li>`).join('');
        }
        function renderWeekSchedules(apps) {
            const container = document.getElementById('weekSchedules');
            if (apps.length === 0) { container.innerHTML = '<li class="text-muted">No upcoming appointments this week</li>'; return; }
            container.innerHTML = apps.map(a => `<li class="schedule-item"><div class="schedule-time">${a.appointment_date}</div><div>${a.patient_name}</div></li>`).join('');
        }

        window.openBioModal = function() {
            document.getElementById('bioTextarea').value = staffProfile?.bio || '';
            new bootstrap.Modal(document.getElementById('bioModal')).show();
        };

        window.saveBio = function() {
            const newBio = document.getElementById('bioTextarea').value.trim();
            if (!staffProfile) return;
            fetch(`/api/staff/${staffProfile.id}/bio`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ bio: newBio })
            }).then(res => res.json()).then(() => {
                loadStaffDashboard();
                bootstrap.Modal.getInstance(document.getElementById('bioModal')).hide();
            }).catch(() => alert('Unable to update bio.'));
        };

        window.clearBio = function() {
            if (!staffProfile) return;
            fetch(`/api/staff/${staffProfile.id}/bio`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ bio: '' })
            }).then(res => res.json()).then(() => loadStaffDashboard()).catch(() => alert('Unable to clear bio.'));
        };

        window.saveStaffOwnAvailability = function() {
            if (!staffProfile) return;
            const availability = {
                start: document.getElementById('staffStartTime').value,
                end: document.getElementById('staffEndTime').value,
                days: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].filter(d => document.getElementById('staff' + d).checked)
            };
            fetch(`/api/staff/${staffProfile.id}/availability`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' },
                body: JSON.stringify({ availability })
            }).then(res => res.json()).then(() => {
                loadStaffDashboard();
                alert('Your availability has been updated successfully.');
            }).catch(() => alert('Unable to update availability.'));
        };

        window.switchStaffTab = function(tabId, e) {
            e.preventDefault();
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelectorAll('.nav-tabs .nav-link').forEach(l => l.classList.remove('active'));
            e.target.classList.add('active');
        };

        function loadStaffDashboard() {
            fetch('/api/dashboard/data')
                .then(res => res.json())
                .then(result => {
                    const staffMembers = Array.isArray(result.staff) ? result.staff : [];
                    appointments = Array.isArray(result.appointments) ? result.appointments : [];
                    staffProfile = staffMembers[0] || null;
                    if (!staffProfile) return;
                    document.getElementById('profileName').innerText = staffProfile.name;
                    document.getElementById('profileRole').innerText = staffProfile.role === 'doctor' ? 'Senior Pathologist' : 'Lab Technician';
                    document.getElementById('profileEmail').innerText = staffProfile.email;
                    document.getElementById('profileContact').innerText = staffProfile.phone || 'N/A';
                    document.getElementById('profileDept').innerText = staffProfile.specialty || 'General';
                    const profileAvatarEl = document.getElementById('profileAvatar');
                    if (staffProfile.image) {
                        profileAvatarEl.innerHTML = `<img src="${staffProfile.image}" alt="${staffProfile.name}" class="profile-avatar-img" onerror="this.parentElement.innerHTML='${staffProfile.name.split(' ').map(n => n[0]).join('')}'">`;
                    } else {
                        profileAvatarEl.innerText = staffProfile.name.split(' ').map(n => n[0]).join('');
                    }
                    document.getElementById('welcomeGreeting').innerText = 'Welcome, ' + staffProfile.name;
                    document.getElementById('currentDate').innerText = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                    renderBio(staffProfile.bio);
                    const todayStr = new Date().toISOString().slice(0, 10);
                    const todayApps = appointments.filter(s => s.appointment_date === todayStr);
                    document.getElementById('todayCount').innerText = todayApps.length;
                    document.getElementById('weekCount').innerText = appointments.length;
                    document.getElementById('upcomingCount').innerText = appointments.filter(s => (s.status || '').toLowerCase() === 'pending').length;
                    renderTodaySchedules(todayApps);
                    renderWeekSchedules(appointments);
                    const availability = staffProfile.availability || { start: '08:00', end: '17:00', days: [] };
                    document.getElementById('staffStartTime').value = availability.start;
                    document.getElementById('staffEndTime').value = availability.end;
                    ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'].forEach(d => {
                        document.getElementById('staff' + d).checked = (availability.days || []).includes(d);
                    });
                })
                .catch(() => {
                    document.getElementById('todayCount').innerText = '0';
                });
        }
        document.addEventListener('DOMContentLoaded', loadStaffDashboard);
    }
})();


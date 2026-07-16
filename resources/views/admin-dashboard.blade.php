@extends('layout.base')

@section('page_title', 'Admin Dashboard')

@section('page_content')

<x-banner message="Admin Control Pannel" page="Dashboard" />

<!-- ===== ADMIN AREA ===== -->
    <div class="admin-area">
        <div class="container">
            <div class="mb-4">
                <h2><i class="bi bi-speedometer2 text-primary"></i> Admin Dashboard</h2>
                <p class="text-muted">Manage staff, schedules, availability, and financial transactions</p>
            </div>
            <div class="row g-4">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="sidebar-nav">
                        <a href="#" class="nav-link active" data-tab="staffListTab"><i class="bi bi-people me-2"></i> Staff List</a>
                        <a href="#" class="nav-link" data-tab="scheduleListTab"><i class="bi bi-calendar-week me-2"></i> Schedule List</a>
                        <a href="#" class="nav-link" data-tab="manageAvailabilityTab"><i class="bi bi-clock-history me-2"></i> Manage Availability</a>
                        <a href="#" class="nav-link" data-tab="specializationsTab"><i class="bi bi-tags me-2"></i> Specializations</a>
                        <a href="#" class="nav-link" data-tab="transactionsTab"><i class="bi bi-credit-card me-2"></i> Transactions</a>
                        <a href="#" class="nav-link" data-tab="pendingApprovalsTab"><i class="bi bi-person-badge me-2"></i> Pending Approvals</a>
                        <a href="/" class="nav-link"><i class="bi bi-box-arrow-left me-2"></i> Back to Home</a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">

                    <!-- ===== STAFF LIST TAB ===== -->
                    <div id="staffListTab" class="content-card tab-content active">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5><i class="bi bi-table"></i> Staff Management</h5>
                            <button class="btn btn-admin btn-sm" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                <i class="bi bi-plus-circle"></i> Add Staff
                            </button>
                        </div>
                        <div class="search-box">
                            <input type="text" id="staffSearch" placeholder="🔍 Search by name, email or role..." onkeyup="filterStaffTable()">
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="bi bi-people fs-3"></i>
                                    <div class="stat-number" id="totalStaff">0</div>
                                    <div>Total Staff</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="fa fa-user-md fa-3x"></i>
                                    <div class="stat-number" id="totalDoctors">0</div>
                                    <div>Doctors</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="bi bi-hospital fs-3"></i>
                                    <div class="stat-number" id="totalTechnicians">0</div>
                                    <div>Technicians</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <i class="bi bi-calendar-check fs-3"></i>
                                    <div class="stat-number" id="scheduledCount">0</div>
                                    <div>With Schedules</div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Salary</th>
                                        <th>Schedules</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staffListBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== SPECIALIZATIONS TAB ===== -->
                    <div id="specializationsTab" class="content-card tab-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5><i class="bi bi-tags"></i> Specialization Management</h5>
                            <button class="btn btn-admin btn-sm" onclick="openSpecializationModal()">
                                <i class="bi bi-plus-circle"></i> Add Specialization
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Linked Services</th>
                                        <th>Doctors</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="specializationListBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== SCHEDULE LIST TAB ===== -->
                    <div id="scheduleListTab" class="content-card tab-content">
                        <h5 class="mb-4"><i class="bi bi-calendar-check"></i> All Scheduled Appointments</h5>
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Staff</th>
                                        <th>Role</th>
                                        <th>Appointment Time</th>
                                        <th>Patient</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="allSchedulesBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== MANAGE AVAILABILITY TAB ===== -->
                    <div id="manageAvailabilityTab" class="content-card tab-content">
                        <h5 class="mb-4"><i class="bi bi-clock"></i> Set Staff Availability</h5>
                        <div class="mb-3">
                            <label class="form-label">Select Staff</label>
                            <select id="availabilityStaffSelect" class="form-select"></select>
                        </div>
                        <div id="availabilityContent" style="display:none;">
                            <div class="availability-form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Start Time</label>
                                            <input type="time" id="avStartTime" class="form-control" placeholder="e.g., 08:00">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>End Time</label>
                                            <input type="time" id="avEndTime" class="form-control" placeholder="e.g., 17:00">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Available Days</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avMon">
                                                <label>Monday</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avTue">
                                                <label>Tuesday</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avWed">
                                                <label>Wednesday</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avThu">
                                                <label>Thursday</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avFri">
                                                <label>Friday</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avSat">
                                                <label>Saturday</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="avSun">
                                                <label>Sunday</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-admin" onclick="saveStaffAvailability()">
                                    <i class="bi bi-save"></i> Save Availability
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- ===== TRANSACTIONS TAB ===== -->
                    <div id="transactionsTab" class="content-card tab-content">
                        <h5 class="mb-4"><i class="bi bi-credit-card"></i> Transaction History &amp; Summary</h5>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="bi bi-calendar-range fs-3"></i>
                                    <div class="stat-number" id="totalLastMonth">₦0</div>
                                    <div>Total (Last 30 days)</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="bi bi-check-circle fs-3"></i>
                                    <div class="stat-number" id="totalPaid">₦0</div>
                                    <div>Paid</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <i class="bi bi-hourglass-split fs-3"></i>
                                    <div class="stat-number" id="totalPending">₦0</div>
                                    <div>Pending</div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Patient</th>
                                        <th>Service</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionsBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== PENDING APPROVALS TAB ===== -->
                    <div id="pendingApprovalsTab" class="content-card tab-content">
                        <h5 class="mb-4"><i class="bi bi-person-check"></i> Pending User Approvals</h5>
                        <div class="table-responsive">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Signup Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingUsersBody"></tbody>
                            </table>
                        </div>
                    </div>

                </div><!-- /col-lg-9 -->
            </div><!-- /row -->
        </div><!-- /container -->
    </div>

    <!-- ===== MODALS ===== -->

    <!-- Staff Detail Modal -->
    <div class="modal fade" id="staffDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Staff Details &amp; All Schedules</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="staffDetailBody"></div>
            </div>
        </div>
    </div>

    <!-- Schedule Detail Modal -->
    <div class="modal fade" id="scheduleDetailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Appointment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="scheduleDetailBody"></div>
            </div>
        </div>
    </div>

    <!-- Add Staff Modal (with Bio) -->
    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Full Name</label>
                        <input type="text" id="newStaffName" class="form-control" placeholder="e.g., Dr. John Doe">
                    </div>
                    <div class="form-group mb-3">
                        <label>Email Address</label>
                        <input type="email" id="newStaffEmail" class="form-control" placeholder="e.g., john.doe@marabahospital.com">
                    </div>
                    <div class="form-group mb-3">
                        <label>Contact Number</label>
                        <input type="text" id="newStaffContact" class="form-control" placeholder="e.g., +234 801 234 5678">
                    </div>
                    <div class="form-group mb-3">
                        <label>Role</label>
                        <select id="newStaffRole" class="form-select">
                            <option value="">-- Select Role --</option>
                            <option value="doctor">Doctor</option>
                            <option value="technician">Lab Technician</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Salary (₦)</label>
                        <input type="number" id="newStaffSalary" class="form-control" placeholder="e.g., 450000">
                    </div>
                    <div class="form-group mb-3">
                        <label>Professional Bio</label>
                        <textarea id="newStaffBio" class="form-control" rows="3" placeholder="Brief professional biography (optional)"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Profile Image</label>
                        <input type="file" id="newStaffImageInput" class="form-control" accept="image/*" onchange="previewAdminStaffImage(event, 'newStaffImagePreview')">
                        <img id="newStaffImagePreview" src="" alt="Preview" class="img-fluid rounded mt-2" style="max-height:120px; display:none;">
                        <input type="hidden" id="newStaffImage">
                    </div>
                    <div class="form-group mb-3">
                        <label>Specializations</label>
                        <input type="text" id="newSpecSearch" class="form-control mb-2" placeholder="Search specializations...">
                        <select id="newStaffSpecializations" class="form-select" multiple size="4"></select>
                        <small class="text-muted">Type to filter; hold Ctrl/Cmd to select multiple</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-admin" onclick="addNewStaff()">Add Staff</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal (with Bio) -->
    <div class="modal fade" id="editStaffModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editStaffId">
                    <div class="form-group mb-3">
                        <label>Full Name</label>
                        <input type="text" id="editStaffName" class="form-control" placeholder="Full Name">
                    </div>
                    <div class="form-group mb-3">
                        <label>Email Address</label>
                        <input type="email" id="editStaffEmail" class="form-control" placeholder="Email">
                    </div>
                    <div class="form-group mb-3">
                        <label>Contact Number</label>
                        <input type="text" id="editStaffContact" class="form-control" placeholder="Contact">
                    </div>
                    <div class="form-group mb-3">
                        <label>Role</label>
                        <select id="editStaffRole" class="form-select">
                            <option value="doctor">Doctor</option>
                            <option value="technician">Lab Technician</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>Salary (₦)</label>
                        <input type="number" id="editStaffSalary" class="form-control" placeholder="Salary">
                    </div>
                    <div class="form-group mb-3">
                        <label>Professional Bio</label>
                        <textarea id="editStaffBio" class="form-control" rows="3" placeholder="Professional biography"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Profile Image</label>
                        <input type="file" id="editStaffImageInput" class="form-control" accept="image/*" onchange="previewAdminStaffImage(event, 'editStaffImagePreview')">
                        <img id="editStaffImagePreview" src="" alt="Preview" class="img-fluid rounded mt-2" style="max-height:120px; display:none;">
                        <input type="hidden" id="editStaffImage">
                    </div>
                    <div class="form-group mb-3">
                        <label>Specializations</label>
                        <input type="text" id="editSpecSearch" class="form-control mb-2" placeholder="Search specializations...">
                        <select id="editStaffSpecializations" class="form-select" multiple size="4"></select>
                        <small class="text-muted">Type to filter; hold Ctrl/Cmd to select multiple</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-admin" onclick="saveEditedStaff()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Specialization Modal -->
    <div class="modal fade" id="specializationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="specializationModalTitle">Add Specialization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="specializationId">
                    <div class="form-group mb-3">
                        <label>Name</label>
                        <input type="text" id="specializationName" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Slug</label>
                        <input type="text" id="specializationSlug" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <label>Description</label>
                        <textarea id="specializationDescription" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Linked Services</label>
                        <select id="specializationServiceSelect" class="form-select" multiple size="6"></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-admin" onclick="saveSpecialization()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    <div class="modal fade" id="transactionDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-receipt"></i> Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="transactionDetailBody"></div>
            </div>
        </div>
    </div>

@endsection
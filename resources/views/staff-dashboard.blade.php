@extends('layout.base')

@section('page_title', 'Staff Dashboard')

@section('page_content')

<x-banner message="Staff Control Pannel" page="Dashboard" />

    <!-- ===== STAFF DASHBOARD AREA ===== -->
    <div class="staff-area">
        <div class="container">
            <!-- Welcome -->
            <div class="welcome-card">
                <div class="welcome-greeting" id="welcomeGreeting">Welcome, Dr. James Okafor</div>
                <div id="currentDate"></div>
            </div>

            <!-- Profile Card (now includes Bio) -->
            <div class="profile-card">
                <div class="profile-avatar" id="profileAvatar">JO</div>
                <div class="profile-name" id="profileName">Dr. James Okafor</div>
                <div class="profile-role" id="profileRole">Senior Pathologist</div>
                <div class="profile-grid">
                    <div class="profile-item">
                        <div class="profile-label">Email</div>
                        <div id="profileEmail">james@marabahospital.com</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Contact</div>
                        <div id="profileContact">+2348034567890</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Department</div>
                        <div id="profileDept">Pathology</div>
                    </div>
                    <div class="profile-item">
                        <div class="profile-label">Status</div>
                        <div>Active</div>
                    </div>
                </div>

                <!-- ===== BIO SECTION ===== -->
                <div class="bio-section">
                    <div class="bio-header">
                        <h6><i class="bi bi-file-text me-2"></i>Professional Bio</h6>
                        <div class="bio-actions">
                            <button class="btn btn-sm btn-outline-primary" onclick="openBioModal()">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="clearBio()">
                                <i class="bi bi-trash me-1"></i> Clear
                            </button>
                        </div>
                    </div>
                    <div id="bioDisplay" class="bio-text empty-bio">
                        No bio added yet. Click "Edit" to write your professional biography.
                    </div>
                </div>
            </div>

            <!-- ===== STATISTICS ROW ===== -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-calendar-check"></i>
                        <div class="stat-number" id="todayCount">0</div>
                        <div class="stat-label">Today's Appointments</div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-calendar-week"></i>
                        <div class="stat-number" id="weekCount">0</div>
                        <div class="stat-label">This Week</div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="stat-card">
                        <i class="bi bi-clock"></i>
                        <div class="stat-number" id="upcomingCount">0</div>
                        <div class="stat-label">Upcoming</div>
                    </div>
                </div>
            </div>

            <!-- Schedule Tabs -->
            <div class="schedule-card">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="switchStaffTab('today', event)">Today</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="switchStaffTab('week', event)">This Week</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="switchStaffTab('availability', event)">Availability</a>
                    </li>
                </ul>

                <!-- Today -->
                <div id="today" class="tab-content active" style="padding-top:20px;">
                    <h6>Today's Appointments</h6>
                    <ul id="todaySchedules" class="schedule-list"></ul>
                </div>

                <!-- Week -->
                <div id="week" class="tab-content" style="padding-top:20px;">
                    <h6>This Week's Appointments</h6>
                    <ul id="weekSchedules" class="schedule-list"></ul>
                </div>

                <!-- Availability -->
                <div id="availability" class="tab-content" style="padding-top:20px;">
                    <h6>Set Your Availability</h6>
                    <div class="availability-form">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Start Time</label>
                                    <input type="time" id="staffStartTime" class="form-control" value="08:00">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>End Time</label>
                                    <input type="time" id="staffEndTime" class="form-control" value="17:00">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Available Days</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffMon">
                                        <label>Monday</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffTue">
                                        <label>Tuesday</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffWed">
                                        <label>Wednesday</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffThu">
                                        <label>Thursday</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffFri">
                                        <label>Friday</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffSat">
                                        <label>Saturday</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="staffSun">
                                        <label>Sunday</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-staff mt-3" onclick="saveStaffOwnAvailability()">Save Availability</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== BIO EDIT MODAL ===== -->
    <div class="modal fade" id="bioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bio-modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Professional Bio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bioTextarea" class="form-label">Write your biography below:</label>
                        <textarea class="form-control" id="bioTextarea" rows="6" placeholder="e.g., Dr. James Okafor is a board-certified pathologist with over 15 years of experience in diagnostic medicine..."></textarea>
                        <small class="text-muted">You can describe your qualifications, experience, specialties, and any other professional information.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-staff" onclick="saveBio()"><i class="bi bi-save me-1"></i>Save Bio</button>
                </div>
            </div>
        </div>
    </div>

@endsection
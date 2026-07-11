@extends('layout.base')

@section('page_title', 'Page Directory')

@section('page_content')

<x-banner message="Navigate through available Pages" page="Page Directory" />

<!-- ===== DIRECTORY SECTION ===== -->
    <div class="directory-section">
        <div class="container">
            <div class="mb-4">
                <h2 class="mb-2"><i class="bi bi-file-earmark text-primary"></i> Pages Directory</h2>
                <p class="text-muted">Complete listing of all pages in the Maraba Hospital system</p>
            </div>

            <div class="stat-cards" id="statCards"></div>

            <div class="search-container">
                <input type="text" class="search-input" id="searchInput" placeholder="Search pages by name or description..." onkeyup="filterPages()">
                <div class="filter-buttons">
                    <button class="filter-btn active" onclick="filterByType('all')">All Pages</button>
                    <button class="filter-btn" onclick="filterByType('public')">Public</button>
                    <button class="filter-btn" onclick="filterByType('appointment')">Appointments</button>
                    <button class="filter-btn" onclick="filterByType('admin')">Admin</button>
                    <button class="filter-btn" onclick="filterByType('staff')">Staff</button>
                </div>
            </div>

            <div class="pages-grid" id="pagesGrid"></div>
            <div id="noResults" class="no-results" style="display: none;">
                <i class="bi bi-search" style="font-size: 48px; color: #ccc;"></i>
                <p class="mt-3">No pages found matching your search.</p>
            </div>
        </div>
    </div>

@endsection
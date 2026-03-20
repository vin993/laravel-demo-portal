@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@include('admin.partials.nav')

<div id="layoutSidenav">
    @include('admin.partials.sidenav')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-5">User Management</h1>
                
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs custom-tabs" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active d-flex align-items-center gap-2" id="all-users-tab" data-bs-toggle="tab" data-bs-target="#all-users" type="button" role="tab">
                            <i class="fas fa-users"></i>
                            <span>All Users</span>
                            <span class="badge rounded-pill bg-secondary">{{ $users->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="primary-users-tab" data-bs-toggle="tab" data-bs-target="#primary-users" type="button" role="tab">
                            <i class="fas fa-user-shield"></i>
                            <span>Primary Users</span>
                            <span class="badge rounded-pill bg-primary">{{ $users->where('is_primary', true)->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="secondary-users-tab" data-bs-toggle="tab" data-bs-target="#secondary-users" type="button" role="tab">
                            <i class="fas fa-user-friends"></i>
                            <span>Secondary Users</span>
                            <span class="badge rounded-pill bg-info">{{ $users->whereNotNull('invited_by')->count() }}</span>
                        </button>
                    </li>
                    @if(auth()->user()->isSuperAdmin())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link d-flex align-items-center gap-2" id="admin-users-tab" data-bs-toggle="tab" data-bs-target="#admin-users" type="button" role="tab">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin Management</span>
                            <span class="badge rounded-pill bg-warning">{{ \App\Models\User::whereHas('role', function($q) { $q->where('name', 'Admin'); })->count() }}</span>
                        </button>
                    </li>
                    @endif
                </ul>



                <!-- Filter Section -->
                <div class="card filter-card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Status Filter</label>
                                <select class="form-select" id="statusFilter">
                                    <option value="">All Statuses</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Location Filter</label>
                                <select class="form-select" id="locationFilter">
                                    <option value="">All Locations</option>
                                    @foreach($users->pluck('userDetail.state')->unique()->filter() as $state)
                                        <option value="{{ $state }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small mb-1">Industry Filter</label>
                                <select class="form-select" id="industryFilter">
                                    <option value="">All Industries</option>
                                    @foreach($users->flatMap->industries->unique('name') as $industry)
                                        <option value="{{ $industry->name }}">{{ $industry->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <div class="flex-fill">
                                    <label class="form-label text-muted small mb-1">&nbsp;</label>
                                    <button class="btn btn-outline-secondary w-100" id="resetFilters">
                                        <i class="fas fa-undo me-1"></i>Reset
                                    </button>
                                </div>
                                <div class="flex-fill">
                                    <label class="form-label text-muted small mb-1">&nbsp;</label>
                                    <button class="btn btn-success w-100" id="exportUsers">
                                        <i class="fas fa-file-export me-1"></i>Export
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="userTabsContent">
                    <div class="tab-pane fade show active" id="all-users" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-users me-1"></i>
                                All Registered Users
                            </div>
                            <div class="card-body">
                                @include('admin.partials.users-table', ['users' => $users, 'tabId' => 'all'])
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="primary-users" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-user-shield me-1"></i>
                                Primary Users
                            </div>
                            <div class="card-body">
                                @include('admin.partials.users-table', ['users' => $users->where('is_primary', true), 'tabId' => 'primary'])
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="secondary-users" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-user-friends me-1"></i>
                                Secondary Users
                            </div>
                            <div class="card-body">
                                @include('admin.partials.users-table', ['users' => $users->whereNotNull('invited_by'), 'tabId' => 'secondary'])
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="tab-pane fade" id="admin-users" role="tabpanel">
                        <div class="card mb-4">
                            <div class="card-header tbl-head-crd-header d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-user-shield me-1"></i>
                                    Admin Management
                                </div>
                                <button class="btn btn-primary btn-sm" id="createAdminBtn">
                                    <i class="fas fa-plus me-1"></i>Create New Admin
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="adminUsersTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $adminUsers = \App\Models\User::whereHas('role', function($q) { 
                                                    $q->where('name', 'Admin'); 
                                                })->get();
                                            @endphp
                                            @foreach($adminUsers as $admin)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $admin->is_active ? 'success' : 'danger' }}">
                                                        {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-{{ $admin->is_active ? 'warning' : 'success' }} toggle-admin-status" 
                                                            data-admin-id="{{ $admin->id }}">
                                                        <i class="fas fa-{{ $admin->is_active ? 'pause' : 'play' }}"></i>
                                                        {{ $admin->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </main>
        @include('admin.partials.footer')
    </div>
</div>

{{-- User Details Modals --}}
@foreach ($users as $user)
    <div class="modal fade userModal" id="userModal{{ $user->id }}" data-invited-by="{{ $user->invited_by }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        <i class="fas fa-user me-2"></i>
                        User Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm{{ $user->id }}" class="row g-4">
                        @csrf
                        @method('POST')

           
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-sitemap me-2"></i>User Relationship
                            </div>
                            <div class="card-body">
                                @if($user->is_primary)
                                    <div class="primary-user-info">
                                        <h6 class="mb-3">
                                            <i class="fas fa-user-shield text-primary me-2"></i>Primary User
                                        </h6>
                                        @if($user->secondaryUsers->count() > 0)
                                            <div class="secondary-users-list">
                                                <strong>Secondary Users ({{ $user->secondaryUsers->count() }}):</strong>
                                                <ul class="list-unstyled mt-2">
                                                    @foreach($user->secondaryUsers as $secondaryUser)
                                                        <li class="mb-2">
                                                            <i class="fas fa-user-friends text-secondary me-2"></i>
                                                            {{ $secondaryUser->name }}
                                                            <small class="text-muted">
                                                                ({{ $secondaryUser->email }})
                                                            </small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No secondary users assigned</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="secondary-user-info">
                                        <h6 class="mb-3">
                                            <i class="fas fa-user-friends text-secondary me-2"></i>Secondary User
                                        </h6>
                                        <p class="mb-0">
                                            <strong>Primary User:</strong>
                                            @if($user->invitedBy)
                                                {{ $user->invitedBy->name }}
                                                <small class="text-muted">({{ $user->invitedBy->email }})</small>
                                            @else
                                                <span class="text-muted">Not assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Basic Information Card -->
                        <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header tbl-head-crd-header">
                                <i class="fas fa-user-circle me-2"></i>Basic Information
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-user me-2 text-dark"></i>
                                        <strong>Name:</strong> {{ $user->name }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-envelope me-2 text-dark"></i>
                                        <strong>Email:</strong> {{ $user->email }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-calendar me-2 text-dark"></i>
                                        <strong>Registered:</strong> {{ $user->created_at->format('M d, Y') }}
                                    </li>
                                    <li>
                                        <i class="fas fa-phone me-2 text-dark"></i>
                                        <strong>Phone:</strong> {{ $user->userDetail->phone ?? 'N/A' }}
                                    </li>
                                    <li>
                                        <div class="form-check mt-3">
                                            <input type="hidden" name="is_primary" value="0">
                                            <input type="checkbox" class="form-check-input" id="isPrimary{{ $user->id }}"
                                                name="is_primary" value="1" {{ $user->is_primary ? 'checked' : '' }}>
                                            <label class="form-check-label" for="isPrimary{{ $user->id }}">
                                                <i class="fas fa-star me-2 text-warning"></i>
                                                <strong>Primary User</strong>
                                                <small class="d-block text-muted ms-4">
                                                    Primary users have elevated privileges and access to additional features
                                                </small>
                                            </label>
                                        </div>
                                    </li>
                                    @if ($user->is_primary)
                                        <li class="mt-2">
                                            <i class="fas fa-clock me-2 text-dark"></i>
                                            <strong>Primary Since:</strong>
                                            {{ $user->primary_user_since ? $user->primary_user_since->format('M d, Y') : 'N/A' }}
                                        </li>
                                        <li>
                                            <i class="fas fa-user-shield me-2 text-dark"></i>
                                            <strong>Set By:</strong>
                                            @if($user->primary_set_by && $user->primarySetBy)
                                                {{ $user->primarySetBy->name }}
                                            @else
                                                N/A
                                            @endif
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                </div>

                <!-- Company Information Card -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header tbl-head-crd-header">
                            <i class="fas fa-building me-2"></i>Company Information
                        </div>
                        <div class="card-body">
                            <!-- <div class="mb-3 d-flex align-items-center">
                                {{ $user->userDetail?->company_name ?? 'N/A' }}
                            </div> -->
                            <!-- Update the industry interests select -->

                            <!-- <div class="mb-3 d-flex align-items-center">
                            <strong>Primary Company: {{ $user->userDetail?->company_name ?? 'N/A' }}</strong>
                            </div> -->

                                                        <!-- Update the dealers select -->
                            <div class="form-group">
                                <label class="fw-bold">
                                    <i class="fas fa-user-tie me-2 text-dark"></i>
                                    Associated Dealer Companies
                                </label>
                                <select class="form-control select2-multiple" name="dealer_id[]" multiple="multiple"
                                    data-placeholder="Search and select dealers">
                                    @foreach ($allDealers as $dealer)
                                        @if ($dealer->id !== $user->userDetail?->dealer_id)
                                            <option value="{{ $dealer->id }}" 
                                                {{ in_array($dealer->id, $user->dealers->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $dealer->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label class="fw-bold">
                                    <i class="fas fa-industry me-2 text-dark"></i>
                                    Industry Interests
                                </label>
                                <select class="form-control select2-multiple" name="industry_interests[]"
                                    multiple="multiple" data-placeholder="Search and select industries">
                                    @foreach ($user->industries as $industry)
                                        <option value="{{ $industry->id }}" selected>{{ $industry->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            <!-- Manufacturers select -->
                            <div class="form-group">
                                <label class="fw-bold">
                                    <i class="fas fa-industry me-2 text-dark"></i>
                                    Manufacturers
                                </label>
                                <select id="manufacturer" class="form-control select2-multiple" name="manufacturer_id[]" multiple="multiple" data-placeholder="Search and select manufacturers">
                                    @foreach ($user->manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}" selected>{{ $manufacturer->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Address Information Card -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header tbl-head-crd-header">
                            <i class="fas fa-map-marker-alt me-2"></i>Address Information
                        </div>
                        <div class="card-body">
                            @if ($user->userDetail && $user->userDetail->address)
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-map-marked me-2 text-dark"></i>
                                        <strong>Address:</strong> {{ $user->userDetail->address }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-city me-2 text-dark"></i>
                                        <strong>City:</strong>
                                        {{ $user->userDetail->city()->first()->name ?? $user->userDetail->city }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-map me-2 text-dark"></i>
                                        <strong>State:</strong>
                                        {{ $user->userDetail->state()->first()->name ?? $user->userDetail->state }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-mail-bulk me-2 text-dark"></i>
                                        <strong>Postal Code:</strong> {{ $user->userDetail->postal_code }}
                                    </li>
                                    <li>
                                        <i class="fas fa-globe me-2 text-dark"></i>
                                        <strong>Country:</strong>
                                        {{ $user->userDetail->country()->first()->name ?? $user->userDetail->country }}
                                    </li>
                                </ul>
                            @else
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No address information provided
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <button type="button" class="btn btn-primary save-user-details" data-user-id="{{ $user->id }}">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </div>

        </div>
    </div>
    </div>
@endforeach

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <form id="deleteUserForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Create Admin Modal --}}
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Create New Admin
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createAdminForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="adminName" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="adminName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminEmail" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="adminEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="adminPassword" name="password" required minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    <div class="mb-3">
                        <label for="adminPasswordConfirm" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="adminPasswordConfirm" name="password_confirmation" required minlength="8">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="adminActive" name="is_active" value="1" checked>
                        <label class="form-check-label" for="adminActive">
                            Active (admin can login immediately)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables for each tab
    const tables = {};
    
    function initializeDataTable(tableId) {
        const table = $(`#${tableId}`);
        const tbody = table.find('tbody');
        
        // Check if table has actual data (not just empty message)
        const hasData = tbody.find('tr').length > 0 && 
                       !tbody.find('tr:first td[colspan]').length;
        
        if (hasData) {
            return table.DataTable({
                order: [[0, 'asc']],
                pageLength: 25,
                language: {
                    search: "Search users: ",
                    lengthMenu: "Show _MENU_ users per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ users"
                },
                columnDefs: [
                    { orderable: false, targets: [5, 6] },
                    {
                        targets: 0,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }
                ],
                responsive: true
            });
        } else {
            // For empty tables, just hide DataTable controls
            table.removeClass('dataTable');
            return null;
        }
    }

    $('.tab-pane').each(function() {
        const tableId = $(this).find('table').attr('id');
        if (tableId && tableId !== 'adminUsersTable') {
            tables[tableId] = initializeDataTable(tableId);
        }
    });

    // Initialize admin table separately
    if ($('#adminUsersTable').length) {
        tables['adminUsersTable'] = $('#adminUsersTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            language: {
                search: "Search admins: ",
                lengthMenu: "Show _MENU_ admins per page",
                info: "Showing _START_ to _END_ of _TOTAL_ admins"
            },
            columnDefs: [
                { orderable: false, targets: [5] }, 
                {
                    targets: 0,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }
            ],
            responsive: true
        });
    }

    // Function to update badge counts
    function updateBadgeCounts() {
        Object.entries(tables).forEach(([tableId, table]) => {
            if (table && typeof table.rows === 'function') { 
                const filteredCount = table.rows({ search: 'applied' }).count();
                const tabId = tableId.split('_')[1] || tableId.replace('Table', '');
                const badge = $(`#${tabId}-users-tab .badge, #${tabId.replace('Users', '-users')}-tab .badge`);
                if (badge.length) {
                    badge.text(filteredCount);
                }
            }
        });
    }

    // Handle filter changes
    $('#statusFilter, #locationFilter, #industryFilter').on('change', function() {
        const statusFilter = $('#statusFilter').val().toLowerCase();
        const locationFilter = $('#locationFilter').val().toLowerCase();
        const industryFilter = $('#industryFilter').val().toLowerCase();

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const status = data[4].toLowerCase(); 
            const location = data[3].toLowerCase(); 
            const associations = data[2].toLowerCase(); 

            const statusMatch = !statusFilter || status.includes(statusFilter);
            const locationMatch = !locationFilter || location.includes(locationFilter);
            const industryMatch = !industryFilter || associations.includes(industryFilter);

            return statusMatch && locationMatch && industryMatch;
        });

        Object.values(tables).forEach(table => {
            if (table) { 
                table.draw();
            }
        });
        $.fn.dataTable.ext.search.pop();

        updateBadgeCounts();
    });

    // Reset filters
    $('#resetFilters').click(function() {
        $('#statusFilter, #locationFilter, #industryFilter').val('');
        
        Object.values(tables).forEach(table => {
            if (table) {
                table.search('').columns().search('');
                table.draw();
            }
        });
        
        updateBadgeCounts();
    });

    // Initial badge count update
    updateBadgeCounts();

    // Handle tab changes
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
        const activeTableId = $($(this).data('bs-target')).find('table').attr('id');
        if (tables[activeTableId]) {
            tables[activeTableId].columns.adjust().responsive.recalc();
        }
    });

    // Handle DataTable events
    Object.values(tables).forEach(table => {
        if (table && typeof table.on === 'function') {
            table.on('search.dt page.dt', function() {
                updateBadgeCounts();
            });
            
            table.on('draw', function() {
                if (typeof table.column === 'function') {
                    table.column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Export users functionality
    $('#exportUsers').on('click', function(e) {
        e.preventDefault();
        
        const statusFilter = $('#statusFilter').val();
        const locationFilter = $('#locationFilter').val();
        const industryFilter = $('#industryFilter').val();
        
        let exportUrl = "{{ route('admin.users.export') }}";
        let params = [];
        
        if (statusFilter) params.push('status=' + encodeURIComponent(statusFilter));
        if (locationFilter) params.push('location=' + encodeURIComponent(locationFilter));
        if (industryFilter) params.push('industry=' + encodeURIComponent(industryFilter));
        
        if (params.length > 0) {
            exportUrl += '?' + params.join('&');
        }
        
        window.location.href = exportUrl;
    });

    // Handle admin status toggle
    $(document).on('click', '.toggle-admin-status', function() {
        const adminId = $(this).data('admin-id');
        const button = $(this);
        
        $.ajax({
            url: `/admin/admins/${adminId}/toggle-status`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    if (response.is_active) {
                        button.removeClass('btn-outline-success').addClass('btn-outline-warning');
                        button.find('i').removeClass('fa-play').addClass('fa-pause');
                        button.html('<i class="fas fa-pause"></i> Deactivate');
                        
                        button.closest('tr').find('.badge')
                            .removeClass('bg-danger').addClass('bg-success')
                            .text('Active');
                    } else {
                        button.removeClass('btn-outline-warning').addClass('btn-outline-success');
                        button.find('i').removeClass('fa-pause').addClass('fa-play');
                        button.html('<i class="fas fa-play"></i> Activate');
                        
                        button.closest('tr').find('.badge')
                            .removeClass('bg-success').addClass('bg-danger')
                            .text('Inactive');
                    }
                    
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred'));
            }
        });
    });


    $('#createAdminBtn').on('click', function() {
        $('#createAdminModal').modal('show');
    });

    $('#createAdminForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("admin.create-admin") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createAdminModal').modal('hide');
                    alert(response.message);
                    location.reload(); 
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                let errorMessage = 'Please fix the following errors:\n';
                
                for (let field in errors) {
                    errorMessage += '- ' + errors[field][0] + '\n';
                }
                
                alert(errorMessage);
            }
        });
    });
});
</script>
@endpush
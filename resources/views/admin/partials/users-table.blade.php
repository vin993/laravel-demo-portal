<table class="table table-bordered table-hover align-middle" id="dataTable_{{ $tabId ?? 'all' }}">
    <thead class="table-light">
        <tr>
            <th width="50">Sr No.</th>
            <th>User Details</th>
            <th>Associations</th>
            <th>Location</th>
            <th>Status</th>
            <th width="100">Details</th>
            <th width="120">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($users->sortBy(function ($user) {
            return [$user->invited_by ?? 0, !$user->is_primary];
        }) as $user)
                    <tr class="{{ $user->invited_by ? 'secondary-user' : '' }}">
                        <td></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->is_primary)
                                    <div class="primary-user-indicator me-2">
                                        <i class="fas fa-user-shield text-primary" data-bs-toggle="tooltip" title="Primary User"></i>
                                    </div>
                                @elseif($user->invited_by)
                                    <div class="ms-4 secondary-user-indicator">
                                        <i class="fas fa-user-friends text-secondary" data-bs-toggle="tooltip"
                                            title="Secondary User"></i>
                                    </div>
                                @endif
                                <div class="ms-2">
                                    <h6 class="mb-0">
                                        {{ $user->name }}
                                        @if($user->is_primary)
                                            <span class="badge bg-primary ms-2">Primary</span>
                                        @endif
                                    </h6>
                                    @if($user->invited_by && $user->invitedBy)
                                        <small class="text-muted">
                                            <i class="fas fa-link fa-sm me-1"></i>Under
                                            {{ $user->invitedBy->name }}
                                        </small>
                                    @endif
                                    <small class="text-muted d-block">
                                        <i class="fas fa-envelope fa-sm me-1"></i>{{ $user->email }}
                                    </small>
                                    @if ($user->userDetail?->phone)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone fa-sm me-1"></i>{{ $user->userDetail->phone }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <!-- Industries Section -->
                            @if ($user->industries->count() > 0)
                                <div style="margin-top: 0.5rem;">
                                    <div style="background: transparent;">
                                        <button class="btn btn-sm btn-outline-info collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#industriesCollapse{{ $user->id }}"
                                            style="padding: 0.25rem 0.5rem; font-size: 9px;">
                                            <i class="fas fa-industry me-1"></i>
                                            Industries ({{ $user->industries->count() }})
                                            <i class="fas fa-chevron-down ms-1"
                                                style="font-size: 0.7rem; transition: transform 0.2s;"></i>
                                        </button>
                                        <div id="industriesCollapse{{ $user->id }}" class="collapse"
                                            style="margin-top: 0.5rem; max-height: 200px; overflow-y: auto;">
                                            @foreach ($user->industries as $industry)
                                                <div class="badge bg-info"
                                                    style="display: block; margin-bottom: 0.25rem; text-align: left; font-size: 0.8rem; padding: 0.4rem 0.6rem; white-space: normal;">
                                                    {{ $industry->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Dealer Companies Section -->
                            @if ($user->dealers->count() > 0)
                                <div style="margin-top: 0.5rem;">
                                    <div style="background: transparent;">
                                        <button class="btn btn-sm btn-outline-secondary collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#dealersCollapse{{ $user->id }}"
                                            style="padding: 0.25rem 0.5rem; font-size: 9px;">
                                            <i class="fas fa-user-tie me-1"></i>
                                            Dealer Companies ({{ $user->dealers->count() }})
                                            <i class="fas fa-chevron-down ms-1"
                                                style="font-size: 0.7rem; transition: transform 0.2s;"></i>
                                        </button>
                                        <div id="dealersCollapse{{ $user->id }}" class="collapse"
                                            style="margin-top: 0.5rem; max-height: 200px; overflow-y: auto;">
                                            @foreach ($user->dealers as $dealer)
                                                <div class="badge bg-secondary"
                                                    style="display: block; margin-bottom: 0.25rem; text-align: left; font-size: 0.8rem; padding: 0.4rem 0.6rem; white-space: normal;">
                                                    {{ $dealer->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Manufacturers Section -->
                            @if ($user->manufacturers->count() > 0)
                                <div style="margin-top: 0.5rem;">
                                    <div style="background: transparent;">
                                        <button class="btn btn-sm btn-outline-primary collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#manufacturersCollapse{{ $user->id }}"
                                            style="padding: 0.25rem 0.5rem; font-size: 9px;">
                                            <i class="fas fa-industry me-1"></i>
                                            Manufacturers ({{ $user->manufacturers->count() }})
                                            <i class="fas fa-chevron-down ms-1"
                                                style="font-size: 0.7rem; transition: transform 0.2s;"></i>
                                        </button>
                                        <div id="manufacturersCollapse{{ $user->id }}" class="collapse"
                                            style="margin-top: 0.5rem; max-height: 200px; overflow-y: auto;">
                                            @foreach ($user->manufacturers as $manufacturer)
                                                <div class="badge bg-primary"
                                                    style="display: block; margin-bottom: 0.25rem; text-align: left; font-size: 0.8rem; padding: 0.4rem 0.6rem; white-space: normal;">
                                                    {{ $manufacturer->name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>

                        <td>
                            @if ($user->userDetail)
                                <small class="d-block">
                                    {{ $user->userDetail->city()->first()->name ?? $user->userDetail->city }},
                                    {{ $user->userDetail->state()->first()->name ?? $user->userDetail->state }}
                                </small>
                                <small class="text-muted">
                                    {{ $user->userDetail->country()->first()->name ?? $user->userDetail->country }}
                                </small>
                            @else
                                <span class="text-muted">No location data</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column align-items-start">
                                @if ($user->is_primary)
                                    <span class="badge bg-warning mb-1">
                                        <i class="fas fa-star me-1"></i>Primary User
                                    </span>
                                @endif
                                <span class="badge bg-{{ $user->is_approved ? 'success' : 'warning' }} mb-1"  data-status="approval">
                                    {{ $user->is_approved ? 'Approved' : 'Pending' }}
                                </span>
                                <small class="text-muted">
                                    <i class="fas fa-clock fa-sm me-1"></i>
                                    {{ $user->created_at->diffForHumans() }}
                                </small>
                                @if ($user->approved_by)
                                    <small class="text-muted">
                                        <i class="fas fa-user-check fa-sm me-1"></i>
                                        by {{ $user->approvedBy->name }}
                                    </small>
                                @endif
                                @if ($user->last_login_at)
                                    <small class="text-muted">
                                        <i class="fas fa-sign-in-alt fa-sm me-1"></i>
                                        Last Login:
                                        {{ \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() }}
                                    </small>
                                @else
                                    <small class="text-muted">
                                        <i class="fas fa-exclamation-circle fa-sm me-1"></i>
                                        Never Logged In
                                    </small>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#userModal{{ $user->id }}">
                                <i class="fas fa-eye me-1"></i>View
                            </button>
                        </td>
                        <td>
                            <button
                                class="btn btn-{{ $user->is_approved ? 'warning' : 'success' }} btn-sm toggle-approve w-100 mb-1"
                                data-id="{{ $user->id }}">
                                <i class="fas fa-{{ $user->is_approved ? 'ban' : 'check' }} me-1"></i>
                                {{ $user->is_approved ? 'Revoke' : 'Approve' }}
                            </button>
                            <button class="btn btn-danger btn-sm w-100 delete-user" data-id="{{ $user->id }}">
                                <i class="fas fa-trash-alt me-1"></i>Delete
                            </button>
                        </td>
                    </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-users fa-2x mb-3"></i>
                        <p class="mb-0">No users found</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
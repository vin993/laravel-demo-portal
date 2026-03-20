<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading"></div>
                <a class="nav-link" href="{{ route('customer.dashboard') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <a class="nav-link" href="{{ route('customer.medias') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-photo-video"></i></div>
                    Media Files
                </a>
                <a class="nav-link" href="{{ route('customer.marketing-materials') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-photo-video"></i></div>
                    Marketing Materials
                </a>
                @if(Auth::user()->is_primary)
                <a class="nav-link" href="{{ route('customer.managed-users') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Managed Users
                </a>
                @endif
                <a class="nav-link" href="{{ route('saved-links.index') }}">
                    <div class="sb-nav-link-icon"><i class="fas fa-bookmark"></i></div>
                    Saved Links
                </a>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            {{ Auth::user()->name }}
        </div>
    </nav>
</div>
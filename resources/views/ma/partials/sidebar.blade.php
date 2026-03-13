<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                <a href="{{ route('ma.dashboard') }}"
                    class="nav-link{{ isset($pageName) && $pageName == 'Dashboard' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('ma.index') }}"
                    class="nav-link{{ isset($pageName) && $pageName == 'Applications' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-list"></i>
                    <p>Applications</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('ma.dashboard.vcapproved') }}"
                    class="nav-link{{ isset($pageName) && $pageName == 'VCChecked' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-check-double"></i>
                    <p>VC Checked</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('ma.status') }}"
                    class="nav-link{{ isset($pageName) && $pageName == 'Status' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>Status</p>
                </a>
            </li>
            <li class="nav-item has-treeview{{ isset($pageName) && $pageName == 'Study Leave' ? ' menu-open' : '' }}">
                <a href="#" class="nav-link{{ isset($pageName) && $pageName == 'Study Leave' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>Study Leave
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave') }}" class="nav-link{{ request()->routeIs('ma.studyleave') ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>In Review</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave.accepted') }}" class="nav-link{{ (request()->routeIs('ma.studyleave.accepted') || (request()->routeIs('ma.show.studyleave') && request()->get('from') == 'accepted')) ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Finalized</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview{{ isset($pageName) && $pageName == 'Study Leave Extensions' ? ' menu-open' : '' }}">
                <a href="#"
                    class="nav-link{{ isset($pageName) && $pageName == 'Study Leave Extensions' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>Extensions
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave.extensions') }}" class="nav-link{{ (request()->routeIs('ma.studyleave.extensions') || (request()->routeIs('ma.show.extension') && request()->get('from') != 'accepted')) ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>In Review</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave.extensions.accepted') }}" class="nav-link{{ (request()->routeIs('ma.studyleave.extensions.accepted') || (request()->routeIs('ma.show.extension') && request()->get('from') == 'accepted')) ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Finalized</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item has-treeview{{ isset($pageName) && $pageName == 'Study Leave Progress' ? ' menu-open' : '' }}">
                <a href="#"
                    class="nav-link{{ isset($pageName) && $pageName == 'Study Leave Progress' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>Study Leave Progress
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave.progress') }}" class="nav-link{{ request()->routeIs('ma.studyleave.progress') || (request()->routeIs('ma.show.studyleave.progressreport') && request()->get('from') != 'accepted') ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>In Review</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('ma.studyleave.progress.accepted') }}" class="nav-link{{ request()->routeIs('ma.studyleave.progress.accepted') || (request()->routeIs('ma.show.studyleave.progressreport') && request()->get('from') == 'accepted') ? ' active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Finalized</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('ma.studyleavestatus') }}"
                    class="nav-link{{ isset($pageName) && $pageName == 'Study Leave Status' ? ' active' : '' }}">
                    <i class="nav-icon fas fa-stream"></i>
                    <p>Study Leave Status</p>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
    /* Fixed sidebar styles */
    .main-sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        z-index: 1031 !important;
        overflow-y: auto !important;
        box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1) !important;
    }

    /* Scrollbar styles for sidebar */
    .main-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .main-sidebar::-webkit-scrollbar-track {
        background: #343a40;
    }

    .main-sidebar::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 3px;
    }

    .main-sidebar::-webkit-scrollbar-thumb:hover {
        background: #5a6268;
    }

    /* Adjust content wrapper for fixed sidebar */
    .content-wrapper {
        margin-left: 250px !important;
    }

    /* When sidebar is collapsed */
    .sidebar-collapse .content-wrapper {
        margin-left: 4.6rem !important;
    }

    /* Active submenu item: white background with black text */
    .nav-treeview > .nav-item > .nav-link.active {
        background-color: rgba(255, 255, 255, 0.9) !important;
        color: #343a40 !important;
    }

    .nav-treeview > .nav-item > .nav-link.active .nav-icon {
        color: #343a40 !important;
    }
</style>

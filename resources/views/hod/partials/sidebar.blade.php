<div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        
                        <li class="nav-item">
                            <a href="{{ route('hod.leave.index') }}" class="nav-link{{ (isset($pageName) && $pageName == 'Applications') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-list"></i>
                                <p>Leaves</p>
                            </a>
                        </li>
                        
                         <li class="nav-item">
                            <a href="{{ route('hod.study.leave.index') }}" class="nav-link{{ (isset($pageName) && $pageName == 'Study Leave') ? ' active' : '' }}">
                                <i class="nav-icon fas fa-stream"></i>
                                <p>Study Leaves</p>
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
        box-shadow: 2px 0 6px rgba(0,0,0,0.1) !important;
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
</style>
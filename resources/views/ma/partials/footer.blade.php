<footer class="main-footer">
    
    <strong>Copyright &copy; 2025 <a href="#">MA Dashboard</a>.</strong> All rights reserved.
</footer>

<style>
    /* Fixed footer styles */
    .main-footer {
        position: fixed !important;
        bottom: 0 !important;
        right: 0 !important;
        left: 250px !important;
        z-index: 1029 !important;
        box-shadow: 0 -2px 4px rgba(0,0,0,0.1) !important;
        transition: left 0.3s;
    }

    /* When sidebar is collapsed */
    .sidebar-collapse .main-footer {
        left: 4.6rem !important;
    }

    /* Adjust content wrapper for fixed footer */
    .content-wrapper {
        margin-bottom: 57px !important;
        min-height: calc(100vh - 114px) !important;
    }
</style> 
<body>
@include('layout.alert')

<style>
.startbar {
    transition: transform .22s ease-in-out !important;
}

.page-wrapper {
    transition:
        margin-left .22s ease-in-out,
        padding-left .22s ease-in-out,
        width .22s ease-in-out !important;
}

#mhSidebarToggle {
    position: fixed;
    top: 50%;
    transform: translateY(-50%);
    z-index: 9999;
    width: 34px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dbe3ee;
    border-radius: 0 10px 10px 0;
    background: #fff;
    color: #315f9f;
    box-shadow: 0 4px 12px rgba(15, 23, 42, .10);
    cursor: pointer;
    padding: 0;
    transition:
        left .22s ease-in-out,
        background .15s ease,
        color .15s ease,
        box-shadow .15s ease;
}

#mhSidebarToggle:hover {
    background: #315f9f;
    color: #fff;
    box-shadow: 0 6px 16px rgba(49, 95, 159, .22);
}

#mhSidebarToggle i {
    font-size: 19px;
    line-height: 1;
}

.nav-link.coming-soon {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(217, 154, 61, .12);
    border: 1px solid rgba(217, 154, 61, .25);
    border-radius: 6px;
    margin: 3px 8px;
    padding: 9px 12px;
}
.nav-link.coming-soon span {
    flex: 1;
    min-width: 0;
    color: #2e210a;
}
.nav-link.coming-soon .menu-icon {
    color: #d99a3d;
}
.nav-link.coming-soon::after {
    content: "SOON";
    padding: 3px 6px;
    border-radius: 4px;
    background: #d99a3d;
    color: #222a32;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: .4px;
    flex-shrink: 0;
}
.nav-link.coming-soon:hover {
    background: rgba(217, 154, 61, .22);
    border-color: rgba(217, 154, 61, .5);
}

@media (min-width: 992px) {
    body.mh-sidebar-collapsed .startbar {
        transform: translateX(-100%) !important;
    }

    body.mh-sidebar-collapsed .page-wrapper {
        margin-left: 0 !important;
        padding-left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    body.mh-sidebar-collapsed .startbar-overlay {
        display: none !important;
    }

    body.mh-sidebar-collapsed .page-content,
    body.mh-sidebar-collapsed .topbar,
    body.mh-sidebar-collapsed .navbar-custom {
        margin-left: 0 !important;
        left: 0 !important;
    }
}

@media (max-width: 991.98px) {
    #mhSidebarToggle {
        display: none !important;
    }
}
</style>

<div class="startbar d-print-none">
    <div class="brand">
        <a href="#" class="logo">
            <span>
                <img src="{{ asset('dashboard') }}/assets/images/logos/logo.png"
                     alt="logo-small"
                     class="logo-sm">
            </span>
        </a>
    </div>

    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label">
                        <span>Dashboards</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.machine-health.komatsu') ? 'active' : '' }}"
                           href="{{ route('dashboard.machine-health.komatsu') }}">
                            <i class="iconoir-truck menu-icon"></i>
                            <span>Komatsu</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard.machine-health.caterpillar') ? 'active' : '' }}"
                           href="{{ route('dashboard.machine-health.caterpillar') }}">
                            <i class="iconoir-truck menu-icon"></i>
                            <span>Caterpillar</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('operator-induced.index') }}">
                            <i class="iconoir-user-caution menu-icon"></i>
                            <span>Operator Induced</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Trend Historical Data">
                            <i class="iconoir-graph-up menu-icon"></i>
                            <span>Trend Historical Data</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Worst Eq. Rank by FOCUS">
                            <i class="iconoir-warning-triangle menu-icon"></i>
                            <span>Worst Eq. Rank by FOCUS</span>
                        </a>
                    </li>

                    <li class="menu-label">
                        <span>Realtime Viewer</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('realtime-series.index') }}">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Realtime Time Series</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Periodic Time Series">
                            <i class="iconoir-timer menu-icon"></i>
                            <span>Periodic Time Series</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('periodic-series.index') }}">
                            <i class="iconoir-multiple-pages menu-icon"></i>
                            <span>Periodic Time Series Vehicle</span>
                        </a>
                    </li>

                    <li class="menu-label">
                        <span>Technical Report</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('caterpillar.fault-code.raw-data') ? 'active' : '' }}"
                           href="{{ route('caterpillar.fault-code.raw-data') }}">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Caterpillar Fault Code Raw</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Komatsu Fault Code Raw">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Komatsu Fault Code Raw</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Caterpillar Last Updated JSON File 20Hours">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Caterpillar Last Updated JSON File 20Hours</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Komatsu Last Updated VHMS 20Hours">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Komatsu Last Updated VHMS 20Hours</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link coming-soon" href="#" data-feature="Periodic Operator Induced YTD Resume">
                            <i class="iconoir-activity menu-icon"></i>
                            <span>Periodic Operator Induced YTD Resume</span>
                        </a>
                    </li>

                    <li class="menu-label">
                        <span>Data Management</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('komatsu.message-ref') ? 'active' : '' }}"
                           href="{{ route('komatsu.message-ref') }}">
                            <i class="iconoir-message-text menu-icon"></i>
                            <span>Komatsu Message Ref</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('caterpillar.message-ref') ? 'active' : '' }}"
                           href="{{ route('caterpillar.message-ref') }}">
                            <i class="iconoir-message-text menu-icon"></i>
                            <span>Caterpillar Message Ref</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('komatsu.sn-ref') ? 'active' : '' }}"
                           href="{{ route('komatsu.sn-ref') }}">
                            <i class="iconoir-database menu-icon"></i>
                            <span>Komatsu SN Ref</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('komatsu.ref-mh-parameter') ? 'active' : '' }}"
                           href="{{ route('komatsu.ref-mh-parameter') }}">
                            <i class="iconoir-settings menu-icon"></i>
                            <span>Komatsu Ref MH Parameter</span>
                        </a>
                    </li>
                </ul>

                <div class="update-msg text-center">
                    <div class="d-flex justify-content-center align-items-center thumb-lg update-icon-box rounded-circle mx-auto">
                        <i class="iconoir-settings h3 align-self-center mb-0 text-primary"></i>
                    </div>

                    <h5 class="mt-3">Machine Health System</h5>

                    <p class="mb-3 text-muted">
                        Created by IT — This system is currently under development.
                    </p>

                    <a href="{{ route('home') }}" class="badge bg-warning text-dark px-3 py-2 rounded-pill text-decoration-none">
                        <i class="iconoir-home me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="startbar-overlay d-print-none"></div>

<button type="button"
        id="mhSidebarToggle"
        class="d-print-none"
        title="Hide Sidebar"
        aria-label="Hide Sidebar">
    <i class="iconoir-nav-arrow-left" id="mhSidebarToggleIcon"></i>
</button>

<div class="page-wrapper">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const sidebar = document.querySelector('.startbar');
    const toggle = document.getElementById('mhSidebarToggle');
    const icon = document.getElementById('mhSidebarToggleIcon');
    const storageKey = 'machineHealthSidebarCollapsed';

    if (!sidebar || !toggle) {
        return;
    }

    function positionToggle() {
        if (window.innerWidth < 992) {
            return;
        }

        const collapsed = body.classList.contains('mh-sidebar-collapsed');

        if (collapsed) {
            toggle.style.left = '8px';
            toggle.style.borderRadius = '10px';
        } else {
            const width = sidebar.getBoundingClientRect().width;
            toggle.style.left = Math.max(8, width - 1) + 'px';
            toggle.style.borderRadius = '0 10px 10px 0';
        }
    }

    function updateToggle() {
        const collapsed = body.classList.contains('mh-sidebar-collapsed');

        if (icon) {
            icon.className = collapsed
                ? 'iconoir-nav-arrow-right'
                : 'iconoir-nav-arrow-left';
        }

        toggle.title = collapsed
            ? 'Show Sidebar'
            : 'Hide Sidebar';

        toggle.setAttribute(
            'aria-label',
            collapsed ? 'Show Sidebar' : 'Hide Sidebar'
        );

        positionToggle();
    }

    if (localStorage.getItem(storageKey) === '1') {
        body.classList.add('mh-sidebar-collapsed');
    }

    toggle.addEventListener('click', function () {
        body.classList.toggle('mh-sidebar-collapsed');

        const collapsed = body.classList.contains('mh-sidebar-collapsed');

        localStorage.setItem(
            storageKey,
            collapsed ? '1' : '0'
        );

        updateToggle();
    });

    window.addEventListener('resize', positionToggle);

    updateToggle();
    setTimeout(positionToggle, 100);
});

document.querySelectorAll('.coming-soon').forEach(function (menu) {
    menu.addEventListener('click', function (event) {
        event.preventDefault();

        const feature = this.dataset.feature;

        Swal.fire({
            icon: 'info',
            title: '<span style="color: #ffffff;">Fitur Dalam Pengembangan</span>',
            html: `
                <p style="margin-bottom: 8px;">
                    <strong>${feature}</strong>
                </p>
                <p style="font-size: 13px; color: #888;">
                    Fitur ini sedang dalam tahap pengembangan
                    dan akan tersedia pada pembaruan sistem berikutnya.
                </p>
            `,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#d99a3d',
            background: '#222a32',
            color: '#edf0f2',
            allowOutsideClick: true
        });
    });
});
</script>

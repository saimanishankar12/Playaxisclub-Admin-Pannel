<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Playaxis Club | @yield('title')</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    @yield('styles')

    <style>
        :root {
            --pac-blue:       #1a56db;
            --pac-blue-dark:  #1040a8;
            --pac-blue-light: #e8f0fe;
            --pac-sidebar-bg: #0f172a;
            --pac-sidebar-w:  240px;
            --pac-text:       #1e293b;
            --pac-muted:      #64748b;
            --pac-border:     #e2e8f0;
            --pac-card-bg:    #ffffff;
            --pac-body-bg:    #f1f5f9;
            --pac-radius:     12px;
            --pac-shadow:     0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--pac-body-bg);
            color: var(--pac-text);
            margin: 0;
        }

        /* ── Sidebar ─────────────────────────────────────── */
        #pac-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--pac-sidebar-w);
            height: 100vh;
            background: var(--pac-sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .3s ease;
        }

        .pac-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            text-decoration: none;
        }
        .pac-sidebar-logo img {
            width: 38px; height: 38px;
            object-fit: contain;
            border-radius: 8px;
            background: rgba(255,255,255,.06);
            padding: 4px;
        }
        .pac-sidebar-logo span {
            font-size: .85rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .3px;
            line-height: 1.2;
        }
        .pac-sidebar-logo small {
            display: block;
            font-size: .65rem;
            font-weight: 400;
            color: rgba(255,255,255,.4);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .pac-nav-section {
            padding: 20px 12px 6px;
        }
        .pac-nav-label {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.3);
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .pac-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: rgba(255,255,255,.6);
            text-decoration: none;
            font-size: .825rem;
            font-weight: 500;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }
        .pac-nav-link i {
            width: 18px;
            text-align: center;
            font-size: .8rem;
            flex-shrink: 0;
        }
        .pac-nav-link:hover {
            background: rgba(255,255,255,.07);
            color: #fff;
            text-decoration: none;
        }
        .pac-nav-link.active {
            background: var(--pac-blue);
            color: #fff;
        }
        .pac-nav-link.active i { color: #fff; }

        .pac-sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .pac-logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            background: none;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 8px;
            color: rgba(255,255,255,.5);
            padding: 9px 12px;
            font-size: .825rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
        }
        .pac-logout-btn:hover {
            background: rgba(239,68,68,.15);
            border-color: rgba(239,68,68,.3);
            color: #f87171;
        }

        /* ── Topbar ─────────────────────────────────────── */
        #pac-topbar {
            position: fixed;
            top: 0;
            left: var(--pac-sidebar-w);
            right: 0;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid var(--pac-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 999;
        }

        #pac-topbar .page-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--pac-text);
            margin: 0;
        }

        .pac-topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pac-admin-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--pac-blue-light);
            border-radius: 30px;
            padding: 5px 12px 5px 6px;
        }
        .pac-admin-badge img {
            width: 28px; height: 28px;
            border-radius: 50%;
        }
        .pac-admin-badge span {
            font-size: .8rem;
            font-weight: 600;
            color: var(--pac-blue);
        }

        /* Mobile hamburger */
        #pac-sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--pac-text);
            cursor: pointer;
            padding: 4px 8px;
        }

        /* ── Main content ────────────────────────────────── */
        #pac-main {
            margin-left: var(--pac-sidebar-w);
            padding-top: 60px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #pac-content {
            padding: 28px 28px 0;
            flex: 1;
        }

        /* ── Footer ─────────────────────────────────────── */
        #pac-footer {
            margin-left: 0;
            padding: 16px 28px;
            font-size: .75rem;
            color: var(--pac-muted);
            border-top: 1px solid var(--pac-border);
            background: #fff;
            margin-top: 28px;
        }

        /* ── Alert override ─────────────────────────────── */
        .alert {
            border-radius: var(--pac-radius);
            border: none;
            font-size: .875rem;
        }

        /* ── Mobile ─────────────────────────────────────── */
        @media (max-width: 768px) {
            #pac-sidebar {
                transform: translateX(-100%);
            }
            #pac-sidebar.open {
                transform: translateX(0);
            }
            #pac-topbar {
                left: 0;
            }
            #pac-main {
                margin-left: 0;
            }
            #pac-sidebar-toggle {
                display: block;
            }
            #pac-content {
                padding: 16px;
            }
            .pac-sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.4);
                z-index: 999;
            }
            .pac-sidebar-overlay.open {
                display: block;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Overlay (mobile) -->
    <div class="pac-sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ── Sidebar ── -->
    <aside id="pac-sidebar">

        <a class="pac-sidebar-logo" href="{{ route('admin-dashboard') }}">
            <img src="{{ asset('img/logo-pa.png') }}" alt="PAC">
            <div>
                <span>Playaxis Club</span>
                <small>Admin Panel</small>
            </div>
        </a>

        <div class="pac-nav-section">
            <div class="pac-nav-label">Main</div>

            <a href="{{ route('admin-dashboard') }}"
               class="pac-nav-link {{ request()->routeIs('admin-dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
                



<a href="{{ route('admin-revenue') }}"
   class="pac-nav-link {{ request()->routeIs('admin-revenue') ? 'active' : '' }}">
    <i class="fas fa-chart-line"></i>
    <span>Revenue Report</span>
</a>

<a href="{{ route('admin-users.index') }}"
   class="pac-nav-link {{ request()->routeIs('admin-users.*') ? 'active' : '' }}">
    <i class="fas fa-users"></i>
    <span>All Users</span>
</a>

<a href="{{ route('admin-match-results') }}"
   class="pac-nav-link {{ request()->routeIs('admin-match-results') ? 'active' : '' }}">
    <i class="fas fa-fist-raised"></i>
    <span>Match Results</span>
</a>

<a href="{{ route('admin-sponsors') }}"
   class="pac-nav-link {{ request()->routeIs('admin-sponsors') ? 'active' : '' }}">
    <i class="fas fa-handshake"></i>
    <span>Sponsors</span>
</a>

<a href="{{ route('admin-audience') }}"
   class="pac-nav-link {{ request()->routeIs('admin-audience') ? 'active' : '' }}">
    <i class="fas fa-bullhorn"></i>
    <span>Audience</span>
</a>

<a href="{{ route('admin-matches.setup') }}"
   class="pac-nav-link {{ request()->routeIs('admin-matches.setup') ? 'active' : '' }}">
    <i class="fas fa-gamepad"></i>
    <span>Game Play</span>
</a>


<a href="{{ route('admin-attendance.index') }}"
   class="pac-nav-link {{ request()->routeIs('admin-attendance.index') ? 'active' : '' }}">
    <i class="fas fa-calendar-check"></i>
    <span>Attendance</span>
</a>









        </div>
        <div class="pac-sidebar-footer">
            <form action="{{ route('admin-logout') }}" method="POST">
                @csrf
                <button type="submit" class="pac-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- ── Main ── -->
    <div id="pac-main">

        <!-- Topbar -->
        <nav id="pac-topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button id="pac-sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="pac-topbar-right">
                <div class="pac-admin-badge">
                    <img src="{{ asset('img/undraw_profile.svg') }}" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div id="pac-content">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer id="pac-footer">
            Copyright &copy; Playaxis Club {{ date('Y') }} — All rights reserved.
        </footer>

    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <script>
        function toggleSidebar() {
            document.getElementById('pac-sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
        }
        document.getElementById('sidebarOverlay').addEventListener('click', function () {
            document.getElementById('pac-sidebar').classList.remove('open');
            this.classList.remove('open');
        });
    </script>

    @yield('scripts')
</body>
</html>
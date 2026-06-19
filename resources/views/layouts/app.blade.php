<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Piket - IT REG 4</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .theme-toggle { background: transparent; border: none; color: var(--text-secondary); cursor: pointer; padding: 0.5rem; font-size: 1.2rem; }
        .theme-toggle:hover { color: var(--accent-primary); }
        .profile-dropdown { position: relative; display: inline-block; cursor: pointer; }
        .profile-dropdown-content { display: none; position: absolute; right: 0; background-color: var(--bg-primary); min-width: 160px; box-shadow: 0px 4px 12px rgba(0,0,0,0.15); z-index: 100; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-color); margin-top: 5px; }
        .profile-dropdown-content.show { display: block; }
        .profile-dropdown-content form { margin: 0; }
        .profile-dropdown-content button { width: 100%; text-align: left; background: none; border: none; padding: 12px 16px; color: var(--danger); cursor: pointer; font-weight: 600; transition: background 0.2s; }
        .profile-dropdown-content button:hover { background-color: rgba(239, 68, 68, 0.1); }
        .profile-user-info { display: flex; align-items: center; gap: 10px; padding: 0.3rem 0.5rem; border-radius: 8px; transition: background 0.2s ease; user-select: none; }
        .profile-user-info:hover { background: var(--bg-tertiary); }
        .profile-avatar { width: 32px; height: 32px; border-radius: 50%; background-color: var(--accent-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
    </style>
</head>
<body>

    @auth
    <header style="background: var(--bg-primary); border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 50;">
        <!-- Top Tier: Brand, Periode, Profile -->
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; padding: 0.8rem 1rem;">
            <div class="text-accent font-bold" style="display: flex; align-items: center; gap: 0.5rem; font-size: 1.25rem;">
                <img src="{{ asset('logo/Logo_Baru_Pelindo.png') }}" alt="Logo Pelindo" style="height: 32px; width: auto; object-fit: contain;">
            </div>
            
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <!-- Periode Card -->
                <div style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: 8px; padding: 0.4rem 0.8rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <div style="line-height: 1.2;">
                        <div style="font-size: 0.65rem; color: var(--text-secondary); font-weight: bold; text-transform: uppercase;">Periode Piket Aktif</div>
                        <div style="font-size: 0.8rem; color: var(--accent-primary); font-weight: bold;">Masa Angkutan Lebaran 2026</div>
                    </div>
                </div>

                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark/Light Theme">☀️</button>
                
                <div class="profile-dropdown">
                    <div class="profile-user-info" onclick="toggleProfileDropdown(event)">
                        <div class="profile-avatar">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div style="display: flex; flex-direction: column; line-height: 1.2; padding-right: 0.5rem;">
                            <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">{{ auth()->user()->name }}</span>
                            <span style="font-size: 0.7rem; color: var(--text-secondary);">{{ auth()->user()->role === 'admin' ? 'Administrator' : 'User Pelindo' }}</span>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-secondary)" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                    <div class="profile-dropdown-content" id="profileDropdown">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: text-bottom; margin-right: 5px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Tier: Navigation Links -->
        <div style="border-top: 1px solid var(--border-color); background: var(--bg-primary);">
            <div class="container" style="display: flex; overflow-x: auto; scrollbar-width: none; gap: 0.5rem; padding: 0 1rem;">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="{{ route('piket.input') }}" class="nav-link {{ request()->routeIs('piket.input') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Monitoring Piket
                </a>
                @can('admin')
                <a href="{{ url('/cabang') }}" class="nav-link {{ request()->is('cabang') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
                    Cabang
                </a>
                @endcan
                <a href="{{ route('piket.laporan') }}" class="nav-link {{ request()->routeIs('piket.laporan') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Laporan
                </a>
                @can('admin')
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                        Manajemen Pengguna
                    </a>
                @endcan
            </div>
        </div>
    </header>
    @endauth

    <main style="flex: 1; display: flex; flex-direction: column;">
        @yield('content')
    </main>

    <footer>
        &copy; 2026 PT PELINDO REGIONAL 4<br>
    </footer>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            if (html.getAttribute('data-theme') === 'light') {
                html.removeAttribute('data-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            }
        }
        
        // Restore theme
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }

        function toggleProfileDropdown(event) {
            event.stopPropagation();
            document.getElementById('profileDropdown').classList.toggle('show');
        }

        window.onclick = function(event) {
            if (!event.target.closest('.profile-dropdown')) {
                var dropdowns = document.getElementsByClassName("profile-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpeg') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>O'Maria — @yield('page-title', 'Dashboard')</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/points.css') }}">
    <link rel="stylesheet" href="{{ asset('css/collete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/depenses.css') }}">

    @yield('styles')
</head>

<body>

    <div class="overlay" id="overlay"></div>

    {{-- ============================================================
    SIDEBAR
    ============================================================ --}}
    <aside class="sidebar" id="sidebar">
        <div class="water-waves"></div>
        <div class="water-waves back"></div>

        {{-- Logo --}}
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-logo-box">
                    <img src="{{ asset('img/logo.jpeg') }}" alt="O'Maria">
                </div>
                <span class="sidebar-brand">O'MARIA</span>
            </div>
            <button class="btn-close-sidebar" id="close-sidebar" title="Fermer">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            @auth
                @php $role = auth()->user()->role; @endphp

                <span class="nav-section-label">Principal</span>

                {{-- Dashboard — admin + proprietaire uniquement --}}
                @if(in_array($role, ['admin', 'proprietaire']))
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span class="nav-icon"><i class="bi bi-droplet-fill"></i></span>
                        Dashboard
                    </a>
                @endif

                {{-- Points d'eau — admin + proprietaire + gestionnaire --}}
                @if(in_array($role, ['admin', 'proprietaire', 'gestionnaire']))
                    <a class="nav-link {{ request()->is('points*') ? 'active' : '' }}" href="{{ route('points.index') }}">
                        <span class="nav-icon"><i class="bi bi-geo-alt"></i></span>
                        Points d'eau
                    </a>
                @endif

                {{-- Collectes — tous les rôles --}}
                <a class="nav-link {{ request()->is('collectes*') ? 'active' : '' }}" href="{{ route('collectes.index') }}">
                    <span class="nav-icon"><i class="bi bi-moisture"></i></span>
                    Collectes
                </a>

                {{-- Dépenses — admin + proprietaire + gestionnaire --}}
                @if(in_array($role, ['admin', 'proprietaire', 'gestionnaire']))
                    <a class="nav-link {{ request()->is('depenses*') ? 'active' : '' }}" href="{{ route('depenses.index') }}">
                        <span class="nav-icon"><i class="bi bi-wallet2"></i></span>
                        Dépenses
                    </a>
                @endif

                {{-- Administration — admin + proprietaire --}}
                @if(in_array($role, ['admin', 'proprietaire']))
                    <span class="nav-section-label">Administration</span>

                    <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <span class="nav-icon"><i class="bi bi-people-fill"></i></span>
                        Utilisateurs
                    </a>

                    <a class="nav-link {{ request()->is('admin/logs*') ? 'active' : '' }}"
                        href="{{ route('admin.logs.index') }}">
                        <span class="nav-icon"><i class="bi bi-clock-history"></i></span>
                        Logs d'activité
                    </a>
                @endif

            @endauth

        </nav>

        {{-- Profil utilisateur + déconnexion --}}
        <div class="sidebar-profile">
            @auth
                @php
                    $roleColors = [
                        'admin' => 'admin',
                        'proprietaire' => 'manager',
                        'gestionnaire' => 'manager',
                        'collecteur' => 'viewer',
                    ];
                    $roleLabels = [
                        'admin' => 'Admin',
                        'proprietaire' => 'Propriétaire',
                        'gestionnaire' => 'Gestionnaire',
                        'collecteur' => 'Collecteur',
                    ];
                    $roleClass = $roleColors[auth()->user()->role] ?? 'viewer';
                    $roleLabel = $roleLabels[auth()->user()->role] ?? 'Utilisateur';
                @endphp

                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="profile-info">
                    <div class="profile-name">{{ auth()->user()->name }}</div>
                    <span class="role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Se déconnecter" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.35);
                                       font-size:17px;padding:4px;transition:color 0.2s;"
                        onmouseover="this.style.color='rgba(239,68,68,0.8)'"
                        onmouseout="this.style.color='rgba(255,255,255,0.35)'">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            @endauth
        </div>
    </aside>

    {{-- ============================================================
    MAIN WRAPPER
    ============================================================ --}}
    <div id="main-wrapper">

        {{-- ============================================================
        NAVBAR — barre supérieure fixe
        Contient : bouton sidebar | logo cliquable | infos | user | excel
        ============================================================ --}}
        <nav class="navbar navbar-expand-lg navbar-omaria sticky-top">
            <div class="container-fluid px-md-4">

                {{-- ---- PARTIE GAUCHE ---- --}}
                <div class="d-flex align-items-center gap-3">

                    {{-- Bouton hamburger pour ouvrir/fermer la sidebar --}}
                    <button class="btn-toggle-sidebar" id="toggle-sidebar" title="Menu">
                        <i class="bi bi-list"></i>
                    </button>

                    {{-- ✅ MODIF 1 : Logo O'MARIA cliquable → redirige vers la page d'accueil
                    - Admin/Propriétaire → dashboard
                    - Gestionnaire/Collecteur → collectes (leur page principale)
                    --}}
                    @auth
                        @php
                            // On détermine la "home" selon le rôle de l'utilisateur connecté
                            $homeRoute = in_array(auth()->user()->role, ['admin', 'proprietaire'])
                                ? route('dashboard')        // Admin et Propriétaire → Dashboard
                                : route('collectes.index'); // Gestionnaire et Collecteur → Collectes
                        @endphp

                        {{-- Lien cliquable sur le nom de l'entreprise --}}
                        <a href="{{ $homeRoute }}" style="text-decoration:none;" title="Retour à l'accueil">
                            <span class="navbar-brand-chip d-none d-sm-flex"
                                style="cursor:pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.75'"
                                onmouseout="this.style.opacity='1'">
                                O'MARIA
                            </span>
                        </a>
                    @else
                        {{-- Si pas connecté, juste le texte sans lien --}}
                        <span class="navbar-brand-chip d-none d-sm-flex">O'MARIA</span>
                    @endauth

                    {{-- Nombre de points d'eau actifs — visible à partir de md --}}
                    <span class="navbar-chip d-none d-md-flex">
                        <i class="bi bi-geo-alt-fill"></i>
                        {{ \App\Models\Point::count() }} Points
                    </span>

                    {{-- Date du jour en français — visible à partir de lg --}}
                    <span class="navbar-chip d-none d-lg-flex">
                        <i class="bi bi-calendar-check"></i>
                        {{ now()->locale('fr')->isoFormat('ddd D MMM YYYY') }}
                    </span>

                </div>
                {{-- ---- FIN PARTIE GAUCHE ---- --}}

                {{-- ---- PARTIE DROITE ---- --}}
                <div class="navbar-actions d-flex align-items-center gap-2">

                    {{-- Bouton Export Excel — masqué pour le Collecteur --}}
                    @auth
                        @if(in_array(auth()->user()->role, ['admin', 'proprietaire', 'gestionnaire']))
                            <button class="btn-export-excel" onclick="exportExcel()" title="Exporter en Excel">
                                <i class="bi bi-file-earmark-excel"></i>
                                <span>Excel</span>
                            </button>
                            {{-- Séparateur vertical --}}
                            <div style="width:1px;height:26px;background:rgba(15,34,82,0.08);margin:0 4px;"></div>
                        @endif
                    @endauth

                    {{-- ✅ MODIF 2 : Bloc utilisateur connecté
                    Affiche : avatar initiales | Nom complet + badge rôle
                    Exemple : [CN] Cheikh Ndiaye · Admin
                    --}}
                    @auth
                        @php
                            // Labels lisibles pour chaque rôle
                            $navRoleLabels = [
                                'admin' => 'Admin',
                                'proprietaire' => 'Propriétaire',
                                'gestionnaire' => 'Gestionnaire',
                                'collecteur' => 'Collecteur',
                            ];

                            // Couleurs du badge rôle dans la navbar
                            $navRoleColors = [
                                'admin' => '#f59e0b', // Jaune/or pour admin
                                'proprietaire' => '#8b5cf6', // Violet pour propriétaire
                                'gestionnaire' => '#3b82f6', // Bleu pour gestionnaire
                                'collecteur' => '#10b981', // Vert pour collecteur
                            ];

                            $navLabel = $navRoleLabels[auth()->user()->role] ?? 'Utilisateur';
                            $navColor = $navRoleColors[auth()->user()->role] ?? '#64748b';
                        @endphp

                        <div class="d-flex align-items-center gap-2">

                            {{-- Avatar circulaire avec les 2 initiales du nom --}}
                            <div style="
                                        width:34px; height:34px; border-radius:50%;
                                        background: linear-gradient(135deg, #1d4088, #3b82f6);
                                        display:flex; align-items:center; justify-content:center;
                                        font-family:'Sora',sans-serif; font-size:11px; font-weight:700;
                                        color:white; border:2px solid rgba(15,34,82,0.1);
                                        flex-shrink:0;">
                                {{-- 2 premières lettres du nom en majuscules --}}
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>

                            {{-- Bloc texte : nom + badge rôle — visible à partir de md --}}
                            <div class="d-none d-md-flex flex-column" style="line-height:1.2;">

                                {{-- Nom complet de l'utilisateur --}}
                                <span style="font-size:13px; font-weight:600; color:#0f172a;">
                                    {{ auth()->user()->name }}
                                </span>

                                {{-- Badge rôle coloré selon le rôle --}}
                                <span style="
                                            font-size:10px; font-weight:700;
                                            font-family:'Sora', sans-serif;
                                            color: {{ $navColor }};
                                            letter-spacing: 0.3px;">
                                    {{-- Icône selon le rôle --}}
                                    @if(auth()->user()->role === 'admin')
                                        <i class="bi bi-shield-fill-check" style="font-size:9px;"></i>
                                    @elseif(auth()->user()->role === 'proprietaire')
                                        <i class="bi bi-house-fill" style="font-size:9px;"></i>
                                    @elseif(auth()->user()->role === 'gestionnaire')
                                        <i class="bi bi-person-badge-fill" style="font-size:9px;"></i>
                                    @else
                                        <i class="bi bi-basket-fill" style="font-size:9px;"></i>
                                    @endif
                                    {{ $navLabel }}
                                </span>

                            </div>

                        </div>
                    @endauth

                </div>
                {{-- ---- FIN PARTIE DROITE ---- --}}

            </div>
        </nav>
        {{-- ============================================================
        FIN NAVBAR
        ============================================================ --}}

        {{-- Alertes flash succès --}}
        @if(session('success'))
            <div class="container-fluid px-md-5 pt-3">
                <div class="flash-alert success" id="flash-success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()"
                        style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:inherit;opacity:0.6;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Alertes flash erreur --}}
        @if(session('error'))
            <div class="container-fluid px-md-5 pt-3">
                <div class="flash-alert error" id="flash-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()"
                        style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:inherit;opacity:0.6;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- Contenu principal de chaque page --}}
        <main class="container-fluid py-4 px-md-5">
            @yield('content')
        </main>

    </div>

    {{-- ============================================================
    SCRIPTS
    ============================================================ --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        // ================================================================
        // SIDEBAR — ouverture / fermeture
        // ================================================================
        const sidebar = document.getElementById('sidebar');
        const mainWrap = document.getElementById('main-wrapper');
        const overlay = document.getElementById('overlay');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        // Ouvre la sidebar + affiche l'overlay sombre
        function openSidebar() {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            // Sur grand écran, on pousse le contenu principal vers la droite
            if (window.innerWidth > 992) mainWrap.classList.add('sidebar-open');
        }

        // Ferme la sidebar + retire l'overlay
        function closeSidebar() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            mainWrap.classList.remove('sidebar-open');
        }

        // Clic sur le bouton hamburger → toggle
        toggleBtn?.addEventListener('click', () =>
            sidebar.classList.contains('active') ? closeSidebar() : openSidebar()
        );

        // Clic sur la croix dans la sidebar → ferme
        closeBtn?.addEventListener('click', closeSidebar);

        // Clic sur l'overlay (zone grise) → ferme
        overlay?.addEventListener('click', closeSidebar);

        // ================================================================
        // ALERTES FLASH — disparaissent automatiquement après 5 secondes
        // ================================================================
        document.querySelectorAll('.flash-alert').forEach(el => {
            setTimeout(() => {
                el.style.transition = 'opacity 0.4s, transform 0.4s';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(() => el.remove(), 400); // retire du DOM après la transition
            }, 5000);
        });

        // ================================================================
        // EXPORT EXCEL — intelligent selon la page
        // Dashboard  → 2 feuilles (collectes/semaine + rentabilité/mois)
        // Autres pages → tableau visible de la page
        // ================================================================
        function exportExcel() {
            const date = new Date().toLocaleDateString('fr-FR').replace(/\//g, '-');
            const isDash = window.location.pathname.includes('dashboard');

            if (isDash && window.chartData?.labels?.length > 0) {
                exportDashboardExcel(date); // Export spécial dashboard
            } else {
                const table = document.querySelector('table');
                if (!table) { showNotice('Aucun tableau trouvé sur cette page.', 'warning'); return; }
                exportTableExcel(table, date); // Export tableau générique
            }
        }

        // Export dashboard : crée 2 feuilles Excel
        function exportDashboardExcel(date) {
            try {
                const wb = XLSX.utils.book_new();

                // Feuille 1 : collectes par semaine
                if (window.chartData?.labels?.length > 0) {
                    const rows = [['Semaine', 'Recettes (FCFA)', 'Dépenses (FCFA)', 'Bénéfice (FCFA)']];
                    window.chartData.labels.forEach((label, i) => {
                        rows.push([
                            label,
                            window.chartData.collectes[i] ?? 0,
                            window.chartData.depenses[i] ?? 0,
                            window.chartData.benefices[i] ?? 0,
                        ]);
                    });
                    const ws1 = XLSX.utils.aoa_to_sheet(rows);
                    ws1['!cols'] = [{ wch: 14 }, { wch: 20 }, { wch: 20 }, { wch: 20 }];
                    XLSX.utils.book_append_sheet(wb, ws1, 'Collectes semaines');
                }

                // Feuille 2 : rentabilité par mois
                if (window.monthlyData?.labels?.length > 0) {
                    const rows2 = [['Mois', 'Recettes (FCFA)', 'Dép. points (FCFA)', 'Charges globales (FCFA)', 'Bénéfice net (FCFA)']];
                    window.monthlyData.labels.forEach((label, i) => {
                        rows2.push([
                            label,
                            window.monthlyData.collectes[i] ?? 0,
                            window.monthlyData.depensesPoints[i] ?? 0,
                            window.monthlyData.depensesGlobales[i] ?? 0,
                            window.monthlyData.benefices[i] ?? 0,
                        ]);
                    });
                    const ws2 = XLSX.utils.aoa_to_sheet(rows2);
                    ws2['!cols'] = [{ wch: 12 }, { wch: 18 }, { wch: 20 }, { wch: 24 }, { wch: 20 }];
                    XLSX.utils.book_append_sheet(wb, ws2, 'Rentabilité mensuelle');
                }

                XLSX.writeFile(wb, 'omaria_dashboard_' + date + '.xlsx');
                showNotice('✅ Export réussi — 2 feuilles créées !', 'success');
            } catch (e) {
                console.error(e);
                showNotice('Erreur lors de l\'export.', 'error');
            }
        }

        // Export générique : exporte le tableau visible de la page
        function exportTableExcel(table, date) {
            try {
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);
                const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
                ws['!cols'] = Array(range.e.c + 1).fill({ wch: 22 });
                const path = window.location.pathname.replace(/\//g, '').replace(/[^a-z]/gi, '_') || 'données';
                XLSX.utils.book_append_sheet(wb, ws, path.substring(0, 31));
                XLSX.writeFile(wb, 'omaria_' + path + '_' + date + '.xlsx');
                showNotice('✅ Export Excel réussi !', 'success');
            } catch (e) {
                console.error(e);
                showNotice('Erreur lors de l\'export.', 'error');
            }
        }

        // Affiche une notification flottante en bas à droite
        function showNotice(msg, type) {
            const n = document.createElement('div');
            n.className = 'flash-alert ' + type;
            n.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;max-width:340px;border-radius:12px;';
            n.innerHTML = '<span>' + msg + '</span>';
            document.body.appendChild(n);
            setTimeout(() => {
                n.style.opacity = '0';
                n.style.transition = 'opacity 0.35s';
                setTimeout(() => n.remove(), 400);
            }, 3500);
        }
    </script>

    @yield('scripts')
    @stack('scripts')

</body>

</html>
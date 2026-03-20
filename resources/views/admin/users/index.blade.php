@extends('layouts.app')

@section('page-title', 'Gestion des utilisateurs')

@section('content')

    <style>
        .users-wrap {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Header page */
        .users-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 22px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 16px rgba(11, 20, 35, 0.07);
            position: relative;
            overflow: hidden;
        }

        .users-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #f59e0b, #3b82f6, #10b981);
        }

        .users-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .users-header-title i {
            color: #3b82f6;
        }

        .users-header-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .btn-add-user {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: linear-gradient(135deg, #0f2252, #1d4088);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(13, 40, 88, 0.3);
            transition: all 0.2s;
        }

        .btn-add-user:hover {
            background: linear-gradient(135deg, #163068, #2554b0);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(13, 40, 88, 0.4);
            color: white;
        }

        /* Stats rôles */
        .role-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .role-stat-chip {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 6px 14px;
            background: white;
            border: 1px solid rgba(15, 34, 82, 0.08);
            border-radius: 99px;
            font-family: 'Sora', sans-serif;
            font-size: 12px;
            font-weight: 600;
        }

        .role-stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Table utilisateurs */
        .users-table-wrap {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(11, 20, 35, 0.07);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table thead tr {
            background: rgba(248, 250, 252, 0.9);
            border-bottom: 1.5px solid #e8eef5;
        }

        .users-table th {
            padding: 11px 16px;
            font-family: 'Sora', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #94a3b8;
            text-align: left;
            white-space: nowrap;
        }

        .users-table tbody tr {
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            transition: background 0.15s;
        }

        .users-table tbody tr:last-child {
            border-bottom: none;
        }

        .users-table tbody tr:hover {
            background: rgba(59, 130, 246, 0.03);
        }

        .users-table tbody tr.inactive-row {
            opacity: 0.55;
        }

        .users-table td {
            padding: 14px 16px;
            vertical-align: middle;
        }

        /* Avatar utilisateur */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1d4088, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .user-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #0f172a;
        }

        .user-email {
            font-size: 11.5px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* Badge rôle — 4 nouveaux rôles */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 99px;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .role-badge.admin {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .role-badge.proprietaire {
            background: rgba(139, 92, 246, 0.1);
            color: #4c1d95;
            border-color: rgba(139, 92, 246, 0.25);
        }

        .role-badge.gestionnaire {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            border-color: rgba(59, 130, 246, 0.25);
        }

        .role-badge.collecteur {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-color: rgba(16, 185, 129, 0.25);
        }

        /* Badge statut */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 99px;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.08);
            color: #065f46;
        }

        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.08);
            color: #7f1d1d;
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, 0.08);
            color: #78350f;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .status-dot.active {
            background: #10b981;
            box-shadow: 0 0 4px rgba(16, 185, 129, 0.5);
        }

        .status-dot.inactive {
            background: #ef4444;
        }

        .status-dot.pending {
            background: #f59e0b;
        }

        /* Actions */
        .action-btn {
            width: 32px;
            height: 32px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .action-btn:hover {
            background: #dbeafe;
            border-color: #3b82f6;
            color: #1d4ed8;
        }

        .action-btn.toggle:hover {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #92400e;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        .action-btn.resend:hover {
            background: #ede9fe;
            border-color: #7c3aed;
            color: #5b21b6;
        }

        /* Badge invitation en attente */
        .invite-pending {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10.5px;
            color: #92400e;
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.2);
            border-radius: 99px;
            padding: 2px 8px;
            margin-top: 3px;
        }

        /* Moi-même */
        .me-badge {
            display: inline-block;
            padding: 2px 7px;
            background: rgba(59, 130, 246, 0.08);
            color: #1d4ed8;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            margin-left: 6px;
        }

        /* Empty state */
        .empty-users {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
            font-size: 13px;
        }

        .empty-users i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 8px;
            opacity: 0.3;
        }
    </style>

    <div class="users-wrap">

        {{-- Header --}}
        <div class="users-header">
            <div>
                <div class="users-header-title">
                    <i class="bi bi-people-fill"></i>
                    Gestion des utilisateurs
                </div>
                <div class="users-header-sub">
                    {{ $users->count() }} utilisateur(s) enregistré(s) — seul l'admin peut créer des comptes
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 flex-wrap">

                {{-- Stats par rôle — 4 nouveaux rôles --}}
                <div class="role-stats">
                    <div class="role-stat-chip">
                        <div class="role-stat-dot" style="background:#f59e0b;"></div>
                        {{ $users->where('role', 'admin')->count() }} Admin
                    </div>
                    <div class="role-stat-chip">
                        <div class="role-stat-dot" style="background:#8b5cf6;"></div>
                        {{ $users->where('role', 'proprietaire')->count() }} Propriétaire
                    </div>
                    <div class="role-stat-chip">
                        <div class="role-stat-dot" style="background:#3b82f6;"></div>
                        {{ $users->where('role', 'gestionnaire')->count() }} Gestionnaire
                    </div>
                    <div class="role-stat-chip">
                        <div class="role-stat-dot" style="background:#10b981;"></div>
                        {{ $users->where('role', 'collecteur')->count() }} Collecteur
                    </div>
                </div>

                <a href="{{ route('admin.users.create') }}" class="btn-add-user">
                    <i class="bi bi-person-plus-fill"></i> Nouvel utilisateur
                </a>
            </div>
        </div>

        {{-- Alertes session --}}
        @if(session('success'))
            <div
                style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div
                style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:12px; padding:12px 16px; font-size:13px; color:#7f1d1d; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-exclamation-circle-fill" style="color:#ef4444;"></i>
                {{ session('error') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="users-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="{{ !$user->is_active ? 'inactive-row' : '' }}">

                            {{-- Avatar + nom + email --}}
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="user-name">
                                            {{ $user->name }}
                                            @if($user->id === auth()->id())
                                                <span class="me-badge">Moi</span>
                                            @endif
                                        </div>
                                        <div class="user-email">{{ $user->email }}</div>
                                        {{-- Badge invitation en attente --}}
                                        @if(!$user->is_active && $user->invitations()->whereNull('accepted_at')->exists())
                                            <div class="invite-pending">
                                                <i class="bi bi-envelope-exclamation" style="font-size:10px;"></i>
                                                Invitation en attente
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Rôle --}}
                            <td>
                                <span class="role-badge {{ $user->role }}">
                                    @if($user->role === 'admin')
                                        <i class="bi bi-shield-fill-check" style="font-size:11px;"></i> Admin
                                    @elseif($user->role === 'proprietaire')
                                        <i class="bi bi-house-fill" style="font-size:11px;"></i> Propriétaire
                                    @elseif($user->role === 'gestionnaire')
                                        <i class="bi bi-person-badge-fill" style="font-size:11px;"></i> Gestionnaire
                                    @else
                                        <i class="bi bi-basket-fill" style="font-size:11px;"></i> Collecteur
                                    @endif
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                @if(!$user->is_active && $user->invitations()->whereNull('accepted_at')->exists())
                                    <span class="status-badge pending">
                                        <div class="status-dot pending"></div>
                                        En attente
                                    </span>
                                @else
                                    <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                        <div class="status-dot {{ $user->is_active ? 'active' : 'inactive' }}"></div>
                                        {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                                    </span>
                                @endif
                            </td>

                            {{-- Date création --}}
                            <td style="font-size:12px; color:#64748b;">
                                {{ $user->created_at->format('d/m/Y') }}
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="d-flex justify-content-end gap-2">

                                    {{-- Modifier --}}
                                    <a href="{{ route('admin.users.edit', $user) }}" class="action-btn" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if($user->id !== auth()->id())

                                        {{-- Renvoyer invitation (compte pas encore activé) --}}
                                        @if(!$user->is_active)
                                            <form method="POST" action="{{ route('admin.users.resend', $user) }}"
                                                style="display:inline;">
                                                @csrf
                                                <button type="submit" class="action-btn resend" title="Renvoyer l'invitation">
                                                    <i class="bi bi-envelope-arrow-up"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Activer / Désactiver --}}
                                        <form method="POST" action="{{ route('admin.users.toggle', $user) }}"
                                            style="display:inline;">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="action-btn toggle"
                                                title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="bi bi-{{ $user->is_active ? 'pause-fill' : 'play-fill' }}"></i>
                                            </button>
                                        </form>

                                        {{-- Supprimer --}}
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            style="display:inline;"
                                            onsubmit="return confirm('Supprimer « {{ $user->name }} » ? Cette action est irréversible.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Supprimer">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>

                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-users">
                                    <i class="bi bi-people"></i>
                                    Aucun utilisateur — créez le premier compte
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info droits --}}
        <div
            style="background:rgba(245,158,11,0.07); border:1px solid rgba(245,158,11,0.2); border-radius:14px; padding:12px 16px; font-size:12.5px; color:#78350f; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-info-circle-fill" style="color:#f59e0b; flex-shrink:0;"></i>
            <div>
                <strong>Droits :</strong>
                <strong>Admin / Propriétaire</strong> — accès complet + gestion utilisateurs. &nbsp;
                <strong>Gestionnaire</strong> — collectes, dépenses, points d'eau. &nbsp;
                <strong>Collecteur</strong> — saisie et modification des collectes uniquement.
            </div>
        </div>

    </div>
@endsection
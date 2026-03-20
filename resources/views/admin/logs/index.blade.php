@extends('layouts.app')

@section('page-title', 'Logs d\'activité')

@section('content')

    <style>
        .logs-wrap {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        /* Header */
        .logs-header {
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

        .logs-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #3b82f6, #10b981);
        }

        .logs-header-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logs-header-title i {
            color: #6366f1;
        }

        .logs-header-sub {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Filtres */
        .logs-filters {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 18px;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 12px rgba(11, 20, 35, 0.05);
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 160px;
        }

        .filter-group label {
            font-family: 'Sora', sans-serif;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
        }

        .filter-group select,
        .filter-group input {
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            transition: border 0.15s;
            font-family: 'DM Sans', sans-serif;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-filter {
            padding: 9px 20px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: 'Sora', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
        }

        .btn-reset {
            padding: 9px 14px;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
        }

        .btn-reset:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        /* Stats rapides */
        .logs-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .log-stat-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: white;
            border: 1px solid rgba(15, 34, 82, 0.08);
            border-radius: 12px;
            font-family: 'Sora', sans-serif;
            font-size: 12px;
            box-shadow: 0 1px 6px rgba(11, 20, 35, 0.04);
        }

        .log-stat-chip strong {
            font-size: 16px;
            color: #0f172a;
        }

        .log-stat-chip span {
            color: #64748b;
        }

        /* Table */
        .logs-table-wrap {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.55);
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(11, 20, 35, 0.07);
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logs-table thead tr {
            background: rgba(248, 250, 252, 0.9);
            border-bottom: 1.5px solid #e8eef5;
        }

        .logs-table th {
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

        .logs-table tbody tr {
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            transition: background 0.15s;
        }

        .logs-table tbody tr:last-child {
            border-bottom: none;
        }

        .logs-table tbody tr:hover {
            background: rgba(99, 102, 241, 0.03);
        }

        .logs-table td {
            padding: 13px 16px;
            vertical-align: middle;
        }

        /* Badge module */
        .module-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 99px;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .module-badge.collectes {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-color: rgba(16, 185, 129, 0.2);
        }

        .module-badge.depenses {
            background: rgba(239, 68, 68, 0.1);
            color: #7f1d1d;
            border-color: rgba(239, 68, 68, 0.2);
        }

        .module-badge.users {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            border-color: rgba(59, 130, 246, 0.2);
        }

        .module-badge.auth {
            background: rgba(245, 158, 11, 0.1);
            color: #78350f;
            border-color: rgba(245, 158, 11, 0.2);
        }

        /* Badge action */
        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12.5px;
            font-weight: 600;
            color: #0f172a;
        }

        .action-badge i {
            font-size: 13px;
        }

        .action-badge.modif {
            color: #1d4ed8;
        }

        .action-badge.suppr {
            color: #b91c1c;
        }

        .action-badge.connexion {
            color: #065f46;
        }

        .action-badge.deconnexion {
            color: #78350f;
        }

        .action-badge.invitation {
            color: #6d28d9;
        }

        .action-badge.activation {
            color: #065f46;
        }

        /* Avatar utilisateur */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1d4088, #3b82f6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Sora', sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .user-name-sm {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        .user-role-sm {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* Détail */
        .log-detail {
            font-size: 12px;
            color: #64748b;
            font-family: 'DM Mono', monospace;
            background: rgba(99, 102, 241, 0.04);
            border: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 6px;
            padding: 4px 8px;
            max-width: 380px;
            word-break: break-word;
        }

        /* Heure */
        .log-time {
            font-size: 12px;
            color: #64748b;
            font-family: 'DM Mono', monospace;
            white-space: nowrap;
        }

        .log-time-date {
            font-size: 10.5px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Pagination */
        .pagination-wrap {
            display: flex;
            justify-content: center;
            padding: 16px;
            border-top: 1px solid rgba(226, 232, 240, 0.5);
        }

        /* Empty */
        .empty-logs {
            text-align: center;
            padding: 3rem;
            color: #94a3b8;
            font-size: 13px;
        }

        .empty-logs i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 8px;
            opacity: 0.3;
        }
    </style>

    <div class="logs-wrap">

        {{-- Header --}}
        <div class="logs-header">
            <div>
                <div class="logs-header-title">
                    <i class="bi bi-clock-history"></i>
                    Logs d'activité
                </div>
                <div class="logs-header-sub">
                    Traçabilité complète — qui a fait quoi et à quelle heure
                </div>
            </div>

            {{-- Stats rapides --}}
            <div class="logs-stats">
                <div class="log-stat-chip">
                    <strong>{{ $logs->total() }}</strong>
                    <span>actions totales</span>
                </div>
                <div class="log-stat-chip">
                    <strong>{{ $logs->where('created_at', '>=', today())->count() }}</strong>
                    <span>aujourd'hui</span>
                </div>
                <div class="log-stat-chip">
                    <strong>{{ $logs->where('module', 'collectes')->count() }}</strong>
                    <span>sur collectes</span>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <form method="GET" action="{{ route('admin.logs.index') }}" class="logs-filters">

            <div class="filter-group">
                <label>Module</label>
                <select name="module">
                    <option value="">Tous les modules</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}" {{ request('module') === $mod ? 'selected' : '' }}>
                            {{ ucfirst($mod) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Utilisateur</label>
                <select name="user_id">
                    <option value="">Tous les utilisateurs</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label>Date</label>
                <input type="date" name="date" value="{{ request('date') }}">
            </div>

            <button type="submit" class="btn-filter">
                <i class="bi bi-funnel-fill"></i> Filtrer
            </button>

            @if(request()->hasAny(['module', 'user_id', 'date']))
                <a href="{{ route('admin.logs.index') }}" class="btn-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            @endif

        </form>

        {{-- Table --}}
        <div class="logs-table-wrap">
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Détail</th>
                        <th>IP</th>
                        <th>Heure</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>

                            {{-- Utilisateur --}}
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">
                                        {{ strtoupper(substr($log->user->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="user-name-sm">{{ $log->user->name ?? 'Supprimé' }}</div>
                                        <div class="user-role-sm">{{ $log->user?->getRoleLabel() ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Action --}}
                            <td>
                                @php
                                    $actionClass = match (true) {
                                        str_contains($log->action, 'Modification') => 'modif',
                                        str_contains($log->action, 'Suppression') => 'suppr',
                                        str_contains($log->action, 'Connexion') => 'connexion',
                                        str_contains($log->action, 'Déconnexion') => 'deconnexion',
                                        str_contains($log->action, 'Invitation') => 'invitation',
                                        str_contains($log->action, 'Activation') => 'activation',
                                        default => ''
                                    };
                                    $actionIcon = match (true) {
                                        str_contains($log->action, 'Modification') => 'bi-pencil-fill',
                                        str_contains($log->action, 'Suppression') => 'bi-trash3-fill',
                                        str_contains($log->action, 'Connexion') => 'bi-box-arrow-in-right',
                                        str_contains($log->action, 'Déconnexion') => 'bi-box-arrow-right',
                                        str_contains($log->action, 'Invitation') => 'bi-envelope-fill',
                                        str_contains($log->action, 'Activation') => 'bi-check-circle-fill',
                                        default => 'bi-activity'
                                    };
                                @endphp
                                <span class="action-badge {{ $actionClass }}">
                                    <i class="bi {{ $actionIcon }}"></i>
                                    {{ $log->action }}
                                </span>
                            </td>

                            {{-- Module --}}
                            <td>
                                <span class="module-badge {{ $log->module }}">
                                    @if($log->module === 'collectes') <i class="bi bi-moisture" style="font-size:10px;"></i>
                                    @elseif($log->module === 'depenses') <i class="bi bi-wallet2" style="font-size:10px;"></i>
                                    @elseif($log->module === 'users') <i class="bi bi-people-fill" style="font-size:10px;"></i>
                                    @elseif($log->module === 'auth') <i class="bi bi-shield-lock-fill"
                                        style="font-size:10px;"></i>
                                    @endif
                                    {{ ucfirst($log->module) }}
                                </span>
                            </td>

                            {{-- Détail --}}
                            <td>
                                @if($log->detail)
                                    <div class="log-detail">{{ $log->detail }}</div>
                                @else
                                    <span style="color:#cbd5e1; font-size:12px;">—</span>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td style="font-size:12px; color:#94a3b8; font-family:'DM Mono',monospace;">
                                {{ $log->ip ?? '—' }}
                            </td>

                            {{-- Heure --}}
                            <td>
                                <div class="log-time">
                                    {{ $log->created_at->format('H:i:s') }}
                                </div>
                                <div class="log-time-date">
                                    {{ $log->created_at->format('d/m/Y') }}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-logs">
                                    <i class="bi bi-clock-history"></i>
                                    Aucune activité enregistrée pour le moment
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="pagination-wrap">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif

        </div>

        {{-- Info --}}
        <div
            style="background:rgba(99,102,241,0.05); border:1px solid rgba(99,102,241,0.15); border-radius:14px; padding:12px 16px; font-size:12.5px; color:#4338ca; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-info-circle-fill" style="color:#6366f1; flex-shrink:0;"></i>
            <div>
                Les logs sont <strong>en lecture seule</strong> — aucune modification possible.
                Visible uniquement par <strong>Admin</strong> et <strong>Propriétaire</strong>.
                Les 50 dernières actions sont affichées par page.
            </div>
        </div>

    </div>

@endsection
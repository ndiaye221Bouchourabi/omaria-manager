@extends('layouts.app')

@section('page-title', 'Gestion des utilisateurs')

@section('content')

    <style>
        .users-wrap { display: flex; flex-direction: column; gap: 1.25rem; }

        .users-header {
            display: flex; align-items: center; justify-content: space-between;
            gap: 1rem; flex-wrap: wrap;
            background: rgba(255,255,255,0.88); backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.55); border-radius: 22px;
            padding: 1.25rem 1.5rem; box-shadow: 0 2px 16px rgba(11,20,35,0.07);
            position: relative; overflow: hidden;
        }
        .users-header::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #f59e0b, #3b82f6, #10b981);
        }
        .users-header-title {
            font-family: 'Sora', sans-serif; font-size: 1.1rem; font-weight: 800;
            color: #0f172a; display: flex; align-items: center; gap: 8px;
        }
        .users-header-title i { color: #3b82f6; }
        .users-header-sub { font-size: 12px; color: #64748b; margin-top: 3px; }

        .btn-add-user {
            display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px;
            background: linear-gradient(135deg, #0f2252, #1d4088); color: white;
            border: none; border-radius: 12px; font-family: 'Sora', sans-serif;
            font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
            box-shadow: 0 4px 14px rgba(13,40,88,0.3); transition: all 0.2s;
        }
        .btn-add-user:hover {
            background: linear-gradient(135deg, #163068, #2554b0);
            transform: translateY(-1px); box-shadow: 0 6px 20px rgba(13,40,88,0.4); color: white;
        }

        .role-stats { display: flex; gap: 1rem; flex-wrap: wrap; }
        .role-stat-chip {
            display: flex; align-items: center; gap: 7px; padding: 6px 14px;
            background: white; border: 1px solid rgba(15,34,82,0.08); border-radius: 99px;
            font-family: 'Sora', sans-serif; font-size: 12px; font-weight: 600;
        }
        .role-stat-dot { width: 8px; height: 8px; border-radius: 50%; }

        .users-table-wrap {
            background: rgba(255,255,255,0.88); backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.55); border-radius: 22px;
            overflow: hidden; box-shadow: 0 2px 16px rgba(11,20,35,0.07);
        }
        .users-table { width: 100%; border-collapse: collapse; }
        .users-table thead tr { background: rgba(248,250,252,0.9); border-bottom: 1.5px solid #e8eef5; }
        .users-table th {
            padding: 11px 16px; font-family: 'Sora', sans-serif; font-size: 10.5px;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
            color: #94a3b8; text-align: left; white-space: nowrap;
        }
        .users-table tbody tr { border-bottom: 1px solid rgba(226,232,240,0.5); transition: background 0.15s; }
        .users-table tbody tr:last-child { border-bottom: none; }
        .users-table tbody tr:hover { background: rgba(59,130,246,0.03); }
        .users-table tbody tr.inactive-row { opacity: 0.55; }
        .users-table td { padding: 14px 16px; vertical-align: middle; }

        .user-cell { display: flex; align-items: center; gap: 12px; }
        .user-avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: linear-gradient(135deg, #1d4088, #3b82f6);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700;
            color: white; flex-shrink: 0; text-transform: uppercase;
        }
        .user-name { font-size: 13.5px; font-weight: 600; color: #0f172a; }
        .user-email { font-size: 11.5px; color: #94a3b8; margin-top: 1px; }

        .role-badge {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
            border-radius: 99px; font-family: 'Sora', sans-serif; font-size: 11px;
            font-weight: 700; border: 1px solid transparent;
        }
        .role-badge.admin { background: rgba(245,158,11,0.1); color: #92400e; border-color: rgba(245,158,11,0.25); }
        .role-badge.proprietaire { background: rgba(139,92,246,0.1); color: #4c1d95; border-color: rgba(139,92,246,0.25); }
        .role-badge.gestionnaire { background: rgba(59,130,246,0.1); color: #1e40af; border-color: rgba(59,130,246,0.25); }
        .role-badge.collecteur { background: rgba(16,185,129,0.1); color: #065f46; border-color: rgba(16,185,129,0.25); }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
            border-radius: 99px; font-family: 'Sora', sans-serif; font-size: 11px; font-weight: 600;
        }
        .status-badge.active { background: rgba(16,185,129,0.08); color: #065f46; }
        .status-badge.inactive { background: rgba(239,68,68,0.08); color: #7f1d1d; }
        .status-badge.pending { background: rgba(245,158,11,0.08); color: #78350f; }
        .status-dot { width: 7px; height: 7px; border-radius: 50%; }
        .status-dot.active { background: #10b981; box-shadow: 0 0 4px rgba(16,185,129,0.5); }
        .status-dot.inactive { background: #ef4444; }
        .status-dot.pending { background: #f59e0b; }

        .action-btn {
            width: 32px; height: 32px; background: #f8fafc; border: 1px solid #e2e8f0;
            border-radius: 8px; color: #64748b; display: inline-flex; align-items: center;
            justify-content: center; cursor: pointer; font-size: 14px; text-decoration: none;
            transition: all 0.15s;
        }
        .action-btn:hover { background: #dbeafe; border-color: #3b82f6; color: #1d4ed8; }
        .action-btn.toggle:hover { background: #fef3c7; border-color: #f59e0b; color: #92400e; }
        .action-btn.delete:hover { background: #fee2e2; border-color: #ef4444; color: #b91c1c; }
        .action-btn.resend:hover { background: #ede9fe; border-color: #7c3aed; color: #5b21b6; }
        .action-btn.reset-pwd:hover { background: #fef3c7; border-color: #f59e0b; color: #92400e; }

        .invite-pending {
            display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px;
            color: #92400e; background: rgba(245,158,11,0.08);
            border: 1px solid rgba(245,158,11,0.2); border-radius: 99px;
            padding: 2px 8px; margin-top: 3px;
        }
        .me-badge {
            display: inline-block; padding: 2px 7px;
            background: rgba(59,130,246,0.08); color: #1d4ed8;
            border-radius: 99px; font-size: 10px; font-weight: 600;
            font-family: 'Sora', sans-serif; margin-left: 6px;
        }
        .empty-users { text-align: center; padding: 3rem; color: #94a3b8; font-size: 13px; }
        .empty-users i { font-size: 2.5rem; display: block; margin-bottom: 8px; opacity: 0.3; }

        .link-box {
            border-radius: 14px; padding: 16px 20px;
        }
        .link-box-title {
            font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 700;
            margin-bottom: 10px; display: flex; align-items: center; gap: 6px;
        }
        .link-box-input {
            width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1;
            border-radius: 10px; font-size: 12.5px; color: #334155;
            background: white; cursor: pointer;
        }
        .btn-copy {
            margin-top: 10px; padding: 9px 18px; color: white; border: none;
            border-radius: 10px; font-family: 'Sora', sans-serif; font-size: 13px;
            font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .btn-copy:hover { opacity: 0.9; transform: translateY(-1px); }
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

        {{-- Message succès --}}
        @if(session('success'))
            <div style="background:rgba(16,185,129,0.08); border:1px solid rgba(16,185,129,0.2); border-radius:12px; padding:12px 16px; font-size:13px; color:#065f46; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Lien invitation --}}
        @if(session('invitation_link'))
            <div class="link-box" style="background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.2);">
                <div class="link-box-title" style="color:#1d4ed8;">
                    🔗 Lien d'invitation — envoyez par WhatsApp ou SMS
                </div>
                <input type="text" class="link-box-input"
                       value="{{ session('invitation_link') }}"
                       onclick="this.select()" readonly id="invitationLinkInput">
                <br>
                <button class="btn-copy" style="background:linear-gradient(135deg,#1d4088,#3b82f6);"
                        onclick="navigator.clipboard.writeText(document.getElementById('invitationLinkInput').value); this.innerHTML='✅ Copié !'">
                    📋 Copier le lien
                </button>
            </div>
        @endif

        {{-- Lien réinitialisation --}}
        @if(session('reset_link'))
            <div class="link-box" style="background:rgba(245,158,11,0.06); border:1px solid rgba(245,158,11,0.3);">
                <div class="link-box-title" style="color:#92400e;">
                    🔑 Lien de réinitialisation — envoyez par WhatsApp ou SMS
                </div>
                <input type="text" class="link-box-input"
                       value="{{ session('reset_link') }}"
                       onclick="this.select()" readonly id="resetLinkInput">
                <br>
                <button class="btn-copy" style="background:linear-gradient(135deg,#92400e,#f59e0b);"
                        onclick="navigator.clipboard.writeText(document.getElementById('resetLinkInput').value); this.innerHTML='✅ Copié !'">
                    📋 Copier le lien
                </button>
            </div>
        @endif

        {{-- Message erreur --}}
        @if(session('error'))
            <div style
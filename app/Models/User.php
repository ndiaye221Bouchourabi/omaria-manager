<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /* ----------------------------------------------------------------
       Rôles disponibles
    ---------------------------------------------------------------- */
    const ROLE_ADMIN = 'admin';
    const ROLE_PROPRIETAIRE = 'proprietaire';
    const ROLE_GESTIONNAIRE = 'gestionnaire';
    const ROLE_COLLECTEUR = 'collecteur';

    const ROLES = [
        'admin' => 'Administrateur',
        'proprietaire' => 'Propriétaire',
        'gestionnaire' => 'Gestionnaire',
        'collecteur' => 'Collecteur',
    ];

    const ROLE_LABELS = [
        'admin' => [
            'label' => 'Admin',
            'color' => '#f59e0b',
            'bg' => 'rgba(245,158,11,0.12)',
        ],
        'proprietaire' => [
            'label' => 'Propriétaire',
            'color' => '#8b5cf6',
            'bg' => 'rgba(139,92,246,0.12)',
        ],
        'gestionnaire' => [
            'label' => 'Gestionnaire',
            'color' => '#3b82f6',
            'bg' => 'rgba(59,130,246,0.12)',
        ],
        'collecteur' => [
            'label' => 'Collecteur',
            'color' => '#10b981',
            'bg' => 'rgba(16,185,129,0.12)',
        ],
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /* ----------------------------------------------------------------
       Helpers de rôle
    ---------------------------------------------------------------- */

    /** L'utilisateur est-il admin ? */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /** Admin ou Propriétaire — accès complet */
    public function isOwner(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PROPRIETAIRE,
        ]);
    }

    /** Peut créer / modifier des données */
    public function canEdit(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PROPRIETAIRE,
            self::ROLE_GESTIONNAIRE,
        ]);
    }

    /** Peut supprimer */
    public function canDelete(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PROPRIETAIRE,
        ]);
    }

    /** Peut gérer les utilisateurs */
    public function canManageUsers(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PROPRIETAIRE,
        ]);
    }

    /** Label lisible du rôle — ex: "Propriétaire" */
    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? 'Inconnu';
    }

    /** Config badge du rôle (color + bg) */
    public function getRoleBadge(): array
    {
        return self::ROLE_LABELS[$this->role]
            ?? self::ROLE_LABELS['collecteur'];
    }

    /** Relation invitations */
    public function invitations()
    {
        return $this->hasMany(\App\Models\Invitation::class);
    }
}
<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Enregistre une action dans les logs
     *
     * @param string $action  ex: "Modification collecte"
     * @param string $module  ex: "collectes"
     * @param string $detail  ex: "Collecte #12 — Fontaine Liberté, S12-2026, 15 000 FCFA"
     */
    public static function log(string $action, string $module, string $detail = ''): void
    {
        if (!Auth::check())
            return;

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'detail' => $detail,
            'ip' => Request::ip(),
        ]);
    }
}
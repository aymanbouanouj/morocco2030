<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AdminController extends Controller
{
    protected function recordAudit(
        Request $request,
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        AuditLogger::log($request, $model, $action, $oldValues, $newValues);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Services\ExternalFootball\FootballDataClient;
use App\Services\ExternalFootball\FootballDataSquadDryRunService;
use App\Services\ExternalFootball\FootballDataSquadImportService;
use App\Services\ExternalFootball\FootballDataWorldCupDryRunService;
use App\Services\ExternalFootball\FootballDataWorldCupImportService;
use App\Services\ExternalFootball\FootballDataWorldCupTeamReconciliationService;
use App\Services\ExternalFootball\FootballDataWorldCupReconciliationService;
use App\Services\ExternalFootball\FootballDataWorldCupStructureService;
use App\Services\ExternalFootball\MoroccoVenueMapper;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FootballDataImportController extends AdminController
{
    public function index(FootballDataClient $client, MoroccoVenueMapper $venueMapper): View
    {
        $this->authorizeDryRunAccess();

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    public function preview(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataWorldCupDryRunService $dryRunService
    ): View {
        $this->authorizeDryRunAccess();

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => $dryRunService->preview(),
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    public function previewSquads(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataSquadDryRunService $squadDryRunService
    ): View {
        $this->authorizeDryRunAccess();

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => $squadDryRunService->preview(),
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    public function importSquads(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataSquadImportService $squadImportService
    ): View {
        $this->authorizeImportAccess();

        $request->validate([
            'squad_import_confirmation' => ['required', 'string', 'in:IMPORT FOOTBALL SQUADS'],
            'understands_squad_import' => ['accepted'],
        ]);

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => $squadImportService->import(),
        ]);
    }

    public function run(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataWorldCupImportService $importService
    ): View {
        $this->authorizeImportAccess();

        $request->validate([
            'confirmation' => ['required', 'string', 'in:IMPORT FOOTBALL DATA'],
            'understands_write' => ['accepted'],
        ]);

        $result = $importService->import(
            userId: $request->user()?->id,
            ipAddress: $request->ip(),
            userAgent: (string) $request->userAgent(),
            routeName: (string) $request->route()?->getName()
        );

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => $result,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    public function reconcile(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataWorldCupReconciliationService $reconciliationService
    ): View {
        $this->authorizeImportAccess();

        $request->validate([
            'reconciliation_confirmation' => ['required', 'string', 'in:RECONCILE FOOTBALL DATA'],
            'understands_reconciliation' => ['accepted'],
        ]);

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => $reconciliationService->reconcile(),
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    public function reconcileTeams(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataWorldCupTeamReconciliationService $teamReconciliationService
    ): View {
        $this->authorizeImportAccess();

        $request->validate([
            'team_reconciliation_confirmation' => ['required', 'string', 'in:RECONCILE FOOTBALL TEAMS'],
            'understands_team_reconciliation' => ['accepted'],
        ]);

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => null,
            'squadPreview' => null,
            'teamReconciliationResult' => $teamReconciliationService->reconcile(),
            'squadImportResult' => null,
        ]);
    }

    public function reconcileStructure(
        Request $request,
        FootballDataClient $client,
        MoroccoVenueMapper $venueMapper,
        FootballDataWorldCupStructureService $structureService
    ): View {
        $this->authorizeImportAccess();

        $request->validate([
            'structure_confirmation' => ['required', 'string', 'in:RECONCILE FOOTBALL STRUCTURE'],
            'understands_structure' => ['accepted'],
        ]);

        return view('admin.football-data-import.index', [
            'configStatus' => $client->configStatus(),
            'mappingStatus' => $venueMapper->status(),
            'preview' => null,
            'importResult' => null,
            'reconciliationResult' => null,
            'structureResult' => $structureService->reconcile(),
            'squadPreview' => null,
            'teamReconciliationResult' => null,
            'squadImportResult' => null,
        ]);
    }

    protected function authorizeDryRunAccess(): void
    {
        $this->authorizeImportAccess();
    }

    protected function authorizeImportAccess(): void
    {
        abort_unless(
            auth()->user()?->hasAnyRole(['super-admin', 'platform-admin']),
            403
        );
    }
}

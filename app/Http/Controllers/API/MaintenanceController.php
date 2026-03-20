<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use App\Models\Vehicule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class MaintenanceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Maintenance::with('vehicule', 'createur');
        if ($request->filled('statut'))           $query->where('statut', $request->statut);
        if ($request->filled('type_maintenance'))  $query->deType($request->type_maintenance);
        if ($request->filled('vehicle_id'))        $query->where('vehicle_id', $request->vehicle_id);
        if ($request->filled('prochaines')) {
            $query->prochainesDans($request->input('jours', 7));
        }
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_maintenance', [$request->date_debut, $request->date_fin]);
        }
        return MaintenanceResource::collection(
            $query->orderBy('date_maintenance', 'desc')->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreMaintenanceRequest $request): MaintenanceResource
    {
        $data               = $request->validated();
        $data['created_by'] = $request->user()->id;
        $maintenance        = Maintenance::create($data);
 
        if (in_array($maintenance->statut, ['planifiee', 'en_cours'])) {
            $maintenance->vehicule->update(['statut' => 'en_maintenance']);
        }
 
        return (new MaintenanceResource($maintenance->load('vehicule')))
            ->additional(['message' => 'Maintenance enregistrée.']);
    }
 
    public function show(Maintenance $maintenance): MaintenanceResource
    {
        return new MaintenanceResource($maintenance->load('vehicule', 'createur', 'documents'));
    }
 
    public function update(StoreMaintenanceRequest $request, Maintenance $maintenance): MaintenanceResource
    {
        $ancienStatut = $maintenance->statut;
        $maintenance->update($request->validated());
 
        if ($maintenance->statut === 'terminee' && $ancienStatut !== 'terminee') {
            $maintenance->vehicule->update(['statut' => 'disponible']);
        } elseif (in_array($maintenance->statut, ['planifiee', 'en_cours'])) {
            $maintenance->vehicule->update(['statut' => 'en_maintenance']);
        }
 
        return (new MaintenanceResource($maintenance->fresh('vehicule')))
            ->additional(['message' => 'Maintenance mise à jour.']);
    }
 
    public function destroy(Maintenance $maintenance): JsonResponse
    {
        if ($maintenance->statut === 'en_cours') {
            return response()->json(['success' => false,
                                     'message' => 'Impossible de supprimer une maintenance en cours.'], 422);
        }
        $maintenance->delete();
        return response()->json(['success' => true, 'message' => 'Maintenance supprimée.']);
    }
 
    public function stats(Request $request): JsonResponse
    {
        $annee = $request->input('annee', now()->year);
        return response()->json([
            'success' => true,
            'data'    => [
                'par_type'  => Maintenance::selectRaw('type_maintenance, COUNT(*) as total, SUM(cout) as cout_total')
                    ->whereYear('date_maintenance', $annee)->where('statut', 'terminee')
                    ->groupBy('type_maintenance')->get(),
                'par_mois'  => Maintenance::selectRaw('MONTH(date_maintenance) as mois, COUNT(*) as total, SUM(cout) as cout_total')
                    ->whereYear('date_maintenance', $annee)->where('statut', 'terminee')
                    ->groupBy('mois')->orderBy('mois')->get(),
                'cout_total_annee'         => Maintenance::whereYear('date_maintenance', $annee)->where('statut', 'terminee')->sum('cout'),
                'vehicules_en_maintenance' => Vehicule::where('statut', 'en_maintenance')->count(),
            ],
        ]);
    }
}
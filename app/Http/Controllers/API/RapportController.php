<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRapportRequest;
use App\Http\Resources\RapportResource;
use App\Models\Affectation;
use App\Models\Rapport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class RapportController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Rapport::with('driver.user', 'vehicule', 'affectation.route', 'validateur');
        if ($request->filled('statut_validation'))  $query->where('statut_validation', $request->statut_validation);
        if ($request->filled('driver_id'))           $query->where('driver_id', $request->driver_id);
        if ($request->filled('vehicle_id'))          $query->where('vehicle_id', $request->vehicle_id);
        if ($request->filled('incident_signale'))    $query->where('incident_signale', $request->boolean('incident_signale'));
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->periode($request->date_debut, $request->date_fin);
        }
        if ($request->user()->isChauffeur()) {
            $driver = $request->user()->driver;
            if ($driver) $query->where('driver_id', $driver->id);
        }
        return RapportResource::collection(
            $query->orderBy('date_rapport', 'desc')->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreRapportRequest $request): RapportResource
    {
        $data               = $request->validated();
        $affectation        = Affectation::find($data['assignment_id']);
        $data['driver_id']  = $affectation->driver_id;
        $data['vehicle_id'] = $affectation->vehicle_id;
        $rapport = Rapport::create($data);
        return (new RapportResource($rapport->load('affectation.route', 'vehicule')))
            ->additional(['message' => 'Rapport soumis. En attente de validation.']);
    }
 
    public function show(Rapport $rapport): RapportResource
    {
        return new RapportResource(
            $rapport->load('driver.user', 'vehicule', 'affectation.route', 'validateur')
        );
    }
 
    public function valider(Request $request, Rapport $rapport): RapportResource|JsonResponse
    {
        if (!$request->user()->isAdmin() && !$request->user()->isGestionnaire()) {
            return response()->json(['success' => false, 'message' => 'Réservé aux gestionnaires.'], 403);
        }
        if ($rapport->statut_validation !== 'en_attente') {
            return response()->json(['success' => false,
                                     'message' => 'Rapport déjà traité : ' . $rapport->statut_validation], 422);
        }
        $rapport->valider($request->user()->id);
        return (new RapportResource($rapport->fresh('vehicule')))
            ->additional(['message' => 'Rapport validé. Kilométrage du véhicule mis à jour.']);
    }
 
    public function rejeter(Request $request, Rapport $rapport): RapportResource|JsonResponse
    {
        $request->validate(['motif' => ['required', 'string', 'max:500']]);
        if (!$request->user()->isAdmin() && !$request->user()->isGestionnaire()) {
            return response()->json(['success' => false, 'message' => 'Réservé aux gestionnaires.'], 403);
        }
        $rapport->rejeter($request->user()->id);
        $rapport->update(['observations' => 'REJETÉ — ' . $request->motif]);
        return (new RapportResource($rapport->fresh()))->additional(['message' => 'Rapport rejeté.']);
    }
 
    public function statistiques(Request $request): JsonResponse
    {
        $annee = $request->input('annee', now()->year);
        $mois  = $request->input('mois');
        $query = Rapport::where('statut_validation', 'valide')->whereYear('date_rapport', $annee);
        if ($mois) $query->whereMonth('date_rapport', $mois);
        return response()->json([
            'success' => true,
            'data'    => [
                'km_total'               => $query->sum('kilometrage_parcouru'),
                'carburant_total_litres' => $query->sum('carburant_consomme'),
                'cout_carburant_total'   => $query->sum('cout_carburant'),
                'total_passagers'        => $query->sum('nombre_passagers_transportes'),
                'total_rapports'         => $query->count(),
                'incidents'              => Rapport::whereYear('date_rapport', $annee)->where('incident_signale', true)->count(),
                'km_par_vehicule'        => Rapport::with('vehicule:id,marque,modele,immatriculation')
                    ->where('statut_validation', 'valide')->whereYear('date_rapport', $annee)
                    ->selectRaw('vehicle_id, SUM(kilometrage_arrivee - kilometrage_depart) as km_total')
                    ->groupBy('vehicle_id')->orderByDesc('km_total')->get(),
                'par_mois'               => Rapport::selectRaw('MONTH(date_rapport) as mois, COUNT(*) as nb_rapports, SUM(kilometrage_arrivee - kilometrage_depart) as km_total, SUM(carburant_consomme) as carburant_total')
                    ->where('statut_validation', 'valide')->whereYear('date_rapport', $annee)
                    ->groupBy('mois')->orderBy('mois')->get(),
            ],
        ]);
    }
}
 
<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAffectationRequest;
use App\Http\Resources\AffectationResource;
use App\Models\Affectation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class AffectationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Affectation::with('driver.user', 'vehicule', 'route', 'rapport');
        if ($request->filled('statut'))     $query->where('statut', $request->statut);
        if ($request->filled('driver_id'))  $query->pourDriver($request->driver_id);
        if ($request->filled('vehicle_id')) $query->pourVehicule($request->vehicle_id);
        if ($request->filled('route_id'))   $query->where('route_id', $request->route_id);
        if ($request->filled('aujourd_hui'))$query->aujourdhui();
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_depart', [$request->date_debut, $request->date_fin]);
        }
        if ($request->user()->isChauffeur()) {
            $driver = $request->user()->driver;
            if ($driver) $query->pourDriver($driver->id);
        }
        return AffectationResource::collection(
            $query->orderBy('date_depart', 'desc')->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreAffectationRequest $request): AffectationResource
    {
        $data = array_merge($request->validated(), ['created_by' => $request->user()->id]);
        $affectation = Affectation::create($data);
        return (new AffectationResource($affectation->load('driver.user', 'vehicule', 'route')))
            ->additional(['message' => 'Affectation créée avec succès.']);
    }
 
    public function show(Affectation $affectation): AffectationResource
    {
        return new AffectationResource(
            $affectation->load('driver.user', 'vehicule', 'route', 'rapport', 'createur')
        );
    }
 
    public function update(StoreAffectationRequest $request, Affectation $affectation): AffectationResource|JsonResponse
    {
        if ($affectation->statut !== 'planifiee') {
            return response()->json(['success' => false,
                                     'message' => 'Seules les affectations planifiées sont modifiables.'], 422);
        }
        $affectation->update($request->validated());
        return (new AffectationResource($affectation->fresh(['driver.user', 'vehicule', 'route'])))
            ->additional(['message' => 'Affectation mise à jour.']);
    }
 
    public function destroy(Affectation $affectation): JsonResponse
    {
        if (in_array($affectation->statut, ['en_cours', 'terminee'])) {
            return response()->json(['success' => false,
                                     'message' => 'Impossible de supprimer cette affectation.'], 422);
        }
        $affectation->delete();
        return response()->json(['success' => true, 'message' => 'Affectation supprimée.']);
    }
 
    public function demarrer(Request $request, Affectation $affectation): AffectationResource|JsonResponse
    {
        $driver = $request->user()->driver;
        if (!$request->user()->isAdmin() && !$request->user()->isGestionnaire()) {
            if (!$driver || $driver->id !== $affectation->driver_id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
            }
        }
        if ($affectation->statut !== 'planifiee') {
            return response()->json(['success' => false,
                                     'message' => 'Statut actuel : ' . $affectation->statut], 422);
        }
        $affectation->demarrer();
        return (new AffectationResource($affectation->fresh(['route', 'vehicule'])))
            ->additional(['message' => 'Mission démarrée. Bon voyage !']);
    }
 
    public function terminer(Request $request, Affectation $affectation): AffectationResource|JsonResponse
    {
        $driver = $request->user()->driver;
        if (!$request->user()->isAdmin() && !$request->user()->isGestionnaire()) {
            if (!$driver || $driver->id !== $affectation->driver_id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé.'], 403);
            }
        }
        if ($affectation->statut !== 'en_cours') {
            return response()->json(['success' => false, 'message' => 'Mission non en cours.'], 422);
        }
        $affectation->terminer();
        return (new AffectationResource($affectation->fresh(['route', 'vehicule'])))
            ->additional(['message' => 'Mission terminée. Pensez à soumettre votre rapport.']);
    }
 
    public function annuler(Request $request, Affectation $affectation): JsonResponse
    {
        $request->validate(['raison' => ['required', 'string', 'max:500']]);
        if (in_array($affectation->statut, ['terminee', 'annulee'])) {
            return response()->json(['success' => false, 'message' => 'Annulation impossible.'], 422);
        }
        $affectation->update(['statut' => 'annulee', 'observations' => $request->raison]);
        if ($affectation->vehicule->statut === 'en_route') {
            $affectation->vehicule->update(['statut' => 'disponible']);
        }
        return response()->json(['success' => true, 'message' => 'Affectation annulée.']);
    }
 
    public function planning(Request $request): JsonResponse
    {
        $request->validate([
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after_or_equal:date_debut'],
        ]);
        $affectations = Affectation::with('driver.user', 'vehicule', 'route')
            ->whereBetween('date_depart', [$request->date_debut, $request->date_fin])
            ->whereNotIn('statut', ['annulee'])
            ->orderBy('date_depart')->orderBy('heure_depart')
            ->get()
            ->groupBy(fn($a) => $a->date_depart->format('Y-m-d'));
 
        // Transformer chaque groupe en Resources
        $planning = $affectations->map(fn($groupe) =>
            AffectationResource::collection($groupe)
        );
 
        return response()->json(['success' => true, 'data' => $planning]);
    }
}
 
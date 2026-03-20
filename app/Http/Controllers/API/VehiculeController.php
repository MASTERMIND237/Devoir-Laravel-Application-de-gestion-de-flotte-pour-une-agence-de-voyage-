<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVehiculeRequest;
use App\Http\Requests\UpdateVehiculeRequest;
use App\Http\Resources\VehiculeResource;
use App\Models\Vehicule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class VehiculeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Vehicule::with('affectationEnCours.driver.user');
        if ($request->filled('statut'))       $query->where('statut', $request->statut);
        if ($request->filled('type_vehicule'))$query->where('type_vehicule', $request->type_vehicule);
        if ($request->filled('disponibles'))  $query->disponibles();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('marque', 'like', "%$s%")
                                      ->orWhere('modele', 'like', "%$s%")
                                      ->orWhere('immatriculation', 'like', "%$s%"));
        }
        return VehiculeResource::collection(
            $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreVehiculeRequest $request): VehiculeResource
    {
        $vehicule = Vehicule::create($request->validated());
        return (new VehiculeResource($vehicule))
            ->additional(['message' => 'Véhicule enregistré avec succès.']);
    }
 
    public function show(Vehicule $vehicule): VehiculeResource
    {
        $vehicule->load(['affectationEnCours.driver.user', 'affectationEnCours.route',
                         'derniereMaintenance', 'prochaineMaintenance', 'documents',
                         'affectations' => fn($q) => $q->with('driver.user', 'route')
                                                         ->latest('date_depart')->limit(5),
                         'maintenances' => fn($q) => $q->latest('date_maintenance')->limit(5)]);
 
        $vehicule->stats = [
            'total_km'              => $vehicule->kilometrage_actuel,
            'total_maintenances'    => $vehicule->maintenances()->count(),
            'cout_maintenance_total'=> $vehicule->maintenances()->where('statut', 'terminee')->sum('cout'),
            'total_affectations'    => $vehicule->affectations()->count(),
        ];
 
        return new VehiculeResource($vehicule);
    }
 
    public function update(UpdateVehiculeRequest $request, Vehicule $vehicule): VehiculeResource
    {
        $vehicule->update($request->validated());
        return (new VehiculeResource($vehicule->fresh()))
            ->additional(['message' => 'Véhicule mis à jour.']);
    }
 
    public function destroy(Vehicule $vehicule): JsonResponse
    {
        if ($vehicule->statut === 'en_route') {
            return response()->json(['success' => false,
                                     'message' => 'Impossible de supprimer un véhicule en route.'], 422);
        }
        $vehicule->delete();
        return response()->json(['success' => true, 'message' => 'Véhicule supprimé.']);
    }
 
    public function updatePosition(Request $request, Vehicule $vehicule): JsonResponse
    {
        $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);
        $vehicule->updatePosition($request->latitude, $request->longitude);
        return response()->json(['success' => true, 'data' => $vehicule->position]);
    }
 
    public function carte(): AnonymousResourceCollection
    {
        $vehicules = Vehicule::where('statut', 'en_route')
            ->whereNotNull('latitude')->whereNotNull('longitude')
            ->with('affectationEnCours.driver.user', 'affectationEnCours.route')
            ->get();
 
        return VehiculeResource::collection($vehicules);
    }
 
    public function alertes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'assurance_expirant'  => VehiculeResource::collection(Vehicule::assuranceExpirantBientot(30)->get()),
                'visite_expirant'     => VehiculeResource::collection(Vehicule::visiteTechniqueExpirantBientot(30)->get()),
                'hors_service'        => VehiculeResource::collection(Vehicule::where('statut', 'hors_service')->get()),
            ],
        ]);
    }
}
<?php

namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\AffectationResource;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class DriverController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Driver::with('user');
 
        if ($request->filled('statut'))     $query->where('statut', $request->statut);
        if ($request->filled('ville'))      $query->where('ville', $request->ville);
        if ($request->filled('disponibles'))$query->disponibles();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn($q) => $q->where('nom', 'like', "%$s%")
                                                   ->orWhere('prenom', 'like', "%$s%"))
                  ->orWhere('numero_permis', 'like', "%$s%");
        }
        if ($request->filled('permis_expirant_bientot')) {
            $query->permisExpirantBientot($request->input('jours', 30));
        }
 
        return DriverResource::collection(
            $query->orderBy('created_at', 'desc')
                  ->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreDriverRequest $request): DriverResource
    {
        $driver = Driver::create($request->validated());
        $driver->user->update(['role' => 'chauffeur']);
 
        return (new DriverResource($driver->load('user')))
            ->additional(['message' => 'Profil chauffeur créé avec succès.']);
    }
 
    public function show(Driver $driver): DriverResource
    {
        $driver->load(['user', 'user.documents', 'documents',
            'affectations' => fn($q) => $q->with('route', 'vehicule')->latest('date_depart')->limit(10),
            'rapports'     => fn($q) => $q->latest('date_rapport')->limit(5),
        ]);
 
        // On injecte les stats directement dans le resource via le modèle
        $driver->stats = [
            'total_affectations'     => $driver->affectations()->count(),
            'affectations_terminees' => $driver->affectations()->where('statut', 'terminee')->count(),
            'km_total'               => $driver->rapports()->where('statut_validation', 'valide')->sum('kilometrage_parcouru'),
            'incidents_signales'     => $driver->rapports()->where('incident_signale', true)->count(),
        ];
 
        return new DriverResource($driver);
    }
 
    public function update(UpdateDriverRequest $request, Driver $driver): DriverResource
    {
        $driver->update($request->validated());
        return (new DriverResource($driver->fresh('user')))
            ->additional(['message' => 'Profil chauffeur mis à jour.']);
    }
 
    public function destroy(Driver $driver): JsonResponse
    {
        if ($driver->affectations()->where('statut', 'en_cours')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible : affectation en cours.',
            ], 422);
        }
        $driver->delete();
        return response()->json(['success' => true, 'message' => 'Profil chauffeur supprimé.']);
    }
 
    public function affectations(Request $request, Driver $driver): AnonymousResourceCollection
    {
        $query = $driver->affectations()->with('route', 'vehicule', 'rapport');
        if ($request->filled('statut')) $query->where('statut', $request->statut);
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_depart', [$request->date_debut, $request->date_fin]);
        }
        return AffectationResource::collection(
            $query->latest('date_depart')->paginate($request->input('per_page', 10))
        );
    }
 
    public function dashboard(Request $request): DriverResource|JsonResponse
    {
        $driver = $request->user()->driver;
 
        if (!$driver) {
            return response()->json(['success' => false,
                                     'message' => 'Aucun profil chauffeur associé.'], 404);
        }
 
        $driver->affectation_du_jour = $driver->affectations()
            ->with('route', 'vehicule')
            ->whereDate('date_depart', today())
            ->whereIn('statut', ['planifiee', 'en_cours'])
            ->first();
 
        $driver->prochaines_affectations = $driver->affectations()
            ->with('route', 'vehicule')
            ->where('statut', 'planifiee')
            ->whereBetween('date_depart', [today(), today()->addDays(7)])
            ->get();
 
        $driver->rapports_en_attente = $driver->affectations()
            ->doesntHave('rapport')
            ->where('statut', 'terminee')
            ->with('route')->latest('date_depart')->limit(5)->get();
 
        return new DriverResource($driver->load('user'));
    }
}
 
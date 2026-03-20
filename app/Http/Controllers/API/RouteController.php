<?php


namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
 
class RouteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Route::query();
        if ($request->filled('statut'))       $query->where('statut', $request->statut);
        if ($request->filled('ville_depart')) $query->depuisVille($request->ville_depart);
        if ($request->filled('ville_arrivee'))$query->versVille($request->ville_arrivee);
        if ($request->filled('de') && $request->filled('vers')) {
            $query->entre($request->de, $request->vers);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                                      ->orWhere('ville_depart', 'like', "%$s%")
                                      ->orWhere('ville_arrivee', 'like', "%$s%"));
        }
        return RouteResource::collection(
            $query->withCount('affectationsActives')
                  ->orderBy('ville_depart')
                  ->paginate($request->input('per_page', 15))
        );
    }
 
    public function store(StoreRouteRequest $request): RouteResource
    {
        $route = Route::create($request->validated());
        return (new RouteResource($route))->additional(['message' => 'Route créée avec succès.']);
    }
 
    public function show(Route $route): RouteResource
    {
        $route->load(['affectations' => fn($q) => $q->with('driver.user', 'vehicule')
                                                      ->latest('date_depart')->limit(10)]);
        $route->stats = [
            'total_affectations'   => $route->affectations()->count(),
            'affectations_actives' => $route->affectationsActives()->count(),
        ];
        return new RouteResource($route);
    }
 
    public function update(StoreRouteRequest $request, Route $route): RouteResource
    {
        $route->update($request->validated());
        return (new RouteResource($route->fresh()))->additional(['message' => 'Route mise à jour.']);
    }
 
    public function destroy(Route $route): JsonResponse
    {
        if ($route->affectationsActives()->exists()) {
            return response()->json(['success' => false,
                                     'message' => 'Impossible : affectations actives sur cette route.'], 422);
        }
        $route->delete();
        return response()->json(['success' => true, 'message' => 'Route supprimée.']);
    }
 
    public function villes(): JsonResponse
    {
        $villes = Route::actives()->distinct()->pluck('ville_depart')
            ->merge(Route::actives()->distinct()->pluck('ville_arrivee'))
            ->unique()->sort()->values();
        return response()->json(['success' => true, 'data' => $villes]);
    }
}
 
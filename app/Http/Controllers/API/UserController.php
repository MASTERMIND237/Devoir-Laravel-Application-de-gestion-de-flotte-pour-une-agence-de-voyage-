<?php

namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
 
class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::with('driver');
 
        if ($request->filled('role'))      $query->where('role', $request->role);
        if ($request->filled('is_active')) $query->where('is_active', $request->boolean('is_active'));
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('nom', 'like', "%$s%")
                                      ->orWhere('prenom', 'like', "%$s%")
                                      ->orWhere('email', 'like', "%$s%"));
        }
 
        $sortBy  = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $users   = $query->orderBy($sortBy, $sortDir)
                         ->paginate($request->input('per_page', 15));
 
        // collection() gère automatiquement la pagination
        return UserResource::collection($users);
    }
 
    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->validated();
        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')
                ->store('photos/profils', 'public');
        }
 
        $user = User::create($data);
 
        return (new UserResource($user))
            ->additional(['message' => 'Utilisateur créé avec succès.'])
            ->response()
            ->setStatusCode(201)
            ->getData(true);
 
        // Version simplifiée (sans 201) :
        // return new UserResource($user);
    }
 
    public function show(User $user): UserResource
    {
        $user->load(['driver', 'driver.affectations.route',
                     'driver.affectations.vehicule', 'documents']);
 
        return new UserResource($user);
    }
 
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $data = $request->validated();
 
        if ($request->hasFile('photo_profil')) {
            if ($user->photo_profil) Storage::disk('public')->delete($user->photo_profil);
            $data['photo_profil'] = $request->file('photo_profil')
                ->store('photos/profils', 'public');
        }
 
        $user->update($data);
 
        return (new UserResource($user->fresh('driver')))
            ->additional(['message' => 'Utilisateur mis à jour avec succès.']);
    }
 
    public function destroy(User $user): JsonResponse
    {
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé.']);
    }
 
    public function toggleActive(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        $statut = $user->is_active ? 'activé' : 'désactivé';
        return response()->json(['success' => true, 'message' => "Compte {$statut}.",
                                 'data' => ['is_active' => $user->is_active]]);
    }
}
 
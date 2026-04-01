<?php

namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class AuthController extends Controller
{
    public function register(RegisterRequest $request): UserResource
    {
        $data = $request->validated();
 
        if ($request->hasFile('photo_profil')) {
            $data['photo_profil'] = $request->file('photo_profil')
                ->store('photos/profils', 'public');
        }
 
        // Par défaut, si aucun rôle fourni, attribuer 'gestionnaire'
        if (empty($data['role'])) {
            $data['role'] = 'gestionnaire';
        }

        $user  = User::create($data);
        $token = $user->createToken($request->input('device', 'web'))->plainTextToken;
 
        // Resource + données additionnelles via ->additional()
        return (new UserResource($user->load('driver')))
            ->additional([
                'token'   => $token,
                'message' => 'Compte créé avec succès.',
            ]);
    }
 
    public function login(LoginRequest $request): JsonResponse|UserResource
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.',
            ], 401);
        }
 
        $user = Auth::user();
 
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Compte suspendu. Contactez l\'administrateur.',
            ], 403);
        }
 
        $device = $request->input('device', 'web');
        $user->tokens()->where('name', $device)->delete();
        $token = $user->createToken($device)->plainTextToken;
 
        return (new UserResource($user->load('driver')))
            ->additional([
                'token'   => $token,
                'message' => 'Connexion réussie.',
            ]);
    }
 
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnexion réussie.']);
    }
 
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['success' => true, 'message' => 'Déconnecté de tous les appareils.']);
    }
 
    public function me(Request $request): UserResource
    {
        // Resource retourne directement le JSON formaté
        return new UserResource($request->user()->load('driver'));
    }
}
 











// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\User;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Auth;

// class AuthController extends Controller
// {
//     //register
//     public function register(Request $request)
// {
//     $data = $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|unique:users',
//         'password' => 'required|confirmed|min:6',
//     ]);
//     User::create([
//         'name' => $data['name'],
//         'email' => $data['email'],
//         'password' => Hash::make($data['password']),
//     ]);
//     return redirect()->route('login');
// }
// //login

// public function login(Request $request)
// {
//     $credentials = $request->validate([
//         'email' => 'required|email',
//         'password' => 'required',
//     ]);
//     if (Auth::attempt($credentials)) {
//         $request->session()->regenerate();
//         return redirect()->route('products.index');
//     }
//     return back()->withErrors([
//         'email' => 'Email ou mot de passe incorrect',
//     ]);
// }
// //logout
// public function logout(Request $request)
// {
//     Auth::logout();
//     $request->session()->invalidate();
//     $request->session()->regenerateToken();
//     return redirect()->route('login');
//         try {
//             if (Auth::attempt($credentials)) {
//                 $request->session()->regenerate();
//                 return redirect()->route('products.index');
//             }
//         } catch (\RuntimeException $e) {
//             // Mot de passe stocké non chiffré ou format inattendu — ne pas exposer l'erreur
//             \Log::warning('Login RuntimeException: '.$e->getMessage());
//         }
// }
// }

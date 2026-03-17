<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TableauController;
use App\Http\Controllers\AuthController;
//use App\Http\Controllers\AuthController2;
use App\Http\Middleware\CheckAuth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::resource('produits', ProductController::class);

Route::get('liste_tab',[TableauController::class,'afficheTableau']);
//authentification

//register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
//login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
//midlleware
Route::middleware('check.auth')->group(function () {
    Route::resource('products', ProductController::class);
});
//logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




// //devoir madame Belinga
// // Page d'accueil
// Route::get('/', function () {
//     return view('welcome');
// });

// // Routes pour les invités (non connectés)
// Route::middleware('guest')->group(function () {
//     // Afficher le formulaire de connexion
//     Route::get('/login', [AuthController2::class, 'showLoginForm'])->name('login');
    
//     // Traiter la connexion
//     Route::post('/login', [AuthController2::class, 'login']);
    
//     // Afficher le formulaire d'inscription
//     Route::get('/register', [AuthController2::class, 'showRegisterForm'])->name('register');
    
//     // Traiter l'inscription
//     Route::post('/register', [AuthController2::class, 'register']);
// });

// // Routes pour les utilisateurs authentifiés
// Route::middleware('auth')->group(function () {
//     // Dashboard (page après connexion)
//     Route::get('/dashboard', function () {
//         return view('app/dashboard');
//     })->name('dashboard');
    
//     // Déconnexion
//     Route::post('/logout', [AuthController2::class, 'logout'])->name('logout');
// });

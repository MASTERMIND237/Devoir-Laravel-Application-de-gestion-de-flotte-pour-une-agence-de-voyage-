<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| NotificationController
|--------------------------------------------------------------------------
| Permet au frontend et à la PWA mobile de :
|  - Lire les notifications non lues
|  - Marquer une ou toutes les notifications comme lues
|  - Compter les notifications non lues (pour le badge 🔔)
|
| Routes à ajouter dans api.php (sous le préfixe satisfy) :
|
|   Route::prefix('notifications')->group(function () {
|       Route::get('/',           [NotificationController::class, 'index']);
|       Route::get('/non-lues',   [NotificationController::class, 'nonLues']);
|       Route::patch('/{id}/lire',[NotificationController::class, 'marquerLue']);
|       Route::patch('/lire-tout',[NotificationController::class, 'marquerToutesLues']);
|       Route::delete('/{id}',    [NotificationController::class, 'destroy']);
|   });
*/

class NotificationController extends Controller
{
    /**
     * GET /satisfy/notifications
     * Toutes les notifications du user connecté (avec pagination)
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'data'    => $notifications,
        ]);
    }

    /**
     * GET /satisfy/notifications/non-lues
     * Notifications non lues + compteur pour le badge 🔔
     */
    public function nonLues(Request $request): JsonResponse
    {
        $user          = $request->user();
        $nonLues       = $user->unreadNotifications()->latest()->get();
        $compteur      = $user->unreadNotifications()->count();

        return response()->json([
            'success'  => true,
            'data'     => [
                'notifications' => $nonLues,
                'compteur'      => $compteur,
            ],
        ]);
    }

    /**
     * PATCH /satisfy/notifications/{id}/lire
     * Marque une notification spécifique comme lue
     */
    public function marquerLue(Request $request, string $id): JsonResponse
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification introuvable.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue.',
        ]);
    }

    /**
     * PATCH /satisfy/notifications/lire-tout
     * Marque TOUTES les notifications comme lues
     */
    public function marquerToutesLues(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.',
        ]);
    }

    /**
     * DELETE /satisfy/notifications/{id}
     * Supprime une notification
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée.',
        ]);
    }
}
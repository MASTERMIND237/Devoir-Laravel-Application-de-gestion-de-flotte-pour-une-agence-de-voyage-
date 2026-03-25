<?php

namespace App\Providers;

use App\Models\Affectation;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Maintenance;
use App\Models\Rapport;
use App\Models\User;
use App\Models\Vehicule;
use App\Policies\AffectationPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DriverPolicy;
use App\Policies\MaintenancePolicy;
use App\Policies\RapportPolicy;
use App\Policies\UserPolicy;
use App\Policies\VehiculePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping Modèle → Policy
     * Laravel utilise ce tableau pour savoir quelle Policy
     * appliquer quand on fait $this->authorize('action', $model)
     */
    protected $policies = [
        User::class        => UserPolicy::class,
        Driver::class      => DriverPolicy::class,
        Vehicule::class    => VehiculePolicy::class,
        Affectation::class => AffectationPolicy::class,
        Maintenance::class => MaintenancePolicy::class,
        Rapport::class     => RapportPolicy::class,
        Document::class    => DocumentPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
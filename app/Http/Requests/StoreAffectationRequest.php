<?php

namespace App\Http\Requests;

use App\Models\Affectation;
use App\Models\Driver;
use App\Models\Vehicule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->isGestionnaire();
    }

    public function rules(): array
    {
        return [
            'driver_id'           => ['required', 'exists:drivers,id'],
            'vehicle_id'          => ['required', 'exists:vehicules,id'],
            'route_id'            => ['required', 'exists:routes,id'],
            'date_depart'         => ['required', 'date', 'after_or_equal:today'],  // Pas de date dans le passé
            'heure_depart'        => ['required', 'date_format:H:i'],
            'date_arrivee_prevue' => ['nullable', 'date', 'after_or_equal:date_depart'],
            'heure_arrivee_prevue'=> ['nullable', 'date_format:H:i'],
            'nombre_passagers'    => ['nullable', 'integer', 'min:0'],
            'observations'        => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Validation supplémentaire APRÈS les règles de base
     * Vérifie les conflits d'affectation (driver et véhicule déjà pris ce jour-là)
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            // Vérifier que le driver est disponible ce jour-là
            if ($this->driver_id && $this->date_depart) {
                $conflit = Affectation::where('driver_id', $this->driver_id)
                    ->whereDate('date_depart', $this->date_depart)
                    ->whereIn('statut', ['planifiee', 'en_cours'])
                    ->exists();

                if ($conflit) {
                    $validator->errors()->add(
                        'driver_id',
                        'Ce chauffeur a déjà une affectation planifiée ou en cours à cette date.'
                    );
                }
            }

            // Vérifier que le véhicule est disponible ce jour-là
            if ($this->vehicle_id && $this->date_depart) {
                $conflit = Affectation::where('vehicle_id', $this->vehicle_id)
                    ->whereDate('date_depart', $this->date_depart)
                    ->whereIn('statut', ['planifiee', 'en_cours'])
                    ->exists();

                if ($conflit) {
                    $validator->errors()->add(
                        'vehicle_id',
                        'Ce véhicule a déjà une affectation planifiée ou en cours à cette date.'
                    );
                }
            }

            // Vérifier que le driver est actif
            if ($this->driver_id) {
                $driver = Driver::find($this->driver_id);
                if ($driver && $driver->statut !== 'actif') {
                    $validator->errors()->add(
                        'driver_id',
                        'Ce chauffeur n\'est pas actif (statut : ' . $driver->statut . ').'
                    );
                }
            }

            // Vérifier que le véhicule est disponible
            if ($this->vehicle_id) {
                $vehicule = Vehicule::find($this->vehicle_id);
                if ($vehicule && $vehicule->statut !== 'disponible') {
                    $validator->errors()->add(
                        'vehicle_id',
                        'Ce véhicule n\'est pas disponible (statut : ' . $vehicule->statut . ').'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'driver_id.required'            => 'Le chauffeur est obligatoire.',
            'driver_id.exists'              => 'Le chauffeur sélectionné n\'existe pas.',
            'vehicle_id.required'           => 'Le véhicule est obligatoire.',
            'vehicle_id.exists'             => 'Le véhicule sélectionné n\'existe pas.',
            'route_id.required'             => 'La route est obligatoire.',
            'route_id.exists'               => 'La route sélectionnée n\'existe pas.',
            'date_depart.required'          => 'La date de départ est obligatoire.',
            'date_depart.after_or_equal'    => 'La date de départ ne peut pas être dans le passé.',
            'heure_depart.required'         => 'L\'heure de départ est obligatoire.',
            'heure_depart.date_format'      => 'L\'heure doit être au format HH:MM (ex: 06:30).',
            'date_arrivee_prevue.after_or_equal' => 'La date d\'arrivée prévue doit être après ou égale à la date de départ.',
        ];
    }
}
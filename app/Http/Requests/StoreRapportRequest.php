<?php

namespace App\Http\Requests;

use App\Models\Affectation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreRapportRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Un chauffeur peut soumettre un rapport pour ses propres affectations
        // Un gestionnaire/admin peut aussi créer un rapport
        return true;
    }

    public function rules(): array
    {
        return [
            'assignment_id'                => ['required', 'exists:affectations,id',
                                              Rule::unique('rapports', 'assignment_id')], // 1 rapport par affectation
            'kilometrage_depart'           => ['required', 'integer', 'min:0'],
            'kilometrage_arrivee'          => ['required', 'integer', 'min:0',
                                              'gt:kilometrage_depart'],                   // Arrivée > Départ
            'carburant_consomme'           => ['nullable', 'numeric', 'min:0', 'max:500'],
            'cout_carburant'               => ['nullable', 'numeric', 'min:0'],
            'nombre_passagers_transportes' => ['nullable', 'integer', 'min:0'],
            'etat_vehicule_depart'         => ['required', Rule::in(['bon', 'moyen', 'mauvais'])],
            'etat_vehicule_arrivee'        => ['required', Rule::in(['bon', 'moyen', 'mauvais'])],
            'incident_signale'             => ['required', 'boolean'],
            'description_incident'         => ['required_if:incident_signale,true', 'nullable', 'string', 'max:2000'],
            'observations'                 => ['nullable', 'string', 'max:2000'],
            'date_rapport'                 => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Validation croisée :
     * - Le rapport doit être soumis par le chauffeur de l'affectation
     * - L'affectation doit être en cours ou terminée (pas planifiée)
     * - Le kilométrage de départ doit correspondre au kilométrage actuel du véhicule
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            if ($this->assignment_id) {
                $affectation = Affectation::with('vehicule', 'driver.user')->find($this->assignment_id);

                if (!$affectation) return;

                // Vérifier que le chauffeur connecté est bien celui de l'affectation
                $user = $this->user();
                if ($user->isChauffeur()) {
                    $driverDeLaffectation = $affectation->driver->user_id ?? null;
                    if ($driverDeLaffectation !== $user->id) {
                        $validator->errors()->add(
                            'assignment_id',
                            'Vous ne pouvez soumettre un rapport que pour vos propres affectations.'
                        );
                    }
                }

                // L'affectation doit être en cours ou terminée
                if (!in_array($affectation->statut, ['en_cours', 'terminee'])) {
                    $validator->errors()->add(
                        'assignment_id',
                        'Un rapport ne peut être soumis que pour une affectation en cours ou terminée.'
                    );
                }

                // Le kilométrage de départ doit être >= kilométrage actuel du véhicule
                if ($this->kilometrage_depart && $affectation->vehicule) {
                    if ($this->kilometrage_depart < $affectation->vehicule->kilometrage_actuel) {
                        $validator->errors()->add(
                            'kilometrage_depart',
                            'Le kilométrage de départ (' . $this->kilometrage_depart . ' km) ne peut pas être inférieur au kilométrage actuel du véhicule (' . $affectation->vehicule->kilometrage_actuel . ' km).'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'assignment_id.required'               => 'L\'affectation est obligatoire.',
            'assignment_id.exists'                 => 'L\'affectation sélectionnée n\'existe pas.',
            'assignment_id.unique'                 => 'Un rapport existe déjà pour cette affectation.',
            'kilometrage_depart.required'          => 'Le kilométrage de départ est obligatoire.',
            'kilometrage_arrivee.required'         => 'Le kilométrage d\'arrivée est obligatoire.',
            'kilometrage_arrivee.gt'               => 'Le kilométrage d\'arrivée doit être supérieur au kilométrage de départ.',
            'etat_vehicule_depart.required'        => 'L\'état du véhicule au départ est obligatoire.',
            'etat_vehicule_arrivee.required'       => 'L\'état du véhicule à l\'arrivée est obligatoire.',
            'etat_vehicule_depart.in'              => 'L\'état doit être : bon, moyen ou mauvais.',
            'etat_vehicule_arrivee.in'             => 'L\'état doit être : bon, moyen ou mauvais.',
            'incident_signale.required'            => 'Veuillez indiquer si un incident a été signalé.',
            'description_incident.required_if'    => 'Veuillez décrire l\'incident signalé.',
            'date_rapport.required'                => 'La date du rapport est obligatoire.',
            'date_rapport.before_or_equal'         => 'La date du rapport ne peut pas être dans le futur.',
        ];
    }
}
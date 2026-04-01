import React from 'react';
import { useForm } from 'react-hook-form';
import { Card } from '../../components/ui/Card';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';

const AffectationForm = () => {
  const { register, handleSubmit } = useForm();

  const onSubmit = (data) => console.log(data);

  return (
    <div className="max-w-2xl mx-auto">
      <Card title="Nouvelle Affectation">
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="flex flex-col gap-1.5">
              <label className="text-cyprus font-medium text-sm ml-1">Véhicule</label>
              <select {...register('vehicule_id')} className="bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus">
                <option value="">Sélectionner un véhicule...</option>
                <option value="1">LT-882-CI (Disponible)</option>
                <option value="2">CE-441-AF (Disponible)</option>
              </select>
            </div>

            <div className="flex flex-col gap-1.5">
              <label className="text-cyprus font-medium text-sm ml-1">Chauffeur</label>
              <select {...register('chauffeur_id')} className="bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus">
                <option value="">Sélectionner un chauffeur...</option>
                <option value="1">Jean Dupont</option>
                <option value="2">Marie Fouda</option>
              </select>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input label="Date de début" type="date" {...register('date_debut')} />
            <Input label="Date de fin prévue" type="date" {...register('date_fin')} />
          </div>

          <div className="flex justify-end gap-3 pt-6 border-t border-sand-dark">
            <Button variant="ghost">Annuler</Button>
            <Button variant="primary" type="submit">Confirmer l'affectation</Button>
          </div>
        </form>
      </Card>
    </div>
  );
};

export default AffectationForm;
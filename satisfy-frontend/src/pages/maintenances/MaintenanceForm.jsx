import React from 'react';
import { useForm } from 'react-hook-form';
import { Card } from '../../components/ui/Card';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';

const MaintenanceForm = () => {
  const { register, handleSubmit } = useForm();

  const onSubmit = (data) => console.log("Maintenance enregistrée:", data);

  return (
    <div className="max-w-3xl mx-auto">
      <Card title="Programmer une intervention technique">
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="flex flex-col gap-1.5">
              <label className="text-cyprus font-medium text-sm">Sélectionner le véhicule</label>
              <select {...register('vehicule_id')} className="bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus">
                <option value="">-- Choisir un véhicule --</option>
                <option value="1">LT-882-CI - Toyota Hilux</option>
                <option value="2">CE-441-AF - Toyota Prado</option>
              </select>
            </div>
            <Input label="Type d'intervention" placeholder="ex: Révision complète, Freins..." {...register('type')} />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input label="Date prévue" type="date" {...register('date_prevue')} />
            <Input label="Coût estimé (XAF)" type="number" placeholder="ex: 50000" {...register('cout_estime')} />
          </div>

          <div className="flex flex-col gap-1.5">
            <label className="text-cyprus font-medium text-sm ml-1">Notes ou Description des travaux</label>
            <textarea 
              {...register('description')}
              rows={4}
              className="bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus"
              placeholder="Détails sur les pièces à changer..."
            />
          </div>

          <div className="flex justify-end gap-4 pt-4 border-t border-sand-dark">
            <Button variant="outline" type="button">Annuler</Button>
            <Button variant="primary" type="submit">Enregistrer l'intervention</Button>
          </div>
        </form>
      </Card>
    </div>
  );
};

export default MaintenanceForm;
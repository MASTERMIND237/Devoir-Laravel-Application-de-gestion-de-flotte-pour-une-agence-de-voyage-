import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { validators } from '../../utils/validators';
import { Card } from '../../components/ui/Card';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';

const VehiculeForm = ({ initialData = null }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    resolver: zodResolver(validators.vehicule),
    defaultValues: initialData || {}
  });

  const onSubmit = (data) => {
    console.log("Envoi des données:", data);
    // Appel à createVehicule ou updateVehicule via le hook
  };

  return (
    <div className="max-w-3xl mx-auto">
      <Card title={initialData ? "Modifier le véhicule" : "Ajouter un nouveau véhicule"}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input 
              label="Immatriculation" 
              placeholder="ex: CE-000-XX"
              {...register('immatriculation')}
              error={errors.immatriculation?.message}
            />
            <Input 
              label="Modèle" 
              placeholder="ex: Toyota Prado"
              {...register('modele')}
              error={errors.modele?.message}
            />
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="flex flex-col gap-1.5">
              <label className="text-cyprus font-medium text-sm">Type de véhicule</label>
              <select 
                {...register('type')}
                className="bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus"
              >
                <option value="camion">Camion</option>
                <option value="pickup">Pickup</option>
                <option value="berline">Berline</option>
              </select>
            </div>
            <Input 
              label="Consommation (L/100km)" 
              type="number"
              step="0.1"
              {...register('consommation_moyenne', { valueAsNumber: true })}
              error={errors.consommation_moyenne?.message}
            />
          </div>

          <div className="flex justify-end gap-4 pt-4">
            <Button variant="outline" type="button">Annuler</Button>
            <Button variant="primary" type="submit">Enregistrer le véhicule</Button>
          </div>
        </form>
      </Card>
    </div>
  );
};

export default VehiculeForm;
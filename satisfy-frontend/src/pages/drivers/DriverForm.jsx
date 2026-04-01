import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { validators } from '../../utils/validators';
import { Card } from '../../components/ui/Card';
import { Input } from '../../components/ui/Input';
import { Button } from '../../components/ui/Button';

const DriverForm = ({ initialData = null }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    resolver: zodResolver(validators.driver),
    defaultValues: initialData || {}
  });

  const onSubmit = (data) => {
    console.log("Données Chauffeur:", data);
  };

  return (
    <div className="max-w-2xl mx-auto">
      <Card title={initialData ? "Modifier le profil" : "Enregistrer un chauffeur"}>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          <Input 
            label="Nom complet" 
            placeholder="Nom et Prénom"
            {...register('nom')}
            error={errors.nom?.message}
          />

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Input 
              label="Numéro de téléphone" 
              placeholder="ex: 699123456"
              {...register('telephone')}
              error={errors.telephone?.message}
            />
            <Input 
              label="Numéro de permis" 
              placeholder="ex: CM-998877"
              {...register('permis_numero')}
              error={errors.permis_numero?.message}
            />
          </div>

          <div className="flex justify-end gap-3 pt-4 border-t border-sand-dark">
            <Button variant="ghost" type="button">Annuler</Button>
            <Button variant="primary" type="submit">Sauvegarder</Button>
          </div>
        </form>
      </Card>
    </div>
  );
};

export default DriverForm;
import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { validators } from '../../utils/validators';
import { useAuth } from '../../hooks/useAuth';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';

const RegisterPage = () => {
  const { register: registerUser, isLoading } = useAuth();

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(validators.register),
    defaultValues: {
      nom: '',
      prenom: '',
      email: '',
      password: '',
      password_confirmation: '',
      role: 'gestionnaire',
    },
  });

  const onSubmit = (data) => {
    registerUser(data);
  };

  return (
    <div className="min-h-screen w-full flex items-center justify-center bg-sand p-4">
      <div className="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10">
        <h2 className="text-2xl font-bold text-cyprus mb-4">Créer un compte</h2>
        <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
          <Input label="Prénom" error={errors.prenom?.message} {...register('prenom')} />
          <Input label="Nom" error={errors.nom?.message} {...register('nom')} />
          <Input label="Email" type="email" error={errors.email?.message} {...register('email')} />
          <Input label="Mot de passe" type="password" error={errors.password?.message} {...register('password')} />
          <Input label="Confirmation" type="password" error={errors.password_confirmation?.message} {...register('password_confirmation')} />

          <div>
            <label className="text-sm text-cyprus/70">Rôle</label>
            <select {...register('role')} className="w-full mt-2 bg-white border-2 border-sand-dark rounded-xl px-4 py-2.5 outline-none focus:border-cyprus">
              <option value="gestionnaire">Gestionnaire</option>
              <option value="chauffeur">Chauffeur</option>
            </select>
          </div>

          <Button type="submit" className="w-full py-3" isLoading={isLoading}>S'inscrire</Button>
        </form>
      </div>
    </div>
  );
};

export default RegisterPage;

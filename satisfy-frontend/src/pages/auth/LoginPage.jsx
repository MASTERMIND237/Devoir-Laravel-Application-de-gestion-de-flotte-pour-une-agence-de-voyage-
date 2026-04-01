import React from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { Mail, Lock, ShieldCheck } from 'lucide-react';
import { validators } from '../../utils/validators';
import { useAuth } from '../../hooks/useAuth';
import { Button } from '../../components/ui/Button';
import { Input } from '../../components/ui/Input';

const LoginPage = () => {
  const { login, isLoading } = useAuth();

  // Initialisation du formulaire avec Zod
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({
    resolver: zodResolver(validators.login),
    defaultValues: {
      email: '',
      password: '',
    },
  });

  const onSubmit = (data) => {
    login(data);
  };

  return (
    <div className="min-h-screen w-full flex items-center justify-center bg-sand p-4">
      <div className="w-full max-w-[1100px] grid grid-cols-1 md:grid-cols-2 bg-white rounded-[40px] shadow-2xl overflow-hidden border border-sand-dark">
        
        {/* Section Gauche : Visuelle (Inspirée du modèle) */}
        <div className="hidden md:flex flex-col justify-between p-12 bg-cyprus text-white relative overflow-hidden">
          <div className="relative z-10">
            <h1 className="text-4xl font-syne font-extrabold text-kiwi tracking-tighter mb-2">
              SATISFY<span className="text-white">.</span>
            </h1>
            <p className="text-sand/60 font-sans max-w-xs">
              Gestion intelligente de flotte et suivi logistique en temps réel.
            </p>
          </div>

          <div className="relative z-10">
            <div className="flex items-center gap-3 mb-6">
              <div className="p-3 bg-kiwi/10 rounded-2xl">
                <ShieldCheck className="text-kiwi" size={32} />
              </div>
              <div>
                <p className="font-bold text-lg">Sécurité Garantie</p>
                <p className="text-sm text-sand/60">Accès sécurisé via protocole TDE.</p>
              </div>
            </div>
          </div>

          {/* Élément décoratif en arrière-plan */}
          <div className="absolute -bottom-20 -left-20 w-80 h-80 bg-kiwi/10 rounded-full blur-3xl" />
        </div>

        {/* Section Droite : Formulaire */}
        <div className="p-8 md:p-16 flex flex-col justify-center">
          <div className="mb-10 text-center md:text-left">
            <h2 className="text-3xl font-syne font-extrabold text-cyprus mb-2">Bon retour !</h2>
            <p className="text-cyprus/60 font-sans">Veuillez vous identifier pour accéder au gestionnaire.</p>
          </div>

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            <div className="relative">
              <Mail className="absolute left-4 top-[46px] text-cyprus/40" size={18} />
              <Input
                label="Adresse Email"
                placeholder="ex: admin@satisfy.cm"
                type="email"
                className="pl-10"
                error={errors.email?.message}
                {...register('email')}
              />
            </div>

            <div className="relative">
              <Lock className="absolute left-4 top-[46px] text-cyprus/40" size={18} />
              <Input
                label="Mot de passe"
                placeholder="••••••••"
                type="password"
                className="pl-10"
                error={errors.password?.message}
                {...register('password')}
              />
            </div>

            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2 text-cyprus/70 cursor-pointer">
                <input type="checkbox" className="rounded border-sand-dark text-kiwi focus:ring-kiwi" />
                Se souvenir de moi
              </label>
              <a href="#" className="text-cyprus font-bold hover:text-kiwi transition-colors">
                Mot de passe oublié ?
              </a>
            </div>

            <Button 
              type="submit" 
              className="w-full py-4 text-lg mt-4" 
              isLoading={isLoading}
            >
              Se connecter
            </Button>
          </form>

          <p className="mt-6 text-center text-sm text-cyprus/70">
            Pas encore de compte ? <a href="/register" className="font-bold text-kiwi">S'inscrire</a>
          </p>

          <p className="mt-8 text-center text-sm text-cyprus/50 font-sans">
            © 2026 Satisfy Fleet Management. Tous droits réservés.
          </p>
        </div>
      </div>
    </div>
  );
};

export default LoginPage;
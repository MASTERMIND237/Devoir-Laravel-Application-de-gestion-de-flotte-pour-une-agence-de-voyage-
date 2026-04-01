import { z } from 'zod';

export const validators = {
  // Validation Login
  login: z.object({
    email: z.string().email("Format d'email invalide"),
    password: z.string().min(6, "Le mot de passe doit faire au moins 6 caractères"),
  }),

  // Validation Véhicule
  vehicule: z.object({
    immatriculation: z.string().min(4, "Immatriculation trop courte"),
    modele: z.string().min(2, "Le modèle est requis"),
    type: z.enum(['camion', 'pickup', 'berline', 'moto']),
    consommation_moyenne: z.number().positive("La consommation doit être positive"),
  }),

  // Validation Inscription
  register: z.object({
    nom: z.string().min(2, "Le nom est requis"),
    prenom: z.string().min(2, "Le prénom est requis"),
    email: z.string().email("Format d'email invalide"),
    password: z.string().min(8, "Le mot de passe doit faire au moins 8 caractères"),
    password_confirmation: z.string().min(8),
    role: z.enum(['gestionnaire', 'chauffeur']).optional(),
  }).refine((data) => data.password === data.password_confirmation, {
    message: "La confirmation du mot de passe ne correspond pas",
    path: ['password_confirmation'],
  }),

  // Validation Chauffeur
  driver: z.object({
    nom: z.string().min(2, "Le nom est requis"),
    telephone: z.string().regex(/^[0-9]{9}$/, "Le numéro doit contenir 9 chiffres"),
    permis_numero: z.string().min(5, "Numéro de permis requis"),
  })
};
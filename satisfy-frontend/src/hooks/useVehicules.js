import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { vehiculesApi } from '../api/vehicules.api';
import toast from 'react-hot-toast';

export const useVehicules = (filters = {}) => {
  const queryClient = useQueryClient();

  // Liste globale avec filtres
  const vehiculesQuery = useQuery({
    queryKey: ['vehicules', filters],
    queryFn: () => vehiculesApi.getAll(filters).then(res => res.data),
  });

  // Données spécifiques pour la carte (refresh toutes les 30s)
  const mapQuery = useQuery({
    queryKey: ['vehicules-carte'],
    queryFn: () => vehiculesApi.getMapPositions().then(res => res.data),
    refetchInterval: 30000, 
  });

  const createMutation = useMutation({
    mutationFn: (data) => vehiculesApi.create(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['vehicules'] });
      toast.success('Véhicule ajouté avec succès');
    }
  });

  return {
    vehicules: vehiculesQuery.data?.data || [],
    mapVehicules: mapQuery.data?.data || [],
    isLoading: vehiculesQuery.isLoading,
    createVehicule: createMutation.mutate,
    isCreating: createMutation.isPending
  };
};
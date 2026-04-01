import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { driversApi } from '../api/drivers.api';
import toast from 'react-hot-toast';

export const useDrivers = () => {
  const queryClient = useQueryClient();

  const driversQuery = useQuery({
    queryKey: ['drivers'],
    queryFn: () => driversApi.getAll().then(res => res.data),
  });

  const deleteMutation = useMutation({
    mutationFn: (id) => driversApi.delete(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['drivers'] });
      toast.success('Chauffeur supprimé');
    },
    onError: () => toast.error('Impossible de supprimer ce chauffeur')
  });

  return {
    drivers: driversQuery.data?.data || [],
    isLoading: driversQuery.isLoading,
    deleteDriver: deleteMutation.mutate
  };
};
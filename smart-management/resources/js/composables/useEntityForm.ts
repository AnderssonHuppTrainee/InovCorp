import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Entity } from '@/types'
import route from '@/routes/entities';
import type { EntityFormData } from '@/schemas/entitySchema';

export function useEntityForm(defaultTypes:string[] = [], type: string = 'client', entity: Entity | null = null) {

  // --- Computed helpers
  const isClient = computed(() => {
    const types = entity?.types || defaultTypes || [];
    return types.includes('client');
  });
  
  const isSupplier = computed(() => {
    const types = entity?.types || defaultTypes || [];
    return types.includes('supplier');
  });

  // --- Manipulação de tipos
  const toggleType = (currentTypes: string[], type: string, checked: boolean) => {
    if (checked && !currentTypes.includes(type)) {
      return [...currentTypes, type];
    } else if (!checked) {
      return currentTypes.filter((t) => t !== type);
    }
    return currentTypes;
  }

  // --- Ações do formulário
  const submitForm = (formData: EntityFormData, isEdit: boolean = false) => {
    if (formData.types.length === 0) {
      alert('Selecione pelo menos um tipo (Cliente e/ou Fornecedor)')
      return
    }

    const routeInfo = isEdit
      ? route.update( {id: entity?.id ?? 0})
      : route.store()

    const payload = { ...formData } as any

    if (isEdit) {
      router.post(routeInfo.url, payload, {
        preserveScroll: true,
        onSuccess: () => {},
        onError: (errors: any) => {
          console.error('Erros no formulário:', errors)
        },
        headers: { 'X-HTTP-Method-Override': 'PUT' },
      })
    } else {
      router.post(routeInfo.url, payload, {
        preserveScroll: true,
        onSuccess: () => {},
        onError: (errors: any) => {
          console.error('Erros no formulário:', errors)
        },
      })
    }
  }

  const goBack = () => {
    router.get(route.index().url, { type })
  }

  return {
    isClient,
    isSupplier,
    toggleType,
    submitForm,
    goBack,
  }
}
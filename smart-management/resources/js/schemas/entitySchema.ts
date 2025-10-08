import { z } from 'zod'

export const entitySchema = z.object({
  tax_number: z.string()
    .min(1, 'NIF é obrigatório')
    .regex(/^PT\d{9}$/, 'NIF deve ter formato PT123456789'),
  
  name: z.string()
    .min(1, 'Nome é obrigatório')
    .min(2, 'Nome deve ter pelo menos 2 caracteres'),
  
  types: z.array(z.enum(['client', 'supplier']))
    .min(1, 'Selecione pelo menos um tipo'),
  
  address: z.string()
    .min(1, 'Morada é obrigatória'),
  
  postal_code: z.string()
    .min(1, 'Código postal é obrigatório')
    .regex(/^\d{4}-\d{3}$/, 'Código postal deve ter formato 1234-567'),
  
  city: z.string()
    .min(1, 'Localidade é obrigatória'),
  
  country_id: z.string()
    .min(1, 'País é obrigatório'),
  
  phone: z.string().optional(),
  
  mobile: z.string().optional(),
  
  website: z.string()
    .url('Website deve ser uma URL válida')
    .optional()
    .or(z.literal('')),
  
  email: z.string()
    .email('Email deve ser válido')
    .optional()
    .or(z.literal('')),
  
  gdpr_consent: z.boolean()
    .refine(val => val === true, 'Deve aceitar o consentimento RGPD'),
  
  observations: z.string().optional(),
  
  status: z.enum(['active', 'inactive']).default('active'),
})

export type EntityFormData = z.infer<typeof entitySchema>

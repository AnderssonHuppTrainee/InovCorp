# 🎨 Toast (Sonner) - Configuração e Uso

**Data:** 13 de Outubro de 2025  
**Componente:** Sonner (vue-sonner)  
**Status:** ✅ Configurado e Pronto para Uso

---

## 📦 O que foi instalado?

### Componente Sonner
- ✅ `resources/js/components/ui/sonner/Sonner.vue`
- ✅ `resources/js/components/ui/sonner/index.ts`
- ✅ Package `vue-sonner` instalado

**Por que Sonner?**
- Toast do Shadcn Vue está **deprecado**
- Sonner é a **alternativa oficial recomendada**
- Mais leve, moderno e performático
- Suporte completo a dark mode

---

## ⚙️ Configuração

### 1. Adicionado no Layout Principal

**Arquivo:** `resources/js/layouts/app/AppSidebarLayout.vue`

```vue
<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
// ...
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
        <Toaster /> <!-- 👈 Adicionado aqui -->
    </AppShell>
</template>
```

### 2. Criado Composable para Facilitar Uso

**Arquivo:** `resources/js/composables/useToast.ts`

Funções disponíveis:
- ✅ `showSuccess(message, description?)` - Notificação de sucesso
- ✅ `showError(message, description?)` - Notificação de erro
- ✅ `showInfo(message, description?)` - Notificação informativa
- ✅ `showWarning(message, description?)` - Notificação de aviso
- ✅ `showLoading(message, description?)` - Notificação de loading
- ✅ `showPromise(promise, messages)` - Notificação com promessa
- ✅ `dismiss(id?)` - Dismissar notificação específica
- ✅ `dismissAll()` - Dismissar todas

---

## 🚀 Como Usar

### 1. Importar o Composable

```typescript
import { useToast } from '@/composables/useToast'

const { showSuccess, showError, showInfo, showWarning } = useToast()
```

### 2. Exemplos de Uso

#### ✅ Sucesso
```typescript
// Simples
showSuccess('Cliente criado com sucesso!')

// Com descrição
showSuccess(
    'Cliente criado com sucesso!',
    'O cliente foi adicionado à base de dados.'
)
```

#### ❌ Erro
```typescript
// Simples
showError('Erro ao eliminar fornecedor')

// Com descrição
showError(
    'Erro ao eliminar fornecedor',
    'Este fornecedor tem encomendas associadas.'
)
```

#### ℹ️ Informação
```typescript
showInfo('A processar encomenda...')

showInfo(
    'Ficheiro enviado',
    'O ficheiro está a ser processado em segundo plano.'
)
```

#### ⚠️ Aviso
```typescript
showWarning('Atenção: Esta ação não pode ser revertida')

showWarning(
    'Stock baixo',
    'O artigo "Cadeira Escritório" tem apenas 3 unidades.'
)
```

#### ⏳ Loading
```typescript
// Mostrar loading
const loadingId = showLoading('A carregar dados...')

// Depois dismissar
dismiss(loadingId)
```

#### 🔄 Com Promessa (Recomendado para operações assíncronas)
```typescript
const promise = axios.post('/api/orders', data)

showPromise(promise, {
    loading: 'A criar encomenda...',
    success: 'Encomenda criada com sucesso!',
    error: 'Erro ao criar encomenda'
})

// Com funções dinâmicas
showPromise(promise, {
    loading: 'A processar...',
    success: (data) => `Encomenda #${data.number} criada!`,
    error: (err) => `Erro: ${err.message}`
})
```

---

## 💡 Exemplos de Integração em CRUD

### Exemplo 1: Create Entity

```typescript
// resources/js/pages/entities/Create.vue
import { useToast } from '@/composables/useToast'

const { showSuccess, showError } = useToast()

const submitForm = form.handleSubmit(async (values) => {
    try {
        await router.post(route('entities.store', { type: props.type }), values, {
            onSuccess: () => {
                showSuccess(
                    `${type === 'client' ? 'Cliente' : 'Fornecedor'} criado com sucesso!`,
                    'Pode agora adicionar contactos e encomendas.'
                )
            },
            onError: (errors) => {
                showError(
                    `Erro ao criar ${type === 'client' ? 'cliente' : 'fornecedor'}`,
                    Object.values(errors).join(', ')
                )
            }
        })
    } catch (error) {
        showError('Erro inesperado', error.message)
    }
})
```

### Exemplo 2: Delete Order

```typescript
// resources/js/pages/orders/Index.vue
import { useToast } from '@/composables/useToast'

const { showSuccess, showError, showWarning } = useToast()

const handleDelete = (id: number) => {
    showWarning(
        'Tem certeza?',
        'Esta ação eliminará a encomenda permanentemente.'
    )
    
    // Usar confirmação do browser
    if (confirm('Confirma a eliminação?')) {
        router.delete(route('orders.destroy', id), {
            onSuccess: () => {
                showSuccess('Encomenda eliminada com sucesso!')
            },
            onError: () => {
                showError('Erro ao eliminar encomenda')
            }
        })
    }
}
```

### Exemplo 3: Update com Promise

```typescript
// resources/js/pages/proposals/Edit.vue
import { useToast } from '@/composables/useToast'

const { showPromise } = useToast()

const submitForm = form.handleSubmit(async (values) => {
    const updatePromise = new Promise((resolve, reject) => {
        router.put(route('proposals.update', props.proposal.id), values, {
            onSuccess: () => resolve(values),
            onError: () => reject(new Error('Falha ao atualizar'))
        })
    })

    showPromise(updatePromise, {
        loading: 'A atualizar proposta...',
        success: 'Proposta atualizada com sucesso!',
        error: 'Erro ao atualizar proposta'
    })
})
```

### Exemplo 4: Validação VIES

```typescript
// resources/js/composables/useViesValidation.ts
import { useToast } from '@/composables/useToast'

const { showSuccess, showError, showLoading, dismiss } = useToast()

const validateVat = async () => {
    const loadingId = showLoading('A validar NIF no VIES...')
    
    try {
        const response = await axios.post('/api/vies-check', {
            vat_number: taxNumber
        })
        
        dismiss(loadingId)
        
        if (response.data.valid) {
            showSuccess(
                'NIF válido!',
                `Empresa: ${response.data.name}`
            )
        } else {
            showError('NIF inválido', 'Verifique o número inserido.')
        }
    } catch (error) {
        dismiss(loadingId)
        showError('Erro ao validar NIF', 'Serviço VIES indisponível.')
    }
}
```

---

## 🎨 Personalização

### Duração das Notificações

Valores padrão configurados:
```typescript
showSuccess() // 4 segundos
showError()   // 5 segundos (mais tempo para ler)
showInfo()    // 4 segundos
showWarning() // 4 segundos
showLoading() // Infinito (até dismissar)
```

Para customizar:
```typescript
import { toast } from 'vue-sonner'

toast.success('Mensagem', {
    duration: 10000, // 10 segundos
})
```

### Posição

Padrão: `top-right`

Para mudar globalmente:
```vue
<!-- AppSidebarLayout.vue -->
<Toaster position="bottom-right" />
```

Posições disponíveis:
- `top-left`
- `top-center`
- `top-right` (padrão)
- `bottom-left`
- `bottom-center`
- `bottom-right`

### Rich Content

```typescript
toast('Encomenda #000026', {
    description: 'Cliente: IKEA Portugal',
    action: {
        label: 'Ver',
        onClick: () => router.visit('/orders/26')
    },
})
```

---

## 🎯 Casos de Uso Comuns

### 1. Feedback de Formulários
```typescript
// Após criar/editar
showSuccess('Dados salvos com sucesso!')

// Erro de validação
showError('Preencha todos os campos obrigatórios')
```

### 2. Operações Assíncronas
```typescript
const promise = fetchData()

showPromise(promise, {
    loading: 'A carregar...',
    success: 'Dados carregados!',
    error: 'Falha ao carregar'
})
```

### 3. Confirmações
```typescript
// Antes de eliminar
showWarning('Tem certeza que deseja eliminar?')

// Após eliminar
showSuccess('Item eliminado com sucesso!')
```

### 4. Uploads
```typescript
showLoading('A enviar ficheiro...')
// ...upload...
dismiss() // ou showSuccess()
```

### 5. Validações
```typescript
// NIF válido
showSuccess('NIF válido!', 'Empresa: IKEA Portugal')

// NIF inválido
showError('NIF inválido', 'Verifique o número inserido')
```

---

## 📊 Substituindo Flash Messages

### ANTES (Laravel Flash Messages)
```php
// Backend
return redirect()->with('success', 'Cliente criado!');
```

```vue
<!-- Frontend - Não havia feedback visual! -->
```

### DEPOIS (Com Sonner)
```typescript
// Frontend
router.post(route, data, {
    onSuccess: () => {
        showSuccess('Cliente criado com sucesso!')
    }
})
```

---

## 🎨 Tipos de Toast Disponíveis

| Função | Cor | Ícone | Uso |
|--------|-----|-------|-----|
| `showSuccess()` | Verde ✅ | Checkmark | Ações bem-sucedidas |
| `showError()` | Vermelho ❌ | X | Erros e falhas |
| `showInfo()` | Azul ℹ️ | Info | Informações gerais |
| `showWarning()` | Laranja ⚠️ | Warning | Avisos importantes |
| `showLoading()` | Cinza ⏳ | Spinner | Operações em andamento |
| `showPromise()` | Dinâmico 🔄 | Dinâmico | Operações assíncronas |

---

## ✅ Checklist de Integração

### Já Configurado
- [x] Sonner instalado via Shadcn CLI
- [x] Toaster adicionado ao AppSidebarLayout
- [x] Composable `useToast` criado
- [x] Documentação criada

### Próximos Passos (Opcional)
- [ ] Integrar em todas as páginas Create/Edit
- [ ] Substituir `console.log` por toasts
- [ ] Adicionar toasts em validações VIES
- [ ] Adicionar toasts em operações de delete
- [ ] Adicionar toasts em uploads de arquivos

---

## 🧪 Teste Rápido

### No Browser Console:
```javascript
// Importar globalmente (apenas para teste)
import { toast } from 'vue-sonner'

// Testar
toast.success('Teste de sucesso!')
toast.error('Teste de erro!')
toast.info('Teste de info!')
toast.warning('Teste de aviso!')
```

### Em Qualquer Componente:
```vue
<script setup lang="ts">
import { useToast } from '@/composables/useToast'

const { showSuccess } = useToast()

// Testar ao clicar em botão
const handleTest = () => {
    showSuccess('Toast funcionando!', 'Sistema configurado corretamente.')
}
</script>

<template>
    <Button @click="handleTest">Testar Toast</Button>
</template>
```

---

## 💡 Boas Práticas

### ✅ FAZER:
```typescript
// Mensagens claras e acionáveis
showSuccess('Cliente criado com sucesso!')

// Fornecer contexto quando útil
showError('Erro ao eliminar fornecedor', 'Este fornecedor tem encomendas associadas.')

// Usar promises para operações assíncronas
showPromise(saveData(), {
    loading: 'A guardar...',
    success: 'Guardado!',
    error: 'Erro ao guardar'
})
```

### ❌ NÃO FAZER:
```typescript
// Mensagens genéricas demais
showError('Erro') // ❌ Não informa o usuário

// Toast para cada detalhe
showInfo('Campo preenchido') // ❌ Spam de notificações

// Não usar para validações de formulário
showError('Nome é obrigatório') // ❌ Usar FormMessage do vee-validate
```

---

## 🎨 Customização Avançada

### Toast com Ação
```typescript
import { toast } from 'vue-sonner'

toast('Encomenda criada!', {
    description: 'Encomenda #000026',
    action: {
        label: 'Ver',
        onClick: () => router.visit('/orders/26')
    },
})
```

### Toast com Dismissal Manual
```typescript
const toastId = toast('Operação em progresso...', {
    duration: Infinity, // Não fecha automaticamente
})

// Depois de concluir
toast.dismiss(toastId)
toast.success('Concluído!')
```

### Toast Customizado
```typescript
toast('Mensagem customizada', {
    duration: 10000,
    position: 'bottom-center',
    style: {
        background: 'linear-gradient(to right, #00b09b, #96c93d)',
    },
    className: 'custom-toast',
})
```

---

## 📊 Quando Usar Toast vs Form Validation

### Use Toast Para:
✅ Feedback de ações (criar, editar, eliminar)  
✅ Operações assíncronas (upload, API calls)  
✅ Mensagens de sistema (conexão perdida, etc.)  
✅ Confirmações de sucesso  
✅ Erros globais da aplicação

### Use Form Validation Para:
✅ Erros de validação de campos  
✅ Campos obrigatórios  
✅ Formatos inválidos  
✅ Feedback inline no formulário

**Regra geral:**
- **Toast** = Feedback de **ações/operações**
- **FormMessage** = Feedback de **validação de campos**

---

## 🔧 Integração com Inertia.js

### Exemplo Completo

```typescript
import { router } from '@inertiajs/vue3'
import { useToast } from '@/composables/useToast'

const { showSuccess, showError, showLoading, dismiss } = useToast()

const handleSubmit = async (values) => {
    const loadingId = showLoading('A criar cliente...')
    
    router.post(route('entities.store'), values, {
        onSuccess: () => {
            dismiss(loadingId)
            showSuccess(
                'Cliente criado com sucesso!',
                'Pode agora adicionar contactos.'
            )
        },
        onError: (errors) => {
            dismiss(loadingId)
            showError(
                'Erro ao criar cliente',
                Object.values(errors).join(', ')
            )
        }
    })
}
```

---

## 🌈 Visual do Toast

### Light Mode
```
┌─────────────────────────────────────┐
│ ✅ Cliente criado com sucesso!      │
│ O cliente foi adicionado.          │
└─────────────────────────────────────┘
```

### Dark Mode
```
┌─────────────────────────────────────┐
│ ✅ Cliente criado com sucesso!      │
│ O cliente foi adicionado.          │
└─────────────────────────────────────┘
(Fundo escuro, texto claro)
```

---

## 📝 TODO: Integração em Páginas Existentes

### Prioridade Alta
- [ ] Integrar em entities/Create.vue e Edit.vue
- [ ] Integrar em orders/Create.vue e Edit.vue
- [ ] Integrar em proposals/Create.vue e Edit.vue
- [ ] Integrar em work-orders/Create.vue e Edit.vue
- [ ] Integrar em validação VIES (useViesValidation.ts)

### Prioridade Média
- [ ] Integrar em todas as páginas de settings
- [ ] Integrar em digital-archive uploads
- [ ] Integrar em financial transactions

### Prioridade Baixa
- [ ] Substituir console.log por toasts onde apropriado
- [ ] Adicionar toasts em erros de API
- [ ] Toasts em operações de sincronização

---

## 🎉 Resultado Final

```
╔════════════════════════════════════════════════════════╗
║         ✅ TOAST (SONNER) CONFIGURADO!               ║
╠════════════════════════════════════════════════════════╣
║                                                        ║
║  Componente:  Sonner (vue-sonner)                     ║
║  Status:      ✅ Instalado e Configurado              ║
║  Localização: AppSidebarLayout.vue                    ║
║                                                        ║
║  Funcionalidades:                                      ║
║    ✅ showSuccess()   - Notificações de sucesso       ║
║    ✅ showError()     - Notificações de erro          ║
║    ✅ showInfo()      - Notificações informativas     ║
║    ✅ showWarning()   - Notificações de aviso         ║
║    ✅ showLoading()   - Notificações de loading       ║
║    ✅ showPromise()   - Notificações com promessas    ║
║                                                        ║
║  Composable:  useToast()                              ║
║  Docs:        TOAST_SETUP.md (500+ linhas)            ║
║                                                        ║
║  Pronto para usar em TODA a aplicação! 🎊            ║
║                                                        ║
╚════════════════════════════════════════════════════════╝
```

---

**Status:** ✅ **PRONTO PARA USO!**  
**Documentação:** 500+ linhas com exemplos completos  
**Próximo Passo:** Integrar nas páginas CRUD

🚀 **Use `showSuccess()`, `showError()`, etc. em qualquer componente!**


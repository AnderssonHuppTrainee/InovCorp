# 🐛 BUG FIX: Naming Convention - camelCase vs snake_case

**Data:** 13 de Outubro de 2025  
**Severidade:** 🔴 **ALTA** (Cannot read properties of undefined)  
**Status:** ✅ **CORRIGIDO**

---

## 🔍 DESCRIÇÃO DO PROBLEMA

Ao acessar páginas **Edit** de Calendar Actions (e possivelmente outros):

**Erro:**

```
Uncaught TypeError: Cannot read properties of undefined (reading 'toString')
```

### Causa Raiz

**Naming convention inconsistente:**

- ❌ Código usava: `calendarAction` (camelCase)
- ✅ Backend envia: `calendar_action` (snake_case)

---

## 📍 EXEMPLO DO BUG

### ❌ Código COM Bug

```vue
<!-- calendar-actions/Show.vue ou Edit.vue -->
<script setup>
interface Props {
    calendarAction: {  // ❌ camelCase
        id: number;
        name: string;
        is_active: boolean;
    };
}

const props = defineProps<Props>();

// Tentando acessar:
const name = props.calendarAction.name;  // ❌ undefined!
</script>
```

### Backend (Laravel)

```php
// CalendarActionController.php
return Inertia::render('calendar-actions/Edit', [
    'calendar_action' => $calendarAction,  // ✅ snake_case (padrão Laravel)
]);
```

### Resultado

```javascript
props.calendarAction; // undefined ❌
props.calendar_action; // { id: 1, name: "..." } ✅
```

Ao tentar acessar `props.calendarAction.name.toString()`, JavaScript tenta:

```
undefined.name → undefined
undefined.toString() → ERROR: Cannot read properties of undefined
```

---

## ✅ SOLUÇÃO

### Usar snake_case (Padrão Laravel)

```vue
<!-- ✅ CORRETO -->
<script setup>
interface Props {
    calendar_action: {  // ✅ snake_case
        id: number;
        name: string;
        is_active: boolean;
    };
}

const props = defineProps<Props>();

// Agora funciona:
const name = props.calendar_action.name;  // ✅
</script>
```

---

## 📊 PADRÃO ESTABELECIDO

### Laravel → Inertia → Vue

**Regra:** Manter snake_case do backend até o frontend

```php
// Backend (Laravel)
return Inertia::render('view', [
    'calendar_action' => $model,      // ✅ snake_case
    'contact_role' => $role,          // ✅ snake_case
    'calendar_event_type' => $type,   // ✅ snake_case
]);
```

```vue
<!-- Frontend (Vue) -->
<script setup>
interface Props {
    calendar_action: any;      // ✅ snake_case
    contact_role: any;         // ✅ snake_case
    calendar_event_type: any;  // ✅ snake_case
}
</script>
```

### ❌ NÃO Fazer

```vue
<!-- ❌ ERRADO: converter para camelCase -->
<script setup>
interface Props {
    calendarAction: any;     // ❌ Backend envia snake_case!
    contactRole: any;        // ❌
    calendarEventType: any;  // ❌
}
</script>
```

---

## 🔬 ANÁLISE TÉCNICA

### Por que Laravel usa snake_case?

1. **Banco de dados:** Colunas são snake_case (`first_name`, `created_at`)
2. **Models:** Atributos são snake_case
3. **Eloquent:** Retorna snake_case por padrão
4. **API:** JSON snake_case é padrão

### Por que Vue aceita snake_case?

```vue
<script>
// Vue permite snake_case em props
const props = defineProps<{
    calendar_action: any  // ✅ Válido em Vue/TypeScript
}>()

// Acesso:
props.calendar_action.name  // ✅ Funciona perfeitamente
</script>
```

**Não é necessário converter para camelCase!**

---

## 🚨 ARQUIVOS QUE PODEM TER ESSE PROBLEMA

### Verificar Naming em:

- [ ] `calendar-actions/Edit.vue`
- [ ] `calendar-actions/Show.vue`
- [ ] `calendar-event-types/Edit.vue`
- [ ] `calendar-event-types/Show.vue`
- [ ] `contact-roles/Edit.vue`
- [ ] `contact-roles/Show.vue`
- [ ] `tax-rates/Edit.vue`
- [ ] `tax-rates/Show.vue`

**Verificar se Props usam:**

- ✅ `calendar_action` (correto)
- ❌ `calendarAction` (errado)

---

## 🔧 CORREÇÃO PADRÃO

### Buscar e Substituir

```typescript
// ANTES (errado)
interface Props {
    calendarAction: {
        // ...
    }
}

// DEPOIS (correto)
interface Props {
    calendar_action: {
        // ...
    }
}

// E atualizar todos os usos:
props.calendarAction → props.calendar_action
```

---

## 🎯 RESULTADO FINAL

### Status: ✅ CORRIGIDO (pelo usuário)

**Problema:**

- ❌ Props usavam camelCase
- ❌ Backend enviava snake_case
- ❌ Mismatch causava undefined
- ❌ Erro ao acessar propriedades

**Solução:**

- ✅ Props agora usam snake_case
- ✅ Match perfeito com backend
- ✅ Sem erros de undefined
- ✅ Tudo funciona

---

## 📚 LIÇÃO APRENDIDA

### Convenção de Nomenclatura

**Regra Universal:**

```
Backend (Laravel)  →  Inertia  →  Frontend (Vue)
    snake_case     →  snake_case  →  snake_case
```

**NÃO converter** para camelCase no frontend quando recebendo de Inertia!

---

## 🎓 PADRÃO PARA EQUIPE

### ✅ FAZER (Padrão Aprovado)

```php
// Controller
return Inertia::render('page', [
    'calendar_action' => $action,  // snake_case
]);
```

```vue
<!-- Vue -->
<script setup lang="ts">
interface Props {
    calendar_action: any; // snake_case (mesmo que backend)
}
const props = defineProps<Props>();

// Uso:
{
    {
        calendar_action.name;
    }
}
</script>
```

### ❌ NÃO FAZER

```vue
<script setup lang="ts">
interface Props {
    calendarAction: any; // ❌ camelCase (backend não envia assim!)
}
</script>
```

---

## 📊 IMPACTO

| Métrica               | Valor                  |
| --------------------- | ---------------------- |
| **Severidade**        | 🔴 ALTA                |
| **Arquivos afetados** | ~4-6 (estimado)        |
| **Tipo de erro**      | TypeError (undefined)  |
| **Correção**          | Rename props           |
| **Tempo**             | ~5 min por arquivo     |
| **Complexidade**      | Baixa (find & replace) |

---

## 🚀 RECOMENDAÇÕES

### 1. Verificar Todos os Edit/Show

Fazer busca global por camelCase em props de entidades multi-palavra:

```bash
# Procurar padrões camelCase em Props
grep -r "calendarAction\|contactRole\|calendarEvent\|supplierOrder" resources/js/pages/
```

### 2. Padronizar TypeScript Interfaces

Criar tipos globais com snake_case:

```typescript
// types/models.ts
export interface CalendarAction {
    id: number;
    name: string;
    is_active: boolean;
}

// Uso em Props:
interface Props {
    calendar_action: CalendarAction; // ✅
}
```

### 3. ESLint Rule (Opcional)

Criar rule para detectar mismatch:

```javascript
// .eslintrc
rules: {
    'vue/prop-name-casing': ['error', 'snake_case']  // Para props de backend
}
```

---

**🎉 BUG IDENTIFICADO E CORRIGIDO! 🎉**

_Correção: 13/10/2025 (pelo usuário)_  
_Causa: camelCase vs snake_case_  
_Lição: Manter naming do backend_

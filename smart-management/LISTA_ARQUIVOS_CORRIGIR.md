# 📝 LISTA DE ARQUIVOS PARA CORREÇÃO IMEDIATA

## 🔴 PRIORIDADE CRÍTICA - CORRIGIR HOJE

### Bug de Formatação Monetária

**Problema:** Código pode causar erro `toFixed is not a function` se valor for NaN/null

**Arquivos identificados com valores monetários:**

#### ✅ JÁ CORRIGIDO

- [x] `resources/js/pages/supplier-orders/columns.ts` ✅

#### ❌ PRECISA CORREÇÃO

1. **resources/js/pages/orders/columns.ts**
    - Linha ~73: `total_amount`
    - Status: ⚠️ USA Intl (correto, mas pode ter outros valores)
2. **resources/js/pages/proposals/columns.ts**
    - Linha ~70+: `total_amount`
    - Status: ⚠️ Verificar padrão usado

3. **resources/js/pages/financial/customer-invoices/columns.ts**
    - Linha ~70+: `total_amount`, `paid_amount`, `remaining_amount`
    - Status: ⚠️ Múltiplos valores monetários

4. **resources/js/pages/financial/supplier-invoices/columns.ts**
    - Linha ~70+: `total_amount`, `paid_amount`, `remaining_amount`
    - Status: ⚠️ Múltiplos valores monetários

5. **resources/js/pages/financial/bank-accounts/columns.ts**
    - Linha ~50+: `balance`, `initial_balance`
    - Status: ⚠️ Valores monetários

6. **resources/js/pages/settings/articles/columns.ts**
    - Linha ~60+: `price`, `cost`
    - Status: ⚠️ Preços de artigos

**Total:** 6 arquivos, ~15-20 correções

---

## 🟡 PRIORIDADE ALTA - CORRIGIR ESTA SEMANA

### Inconsistência de Checkboxes

**Arquivos usando Shadcn Checkbox (pode ter problemas):**

1. `resources/js/pages/settings/tax-rates/Create.vue` - linha 44
2. `resources/js/pages/settings/tax-rates/Edit.vue` - linha 44
3. `resources/js/pages/settings/countries/Create.vue` - linha 62
4. `resources/js/pages/settings/countries/Edit.vue` - linha 62
5. `resources/js/pages/settings/contact-roles/Create.vue` - linha ~40
6. `resources/js/pages/settings/contact-roles/Edit.vue` - linha ~40
7. `resources/js/pages/settings/calendar-actions/Create.vue` - linha ~40
8. `resources/js/pages/settings/calendar-actions/Edit.vue` - linha ~40
9. `resources/js/pages/settings/calendar-event-types/Create.vue` - linha ~40
10. `resources/js/pages/settings/calendar-event-types/Edit.vue` - linha ~40
11. `resources/js/pages/settings/articles/Create.vue` - linha ~50
12. `resources/js/pages/settings/articles/Edit.vue` - linha ~50

**Arquivos usando Native Input (funcionando corretamente):**

- ✅ `resources/js/pages/calendar/Index.vue`
- ✅ `resources/js/pages/entities/Create.vue`
- ✅ `resources/js/pages/entities/Edit.vue`

**Recomendação:** Migrar todos para input nativo

---

## 📋 CHECKLIST DE CORREÇÃO

### Formatação Monetária (30 minutos)

Para cada arquivo em `**/columns.ts`:

```typescript
// ❌ PROCURAR POR:
const amount = parseFloat(row.getValue('xxx'));
const amount = parseFloat(row.original.xxx as any);
amount.toFixed(2);

// ✅ SUBSTITUIR POR:
const value = row.getValue('xxx'); // ou row.original.xxx
const amount = typeof value === 'number' ? value : parseFloat(value ?? '0');
const validAmount = isNaN(amount) ? 0 : amount;
return h('div', { class: 'font-medium' }, `€${validAmount.toFixed(2)}`);

// OU MELHOR: Usar Intl.NumberFormat
const amount = parseFloat(row.getValue('xxx') ?? '0');
const formatted = new Intl.NumberFormat('pt-PT', {
    style: 'currency',
    currency: 'EUR',
}).format(isNaN(amount) ? 0 : amount);
return h('div', { class: 'font-medium' }, formatted);
```

**Checklist:**

- [ ] orders/columns.ts
- [ ] proposals/columns.ts
- [ ] customer-invoices/columns.ts
- [ ] supplier-invoices/columns.ts
- [ ] bank-accounts/columns.ts
- [ ] articles/columns.ts
- [ ] Verificar outros columns.ts para valores numéricos

---

### Checkboxes (2 horas)

Para cada arquivo `settings/*/Create.vue` e `Edit.vue`:

```vue
<!-- ❌ REMOVER: -->
<FormField v-slot="{ value, handleChange }" name="is_active">
    <FormItem class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
        <FormControl>
            <Checkbox :checked="value" @update:checked="(checked: boolean) => handleChange(checked)" />
        </FormControl>
        <div class="space-y-1 leading-none">
            <FormLabel>XXX Ativo</FormLabel>
            <FormDescription>Descrição...</FormDescription>
        </div>
    </FormItem>
</FormField>

<!-- ✅ SUBSTITUIR POR: -->
<div class="flex items-center space-x-3 rounded-lg border p-4">
    <input
        type="checkbox"
        id="is_active"
        :checked="form.values.is_active"
        @change="(e) => form.setFieldValue('is_active', e.target.checked)"
        class="peer h-4 w-4 shrink-0 cursor-pointer rounded-sm border border-primary ring-offset-background focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
    />
    <div class="grid gap-1.5 leading-none">
        <label for="is_active" class="cursor-pointer text-sm font-medium">
            XXX Ativo
        </label>
        <p class="text-sm text-muted-foreground">
            Descrição...
        </p>
    </div>
</div>
```

**Checklist:**

- [ ] tax-rates/Create.vue
- [ ] tax-rates/Edit.vue
- [ ] countries/Create.vue
- [ ] countries/Edit.vue
- [ ] contact-roles/Create.vue
- [ ] contact-roles/Edit.vue
- [ ] calendar-actions/Create.vue
- [ ] calendar-actions/Edit.vue
- [ ] calendar-event-types/Create.vue
- [ ] calendar-event-types/Edit.vue
- [ ] articles/Create.vue
- [ ] articles/Edit.vue

---

## 🔍 SCRIPT DE VALIDAÇÃO

Execute após correções para validar:

```bash
#!/bin/bash
# validate-corrections.sh

echo "🔍 Verificando formatação monetária..."
# Não deve encontrar nenhum
grep -r "toFixed" resources/js/pages/*/columns.ts | grep -v "validAmount\|isNaN" && echo "⚠️ Encontrados patterns não seguros!" || echo "✅ OK"

echo "🔍 Verificando checkboxes Shadcn..."
# Deve listar só os já migrados
grep -r "Checkbox :checked" resources/js/pages | grep -v "entities\|calendar" && echo "⚠️ Ainda há Shadcn Checkboxes" || echo "✅ OK"

echo "🔍 Verificando uso de 'as any'..."
# Deve minimizar
TOTAL=$(grep -r "as any" resources/js/pages --include="*.ts" --include="*.vue" | wc -l)
echo "Total 'as any': $TOTAL (Meta: <10)"

echo "🔍 Build sem erros..."
npm run build 2>&1 | grep -i "error" && echo "❌ Erros no build" || echo "✅ Build OK"
```

---

## 📊 PROGRESSO TRACKING

### Template de Commit

Após cada correção:

```
fix: corrigir formatação monetária em [módulo]/columns.ts

- Adicionar validação isNaN antes de toFixed
- Garantir fallback para 0 em valores null/undefined
- Usar Intl.NumberFormat para consistência

Refs: ANALISE_PROJETO_COMPLETA.md
```

### Tabela de Progresso

| Arquivo                      | Bug Money | Checkbox | Status  | Responsável | Data |
| ---------------------------- | --------- | -------- | ------- | ----------- | ---- |
| orders/columns.ts            | ⚠️        | -        | 🔴 TODO | -           | -    |
| proposals/columns.ts         | ⚠️        | -        | 🔴 TODO | -           | -    |
| customer-invoices/columns.ts | ⚠️        | -        | 🔴 TODO | -           | -    |
| supplier-invoices/columns.ts | ⚠️        | -        | 🔴 TODO | -           | -    |
| bank-accounts/columns.ts     | ⚠️        | -        | 🔴 TODO | -           | -    |
| articles/columns.ts          | ⚠️        | -        | 🔴 TODO | -           | -    |
| tax-rates/Create.vue         | -         | ⚠️       | 🔴 TODO | -           | -    |
| tax-rates/Edit.vue           | -         | ⚠️       | 🔴 TODO | -           | -    |
| countries/Create.vue         | -         | ⚠️       | 🔴 TODO | -           | -    |
| countries/Edit.vue           | -         | ⚠️       | 🔴 TODO | -           | -    |
| ...                          | ...       | ...      | ...     | ...         | ...  |

**Legenda:**

- 🔴 TODO - Não iniciado
- 🟡 IN PROGRESS - Em progresso
- ✅ DONE - Completo
- ⚠️ - Precisa correção
- ✓ - OK

---

## 🎯 META PARA ESTA SEMANA

**Objetivo:** Eliminar todos os bugs críticos

**Checklist:**

- [ ] Corrigir 6 arquivos de formatação monetária (30 min)
- [ ] Migrar 12 checkboxes para native input (2h)
- [ ] Validar com script automatizado (5 min)
- [ ] Build sem erros (verificação)
- [ ] Commit e push

**Total:** ~2h 35min  
**Quando:** Hoje/Amanhã  
**Blocker:** Nenhum

---

_Lista de tarefas acionáveis_  
_Atualizar após cada correção_

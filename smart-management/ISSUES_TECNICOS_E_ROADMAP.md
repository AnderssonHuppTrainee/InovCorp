# 🐛 ISSUES TÉCNICOS E ROADMAP DE CORREÇÃO

## 🚨 PROBLEMAS TÉCNICOS IDENTIFICADOS

### CRÍTICOS (Corrigir Imediatamente)

#### 1. ❌ Bug em 14+ arquivos columns.ts - Formatação Monetária

**Status:** ⚠️ Corrigido apenas em `supplier-orders/columns.ts`

**Arquivos afetados:**

- `proposals/columns.ts`
- `customer-invoices/columns.ts`
- `supplier-invoices/columns.ts`
- `bank-accounts/columns.ts`
- `work-orders/columns.ts`
- ... (10+ arquivos)

**Código problemático:**

```typescript
const amount = parseFloat(row.original.total_amount as any) || 0;
return h('div', {}, `€${amount.toFixed(2)}`);
// ⚠️ Se parseFloat retornar NaN, .toFixed() causa crash
```

**Solução imediata:**

```typescript
const value = row.original.total_amount;
const amount = typeof value === 'number' ? value : parseFloat(value ?? '0');
const validAmount = isNaN(amount) ? 0 : amount;
return h('div', {}, `€${validAmount.toFixed(2)}`);
```

**Prioridade:** 🔴 CRÍTICA  
**Estimativa:** 30 minutos  
**Arquivos:** 14

---

#### 2. ❌ Inconsistência de Checkboxes (Shadcn vs Native)

**Status:** ⚠️ Mistura de abordagens

**Problema:**

- Shadcn Checkbox não emite `@update:checked` em alguns casos
- Solução atual: input nativo em `calendar/Index.vue` e `entities/Create.vue`
- Outros ainda usam Shadcn (podem ter bugs futuros)

**Arquivos com Shadcn Checkbox:**

- `settings/*/Create.vue` (7 arquivos)
- `settings/*/Edit.vue` (7 arquivos)

**Solução:**

1. **Opção A:** Padronizar tudo em input nativo (mais confiável)
2. **Opção B:** Corrigir Shadcn Checkbox para sempre funcionar
3. **Opção C:** Criar wrapper que decide qual usar

**Recomendação:** Opção A (input nativo)

**Prioridade:** 🟡 ALTA  
**Estimativa:** 2 horas  
**Arquivos:** 14+

---

### IMPORTANTES (Corrigir Esta Sprint)

#### 3. ⚠️ Validação de entity_id opcional inconsistente

**Problema:** Algumas páginas permitem `entity_id` vazio, outras não

**Solução:**

```typescript
// Padronizar em TODOS os schemas que têm entity_id opcional:
entity_id: z.string()
    .transform((val) => val || undefined)
    .optional();
```

**Arquivos:** `contacts`, `calendar`, outros

**Prioridade:** 🟡 MÉDIA  
**Estimativa:** 1 hora

---

#### 4. ⚠️ event_time pode vir como HH:MM ou HH:MM:SS

**Status:** ✅ Corrigido em `calendar/Index.vue`  
**Problema:** Outros lugares podem ter o mesmo issue

**Verificar:**

- Todos os usos de `event_time`
- Garantir normalização para HH:MM

**Prioridade:** 🟡 MÉDIA  
**Estimativa:** 1 hora

---

#### 5. ⚠️ TypeScript warnings em vários arquivos

**Problemas encontrados:**

- `as any` usado extensivamente
- Tipos implícitos
- Conversões não seguras

**Solução:** Criar tipos adequados em `types/models.d.ts`

**Prioridade:** 🟡 MÉDIA  
**Estimativa:** 4 horas

---

### DESEJÁVEIS (Backlog)

#### 6. 💡 Falta de tratamento de erros global

**Problema:** Cada página trata erros de forma diferente

**Solução:** Toast notifications centralizadas

```typescript
// composables/ui/useToast.ts
export function useToast() {
  const success = (message: string) => { ... }
  const error = (message: string) => { ... }
  const warning = (message: string) => { ... }
  return { success, error, warning }
}
```

**Prioridade:** 🟢 BAIXA  
**Estimativa:** 3 horas

---

#### 7. 💡 Falta de loading states consistentes

**Problema:** isSubmitting manual em cada componente

**Solução:** Integrar no useCrudForm

**Prioridade:** 🟢 BAIXA  
**Estimativa:** Incluído no useCrudForm

---

#### 8. 💡 Sem testes unitários

**Problema:** 0 testes para composables e componentes

**Solução:** Vitest + Testing Library

**Prioridade:** 🟢 BAIXA (mas importante longo prazo)  
**Estimativa:** 20 horas (setup + testes principais)

---

## 📅 ROADMAP DE IMPLEMENTAÇÃO

### 🎯 SPRINT 1 (Semana 11-15 Out) - Fundação

#### Dia 1-2: Correções Críticas

- [x] Corrigir formatação monetária em supplier-orders ✅
- [ ] Corrigir formatação monetária em TODOS os columns.ts (14 arquivos)
- [ ] Padronizar checkboxes

#### Dia 3-4: Composables Base

- [ ] Criar `useMoneyFormatter`
- [ ] Criar `useDateFormatter`
- [ ] Testes básicos

#### Dia 5: Aplicar Formatters

- [ ] Refatorar todos os `columns.ts` para usar os novos formatters
- [ ] Validar funcionamento

**Entregáveis:**

- ✅ 0 bugs de formatação
- ✅ 2 composables prontos
- ✅ ~200 linhas reduzidas

---

### 🎯 SPRINT 2 (Semana 18-22 Out) - Componentização

#### Dia 1-2: Componentes Wrapper

- [ ] Criar `FormWrapper.vue`
- [ ] Criar `IndexWrapper.vue`
- [ ] Criar `ShowWrapper.vue`

#### Dia 3: Componentes Helper

- [ ] Criar `CheckboxField.vue`
- [ ] Criar `RelationSelectField.vue`
- [ ] Criar `MoneyDisplay.vue`

#### Dia 4-5: Refatoração Piloto

- [ ] Migrar `settings/tax-rates` completo (4 arquivos)
- [ ] Migrar `settings/countries` completo (4 arquivos)
- [ ] Validar e ajustar

**Entregáveis:**

- ✅ 5 componentes wrapper
- ✅ 2 módulos migrados
- ✅ ~400 linhas reduzidas

---

### 🎯 SPRINT 3 (Semana 25-29 Out) - Composable Avançado

#### Dia 1-3: useCrudForm

- [ ] Implementar `useCrudForm` completo
- [ ] Documentação e exemplos
- [ ] Testes

#### Dia 4-5: Aplicar em Settings

- [ ] Migrar todos os 7 módulos de settings
- [ ] Validar funcionamento

**Entregáveis:**

- ✅ useCrudForm production-ready
- ✅ 7 módulos migrados
- ✅ ~800 linhas reduzidas

---

### 🎯 SPRINT 4 (Semana 1-5 Nov) - Escalar

#### Semana completa: Migração em Massa

- [ ] Access Management (users, roles)
- [ ] Financial (bank-accounts, invoices)
- [ ] Core (entities, contacts)
- [ ] Business (orders, proposals, work-orders)

**Entregáveis:**

- ✅ 80% do projeto refatorado
- ✅ ~1.500 linhas reduzidas

---

### 🎯 SPRINT 5 (Semana 8-12 Nov) - Polimento

#### Dia 1-2: Calendário

- [ ] Extrair `useCalendarEvents`
- [ ] Refatorar `calendar/Index.vue`

#### Dia 3-4: Review e Ajustes

- [ ] Code review de todas as mudanças
- [ ] Corrigir inconsistências
- [ ] Performance testing

#### Dia 5: Documentação

- [ ] Atualizar README
- [ ] Guia de desenvolvimento
- [ ] Catálogo de componentes

**Entregáveis:**

- ✅ 100% migrado
- ✅ Documentação completa
- ✅ Zero regressões

---

## 🔧 FERRAMENTAS E HELPERS IMEDIATOS

### Script de Busca de Duplicação

```bash
# Encontrar código duplicado
npx jscpd resources/js --min-lines 10 --min-tokens 50

# Encontrar uso de 'as any'
grep -r "as any" resources/js --include="*.ts" --include="*.vue"

# Encontrar toFixed sem validação
grep -r "toFixed" resources/js --include="*.ts" -B 2
```

### Checklist de Code Review

```markdown
## Novo Componente/Composable - Checklist

- [ ] Nome segue padrão (use\* para composables)
- [ ] TypeScript sem erros
- [ ] Props/parâmetros tipados
- [ ] Tratamento de edge cases (null, undefined, NaN)
- [ ] Documentação inline (JSDoc)
- [ ] Exemplo de uso em comentário
- [ ] Testado em pelo menos 2 contextos diferentes
```

---

## 📈 MÉTRICAS DE ACOMPANHAMENTO

### KPIs Sugeridos

| Métrica                 | Meta Sprint 1  | Meta Sprint 3 | Meta Sprint 5 |
| ----------------------- | -------------- | ------------- | ------------- |
| **Código duplicado**    | < 1.200 linhas | < 600 linhas  | < 200 linhas  |
| **Bugs de formatação**  | 0              | 0             | 0             |
| **Tempo novo CRUD**     | 3h             | 2h            | 1h            |
| **Cobertura de testes** | 0%             | 20%           | 50%           |
| **TypeScript errors**   | 5              | 2             | 0             |

---

## 🎓 BENEFÍCIOS EDUCACIONAIS

### Para a Equipe

1. **Padrões claros** - Novos desenvolvedores aprendem rápido
2. **Menos decisões** - Caminho certo já está definido
3. **Foco no negócio** - Menos boilerplate, mais features
4. **Qualidade** - Bugs reduzidos automaticamente

### Para o Projeto

1. **Manutenibilidade** - Mudanças globais em 1 lugar
2. **Escalabilidade** - Adicionar CRUD leva minutos
3. **Consistência** - UX uniforme
4. **Performance** - Código menor = bundle menor

---

## 🔄 RETROCOMPATIBILIDADE

### Estratégia de Migração

**Não quebrar nada:**

1. Criar novos componentes/composables
2. Manter código antigo funcionando
3. Migrar 1 página por vez
4. Validar cada migração
5. Só remover código antigo quando 100% migrado

**Feature Flags (Opcional):**

```typescript
// config/features.ts
export const FEATURES = {
    USE_NEW_FORM_WRAPPER: true, // Ativar gradualmente
    USE_NEW_FORMATTERS: true,
};
```

---

## 💡 INOVAÇÕES EXTRAS

### 1. Auto-generate CRUD Pages

**Futuro (Sprint 6+):**

```bash
# CLI para gerar CRUD completo
npm run generate:crud -- --name=Product --schema=productSchema

# Gera automaticamente:
# - pages/products/Index.vue
# - pages/products/Create.vue
# - pages/products/Edit.vue
# - pages/products/Show.vue
# - pages/products/columns.ts
# - schemas/productSchema.ts (template)
```

**Tempo economizado:** 4h → 5 minutos

---

### 2. Component Preview/Storybook

**Futuro (Sprint 7+):**

Criar preview de todos os componentes common para facilitar desenvolvimento:

```
npm run storybook
```

---

### 3. Shared Component Library

**Muito Futuro:**

Se houver múltiplos projetos Laravel+Vue:

```
@inovcorp/smart-components
  ├── FormWrapper
  ├── IndexWrapper
  ├── useCrudForm
  └── useMoneyFormatter
```

---

## 📊 CHECKLIST FINAL DE VALIDAÇÃO

Após completar todas as sprints:

### Funcional

- [ ] Todos os CRUDs funcionando (17 módulos)
- [ ] Filtros funcionando (15+ páginas)
- [ ] Formatação de dinheiro correta (15+ tabelas)
- [ ] Datas formatadas corretamente (15+ tabelas)
- [ ] Checkboxes funcionando (15+ formulários)
- [ ] Calendário totalmente funcional
- [ ] Zero erros no console

### Técnico

- [ ] TypeScript 0 errors
- [ ] ESLint 0 warnings
- [ ] Build sem warnings
- [ ] Bundle size não aumentou
- [ ] Performance equivalente ou melhor

### Qualidade

- [ ] Code review aprovado
- [ ] Testes principais passando
- [ ] Documentação atualizada
- [ ] Sem código comentado/obsoleto
- [ ] Git history limpo

### UX

- [ ] Navegação consistente
- [ ] Loading states claros
- [ ] Mensagens de erro úteis
- [ ] Responsivo em mobile
- [ ] Acessibilidade básica

---

## 🎯 DECISÕES A TOMAR

### 1. Checkbox: Shadcn vs Native?

**Contexto:** Shadcn tem bugs, Native funciona sempre

**Opções:**

- A) Migrar tudo para Native (RECOMENDADO)
- B) Fix Shadcn e manter
- C) Wrapper que decide

**Decisão:** ****\_\_\_\_****  
**Justificativa:** ****\_\_\_\_****

---

### 2. Formatação Monetária: Intl vs Manual?

**Contexto:** Intl é correto, mas mais verboso

**Opções:**

- A) Intl.NumberFormat (correto, i18n-ready)
- B) toFixed(2) com € prefix (simples)
- C) Biblioteca externa (ex: currency.js)

**Decisão:** ****\_\_\_\_****  
**Justificativa:** ****\_\_\_\_****

---

### 3. Estratégia de Testes

**Contexto:** Atualmente 0% de cobertura

**Opções:**

- A) Testes só para composables/utils
- B) Testes para componentes também
- C) E2E com Playwright
- D) Tudo acima

**Decisão:** ****\_\_\_\_****  
**Justificativa:** ****\_\_\_\_****

---

## 🚀 COMANDO RÁPIDOS

### Desenvolvimento

```bash
# Encontrar código duplicado
npx jscpd resources/js

# Analisar bundle size
npm run build -- --analyze

# Verificar TypeScript
npx tsc --noEmit

# Formatar código
npm run format

# Lint
npm run lint
```

### Refatoração

```bash
# Buscar padrões a refatorar
grep -r "const isSubmitting = ref(false)" resources/js/pages
grep -r "router.post" resources/js/pages
grep -r "toFixed" resources/js/pages

# Encontrar arquivos sem usar composable
grep -L "useCrudForm" resources/js/pages/*/Create.vue
```

---

## 📚 REFERÊNCIAS

### Documentação Útil

- [Vue 3 Composition API](https://vuejs.org/guide/reusability/composables.html)
- [VeeValidate](https://vee-validate.logaretm.com/v4/)
- [Inertia.js](https://inertiajs.com/)
- [Shadcn Vue](https://www.shadcn-vue.com/)

### Exemplos de Projetos Similares

- [Laravel Breeze Inertia Vue](https://github.com/laravel/breeze)
- [Ping CRM](https://github.com/inertiajs/pingcrm)

---

## ✅ CONCLUSÃO

### Estado Atual

- ✅ Aplicação funcional e estável
- ⚠️ Código duplicado alto (~15%)
- ⚠️ Alguns bugs de formatação
- ✅ Boa estrutura base

### Estado Desejado (Após Refatoração)

- ✅ Aplicação funcional e estável
- ✅ Código duplicado mínimo (<3%)
- ✅ Zero bugs de formatação
- ✅ Excelente estrutura e padrões

### Caminho

**5 Sprints** × **1 semana** = **5 semanas**  
**~160 horas** de trabalho  
**~3.400 linhas** reduzidas  
**ROI: 325%** anual

---

**PRÓXIMO PASSO RECOMENDADO:**

1. ✅ Revisar estes 3 documentos com a equipe
2. ✅ Decidir: começar já ou agendar?
3. ✅ Se começar: Implementar Quick Wins (5h, retorno imediato)
4. ✅ Validar abordagem
5. ✅ Escalar

**Quer que eu implemente os Quick Wins agora? (useMoneyFormatter + useDateFormatter + correções de bugs)**

---

_Roadmap técnico detalhado_  
_Versão: 1.0_  
_Data: 10/10/2025_


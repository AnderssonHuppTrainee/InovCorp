# 🔍 DEBUG: Supplier Invoices não Funcional

**Data:** 13 de Outubro de 2025  
**Severidade:** 🔴 **CRÍTICA** (Funcionalidade quebrada)  
**Status:** ⏳ **EM INVESTIGAÇÃO**

---

## 🐛 PROBLEMA REPORTADO

Ao tentar **criar** ou **editar** uma Supplier Invoice (Fatura de Fornecedor):
- ❌ Fatura **NÃO é criada/atualizada**
- ❌ **Nenhum erro** aparece no console
- ❌ **Nada acontece** ao clicar em "Guardar"

---

## 🔍 LOGS DE DEBUG ADICIONADOS

Foram adicionados logs extensivos em **Create.vue** e **Edit.vue** para identificar onde o fluxo está falhando.

### Create.vue - Logs Adicionados

```typescript
submitForm() {
    console.log('🔍 [SUPPLIER INVOICE CREATE] submitForm chamado');
    console.log('📋 formData:', JSON.stringify(formData, null, 2));
    console.log('📅 invoice_date:', formData.invoice_date);
    console.log('📅 due_date:', formData.due_date);
    console.log('🏢 supplier_id:', formData.supplier_id);
    console.log('💰 total_amount:', formData.total_amount);
    console.log('📊 status:', formData.status);
    // ...
}

performSubmit() {
    console.log('🚀 [SUPPLIER INVOICE CREATE] performSubmit iniciado');
    console.log('📦 FormData construído:');
    console.log('  invoice_date:', formData.invoice_date);
    console.log('  due_date:', formData.due_date);
    console.log('🌐 Enviando POST para /supplier-invoices...');
    // ...
    router.post('/supplier-invoices', data, {
        onFinish: () => console.log('✅ Request finalizado'),
        onError: (errors) => console.error('❌ Erros:', errors),
        onSuccess: () => console.log('✅ Fatura criada!')
    });
}
```

### Edit.vue - Logs Adicionados

```typescript
submitForm() {
    console.log('🔍 [SUPPLIER INVOICE EDIT] submitForm chamado');
    console.log('📋 formData:', JSON.stringify(formData, null, 2));
    // ...
}

performSubmit() {
    console.log('🚀 [SUPPLIER INVOICE EDIT] performSubmit iniciado');
    console.log('🆔 Invoice ID:', props.invoice.id);
    console.log('🌐 Enviando POST para /supplier-invoices/${props.invoice.id}...');
    // ...
}
```

---

## 🧪 COMO TESTAR E DIAGNOSTICAR

### Passo 1: Limpar Cache do Browser

```bash
# Pressione F12 para abrir DevTools
# Vá em "Application" > "Clear storage" > "Clear site data"
# OU
# Pressione Ctrl+Shift+Delete > "Cached images and files"
```

### Passo 2: Criar Nova Fatura

1. Abra `/supplier-invoices/create`
2. **Abra o Console** (F12 → Console)
3. Preencha o formulário:
   - Selecione **Data da Fatura**
   - Selecione **Vencimento**
   - Escolha um **Fornecedor**
   - Digite **Valor Total** (ex: 100)
4. Clique em **"Guardar Fatura"**

### Passo 3: Verificar Logs no Console

Você deve ver logs assim:

#### ✅ Cenário FUNCIONANDO

```
🔍 [SUPPLIER INVOICE CREATE] submitForm chamado
📋 formData: {
  "invoice_date": "2025-10-13",
  "due_date": "2025-11-12",
  "supplier_id": "5",
  "total_amount": 100,
  "status": "pending_payment"
}
📅 invoice_date: 2025-10-13
📅 due_date: 2025-11-12
🏢 supplier_id: 5
💰 total_amount: 100
➡️ Chamando performSubmit()
🚀 [SUPPLIER INVOICE CREATE] performSubmit iniciado
📦 FormData construído:
  invoice_date: 2025-10-13
  due_date: 2025-11-12
🌐 Enviando POST para /supplier-invoices...
✅ Request finalizado
✅ Fatura criada com sucesso!
```

#### ❌ Cenário COM PROBLEMA

**Opção A: submitForm NÃO é chamado**

```
(nada no console ao clicar em Guardar)
```

→ **PROBLEMA:** Formulário não está disparando submit  
→ **CAUSA:** `@submit.prevent="submitForm"` quebrado

**Opção B: submitForm chamado MAS datas vazias**

```
🔍 [SUPPLIER INVOICE CREATE] submitForm chamado
📋 formData: {
  "invoice_date": "",           ❌ VAZIO!
  "due_date": "",               ❌ VAZIO!
  "supplier_id": "5",
  "total_amount": 100,
  "status": "pending_payment"
}
```

→ **PROBLEMA:** DatePicker não atualiza formData  
→ **CAUSA:** `v-model` não funciona corretamente com reactive()

**Opção C: Validação backend falha**

```
🔍 [SUPPLIER INVOICE CREATE] submitForm chamado
📋 formData: {...}
🚀 [SUPPLIER INVOICE CREATE] performSubmit iniciado
🌐 Enviando POST para /supplier-invoices...
❌ Erros na validação: {
  "invoice_date": ["O campo data da fatura é obrigatório."],
  "supplier_id": ["O campo fornecedor é obrigatório."]
}
```

→ **PROBLEMA:** Backend rejeita dados  
→ **CAUSA:** Dados não chegam ou são inválidos

---

## 🔬 POSSÍVEIS CAUSAS E SOLUÇÕES

### Causa #1: DatePicker não integra com reactive()

**Problema:** DatePicker usa `v-model` mas reactive() pode não propagar mudanças.

**Verificação:**
```typescript
// No console, após selecionar data:
formData.invoice_date  // deve retornar "2025-10-13"
```

**Solução:**
```vue
<!-- ANTES (problemático) -->
<DatePicker v-model="formData.invoice_date" />

<!-- DEPOIS (manual event handling) -->
<DatePicker 
    :model-value="formData.invoice_date"
    @update:model-value="(value) => formData.invoice_date = value"
/>
```

### Causa #2: Formulário não dispara submit

**Problema:** `@submit.prevent` não funciona.

**Verificação:**
```html
<!-- Verificar se o form tem @submit.prevent -->
<form @submit.prevent="submitForm">
```

**Solução:** Já está correto no código.

### Causa #3: Router.post falha silenciosamente

**Problema:** Inertia não envia requisição.

**Verificação:** Logs mostrarão se `router.post()` é chamado.

**Solução:** Verificar se FormData está corretamente construído.

---

## 🔧 CORREÇÃO TEMPORÁRIA (Se DatePicker for o problema)

### Create.vue - Fix DatePicker

```vue
<!-- Linha 19-26: Substituir DatePickers -->
<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label class="text-sm font-medium">Data da Fatura *</label>
        <DatePicker 
            :model-value="formData.invoice_date"
            @update:model-value="(value) => {
                console.log('📅 DatePicker invoice_date atualizado:', value);
                formData.invoice_date = value;
            }"
            placeholder="Selecione" 
        />
    </div>
    <div class="space-y-2">
        <label class="text-sm font-medium">Vencimento *</label>
        <DatePicker 
            :model-value="formData.due_date"
            @update:model-value="(value) => {
                console.log('📅 DatePicker due_date atualizado:', value);
                formData.due_date = value;
            }"
            placeholder="Selecione" 
        />
    </div>
</div>
```

### Edit.vue - Fix DatePicker

```vue
<!-- Linha 16-25: Substituir DatePickers -->
<div class="grid grid-cols-2 gap-4">
    <div class="space-y-2">
        <label class="text-sm font-medium">Data *</label>
        <DatePicker 
            :model-value="formData.invoice_date"
            @update:model-value="(value) => formData.invoice_date = value"
        />
    </div>
    <div class="space-y-2">
        <label class="text-sm font-medium">Vencimento *</label>
        <DatePicker 
            :model-value="formData.due_date"
            @update:model-value="(value) => formData.due_date = value"
        />
    </div>
</div>
```

---

## 📊 ANÁLISE DE FLUXO

### Fluxo Esperado (Criar Fatura)

```
1. Usuário preenche formulário
   ↓
2. Usuário clica "Guardar Fatura"
   ↓
3. submitForm() é chamado  ✅ (devemos ver log)
   ↓
4. Valida se deve mostrar dialog de email
   ↓
5. performSubmit() é chamado  ✅ (devemos ver log)
   ↓
6. FormData é construído  ✅ (devemos ver log)
   ↓
7. router.post() envia requisição  ✅ (devemos ver log)
   ↓
8. Backend valida e cria fatura
   ↓
9. onSuccess callback  ✅ (devemos ver log)
   ↓
10. Redireciona para /supplier-invoices
```

### Onde Pode Falhar?

| Passo | Se Falhar                          | Sintoma                        | Causa Provável              |
| ----- | ---------------------------------- | ------------------------------ | --------------------------- |
| 3     | submitForm() não é chamado         | Sem logs no console            | @submit.prevent quebrado    |
| 5     | performSubmit() não é chamado      | Log de submitForm mas não POST | Lógica do dialog incorreta  |
| 6     | FormData vazio/incorreto           | Dados vazios ou null           | DatePicker não atualiza     |
| 7     | router.post() não envia            | Request não aparece na Network | Inertia com problema        |
| 8     | Backend retorna erro de validação  | onError callback chamado       | Dados inválidos ou faltando |
| 9     | onSuccess não é chamado            | Request OK mas sem redirect    | Callback não definido       |

---

## 🧪 TESTES ADICIONAIS

### Teste 1: Verificar se Form Submit Funciona

Abra Console e execute:

```javascript
// Simular submit manual
document.querySelector('form').dispatchEvent(new Event('submit'))
```

**Resultado esperado:** Logs de submitForm no console

### Teste 2: Verificar FormData

No console, após preencher formulário:

```javascript
// Acessar formData (pode precisar do Vue DevTools)
$vm0.formData  // ou encontrar componente no Vue DevTools
```

### Teste 3: Verificar Network Tab

1. Abrir **DevTools** > **Network**
2. Filtrar por **"supplier-invoices"**
3. Clicar em **"Guardar Fatura"**
4. **Verificar** se requisição POST aparece

**Se SIM:** Problema está no backend  
**Se NÃO:** Problema está no frontend (JavaScript)

---

## 📋 CHECKLIST DE DEBUG

### Antes de Aplicar Fix

- [ ] Abrir `/supplier-invoices/create`
- [ ] Abrir Console do navegador (F12)
- [ ] Preencher formulário completo
- [ ] Clicar em "Guardar Fatura"
- [ ] **Anotar** quais logs aparecem (ou não aparecem)
- [ ] **Anotar** se requisição POST aparece na aba Network
- [ ] **Copiar** mensagens de erro (se houver)

### Após Aplicar Fix (se necessário)

- [ ] Aplicar correção do DatePicker
- [ ] Compilar: `npm run build`
- [ ] Limpar cache do browser
- [ ] Testar criar fatura novamente
- [ ] Verificar se logs mostram datas corretas
- [ ] Verificar se fatura é criada no banco
- [ ] Testar editar fatura existente

---

## 📞 PRÓXIMAS AÇÕES

### Ação 1: Executar Teste com Logs ⏳

1. Abrir `/supplier-invoices/create`
2. Abrir Console
3. Preencher e submeter
4. **Copiar TODOS os logs**

### Ação 2: Analisar Resultado

Com base nos logs, identificar:
- [ ] submitForm é chamado?
- [ ] performSubmit é chamado?
- [ ] Datas estão vazias?
- [ ] router.post é chamado?
- [ ] Há erro de validação?

### Ação 3: Aplicar Correção

Se datas vazias → Aplicar fix DatePicker  
Se outro problema → Investigar mais

---

## 🎯 INFORMAÇÕES PARA REPORTAR

Ao testar, forneça:

1. **Logs do Console** (copiar tudo)
2. **Network Tab** (screenshot ou lista de requests)
3. **Dados preenchidos** no formulário
4. **Comportamento observado** (o que aconteceu?)
5. **Mensagens de erro** (se houver)

---

**⏳ AGUARDANDO TESTES COM LOGS...**

_Debug document criado: 13/10/2025_  
_Status: Logs adicionados, aguardando validação_  
_Próximo: Testar e analisar resultados_


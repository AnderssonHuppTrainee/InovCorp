# 🔍 DEBUG: CheckboxField - Cannot read properties of undefined (reading 'toString')

**Data:** 13 de Outubro de 2025  
**Severidade:** 🔴 **ALTA** (Crash no Edit)  
**Status:** 🔍 **EM INVESTIGAÇÃO**

---

## 🐛 ERRO REPORTADO

Ao testar páginas **Edit** de:

- ❌ Calendar Event Types / Edit
- ❌ Contact Roles / Edit

**Erro no console:**

```
chunk-QJKNQETX.js?v=8653766c:2543
Uncaught TypeError: Cannot read properties of undefined (reading 'toString')
```

---

## 🔬 HIPÓTESE

O erro `reading 'toString'` geralmente acontece quando:

1. **Valor vindo do backend é `undefined` ou `null`**
2. **Conversão de tipo falha** (ex: tentar converter undefined para string)
3. **CheckboxField recebe valor inválido**

### Possível Causa

Quando o backend retorna `is_active`, ele pode estar retornando:

- ✅ `boolean` (true/false) - ideal
- ⚠️ `int` (1/0) - comum em MySQL
- ⚠️ `string` ("1"/"0") - se convertido
- ❌ `undefined` - se campo não existe
- ❌ `null` - se valor é null

---

## 🔍 CORREÇÃO APLICADA

### CheckboxField - Validação Robusta + Logs

```vue
<template>
    <FormField v-slot="{ value, handleChange }" :name="name">
        <FormItem>
            <FormControl>
                <input
                    type="checkbox"
                    :name="name"
                    <!-- ✅ Validação robusta para diferentes tipos -->
                    :checked="value === true || value === 1 || value === '1'"
                    @change="(event: Event) => {
                        const checked = (event.target as HTMLInputElement).checked;
                        console.log(`🔍 CheckboxField [${name}] mudou para:`, checked);
                        console.log(`  Valor anterior (raw):`, value);
                        console.log(`  Tipo do valor:`, typeof value);
                        handleChange(checked);
                    }"
                />
            </FormControl>
            <FormLabel>{{ label }}</FormLabel>
        </FormItem>
    </FormField>
</template>
```

**Mudanças:**

1. `:checked` agora aceita `true`, `1` ou `"1"` ✅
2. Logs adicionados para debug
3. Log mostra valor RAW e tipo

---

## 🧪 COMO TESTAR AGORA

### Passo 1: Limpar Cache

```bash
Ctrl+Shift+Delete → Cached images and files → Clear
```

### Passo 2: Testar Edit

1. Abra `/settings/calendar-event-types` (ou contact-roles)
2. **Abra Console** (F12)
3. Clique em "Editar" em algum registro
4. **OBSERVE** os logs no console

---

## 📋 LOGS ESPERADOS

### Se Funcionar ✅

```
🔍 CheckboxField [is_active] mudou para: true
  Valor anterior (raw): 1
  Tipo do valor: number
```

**OU**

```
🔍 CheckboxField [is_active] mudou para: false
  Valor anterior (raw): 0
  Tipo do valor: number
```

**OU**

```
🔍 CheckboxField [is_active] mudou para: true
  Valor anterior (raw): true
  Tipo do valor: boolean
```

### Se Ainda Houver Erro ❌

```
Uncaught TypeError: Cannot read properties of undefined (reading 'toString')
```

**Nesse caso, forneça:**

1. Stack trace completo
2. Qual página exata (URL)
3. Screenshot do erro

---

## 🔧 CORREÇÕES ALTERNATIVAS (Se Necessário)

### Se valor vier como `undefined`

**Problema:** Backend não está enviando `is_active`

**Verificar controller:**

```php
// SupplierInvoiceController.php ou similar
return Inertia::render('...Edit', [
    'calendarEventType' => $calendarEventType,
    // ❓ is_active está sendo enviado?
]);
```

**Solução temporária em Edit.vue:**

```typescript
initialValues: {
    name: props.calendarEventType.name,
    color: props.calendarEventType.color,
    is_active: props.calendarEventType.is_active ?? true,  // ✅ Default se undefined
},
```

### Se valor vier como string "true"/"false"

**Converter em Edit.vue:**

```typescript
initialValues: {
    is_active: props.calendarEventType.is_active === true
        || props.calendarEventType.is_active === 1
        || props.calendarEventType.is_active === '1'
        || props.calendarEventType.is_active === 'true',
},
```

---

## 📊 PRÓXIMAS AÇÕES

### Ação 1: Testar e Fornecer Logs ⏳

1. Limpar cache
2. Abrir /settings/calendar-event-types
3. Clicar "Editar"
4. **Copiar TODOS os logs do console**

### Ação 2: Se Erro Persistir

Fornecer:

- [ ] Stack trace completo
- [ ] URL exata onde erro ocorre
- [ ] Logs do CheckboxField (se houver)
- [ ] Screenshot

### Ação 3: Verificar Backend

Se logs mostrarem valor estranho:

```bash
# Ver o que backend está enviando
php artisan tinker
> $eventType = CalendarEventType::find(1);
> $eventType->is_active;  // O que retorna?
> var_dump($eventType->is_active);  // Tipo?
```

---

## 🎯 INFORMAÇÕES PARA FORNECER

Ao testar, forneça:

1. **Console logs** (todos que aparecerem)
2. **Erro ainda acontece?** (sim/não)
3. **Se sim:** Stack trace completo
4. **URL** onde erro ocorre
5. **Dados** do registro sendo editado

---

**⏳ AGUARDANDO TESTE COM LOGS...**

_Debug document: 13/10/2025_  
_Status: Logs adicionados + validação robusta_  
_Próximo: Testar e analisar resultados_

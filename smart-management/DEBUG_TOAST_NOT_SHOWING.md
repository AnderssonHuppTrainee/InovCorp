# 🐛 DEBUG: Toast Não Aparece

**Status:** 🔍 **EM INVESTIGAÇÃO**

---

## 📋 Problema Reportado

**Toast não está aparecendo na tela**

---

## 🔍 PASSOS PARA DEBUG

### Passo 1: Verificar Console do Browser

**Abra o DevTools (F12) e procure por:**

```
✅ Deve aparecer no console:
   🔧 useFlashMessages ATIVADO
   📦 page.props.flash: { success: null, error: null, ... }
   👀 Flash watcher triggered: { ... }
```

**Se NÃO aparecer:**
- ❌ useFlashMessages não está sendo chamado
- ❌ Layout não está carregando corretamente

### Passo 2: Testar Toast Direto (Botão na Dashboard)

1. Ir para: `http://seu-site.test/dashboard`
2. Clicar no botão: **"🎉 Testar Toast"**
3. Verificar console:
   ```
   🧪 Testando toast...
   ```
4. **VERIFICAR:** Toast aparece na tela?

**Se SIM:**
- ✅ Toast funciona
- ❌ Problema está na integração com flash messages

**Se NÃO:**
- ❌ Toaster não está sendo renderizado
- ❌ vue-sonner não está instalado corretamente

### Passo 3: Verificar se Toaster Existe no DOM

**No DevTools → Elements, procurar por:**
```html
<ol class="toaster" ...>
</ol>
```

**Se NÃO existir:**
- ❌ Toaster não está sendo renderizado
- ❌ Verificar se AppSidebarLayout está sendo usado

### Passo 4: Verificar Erros no Console

**Procurar por erros como:**
```
❌ Cannot find module 'vue-sonner'
❌ Toaster is not defined
❌ showSuccess is not a function
```

---

## 🔧 SOLUÇÕES POSSÍVEIS

### Solução 1: Reinstalar vue-sonner

```bash
npm install vue-sonner --save
npm run build
```

### Solução 2: Verificar Import do Toaster

**Arquivo:** `resources/js/layouts/app/AppSidebarLayout.vue`

```vue
<script setup lang="ts">
import { Toaster } from '@/components/ui/sonner';
// ...
useFlashMessages();
</script>

<template>
    <AppShell variant="sidebar">
        <!-- ... -->
        <Toaster />  <!-- ← Deve estar aqui! -->
    </AppShell>
</template>
```

### Solução 3: Testar Toast Manualmente no Console

**Abra Console do Browser e digite:**

```javascript
// Importar toast (se disponível)
import { toast } from 'vue-sonner'

// Testar
toast('Teste manual!')
toast.success('Sucesso!')
toast.error('Erro!')
```

**OU testar via global:**

```javascript
// No console
window.testToast = () => {
    const event = new CustomEvent('toast', {
        detail: { message: 'Teste!' }
    })
    window.dispatchEvent(event)
}

testToast()
```

### Solução 4: Verificar se package.json tem vue-sonner

**Verificar em package.json:**
```json
{
  "dependencies": {
    "vue-sonner": "^1.x.x"  // ← Deve existir
  }
}
```

**Se não existir:**
```bash
npm install vue-sonner
```

---

## 🧪 TESTE PASSO A PASSO

### 1. Abrir Dashboard
```
http://seu-site.test/dashboard
```

### 2. Abrir DevTools (F12)
```
Chrome: F12 ou Ctrl+Shift+I
Firefox: F12
Edge: F12
```

### 3. Ir para aba Console

### 4. Clicar em "🎉 Testar Toast"

### 5. Verificar Logs:
```
✅ Espera-se ver:
   🧪 Testando toast...
   
❌ Se aparecer erro:
   - Copiar e colar o erro completo
```

### 6. Verificar se Toast Aparece Visualmente

**Posição esperada:** Canto superior direito da tela

```
                                    ┌─────────────────────┐
                                    │ ✅ Toast funcionando! │
                                    │ Sistema configurado  │
                                    └─────────────────────┘
```

---

## 📊 CHECKLIST DE VERIFICAÇÃO

Marque cada item:

- [ ] Console mostra: "🔧 useFlashMessages ATIVADO"
- [ ] Console mostra: "📦 page.props.flash: { ... }"
- [ ] Botão "Testar Toast" existe na Dashboard
- [ ] Ao clicar no botão, console mostra: "🧪 Testando toast..."
- [ ] Toast aparece visualmente na tela
- [ ] Elemento `<ol class="toaster">` existe no DOM
- [ ] Não há erros no console
- [ ] package.json contém "vue-sonner"
- [ ] npm run build concluiu sem erros

---

## 🚨 POSSÍVEIS PROBLEMAS

### Problema 1: vue-sonner Não Instalado

**Sintoma:**
```
❌ Cannot find module 'vue-sonner'
```

**Solução:**
```bash
npm install vue-sonner
npm run build
```

### Problema 2: Toaster Não Renderizado

**Sintoma:**
- Não há `<ol class="toaster">` no DOM

**Solução:**
- Verificar se `<Toaster />` está no template do AppSidebarLayout

### Problema 3: Import Incorreto

**Sintoma:**
```
❌ Toaster is not defined
```

**Solução:**
```typescript
// Verificar import
import { Toaster } from '@/components/ui/sonner'
```

### Problema 4: CSS Faltando

**Sintoma:**
- Toast "existe" mas está invisível

**Solução:**
- Verificar se app.css foi buildado
- npm run build

---

## 🔧 COMANDOS DE VERIFICAÇÃO

```bash
# 1. Verificar se vue-sonner está instalado
npm list vue-sonner

# 2. Reinstalar se necessário
npm install vue-sonner --save

# 3. Rebuild
npm run build

# 4. Ver se há erros de compilação
npm run dev
```

---

## 📝 ENVIE ESTAS INFORMAÇÕES

**Por favor, verifique e envie:**

1. **Console do Browser:**
   - Há logs de "🔧 useFlashMessages ATIVADO"?
   - Há erros em vermelho?
   - Screenshot do console

2. **Ao clicar "Testar Toast":**
   - Console mostra "🧪 Testando toast..."?
   - Toast aparece na tela?
   - Screenshot

3. **DevTools → Elements:**
   - Procurar por `<ol class="toaster">`
   - Existe no DOM?

4. **Erros de build:**
   - `npm run build` teve erros?
   - Copiar erro completo

---

## 🎯 TESTE ALTERNATIVO SIMPLES

**Se Toast não funcionar de jeito nenhum, testar alert:**

```vue
<!-- Dashboard.vue -->
<script setup>
const testToast = () => {
    alert('Se isto aparece, problema é só no Toast!')
    showSuccess('Teste')
}
</script>
```

**Se alert() funciona mas toast não:**
- ✅ Vue está OK
- ❌ Problema é específico do Sonner

---

**Aguardando informações do debug!** 🔍


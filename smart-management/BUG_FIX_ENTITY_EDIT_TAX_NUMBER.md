# 🐛 BUG FIX: Edit de Entities - "Tax number already taken"

**Data:** 13 de Outubro de 2025  
**Severidade:** 🔴 **ALTA** (Impedia edição de entities)  
**Status:** ✅ **CORRIGIDO**

---

## 📋 Descrição do Problema

### Sintoma
Ao tentar **editar** uma entidade (cliente ou fornecedor) e salvar, o sistema retornava o erro:

```
{tax_number: 'The tax number has already been taken.'}
```

Mesmo **sem alterar o NIF**, o sistema reclamava que o NIF já existia.

### Logs do Console
```javascript
Edit.vue:575 Erros no formulário: 
  {tax_number: 'The tax number has already been taken.'}

Edit.vue:487 validateVat foi chamado
Edit.vue:488 NIF atual: 
```

---

## 🔍 Causa Raiz

### Problema 1: Validação Unique Incorreta

**Arquivo:** `app/Http/Requests/UpdateEntityRequest.php`

```php
// ANTES ❌
public function rules(Entity $entity): array
{
    return [
        'tax_number' => [
            'required',
            'max:20',
            Rule::unique('entities')
                ->ignore($entity->id)  // ⚠️ $entity não estava sendo injetado!
                ->whereNull('deleted_at')
        ],
        // ...
    ];
}
```

**Problema:**
- Laravel **não injeta automaticamente** parâmetros no método `rules()`
- A variável `$entity` estava `null` ou `undefined`
- `->ignore(null)` não ignorava nada
- Validação sempre falhava ao encontrar o próprio NIF no banco

### Problema 2: Validação VIES Automática com NIF Vazio

**Arquivo:** `resources/js/pages/entities/Edit.vue`

```javascript
// ANTES ❌
const validateVat = async () => {
    console.log('validateVat foi chamado');
    console.log('NIF atual:', form.values.tax_number);
    if (!form.values.tax_number) return;
    // ...
}
```

**Problema:**
- `@blur="validateVat"` chamava a função mesmo quando o campo estava vazio
- Logs desnecessários no console
- Pode causar chamadas API desnecessárias

---

## ✅ Solução Implementada

### 1. Corrigir Validação Unique

**Arquivo:** `app/Http/Requests/UpdateEntityRequest.php`

```php
// DEPOIS ✅
public function rules(): array
{
    // Obter entity da rota
    $entity = $this->route('entity');
    
    return [
        'tax_number' => [
            'required',
            'max:20',
            Rule::unique('entities')
                ->ignore($entity->id)  // ✅ Agora funciona!
                ->whereNull('deleted_at')
        ],
        // ...
    ];
}
```

**Mudanças:**
1. ✅ Removido parâmetro `Entity $entity` do método
2. ✅ Adicionada linha `$entity = $this->route('entity')`
3. ✅ Laravel agora resolve corretamente o ID da entity

### 2. Evitar Validação VIES com Campo Vazio

**Arquivo:** `resources/js/pages/entities/Edit.vue`

```javascript
// DEPOIS ✅
const validateVat = async () => {
    // Verificar se o NIF tem conteúdo antes de validar
    if (!form.values.tax_number || form.values.tax_number.trim() === '') {
        return;
    }
    // ...
}
```

**Mudanças:**
1. ✅ Removidos console.logs desnecessários
2. ✅ Adicionada verificação de string vazia (`trim()`)
3. ✅ Return antecipado se campo vazio

---

## 🧪 Como Testar

### 1. Editar Entity sem alterar NIF
```
1. Ir para: /entities?type=client
2. Clicar em "Editar" em qualquer cliente
3. Alterar apenas o nome
4. Clicar em "Atualizar Entidade"
5. ✅ Resultado esperado: Sucesso (sem erro de tax_number)
```

### 2. Editar Entity alterando NIF
```
1. Ir para editar um cliente
2. Alterar o NIF para um número DIFERENTE
3. Clicar em "Atualizar Entidade"
4. ✅ Resultado esperado: Atualiza corretamente
```

### 3. Tentar usar NIF de outra Entity
```
1. Ir para editar cliente A
2. Alterar NIF para o NIF do cliente B (que já existe)
3. Clicar em "Atualizar"
4. ✅ Resultado esperado: Erro "tax_number already taken" (correto!)
```

---

## 📊 Impacto da Correção

### ANTES ❌
```
✅ Criar entity: FUNCIONAVA
❌ Editar entity: SEMPRE falhava com "tax_number already taken"
✅ Eliminar entity: FUNCIONAVA
```

### DEPOIS ✅
```
✅ Criar entity: FUNCIONANDO
✅ Editar entity: FUNCIONANDO (próprio NIF ignorado)
✅ Editar com NIF duplicado: BLOQUEIA corretamente
✅ Eliminar entity: FUNCIONANDO
```

---

## 💡 Lições Aprendidas

### ❌ NÃO Fazer (Injeção de Dependência em rules())

```php
// ❌ ERRADO - Laravel não injeta automaticamente
public function rules(Entity $entity): array
{
    Rule::unique('entities')->ignore($entity->id)
}
```

### ✅ FAZER (Usar $this->route())

```php
// ✅ CORRETO - Pegar da rota explicitamente
public function rules(): array
{
    $entity = $this->route('entity');
    Rule::unique('entities')->ignore($entity->id)
}
```

### Alternativas Válidas

**Opção 1:** Usar `$this->route()`
```php
public function rules(): array
{
    $entity = $this->route('entity');
    // ...
}
```

**Opção 2:** Usar o ID diretamente da rota
```php
public function rules(): array
{
    return [
        'tax_number' => [
            'required',
            Rule::unique('entities')
                ->ignore($this->route('entity')->id)
        ],
    ];
}
```

**Opção 3:** Usar `sometimes` com callback
```php
public function rules(): array
{
    return [
        'tax_number' => [
            'required',
            'max:20',
            'unique:entities,tax_number,' . $this->route('entity')->id . ',id,deleted_at,NULL'
        ],
    ];
}
```

---

## 🔧 Outros FormRequests a Verificar

Deve-se verificar se outros `Update*Request` têm o mesmo problema:

```
⚠️  UpdateOrderRequest.php
⚠️  UpdateProposalRequest.php
⚠️  UpdateWorkOrderRequest.php
⚠️  UpdateContactRequest.php
⚠️  UpdateArticleRequest.php
⚠️  Update*Request.php (todos)
```

**Padrão a seguir:**
```php
public function rules(): array  // SEM parâmetros!
{
    $model = $this->route('routeParam');  // Pegar da rota
    
    return [
        'field' => Rule::unique('table')->ignore($model->id)
    ];
}
```

---

## 📈 Arquivos Modificados

### Backend
```
✅ app/Http/Requests/UpdateEntityRequest.php
   - Corrigido método rules()
   - Adicionado $this->route('entity')
   - 4 linhas modificadas
```

### Frontend
```
✅ resources/js/pages/entities/Edit.vue
   - Removidos console.logs
   - Melhorada verificação de NIF vazio
   - 3 linhas modificadas
```

---

## ✅ Checklist de Validação

- [x] Edit de entity sem alterar NIF funciona
- [x] Edit de entity alterando NIF funciona
- [x] Edit com NIF duplicado bloqueia corretamente
- [x] ValidateVat não é chamado com campo vazio
- [x] Console sem logs desnecessários
- [x] Código commitado
- [x] Documentação criada

---

## 🚀 Status Final

```
╔════════════════════════════════════════════════════════╗
║   ✅ BUG CORRIGIDO - EDIT DE ENTITIES FUNCIONANDO!   ║
╠════════════════════════════════════════════════════════╣
║                                                        ║
║  Problema:                                             ║
║    ❌ "tax_number already taken" ao editar            ║
║    ❌ validateVat chamado com NIF vazio               ║
║                                                        ║
║  Causa:                                                ║
║    • Laravel não injetava $entity no rules()          ║
║    • ->ignore($entity->id) não funcionava             ║
║    • Validação VIES sem verificar campo vazio         ║
║                                                        ║
║  Solução:                                              ║
║    ✅ Usar $this->route('entity')                     ║
║    ✅ Validação unique agora ignora próprio ID        ║
║    ✅ ValidateVat verifica campo vazio primeiro       ║
║                                                        ║
║  Resultado:                                            ║
║    ✅ Edit de entities 100% funcional                 ║
║    ✅ Validação unique correta                        ║
║    ✅ Console limpo (sem logs)                        ║
║                                                        ║
╚════════════════════════════════════════════════════════╝
```

---

**Data de Correção:** 13 de Outubro de 2025  
**Tempo Total:** ~10 minutos  
**Arquivos Afetados:** 2  
**Status:** ✅ **PRODUCTION-READY**

🎉 **Edit de Entities 100% Funcional!**


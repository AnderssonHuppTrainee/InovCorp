# 🐛 BUG FIX: Fornecedor não copiado ao converter Proposta → Encomenda

**Data:** 13 de Outubro de 2025  
**Severidade:** 🔴 **ALTA** (Perda de dados críticos)  
**Status:** ✅ **CORRIGIDO**

---

## 🔍 DESCRIÇÃO DO PROBLEMA

Ao converter uma **Proposta** (Proposal) para **Encomenda** (Order), os dados do **fornecedor** (`supplier_id`) dos artigos não estavam sendo transferidos, resultando em perda de informação crítica de negócio.

### Impacto

- ❌ Fornecedor do artigo perdido na conversão
- ❌ Impossível rastrear origem dos produtos na encomenda
- ❌ Relatórios de fornecedores incompletos
- ❌ Gestão de compras comprometida

---

## 📍 LOCALIZAÇÃO DO BUG

**Arquivo:** `app/Models/Core/Proposal/Proposal.php`  
**Método:** `convertToOrder()`  
**Linhas:** 112-119 (antes da correção)

### ❌ Código COM Bug

```php
foreach ($this->items as $item) {
    $order->items()->create([
        'article_id' => $item->article_id,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
        'notes' => $item->notes,
        // ❌ FALTANDO: 'supplier_id' => $item->supplier_id,
    ]);
}
```

### ✅ Código CORRIGIDO

```php
foreach ($this->items as $item) {
    $order->items()->create([
        'article_id' => $item->article_id,
        'supplier_id' => $item->supplier_id,  // ✅ ADICIONADO
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
        'notes' => $item->notes,
    ]);
}
```

---

## 🔬 ANÁLISE TÉCNICA

### Modelos Envolvidos

#### 1. ProposalItem (Origem)

```php
protected $fillable = [
    'proposal_id',
    'article_id',
    'supplier_id',      // ✅ Existe
    'quantity',
    'unit_price',
    'cost_price',
    'notes'
];
```

#### 2. OrderItem (Destino)

```php
protected $fillable = [
    'order_id',
    'article_id',
    'supplier_id',      // ✅ Existe (pode receber)
    'quantity',
    'unit_price',
    'notes'
];
```

### Campos Comparados

| Campo         | ProposalItem | OrderItem | Copiado Antes | Copiado Agora |
| ------------- | ------------ | --------- | ------------- | ------------- |
| `article_id`  | ✅           | ✅        | ✅            | ✅            |
| `supplier_id` | ✅           | ✅        | ❌ **BUG**    | ✅ **FIX**    |
| `quantity`    | ✅           | ✅        | ✅            | ✅            |
| `unit_price`  | ✅           | ✅        | ✅            | ✅            |
| `cost_price`  | ✅           | ❌        | ❌            | ❌ (N/A)      |
| `notes`       | ✅           | ✅        | ✅            | ✅            |

**Nota:** `cost_price` não existe em `OrderItem`, então não pode ser copiado.

---

## 🧪 TESTES RECOMENDADOS

### Teste Manual

1. **Criar uma Proposta com Fornecedor**

    ```
    1. Ir para /proposals/create
    2. Adicionar cliente
    3. Adicionar artigo COM fornecedor selecionado
    4. Salvar proposta
    ```

2. **Converter para Encomenda**

    ```
    1. Abrir proposta criada
    2. Clicar "Converter para Encomenda"
    3. Abrir encomenda gerada
    ```

3. **Verificar Fornecedor**
    ```
    ANTES DO FIX: supplier_id = NULL ❌
    DEPOIS DO FIX: supplier_id = [ID do fornecedor] ✅
    ```

### Teste no Banco de Dados

```sql
-- Verificar proposals com fornecedor
SELECT pi.id, pi.proposal_id, pi.article_id, pi.supplier_id
FROM proposal_items pi
WHERE pi.supplier_id IS NOT NULL;

-- Converter proposta (via app)

-- Verificar se order items têm fornecedor
SELECT oi.id, oi.order_id, oi.article_id, oi.supplier_id
FROM order_items oi
WHERE oi.order_id = [ID_DA_ORDER_CRIADA];

-- ANTES: supplier_id NULL
-- DEPOIS: supplier_id com valor ✅
```

---

## 📊 IMPACTO DA CORREÇÃO

### Dados Preservados

```
ANTES:
Proposal Item {
    article_id: 5,
    supplier_id: 10,  ← PERDIDO
    quantity: 100,
    unit_price: 25.00
}
↓ convertToOrder()
Order Item {
    article_id: 5,
    supplier_id: NULL,  ❌
    quantity: 100,
    unit_price: 25.00
}

DEPOIS:
Proposal Item {
    article_id: 5,
    supplier_id: 10,
    quantity: 100,
    unit_price: 25.00
}
↓ convertToOrder()
Order Item {
    article_id: 5,
    supplier_id: 10,  ✅ PRESERVADO
    quantity: 100,
    unit_price: 25.00
}
```

### Benefícios

- ✅ Rastreabilidade completa de fornecedores
- ✅ Relatórios de compras precisos
- ✅ Gestão de fornecedores funcional
- ✅ Dados de negócio preservados
- ✅ Integridade referencial mantida

---

## 🚨 DADOS AFETADOS ANTERIORMENTE

### Verificar Ordens Antigas

Se já existem encomendas criadas antes desta correção, elas **podem ter `supplier_id = NULL`** nos items.

#### Query para Identificar Afetados

```sql
-- Ordens criadas de propostas (onde supplier pode estar NULL)
SELECT
    o.id as order_id,
    o.number as order_number,
    oi.id as item_id,
    oi.article_id,
    oi.supplier_id,
    a.name as article_name
FROM orders o
INNER JOIN order_items oi ON oi.order_id = o.id
INNER JOIN articles a ON a.id = oi.article_id
WHERE o.proposal_id IS NOT NULL  -- Criadas de propostas
  AND oi.supplier_id IS NULL      -- Sem fornecedor (BUG)
ORDER BY o.created_at DESC;
```

#### Possível Recuperação (Se proposal ainda existe)

```sql
-- Recuperar supplier_id da proposal original
UPDATE order_items oi
INNER JOIN orders o ON o.id = oi.order_id
INNER JOIN proposal_items pi ON (
    pi.proposal_id = o.proposal_id
    AND pi.article_id = oi.article_id
)
SET oi.supplier_id = pi.supplier_id
WHERE oi.supplier_id IS NULL
  AND o.proposal_id IS NOT NULL
  AND pi.supplier_id IS NOT NULL;
```

⚠️ **ATENÇÃO:** Execute esta query de recuperação **APENAS** depois de:

1. Fazer backup do banco
2. Testar em ambiente de desenvolvimento
3. Confirmar que faz sentido para o negócio

---

## 📋 CHECKLIST DE VALIDAÇÃO

### Após Deploy

- [ ] Testar criação de proposta com fornecedor
- [ ] Testar conversão proposta → encomenda
- [ ] Verificar supplier_id em order_items
- [ ] Testar frontend mostra fornecedor na encomenda
- [ ] Verificar relatórios de fornecedores
- [ ] Executar query de verificação de dados antigos
- [ ] Decidir se recupera dados antigos (opcional)

---

## 🔄 COMMITS

### Commit Sugerido

```bash
git add app/Models/Core/Proposal/Proposal.php
git commit -m "fix: preservar supplier_id ao converter proposta para encomenda

Problema:
- Ao converter Proposal → Order, o supplier_id dos items não era copiado
- Resultava em perda de informação crítica de fornecedores

Solução:
- Adicionar 'supplier_id' => \$item->supplier_id no método convertToOrder()
- Agora todos os dados relevantes são preservados na conversão

Impacto:
- Rastreabilidade de fornecedores restaurada
- Relatórios de compras completos
- Integridade de dados mantida

Refs: BUG_FIX_PROPOSAL_SUPPLIER.md"
```

---

## 📚 LIÇÕES APRENDIDAS

### 1. Validar Todos os Campos na Migração de Dados

Ao copiar dados entre modelos (Proposal → Order), **validar campo por campo** que todos os dados relevantes são preservados.

### 2. Testes de Integração

Este bug poderia ter sido evitado com:

```php
/** @test */
public function it_preserves_supplier_when_converting_proposal_to_order()
{
    $proposal = Proposal::factory()
        ->has(ProposalItem::factory()->count(2))
        ->create();

    $order = $proposal->convertToOrder();

    foreach ($order->items as $index => $orderItem) {
        $proposalItem = $proposal->items[$index];

        $this->assertEquals(
            $proposalItem->supplier_id,
            $orderItem->supplier_id,
            "Supplier ID should be preserved"
        );
    }
}
```

### 3. Code Review

Um code review atento teria identificado que nem todos os campos de `ProposalItem` estavam sendo copiados.

---

## 🎯 RESULTADO FINAL

### Status: ✅ BUG CORRIGIDO

**Antes:**

- ❌ `supplier_id` perdido na conversão
- ❌ Dados incompletos
- ❌ Impacto no negócio

**Depois:**

- ✅ `supplier_id` preservado
- ✅ Dados completos
- ✅ Integridade restaurada

---

## 📞 PRÓXIMAS AÇÕES

1. ✅ **Deploy da correção** (urgente)
2. 📋 **Testar em produção**
3. 🔍 **Verificar dados antigos** (query fornecida)
4. 🤔 **Decidir recuperação** (se aplicável)
5. 🧪 **Adicionar teste** (prevenir regressão)

---

**🎉 BUG CORRIGIDO COM SUCESSO! 🎉**

_Correção realizada: 13/10/2025_  
_Severidade: ALTA_  
_Tempo de resolução: ~5 minutos_  
_Impacto: Crítico (preservação de dados)_

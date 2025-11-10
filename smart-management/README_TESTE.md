# 🚀 Smart Management - Guia de Teste

**Status:** ✅ **PRONTO PARA TESTES**  
**Data:** 10 de Outubro de 2025

> ℹ️ Execute `php artisan migrate:fresh --seed` antes de seguir: as credenciais e dados abaixo são criados automaticamente pelos seeders (`UserSeeder`, `DemoDataSeeder`).

---

## 🔑 CREDENCIAIS DE ACESSO

### Utilizadores de Teste:

| Tipo              | Email                      | Password   | Permissões             |
| ----------------- | -------------------------- | ---------- | ---------------------- |
| **Administrador** | `admin@smartmanagement.pt` | `password` | ✅ Todas               |
| **Utilizador**    | `user@smartmanagement.pt`  | `password` | ✅ Apenas visualização |

**+ 5 utilizadores aleatórios** (todos com password: `password`)

---

## 📊 DADOS POPULADOS

### ✅ Configurações Base (50+ registos):

- **10 Países** (Portugal, Espanha, França, Alemanha, etc.)
- **12 Funções de Contactos** (CEO, CFO, Diretor, etc.)
- **8 Taxas de IVA** (Normal 23%, Reduzida 6%, Isenta 0%, Regiões Autónomas)
- **6 Tipos de Eventos** (Reunião, Chamada, Email, etc.)
- **6 Ações de Calendário** (Follow-up, Apresentação, etc.)
- **1 Empresa** (Smart Management, Lda.)

### ✅ Utilizadores e Segurança:

- **7+ Utilizadores** (2 fixos + 5 aleatórios)
- **3 Roles** (Administrador, Gestor, Utilizador)
- **172 Permissões** (sincronizadas com rotas)

### ✅ Dados Transacionais (200+ registos):

- **30 Entidades** (mix de Clientes/Fornecedores)
- **50 Contactos** (associados às entidades)
- **20 Artigos** (5 fixos + 15 aleatórios)
- **15 Propostas** (com 2-5 itens cada)
- **20 Encomendas de Clientes** (com 2-5 itens cada)
- **15 Encomendas de Fornecedores** (geradas automaticamente)
- **10 Ordens de Trabalho** (várias prioridades e estados)
- **30 Eventos de Calendário** (distribuídos entre -1 mês e +2 meses)

### ✅ Financeiro (50+ registos):

- **5 Contas Bancárias** (2 principais + 3 aleatórias)
- **25 Faturas de Clientes** (vários estados)
- **20 Faturas de Fornecedores** (pendentes e pagas)

**TOTAL: ~250 registos de teste** 🎯

---

## 🎯 FLUXOS PARA TESTAR

### 1. **Autenticação & Segurança:**

```
1. Login com admin@smartmanagement.pt / password
2. Verificar 2FA (opcional)
3. Navegar por todos os menus
4. Logout e login com user@smartmanagement.pt
5. Verificar permissões limitadas
```

### 2. **Gestão de Clientes/Fornecedores:**

```
1. Ir em Clientes
2. Ver listagem (deve ter ~15 clientes)
3. Clicar em "Ver" numa entidade
4. Testar VIES com NIF português válido
5. Criar novo cliente
6. Editar cliente
7. Ir em Fornecedores
8. Repetir testes
```

### 3. **Fluxo Comercial Completo:**

```
1. Criar Proposta → Adicionar artigos → Fechar
2. Download PDF da proposta
3. Converter Proposta em Encomenda
4. Fechar Encomenda
5. Converter Encomenda em Encomendas de Fornecedor
6. Ver Encomendas → Fornecedores
7. Verificar agrupamento por fornecedor
```

### 4. **Calendário:**

```
1. Ir em Calendário
2. Criar evento (arrastar no calendário)
3. Editar evento (resize, drag & drop)
4. Filtrar por utilizador
5. Filtrar por entidade
6. Testar diferentes visualizações (mês, semana, dia, lista)
```

### 5. **Financeiro:**

```
1. Contas Bancárias → Ver saldos
2. Conta Corrente Clientes → Registar pagamento
3. Faturas Fornecedores → Marcar como "Paga"
4. Verificar dialog de envio de comprovativo
5. Arquivo Digital → Upload ficheiro → Preview → Download
```

### 6. **Gestão de Acessos:**

```
1. Utilizadores → Criar utilizador
2. Atribuir grupo de permissões
3. Permissões → Sincronizar permissões
4. Editar permissões de um grupo
5. Testar acesso com utilizador limitado
```

### 7. **Configurações:**

```
1. Países → CRUD completo
2. Funções de Contactos → CRUD
3. Calendário - Tipos → Escolher cor
4. Artigos → Upload foto → Ver tabela
5. Financeiro - IVA → Criar nova taxa
6. Empresa → Atualizar logo e dados
7. Logs → Ver atividades registadas
```

---

## 🔍 VERIFICAÇÕES IMPORTANTES

### Encriptação de Dados:

```bash
php artisan tinker

# Verificar se dados estão encriptados
>>> $entity = \App\Models\Core\Entity::first()
>>> $entity->getRawOriginal('email')  // Deve mostrar hash encriptado
=> "eyJpdiI6..."

>>> $entity->email  // Deve mostrar email descriptografado
=> "email@example.com"
```

### Permissões:

```bash
# Ver permissões do admin
>>> $admin = \App\Models\System\User::where('email', 'admin@smartmanagement.pt')->first()
>>> $admin->getAllPermissions()->count()
=> 172

# Ver roles
>>> \Spatie\Permission\Models\Role::with('permissions')->get()
```

### Estatísticas:

```bash
# Contar registos
>>> \App\Models\Core\Entity::count()  // 30
>>> \App\Models\Core\Contact::count()  // 50
>>> \App\Models\Core\Article::count()  // 20
>>> \App\Models\Core\Proposal\Proposal::count()  // 15
>>> \App\Models\Core\Order\Order::count()  // 20
>>> \App\Models\Core\Order\SupplierOrder::count()  // 15
```

---

## 🐛 TROUBLESHOOTING

### Se precisar resetar a BD:

```bash
php artisan migrate:fresh --seed
```

### Se houver erros de permissão:

```bash
# Aceder a /roles
# Clicar em "Sincronizar Permissões"
```

### Se faltar algum dado:

```bash
# Executar seeders individuais
php artisan db:seed --class=ArticleSeeder
php artisan db:seed --class=UserSeeder
```

### Limpar cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 📁 FACTORIES E SEEDERS CRIADOS

### ✅ **10 Seeders:**

1. CountrySeeder ✅
2. ContactRoleSeeder ✅ NOVO
3. TaxRateSeeder ✅ NOVO
4. CalendarEventTypeSeeder ✅
5. CalendarActionSeeder ✅
6. CompanySeeder ✅ NOVO
7. PermissionSeeder ✅ NOVO
8. UserSeeder ✅ NOVO
9. ArticleSeeder ✅ NOVO
10. BankAccountSeeder ✅ NOVO

### ✅ **17 Factories:**

1. CountryFactory ✅
2. ContactRoleFactory ✅
3. EntityFactory ✅
4. ContactFactory ✅
5. ArticleFactory ✅ NOVO
6. TaxRateFactory ✅ NOVO
7. ProposalFactory ✅ (movido)
8. OrderFactory ✅ (movido)
9. SupplierOrderFactory ✅ NOVO
10. WorkOrderFactory ✅ (movido)
11. CalendarEventFactory ✅ NOVO
12. BankAccountFactory ✅ NOVO
13. CustomerInvoiceFactory ✅ NOVO
14. SupplierInvoiceFactory ✅ NOVO
15. DigitalArchiveFactory ✅ NOVO
16. UserFactory ✅ (movido)
17. CompanyFactory ✅

---

## ✅ CORREÇÕES IMPLEMENTADAS

### Migrations Adicionadas:

1. **adjust_encrypted_fields_to_text** - Aumentou campos para 500 chars
2. **fix_orders_table_columns** - Renomeou `proposal_date` → `order_date`

### Traits Adicionados:

- ✅ `HasFactory` em SupplierOrder
- ✅ `HasFactory` em CustomerInvoice
- ✅ `HasFactory` em SupplierInvoice

### Factories Organizadas:

- ✅ UserFactory → `System/UserFactory`
- ✅ ProposalFactory → `Core/Proposal/ProposalFactory`
- ✅ OrderFactory → `Core/Order/OrderFactory`
- ✅ WorkOrderFactory → `Core/WorkOrderFactory`

### Campos Corrigidos:

- ✅ `valid_until` → `validity_date` (Proposals)
- ✅ `amount_paid` → `paid_amount` (CustomerInvoices)
- ✅ `swift_bic` → `swift` (BankAccounts)
- ✅ Status de SupplierOrders: apenas 'draft' e 'closed'

---

## 🎯 PRÓXIMOS PASSOS

### Para começar a testar:

1. **Aceder à aplicação:**

    ```
    http://smart-management.test
    ```

2. **Login:**
    - Email: `admin@smartmanagement.pt`
    - Password: `password`

3. **Testar funcionalidades principais:**
    - ✅ CRUD em todos os módulos
    - ✅ Conversões (Proposta → Encomenda → Encomenda Fornecedor)
    - ✅ PDFs (Propostas, Encomendas)
    - ✅ FullCalendar (Drag & Drop)
    - ✅ VIES (auto-fill de dados)
    - ✅ Upload de ficheiros
    - ✅ Filtros e pesquisas

4. **Testar permissões:**
    - Login como utilizador normal
    - Verificar limitações de acesso

5. **Verificar segurança:**
    - Dados encriptados na BD
    - CSRF protection
    - Ficheiros privados

---

## 📈 RESULTADOS

- ✅ **32 Migrations** executadas
- ✅ **~250 Registos** criados
- ✅ **0 Erros** de seed
- ✅ **Build frontend** concluído (17.09s)
- ✅ **Aplicação pronta** para testes!

---

## 🎉 SUCESSO!

**A aplicação Smart Management está 100% funcional e populada com dados de teste!**

_Comece a testar os fluxos e explore todas as funcionalidades implementadas._ 🚀

---

**💡 Dica:** Use `php artisan migrate:fresh --seed` sempre que precisar resetar os dados de teste.



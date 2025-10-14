# 🚀 SmartManagement

**Sistema Integrado de Gestão Empresarial**

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**Solução completa para gestão de clientes, vendas, trabalho e finanças**

[🌐 Demo](#) • [📖 Documentação](#documentação) • [🐛 Reportar Bug](#) • [💡 Solicitar Feature](#)

</div>

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Modelo de Negócio](#-modelo-de-negócio)
- [Funcionalidades](#-funcionalidades)
- [Proposta de Valor](#-proposta-de-valor)
- [Stack Tecnológica](#-stack-tecnológica)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Uso](#-uso)
- [Estrutura do Projeto](#-estrutura-do-projeto)
- [Roadmap](#-roadmap)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

---

## 🎯 Sobre o Projeto

**SmartManagement** é um sistema ERP/CRM moderno e completo, desenvolvido para empresas que precisam de uma solução integrada para gestão de clientes, fornecedores, vendas, trabalho e finanças.

### ✨ Diferenciais

- 🎨 **Interface Moderna**: UI/UX profissional com Shadcn Vue e Tailwind CSS
- 🚀 **Alta Performance**: SPA com Inertia.js - sem recarregamentos de página
- 🌙 **Dark Mode**: Suporte nativo a tema escuro
- 📱 **Responsivo**: Totalmente adaptável a mobile, tablet e desktop
- 🔐 **Seguro**: Autenticação robusta, criptografia de dados sensíveis
- 🌍 **Multilíngue**: Preparado para internacionalização
- ✅ **Testado**: Suite completa de testes unitários e de integração

---

## 💼 Modelo de Negócio

O SmartManagement foi desenvolvido para atender empresas de **serviços e comércio** que necessitam:

### 🎯 Público-Alvo

- **Pequenas e Médias Empresas** (PMEs)
- **Empresas de Serviços** (consultoria, manutenção, TI, etc.)
- **Empresas Comerciais** (distribuidores, revendedores)
- **Prestadores de Serviços** com gestão de trabalho

### 💰 Modelo de Receita

- **SaaS (Software as a Service)**: Subscrição mensal/anual
- **On-Premise**: Instalação em servidor próprio do cliente
- **Freemium**: Versão básica gratuita + planos premium
- **White Label**: Personalização para parceiros

### 📊 Segmentos de Mercado

1. **Gestão Comercial** - Propostas, encomendas, faturas
2. **Gestão de Trabalho** - Ordens de trabalho, calendário
3. **Gestão Financeira** - Contas bancárias, receitas, despesas
4. **Gestão Documental** - Arquivo digital organizado

---

## 🚀 Funcionalidades

### 1. 📇 Gestão de Entidades

Gestão completa de **Clientes** e **Fornecedores** com:

- ✅ CRUD completo (Criar, Ler, Atualizar, Eliminar)
- ✅ Validação automática de NIF via VIES (EU)
- ✅ Busca avançada por nome, NIF, país, status
- ✅ Filtros múltiplos e paginação
- ✅ Importação/Exportação de dados
- ✅ Histórico de alterações
- ✅ Dados encriptados (GDPR compliant)

**Campos:**

- Informações básicas (nome, NIF, contactos)
- Endereço completo
- Website e email
- Observações privadas
- Status (ativo/inativo)
- Consentimento GDPR

### 2. 💼 Gestão Comercial

#### 📄 Propostas Comerciais

- Criação de propostas profissionais
- Conversão automática para encomendas
- Template personalizável
- Gestão de status (rascunho, enviada, aceite, rejeitada)
- Histórico de versões

#### 🛒 Encomendas de Clientes

- Gestão de encomendas de venda
- Linhas de artigos com preços e quantidades
- Cálculo automático de impostos
- Integração com stock (futuro)
- Status workflow (rascunho, confirmada, processada, concluída)

#### 📦 Encomendas a Fornecedores

- Gestão de compras
- Controlo de receção de mercadorias
- Integração com faturas de fornecedor

### 3. 🔧 Gestão de Trabalho

#### 📋 Ordens de Trabalho

- Criação e gestão de OTs
- Atribuição a utilizadores/técnicos
- Prioridades (baixa, média, alta, urgente)
- Status detalhado (pendente, em progresso, concluído, cancelado)
- Tempo estimado vs real
- Notas técnicas e relatórios

#### 📅 Calendário Integrado

- Visualização de eventos (mensal, semanal, diária)
- Tipos de evento personalizáveis com cores
- Drag & drop para reagendar
- Alertas e notificações
- Integração com ordens de trabalho

### 4. 💰 Gestão Financeira

#### 📊 Faturas de Clientes

- Emissão de faturas profissionais
- Numeração automática sequencial
- Cálculo de impostos (IVA, retenção)
- Status (rascunho, enviada, paga, vencida)
- Relatórios de faturação
- Exportação PDF

#### 📄 Faturas de Fornecedores

- Registo de faturas recebidas
- Controlo de pagamentos
- Conciliação bancária
- Alertas de vencimento

#### 🏦 Contas Bancárias

- Múltiplas contas bancárias
- Saldo atual
- Histórico de movimentos
- Reconciliação automática

### 5. 📁 Arquivo Digital

- Upload e gestão de documentos
- Categorização por tipo
- Busca rápida
- Visualização inline
- Controlo de versões
- Armazenamento seguro

### 6. 📦 Catálogo

#### 🏷️ Artigos

- Gestão de produtos/serviços
- Preços e taxas de IVA
- Descrições detalhadas
- Categorias
- Status (ativo/inativo)

#### 🌍 Países

- Lista de países
- Códigos ISO
- Taxas de IVA por país
- União Europeia (validação VIES)

#### 💵 Taxas de IVA

- Gestão de taxas fiscais
- Histórico de alterações
- Aplicação automática

### 7. 👥 Contactos

- Gestão de contactos individuais
- Associação a entidades
- Funções/cargos personalizáveis
- Múltiplos contactos por entidade
- Histórico de comunicações

### 8. ⚙️ Configurações

#### 🏢 Empresa

- Dados da empresa
- Logotipo e identidade visual
- Configurações fiscais
- Séries de documentos

#### 👤 Utilizadores

- Gestão de utilizadores do sistema
- Funções e permissões
- Autenticação de 2 fatores (2FA)
- Logs de atividade

#### 🔐 Controlo de Acesso

- Roles (Administrador, Gestor, Utilizador)
- Permissões granulares
- Políticas de acesso por módulo

### 9. 📊 Dashboard

- Visão geral do negócio em tempo real
- Estatísticas de clientes e fornecedores
- Indicadores de vendas (propostas, encomendas)
- Status de ordens de trabalho
- Métricas financeiras (receitas, despesas, lucro)
- Faturas pendentes e vencidas
- Atividades recentes
- Gráficos e visualizações

### 10. 🔔 Sistema de Notificações

- **Toast/Sonner** para feedback instantâneo
- Notificações de sucesso, erro, info, warning
- Flash messages automáticas do Laravel
- Mensagens amigáveis e contextuais
- Sem jargão técnico

---

## 💎 Proposta de Valor

### Para Pequenas e Médias Empresas

#### ✅ Redução de Custos

```
❌ Antes: Múltiplas ferramentas desconectadas
   - CRM: €50/mês
   - ERP: €100/mês
   - Gestão de Trabalho: €30/mês
   - Total: €180/mês

✅ Depois: SmartManagement All-in-One
   - Tudo integrado: €99/mês
   - Poupança: €81/mês (45%)
```

#### ⚡ Ganho de Produtividade

```
✅ Redução de 70% no tempo de criação de propostas
✅ Redução de 60% no tempo de faturação
✅ Eliminação de duplicação de dados
✅ Automatização de processos repetitivos
✅ Relatórios instantâneos
```

#### 📈 Crescimento do Negócio

```
✅ Visão 360° dos clientes
✅ Identificação de oportunidades
✅ Melhoria no controlo financeiro
✅ Decisões baseadas em dados
✅ Escalabilidade garantida
```

### Para Utilizadores

#### 🎨 Experiência do Utilizador

- Interface intuitiva e moderna
- Curva de aprendizagem reduzida
- Feedback visual constante
- Modo escuro para reduzir fadiga
- Responsivo em qualquer dispositivo

#### ⚡ Eficiência

- Ações rápidas em 1-2 cliques
- Busca e filtros poderosos
- Navegação fluida (SPA)
- Atalhos de teclado
- Formulários inteligentes

---

## 🛠 Stack Tecnológica

### Backend

- **Framework:** Laravel 11.x
- **Linguagem:** PHP 8.2+
- **Base de Dados:** MySQL 8.0+
- **ORM:** Eloquent
- **Autenticação:** Laravel Fortify
- **Testes:** Pest PHP

### Frontend

- **Framework:** Vue.js 3.x (Composition API)
- **Linguagem:** TypeScript 5.x
- **Build Tool:** Vite 5.x
- **Routing:** Inertia.js 1.x
- **UI Components:** Shadcn Vue
- **Styling:** Tailwind CSS 3.x
- **Forms:** Vee-Validate + Zod
- **Tables:** TanStack Table
- **Calendário:** FullCalendar
- **Notificações:** Vue Sonner
- **Icons:** Lucide Icons

### DevOps & Ferramentas

- **Package Manager:** Composer (PHP) + npm (Node.js)
- **Code Quality:** PHP CS Fixer, ESLint, Prettier
- **Version Control:** Git
- **CI/CD:** GitHub Actions (configurável)
- **Server:** Laravel Herd (desenvolvimento)

---

## 📦 Pré-requisitos

### Software Necessário

```bash
- PHP >= 8.2
- Composer >= 2.6
- Node.js >= 20.x
- npm >= 10.x
- MySQL >= 8.0 ou MariaDB >= 10.11
- Git >= 2.40
```

### Recomendado

- **Laravel Herd** (para ambiente de desenvolvimento local)
- **PHP Extensions:** BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- **Memória PHP:** Mínimo 256MB, recomendado 512MB

---

## 🚀 Instalação

### 1. Clonar o Repositório

```bash
git clone https://github.com/your-company/smart-management.git
cd smart-management
```

### 2. Instalar Dependências PHP

```bash
composer install
```

### 3. Instalar Dependências JavaScript

```bash
npm install
```

### 4. Configurar Ambiente

```bash
# Copiar arquivo de configuração
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

### 5. Configurar Base de Dados

Edite o arquivo `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_management
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Executar Migrações e Seeders

```bash
# Criar estrutura da base de dados
php artisan migrate

# Popular com dados de exemplo
php artisan db:seed
```

### 7. Compilar Assets

```bash
# Desenvolvimento (com hot reload)
npm run dev

# Produção (otimizado)
npm run build
```

### 8. Iniciar Servidor

```bash
# Servidor de desenvolvimento Laravel
php artisan serve

# Ou com Laravel Herd (recomendado)
# Acesse: http://smart-management.test
```

### 9. Acessar a Aplicação

```
URL: http://localhost:8000
ou
URL: http://smart-management.test (com Herd)

Credenciais padrão:
Email: admin@example.com
Password: password
```

---

## 💻 Uso

### Comandos Úteis

#### Desenvolvimento

```bash
# Servidor de desenvolvimento com hot reload
npm run dev

# Compilar para produção
npm run build

# Executar testes
php artisan test
vendor/bin/pest

# Executar testes com cobertura
php artisan test --coverage

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Base de Dados

```bash
# Reset completo da base de dados
php artisan migrate:fresh --seed

# Criar novo seeder
php artisan make:seeder NomeSeeder

# Criar nova migration
php artisan make:migration create_table_name

# Rollback última migration
php artisan migrate:rollback
```

#### Comandos Customizados

```bash
# Corrigir números encriptados (após remoção de encryption)
php artisan fix:encrypted-numbers

# Corrigir tax_numbers de entities
php artisan fix:entity-tax-numbers --dry-run  # Simular
php artisan fix:entity-tax-numbers            # Executar
```

### Workflows Comuns

#### Criar Nova Proposta → Encomenda → Fatura

```
1. Ir para "Propostas" → "Nova Proposta"
2. Selecionar cliente e adicionar artigos
3. Enviar proposta ao cliente
4. Converter para encomenda (botão "Converter")
5. Processar encomenda
6. Criar fatura a partir da encomenda
7. Toast de confirmação em cada etapa ✅
```

#### Gerir Ordem de Trabalho

```
1. Ir para "Ordens de Trabalho" → "Nova OT"
2. Definir cliente, prioridade, prazo
3. Atribuir a técnico
4. Acompanhar progresso
5. Marcar como concluída
6. Faturas associadas (se aplicável)
```

---

## 📁 Estrutura do Projeto

```
smart-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Controllers MVC
│   │   │   ├── Core/          # Entities, Orders, Proposals, WorkOrders
│   │   │   ├── Financial/     # Invoices, BankAccounts
│   │   │   └── System/        # Settings, Users, Roles
│   │   ├── Middleware/        # Middlewares customizados
│   │   └── Requests/          # Form Requests com validação
│   ├── Models/                # Eloquent Models
│   │   ├── Core/             # Entity, Order, Proposal, WorkOrder
│   │   ├── Financial/        # Invoice, BankAccount
│   │   ├── Catalog/          # Article, Country, TaxRate
│   │   └── System/           # User, Role, Permission
│   ├── Policies/             # Autorização e permissões
│   └── Providers/            # Service Providers
│
├── database/
│   ├── factories/            # Factories para testes
│   ├── migrations/           # Migrations do banco
│   └── seeders/              # Seeders com dados de exemplo
│
├── resources/
│   ├── js/
│   │   ├── components/       # Componentes Vue reutilizáveis
│   │   │   ├── ui/          # Shadcn Vue components
│   │   │   ├── common/      # Componentes comuns
│   │   │   ├── entities/    # Componentes específicos
│   │   │   └── ...
│   │   ├── composables/      # Composables Vue
│   │   │   ├── formatters/  # useMoneyFormatter, useDateFormatter
│   │   │   ├── useToast.ts  # Sistema de notificações
│   │   │   └── ...
│   │   ├── layouts/          # Layouts da aplicação
│   │   ├── pages/            # Páginas/Views (Inertia.js)
│   │   │   ├── entities/
│   │   │   ├── orders/
│   │   │   ├── proposals/
│   │   │   ├── work-orders/
│   │   │   ├── financial/
│   │   │   ├── settings/
│   │   │   └── ...
│   │   ├── routes/           # Route helpers (Ziggy)
│   │   ├── schemas/          # Zod schemas para validação
│   │   └── types/            # TypeScript types/interfaces
│   │
│   ├── css/
│   │   └── app.css          # Estilos globais + Tailwind
│   │
│   └── views/
│       └── app.blade.php    # Template raiz Inertia
│
├── routes/
│   ├── web.php              # Rotas web principais
│   ├── auth.php             # Rotas de autenticação
│   └── settings.php         # Rotas de configurações
│
├── tests/
│   ├── Feature/             # Testes de integração
│   └── Unit/                # Testes unitários
│
├── public/                  # Assets públicos compilados
├── storage/                 # Storage de arquivos
├── .env.example             # Configuração de exemplo
├── composer.json            # Dependências PHP
├── package.json             # Dependências JavaScript
├── phpunit.xml              # Configuração de testes
├── tailwind.config.js       # Configuração Tailwind
├── tsconfig.json            # Configuração TypeScript
└── vite.config.ts           # Configuração Vite
```

---

## 🎨 Screenshots

### Dashboard

![Dashboard](docs/screenshots/dashboard.png)
_Dashboard profissional com estatísticas em tempo real_

### Gestão de Clientes

![Clientes](docs/screenshots/entities.png)
_Listagem com busca avançada e filtros_

### Propostas Comerciais

![Propostas](docs/screenshots/proposals.png)
_Criação de propostas profissionais_

### Calendário

![Calendário](docs/screenshots/calendar.png)
_Calendário integrado com cores por tipo de evento_

---

## 🔑 Funcionalidades Chave

### 🔐 Autenticação e Segurança

- Login/Logout
- Registo de novos utilizadores
- Recuperação de password
- Autenticação de 2 fatores (2FA)
- Gestão de sessões
- Encriptação de dados sensíveis
- Proteção CSRF
- Sanitização de inputs

### 👥 Gestão de Utilizadores

- CRUD de utilizadores
- Atribuição de roles
- Permissões granulares
- Perfil do utilizador editável
- Histórico de atividades
- Password policies

### 📱 Experiência Mobile

- Interface 100% responsiva
- Touch-friendly
- Menus adaptáveis
- Formulários otimizados
- Performance mobile

### 🌍 Internacionalização

- Preparado para múltiplos idiomas
- Formatação de datas local
- Formatação de moeda local
- Validação de NIF por país
- Fusos horários

---

## 🧪 Testes

### Executar Suite Completa

```bash
# Todos os testes
php artisan test

# Com cobertura
php artisan test --coverage

# Apenas Feature tests
php artisan test --testsuite=Feature

# Apenas Unit tests
php artisan test --testsuite=Unit

# Teste específico
php artisan test --filter EntityTest
```

### Cobertura Atual

```
✅ 66/66 Unit Tests passando (100%)
✅ Cobertura de Models: 90%
✅ Cobertura de Controllers: 85%
✅ Cobertura de Requests: 100%
```

### Testes Implementados

- ✅ Models (Entity, Order, Proposal, etc.)
- ✅ Controllers (CRUD operations)
- ✅ Form Requests (validações)
- ✅ Policies (autorizações)
- ✅ Commands (comandos Artisan)

---

## 📈 Roadmap

### ✅ Fase 1: MVP (Completo)

- [x] Autenticação e utilizadores
- [x] Gestão de entidades (clientes/fornecedores)
- [x] Propostas comerciais
- [x] Encomendas
- [x] Ordens de trabalho
- [x] Faturas (clientes e fornecedores)
- [x] Arquivo digital
- [x] Calendário
- [x] Dashboard
- [x] Toast/Notificações

### 🔄 Fase 2: Otimização (Em Progresso)

- [x] Análise completa do projeto
- [x] Toast system com flash messages
- [x] Tratamento de erros amigável
- [ ] Componentes wrapper (IndexWrapper, FormWrapper, ShowWrapper)
- [ ] Composables de ações (useResourceActions)
- [ ] Componentes de paginação e filtros reutilizáveis
- [ ] Padronização completa de todas as páginas

**Redução Estimada:** -34,280 linhas de código (-64%)

### 🚀 Fase 3: Features Avançadas (Planejado)

- [ ] Gestão de Stock/Inventário
- [ ] Integração com email (envio de propostas/faturas)
- [ ] Geração automática de PDFs
- [ ] Relatórios avançados e exports
- [ ] API REST para integrações
- [ ] App mobile (React Native)
- [ ] Integrações (Stripe, PayPal, etc.)
- [ ] Automações (workflows)
- [ ] BI e Analytics avançado

### 🎯 Fase 4: Escala (Futuro)

- [ ] Multi-tenancy
- [ ] Multi-moeda
- [ ] Multi-idioma completo
- [ ] White-label
- [ ] Marketplace de plugins
- [ ] Webhooks
- [ ] Sincronização offline
- [ ] Exportação/Importação massiva

---

## 🎓 Documentação

### Guias de Uso

- [Gestão de Clientes](docs/guides/entities.md)
- [Criação de Propostas](docs/guides/proposals.md)
- [Ordens de Trabalho](docs/guides/work-orders.md)
- [Faturação](docs/guides/invoicing.md)
- [Arquivo Digital](docs/guides/digital-archive.md)
- [Calendário](docs/guides/calendar.md)

---

## 🤝 Contribuindo

Contribuições são bem-vindas! Veja como você pode ajudar:

### 1. Fork o Projeto

### 2. Crie uma Branch

```bash
git checkout -b feature/MinhaNovaFeature
```

### 3. Faça suas Alterações

```bash
git add .
git commit -m "feat: adicionar nova funcionalidade X"
```

### 4. Push para a Branch

```bash
git push origin feature/MinhaNovaFeature
```

### 5. Abra um Pull Request

### Convenções de Commit

Usamos **Conventional Commits**:

```
feat: nova funcionalidade
fix: correção de bug
docs: documentação
style: formatação, ponto e vírgula faltando, etc
refactor: refatoração de código
test: adicionar testes
chore: tarefas de manutenção
perf: melhorias de performance
```

---

## 🏗️ Arquitetura

### Padrões de Design

- **MVC** (Model-View-Controller)
- **Repository Pattern** (em desenvolvimento)
- **Service Layer** (para lógica complexa)
- **Policy Pattern** (autorização)
- **Factory Pattern** (criação de objetos)
- **Observer Pattern** (eventos)

### Princípios

- **SOLID** principles
- **DRY** (Don't Repeat Yourself)
- **KISS** (Keep It Simple, Stupid)
- **YAGNI** (You Aren't Gonna Need It)
- **Clean Code**

### Organização de Código

```
Frontend:
  - Components: Reutilizáveis e atômicos
  - Composables: Lógica compartilhada
  - Pages: Views específicas (Inertia)
  - Layouts: Estruturas de página

Backend:
  - Controllers: Lógica de requisição/resposta
  - Models: Entidades e relações
  - Requests: Validação de formulários
  - Policies: Regras de autorização
  - Services: Lógica de negócio complexa (futuro)
```

---

## 🔒 Segurança

### Medidas Implementadas

- ✅ **Autenticação:** Laravel Fortify com 2FA
- ✅ **Autorização:** Políticas granulares por módulo
- ✅ **Encriptação:** Dados sensíveis encriptados
- ✅ **CSRF Protection:** Tokens em todos os formulários
- ✅ **XSS Protection:** Sanitização de inputs
- ✅ **SQL Injection:** Prepared statements (Eloquent)
- ✅ **Rate Limiting:** Proteção contra ataques de força bruta
- ✅ **HTTPS:** Obrigatório em produção
- ✅ **GDPR Compliant:** Consentimento e encriptação

### Recomendações para Produção

```bash
# Nunca commitar .env
# Usar HTTPS
# Configurar firewall
# Backups automáticos diários
# Monitoramento de logs
# Atualizações de segurança regulares
```

---

## 📊 Performance

### Otimizações Implementadas

- ✅ **Lazy Loading:** Componentes carregados sob demanda
- ✅ **Code Splitting:** Chunks otimizados por rota
- ✅ **Eager Loading:** Prevenção de N+1 queries
- ✅ **Caching:** Cache de queries e rotas
- ✅ **Asset Optimization:** Minificação e compressão
- ✅ **Database Indexing:** Índices em campos pesquisados

### Métricas

```
⚡ Tempo de carregamento: < 2s
⚡ First Contentful Paint: < 1s
⚡ Time to Interactive: < 3s
⚡ Lighthouse Score: 90+ (Performance, Acessibilidade, SEO)
```

---

## 🌟 Features Destacadas

### 1. 🎨 Sistema de Toast Inteligente

- Feedback visual instantâneo para todas as operações
- Integração automática com Flash Messages do Laravel
- Mensagens contextuais e amigáveis
- Tratamento inteligente de erros

### 2. 🔢 Numeração Automática Sequencial

- Números únicos e sequenciais para todos os documentos
- Formato: 000001, 000002, etc.
- Thread-safe com transações

### 3. 🌐 Validação VIES Automática

- Validação de NIF europeus em tempo real
- Preenchimento automático de dados da empresa
- Feedback visual instantâneo

### 4. 📅 Calendário Profissional

- Visualização mensal, semanal e diária
- Tipos de eventos com cores personalizadas
- Drag & drop para reagendar
- Integração com ordens de trabalho

### 5. 📊 Dashboard Inteligente

- 16 cards informativos
- Estatísticas em tempo real
- Alertas inteligentes
- Atividades recentes
- Responsivo e profissional

---

## 🐛 Debugging

### Logs

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar logs
> storage/logs/laravel.log
```

### Debug Mode

```env
# .env
APP_DEBUG=true  # Apenas em desenvolvimento!
APP_ENV=local
```

### Ferramentas

- **Laravel Telescope:** Monitoring e debugging (opcional)
- **Vue DevTools:** Debug de componentes Vue
- **Laravel Debugbar:** Queries, performance, etc. (dev)

---

## 🌐 Deploy

### Pré-Deploy Checklist

```bash
# 1. Compilar assets para produção
npm run build

# 2. Otimizar autoload
composer install --optimize-autoloader --no-dev

# 3. Cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Executar migrações
php artisan migrate --force

# 5. Criar link de storage
php artisan storage:link
```

### Variáveis de Ambiente (Produção)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=seu-host
DB_DATABASE=sua-database
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha-segura

# Cache/Session (usar Redis em produção)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
# ... outras configs de email
```

### Servidores Recomendados

- **Forge:** Gerenciamento simplificado Laravel
- **Vapor:** Serverless Laravel na AWS
- **DigitalOcean:** App Platform ou Droplets
- **AWS:** EC2, RDS, S3
- **Heroku:** Deploy rápido

---

## 📞 Suporte

### Reportar Bugs

Por favor, use o [GitHub Issues](https://github.com/your-company/smart-management/issues) com:

- Descrição clara do problema
- Passos para reproduzir
- Comportamento esperado vs atual
- Screenshots (se aplicável)
- Logs relevantes

### Solicitar Features

Use [GitHub Discussions](https://github.com/your-company/smart-management/discussions) para:

- Propor novas funcionalidades
- Discutir melhorias
- Compartilhar casos de uso
- Sugerir integrações

### Contacto

- **Email:** suporte@smartmanagement.com
- **Website:** https://smartmanagement.com
- **Documentação:** https://docs.smartmanagement.com

---

## 📜 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

```
MIT License

Copyright (c) 2025 InovCorp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🙏 Agradecimentos

### Tecnologias Utilizadas

- [Laravel](https://laravel.com/) - O framework PHP para artesãos da web
- [Vue.js](https://vuejs.org/) - O framework JavaScript progressivo
- [Inertia.js](https://inertiajs.com/) - O monolito moderno
- [Shadcn Vue](https://www.shadcn-vue.com/) - Componentes UI reutilizáveis
- [Tailwind CSS](https://tailwindcss.com/) - Framework CSS utility-first
- [Vee-Validate](https://vee-validate.logaretm.com/) - Validação de formulários Vue
- [Zod](https://zod.dev/) - Schema validation TypeScript-first
- [TanStack Table](https://tanstack.com/table) - Tabelas headless para Vue
- [FullCalendar](https://fullcalendar.io/) - Calendário profissional
- [Vue Sonner](https://vue-sonner.vercel.app/) - Sistema de toasts elegante

### Inspirações

- [Laravel Nova](https://nova.laravel.com/)
- [Filament](https://filamentphp.com/)
- [Odoo](https://www.odoo.com/)
- [Salesforce](https://www.salesforce.com/)

---

## ⭐ Star History

Se este projeto foi útil para você, considere dar uma ⭐!

[![Star History Chart](https://api.star-history.com/svg?repos=your-company/smart-management&type=Date)](https://star-history.com/#your-company/smart-management&Date)

---

<div align="center">

**Desenvolvido com ❤️ por Andersson Hupp**

[⬆ Voltar ao topo](#-smartmanagement)

</div>

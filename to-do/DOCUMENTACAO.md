# Documentação - Sistema de Gerenciamento de Tarefas

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Stack Tecnológica](#stack-tecnológica)
3. [Arquitetura do Sistema](#arquitetura-do-sistema)
4. [Funcionalidades](#funcionalidades)
5. [Instalação e Configuração](#instalação-e-configuração)
6. [Estrutura do Projeto](#estrutura-do-projeto)
7. [Rotas e Endpoints](#rotas-e-endpoints)
8. [Modelos e Banco de Dados](#modelos-e-banco-de-dados)
9. [Frontend e Componentes](#frontend-e-componentes)
10. [Autenticação e Segurança](#autenticação-e-segurança)
11. [Testes](#testes)
12. [Desenvolvimento](#desenvolvimento)
13. [Extensões Futuras](#extensões-futuras)

---

## 🎯 Visão Geral

Este é um sistema completo de gerenciamento de tarefas (To-Do) construído com Laravel 11, Inertia.js e Vue 3. A aplicação oferece uma experiência de usuário moderna e responsiva, permitindo que usuários autenticados gerenciem suas tarefas pessoais com funcionalidades avançadas de filtragem, ordenação e paginação.

### Características Principais

- ✅ CRUD completo de tarefas
- 🔐 Autenticação completa com Laravel Fortify
- 🔒 Autenticação de dois fatores (2FA)
- 🎨 Interface moderna com Vue 3 e Tailwind CSS
- 📱 Design responsivo
- 🔍 Filtros avançados e busca
- 📊 Dashboard com estatísticas
- 🎯 Notificações via toasts (vue-sonner)

---

## 🛠 Stack Tecnológica

### Backend

- **PHP 8.2+**
- **Laravel 12** - Framework PHP
- **Laravel Fortify** - Autenticação e 2FA
- **Inertia.js** - Bridge entre Laravel e Vue
- **SQLite** - Banco de dados (configurável para MySQL/PostgreSQL)

### Frontend

- **Vue 3** - Framework JavaScript
- **TypeScript** - Tipagem estática
- **Inertia.js** - SPA sem API REST
- **Tailwind CSS 4** - Framework CSS
- **Vite** - Build tool
- **vue-sonner** - Sistema de notificações (toasts)
- **Reka UI** - Componentes UI
- **Lucide Vue** - Ícones

### Ferramentas de Desenvolvimento

- **Pest** - Framework de testes PHP
- **ESLint** - Linter JavaScript/TypeScript
- **Prettier** - Formatador de código
- **Laravel Pint** - Formatador PHP

---

## 🏗 Arquitetura do Sistema

### Padrão Arquitetural

A aplicação utiliza o padrão **MVC (Model-View-Controller)** adaptado para uma arquitetura SPA moderna com Inertia.js:

```
┌─────────────────┐
│   Frontend      │
│   (Vue 3)       │
│   ┌───────────┐ │
│   │ Inertia   │ │
│   └─────┬─────┘ │
└─────────┼───────┘
          │
          │ HTTP Requests
          │
┌─────────▼───────┐
│   Backend       │
│   (Laravel)     │
│   ┌───────────┐ │
│   │ Controllers│ │
│   │ Models    │ │
│   │ Middleware│ │
│   └───────────┘ │
└─────────┬───────┘
          │
          │
┌─────────▼───────┐
│   Database      │
│   (SQLite)      │
└─────────────────┘
```

### Fluxo de Dados

1. **Requisição do Usuário**: O usuário interage com componentes Vue
2. **Inertia.js**: Captura a interação e faz requisição HTTP ao Laravel
3. **Controller**: Processa a requisição, valida dados e interage com Models
4. **Model**: Acessa o banco de dados e retorna dados
5. **Response Inertia**: Controller retorna dados via Inertia::render()
6. **Atualização SPA**: Vue atualiza a interface sem recarregar a página

---

## ✨ Funcionalidades

### Autenticação

- ✅ Registro de novos usuários
- ✅ Login/Logout
- ✅ Recuperação de senha
- ✅ Verificação de e-mail
- ✅ Autenticação de dois fatores (2FA)
- ✅ Lembrar-me (Remember Me)

### Gerenciamento de Tarefas

- ✅ **Criar tarefas** com título, descrição, prioridade e data limite
- ✅ **Listar tarefas** com paginação
- ✅ **Editar tarefas** existentes
- ✅ **Visualizar detalhes** de uma tarefa
- ✅ **Completar tarefas** (marcar como concluída)
- ✅ **Excluir tarefas**
- ✅ **Filtros avançados**:
    - Por status (pendente/completa)
    - Por prioridade (baixa/média/alta)
    - Por intervalo de datas
    - Busca por título
- ✅ **Ordenação** por:
    - Data limite
    - Prioridade
    - Título
    - Data de criação
- ✅ **Paginação** configurável

### Dashboard

- ✅ Estatísticas gerais:
    - Total de tarefas
    - Tarefas completadas
    - Tarefas pendentes
    - Tarefas atrasadas
- ✅ Tarefas recentes (últimas 5)
- ✅ Progresso semanal (gráfico)

### Configurações do Usuário

- ✅ Editar perfil (nome, e-mail)
- ✅ Alterar senha
- ✅ Configurar aparência (tema claro/escuro)
- ✅ Gerenciar autenticação de dois fatores

### Feedback ao Usuário

- ✅ Notificações toast para:
    - Sucesso (verde)
    - Erro (vermelho)
    - Aviso (amarelo)
    - Informação (azul)

---

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js 18+ e npm
- SQLite (ou MySQL/PostgreSQL)

### Passo a Passo

#### 1. Clonar o Repositório

```bash
git clone <url-do-repositorio>
cd to-do
```

#### 2. Instalar Dependências PHP

```bash
composer install
```

#### 3. Configurar Ambiente

```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Gerar chave da aplicação
php artisan key:generate
```

#### 4. Configurar Banco de Dados

**Opção A: SQLite (Padrão)**

O arquivo `database/database.sqlite` já está incluído. Certifique-se de que o `.env` contém:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/caminho/absoluto/para/database/database.sqlite
```

**Opção B: MySQL**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

#### 5. Executar Migrations e Seeders

```bash
php artisan migrate --seed
```

Isso criará as tabelas e populará o banco com dados de exemplo.

#### 6. Instalar Dependências JavaScript

```bash
npm install
```

#### 7. Compilar Assets

**Desenvolvimento:**

```bash
npm run dev
```

**Produção:**

```bash
npm run build
```

#### 8. Iniciar Servidor

**Opção A: Servidor PHP Built-in**

```bash
php artisan serve
```

**Opção B: Comando Dev Completo (Laravel + Vite + Queue)**

```bash
composer dev
```

A aplicação estará disponível em `http://localhost:8000`

### Criar Usuário de Teste

Você pode criar um usuário através da interface de registro ou usando o Tinker:

```bash
php artisan tinker
```

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
]);
```

---

## 📁 Estrutura do Projeto

```
to-do/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/              # Controladores de autenticação
│   │   │   ├── Settings/          # Controladores de configurações
│   │   │   ├── DashboardController.php
│   │   │   └── TaskController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       ├── StoreTaskRequest.php
│   │       └── UpdateTaskRequest.php
│   ├── Models/
│   │   ├── Task.php
│   │   └── User.php
│   └── Policies/
│       └── TaskPolicy.php
├── database/
│   ├── migrations/                # Migrations do banco
│   ├── factories/                 # Factories para testes
│   ├── seeders/                   # Seeders
│   └── database.sqlite            # Banco SQLite
├── resources/
│   ├── js/
│   │   ├── components/            # Componentes Vue reutilizáveis
│   │   │   ├── tasks/             # Componentes de tarefas
│   │   │   └── ui/                # Componentes UI (Reka UI)
│   │   ├── composables/           # Composables Vue
│   │   ├── layouts/               # Layouts da aplicação
│   │   ├── pages/                 # Páginas Inertia
│   │   │   ├── auth/              # Páginas de autenticação
│   │   │   ├── settings/          # Páginas de configurações
│   │   │   ├── tasks/             # Páginas de tarefas
│   │   │   ├── Dashboard.vue
│   │   │   └── Welcome.vue
│   │   ├── types/                 # Definições TypeScript
│   │   ├── utils/                 # Utilitários
│   │   ├── app.ts                 # Bootstrap da aplicação
│   │   └── ssr.ts                 # Server-Side Rendering
│   └── css/
│       └── app.css                # Estilos globais
├── routes/
│   ├── web.php                    # Rotas principais
│   ├── auth.php                   # Rotas de autenticação
│   └── settings.php               # Rotas de configurações
├── tests/                         # Testes automatizados
├── public/                        # Arquivos públicos
├── storage/                       # Arquivos de armazenamento
├── vendor/                        # Dependências PHP
├── node_modules/                  # Dependências JavaScript
├── composer.json
├── package.json
├── vite.config.ts
└── tsconfig.json
```

---

## 🛣 Rotas e Endpoints

### Rotas Públicas

| Método | Rota                      | Descrição                      |
| ------ | ------------------------- | ------------------------------ |
| GET    | `/`                       | Página inicial (Welcome)       |
| GET    | `/login`                  | Formulário de login            |
| POST   | `/login`                  | Processar login                |
| GET    | `/register`               | Formulário de registro         |
| POST   | `/register`               | Processar registro             |
| GET    | `/forgot-password`        | Solicitar recuperação de senha |
| POST   | `/forgot-password`        | Enviar e-mail de recuperação   |
| GET    | `/reset-password/{token}` | Formulário de redefinição      |
| POST   | `/reset-password`         | Processar redefinição          |

### Rotas Autenticadas

#### Dashboard

| Método | Rota         | Descrição            |
| ------ | ------------ | -------------------- |
| GET    | `/dashboard` | Dashboard do usuário |

#### Tarefas

| Método | Rota                   | Descrição                    |
| ------ | ---------------------- | ---------------------------- |
| GET    | `/tasks`               | Listar tarefas (com filtros) |
| GET    | `/tasks/create`        | Formulário de criação        |
| POST   | `/tasks`               | Criar nova tarefa            |
| GET    | `/tasks/{id}`          | Visualizar tarefa            |
| GET    | `/tasks/{id}/edit`     | Formulário de edição         |
| PATCH  | `/tasks/{id}`          | Atualizar tarefa             |
| PATCH  | `/tasks/{id}/complete` | Completar tarefa             |
| DELETE | `/tasks/{id}`          | Excluir tarefa               |

#### Configurações

| Método | Rota                   | Descrição            |
| ------ | ---------------------- | -------------------- |
| GET    | `/settings/profile`    | Editar perfil        |
| PATCH  | `/settings/profile`    | Atualizar perfil     |
| DELETE | `/settings/profile`    | Excluir conta        |
| GET    | `/settings/password`   | Alterar senha        |
| PUT    | `/settings/password`   | Atualizar senha      |
| GET    | `/settings/appearance` | Configurar aparência |
| GET    | `/settings/two-factor` | Configurar 2FA       |

### Parâmetros de Query (Tarefas)

A rota `/tasks` aceita os seguintes parâmetros de query:

- `search` - Busca por título
- `status` - Filtrar por status (`pending` ou `completed`)
- `priority` - Filtrar por prioridade (`low`, `medium`, `high`)
- `due_from` - Data inicial (formato: YYYY-MM-DD)
- `due_to` - Data final (formato: YYYY-MM-DD)
- `sort_by` - Campo para ordenação (`due_date`, `priority`, `title`, `created_at`)
- `sort_dir` - Direção (`asc` ou `desc`)
- `per_page` - Itens por página (padrão: 10)

**Exemplo:**

```
/tasks?status=pending&priority=high&sort_by=due_date&sort_dir=asc&per_page=20
```

---

## 🗄 Modelos e Banco de Dados

### Modelo User

```php
App\Models\User
```

**Atributos:**

- `id` - ID único
- `name` - Nome do usuário
- `email` - E-mail (único)
- `password` - Senha (hasheada)
- `email_verified_at` - Data de verificação do e-mail
- `two_factor_secret` - Secret do 2FA
- `two_factor_recovery_codes` - Códigos de recuperação
- `two_factor_confirmed_at` - Data de confirmação do 2FA
- `remember_token` - Token de "lembrar-me"
- `created_at` - Data de criação
- `updated_at` - Data de atualização

**Relações:**

- `hasMany(Task)` - Um usuário tem muitas tarefas

### Modelo Task

```php
App\Models\Task
```

**Atributos:**

- `id` - ID único
- `user_id` - ID do usuário (foreign key)
- `title` - Título da tarefa (obrigatório, max 255)
- `description` - Descrição (opcional)
- `priority` - Prioridade (`low`, `medium`, `high`, padrão: `medium`)
- `due_date` - Data limite (opcional, formato: date)
- `status` - Status (`pending`, `completed`, padrão: `pending`)
- `created_at` - Data de criação
- `updated_at` - Data de atualização

**Relações:**

- `belongsTo(User)` - Uma tarefa pertence a um usuário

### Estrutura das Tabelas

#### users

```sql
id (bigint, primary key)
name (string)
email (string, unique)
email_verified_at (timestamp, nullable)
password (string)
two_factor_secret (text, nullable)
two_factor_recovery_codes (text, nullable)
two_factor_confirmed_at (timestamp, nullable)
remember_token (string, nullable)
created_at (timestamp)
updated_at (timestamp)
```

#### tasks

```sql
id (bigint, primary key)
user_id (bigint, foreign key -> users.id)
title (string)
description (text, nullable)
priority (enum: 'low', 'medium', 'high')
due_date (date, nullable)
status (enum: 'pending', 'completed')
created_at (timestamp)
updated_at (timestamp)
```

**Índices:**

- `user_id` - Índice para consultas por usuário
- `status` - Índice para filtros de status
- `priority` - Índice para filtros de prioridade
- `due_date` - Índice para ordenação e filtros de data

---

## 🎨 Frontend e Componentes

### Estrutura Vue

A aplicação utiliza Vue 3 com Composition API e `<script setup>`.

### Layouts

#### AppLayout.vue

Layout principal que envolve todas as páginas autenticadas.

#### AppSidebarLayout.vue

Layout com sidebar, breadcrumb e área de conteúdo.

### Páginas Principais

#### Dashboard.vue

Exibe estatísticas e tarefas recentes do usuário.

#### tasks/Index.vue

Lista de tarefas com:

- Filtros
- Paginação
- Modal para criar/editar
- Ações de completar/excluir

#### tasks/Show.vue

Visualização detalhada de uma tarefa.

#### tasks/Create.vue / Edit.vue

Formulários dedicados para criação/edição (alternativa ao modal).

### Componentes de Tarefas

#### TaskList.vue

Lista de tarefas com renderização de itens.

#### TaskItem.vue

Item individual de tarefa com ações.

#### TaskForm.vue

Formulário reutilizável para criar/editar tarefas.

#### TaskFilters.vue

Componente de filtros com sincronização de URL.

#### TaskHeader.vue

Cabeçalho da página de tarefas com botões de ação.

### Composables

#### useFlashMessages.ts

Gerencia mensagens flash e exibe toasts:

- Observa `page.props.flash`
- Exibe toasts baseado no tipo (success/error/warning/info)
- Funciona tanto no `onMounted` quanto via `watch` para modais

#### useTaskFilters.ts

Gerencia filtros de tarefas:

- Sincroniza filtros com query parameters da URL
- Permite resetar filtros
- Mantém estado durante navegação

#### useAppearance.ts

Gerencia tema claro/escuro:

- Salva preferência no localStorage
- Aplica tema na inicialização

#### useTwoFactorAuth.ts

Gerencia autenticação de dois fatores.

#### useInitials.ts

Gera iniciais do nome do usuário para avatares.

### Sistema de Notificações

Utiliza `vue-sonner` para exibir toasts:

```typescript
import { toast } from 'vue-sonner';

toast.success('Tarefa criada com sucesso!');
toast.error('Erro ao criar tarefa');
toast.warning('Atenção!');
toast.info('Informação');
```

O componente `Toaster` está registrado globalmente no `app.ts` e renderizado no layout.

---

## 🔐 Autenticação e Segurança

### Laravel Fortify

A aplicação utiliza Laravel Fortify para gerenciar autenticação:

- **Registro**: Validação de e-mail único e senha forte
- **Login**: Suporte a "lembrar-me"
- **Recuperação de Senha**: Tokens seguros via e-mail
- **Verificação de E-mail**: Links assinados
- **2FA**: Autenticação de dois fatores com códigos de recuperação

### Middleware

- `auth` - Protege rotas que requerem autenticação
- `verified` - Requer e-mail verificado
- `guest` - Apenas para usuários não autenticados

### Políticas

#### TaskPolicy

Controla autorização de ações em tarefas:

- Usuários só podem ver/editar/excluir suas próprias tarefas

### Validação

#### StoreTaskRequest

- `title`: obrigatório, string, máximo 255 caracteres
- `description`: opcional, string
- `priority`: deve ser `low`, `medium` ou `high`
- `due_date`: opcional, data, não pode ser anterior a hoje

#### UpdateTaskRequest

Mesmas regras de `StoreTaskRequest`.

### Segurança Adicional

- Senhas são hasheadas com bcrypt
- Tokens CSRF em todos os formulários
- Proteção contra SQL Injection (Eloquent ORM)
- Proteção XSS (Vue escapa automaticamente)
- Rate limiting em rotas sensíveis (ex: reset de senha)

---

## 🧪 Testes

A aplicação utiliza **Pest** como framework de testes.

### Executar Testes

```bash
# Todos os testes
php artisan test

# Ou usando Pest diretamente
vendor/bin/pest

# Com cobertura
php artisan test --coverage
```

### Estrutura de Testes

```
tests/
├── Feature/
│   ├── Auth/              # Testes de autenticação
│   ├── Settings/          # Testes de configurações
│   ├── Tasks/             # Testes de tarefas
│   ├── DashboardTest.php
│   └── ExampleTest.php
└── Unit/
    └── ExampleTest.php
```

### Testes Implementados

#### Autenticação

- ✅ Registro de usuário
- ✅ Login
- ✅ Logout
- ✅ Recuperação de senha
- ✅ Verificação de e-mail
- ✅ Autenticação de dois fatores

#### Dashboard

- ✅ Acesso protegido
- ✅ Exibição de estatísticas

#### Tarefas

- ✅ Listagem com filtros
- ✅ Criação
- ✅ Edição
- ✅ Completar tarefa
- ✅ Exclusão

#### Configurações

- ✅ Atualização de perfil
- ✅ Alteração de senha

### Exemplo de Teste

```php
test('usuário pode criar uma tarefa', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/tasks', [
            'title' => 'Nova Tarefa',
            'priority' => 'high',
            'due_date' => now()->addDay(),
        ]);

    $response->assertRedirect('/tasks');
    $this->assertDatabaseHas('tasks', [
        'title' => 'Nova Tarefa',
        'user_id' => $user->id,
    ]);
});
```

---

## 💻 Desenvolvimento

### Comandos Úteis

```bash
# Desenvolvimento completo (Laravel + Vite + Queue)
composer dev

# Apenas servidor Laravel
php artisan serve

# Apenas Vite (frontend)
npm run dev

# Build para produção
npm run build

# Build com SSR
npm run build:ssr
composer dev:ssr

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rodar migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Criar migration
php artisan make:migration nome_da_migration

# Criar model
php artisan make:model NomeModel

# Criar controller
php artisan make:controller NomeController

# Criar request de validação
php artisan make:request NomeRequest

# Formatar código PHP
./vendor/bin/pint

# Formatar código JavaScript/TypeScript
npm run format

# Linter JavaScript/TypeScript
npm run lint
```

### Workflow de Desenvolvimento

1. **Criar branch**: `git checkout -b feature/nova-funcionalidade`
2. **Desenvolver**: Fazer alterações no código
3. **Testar**: Executar testes e verificar manualmente
4. **Formatar**: Executar formatters (Pint, Prettier)
5. **Commit**: Fazer commit com mensagem descritiva
6. **Push**: Enviar para repositório remoto
7. **Pull Request**: Criar PR para revisão

### Debugging

#### Laravel

- Logs em `storage/logs/laravel.log`
- Usar `dd()` ou `dump()` para debug
- Laravel Pail: `php artisan pail` (em desenvolvimento)

#### Vue/TypeScript

- DevTools do Vue no navegador
- Console do navegador
- Source maps habilitados em desenvolvimento

### Variáveis de Ambiente

Principais variáveis no `.env`:

```env
APP_NAME="Task Flow"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/caminho/para/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

FORTIFY_VIEWS=true
```

---

## 🔮 Extensões Futuras

### Funcionalidades Planejadas

- [ ] **Categorias/Tags**: Organizar tarefas por categorias
- [ ] **Projetos**: Agrupar tarefas em projetos
- [ ] **Colaboração**: Compartilhar tarefas entre usuários
- [ ] **Comentários**: Adicionar comentários em tarefas
- [ ] **Anexos**: Upload de arquivos para tarefas
- [ ] **Lembretes**: Notificações antes da data limite
- [ ] **Recorrência**: Tarefas recorrentes
- [ ] **Exportação**: Exportar tarefas para CSV/PDF
- [ ] **API REST**: Endpoint para integrações externas
- [ ] **Webhooks**: Notificações para sistemas externos
- [ ] **Temas personalizados**: Mais opções de aparência
- [ ] **Modo offline**: Funcionalidade PWA
- [ ] **Busca avançada**: Busca full-text em descrições
- [ ] **Filtros salvos**: Salvar combinações de filtros
- [ ] **Visualizações**: Kanban, calendário, etc.

### Melhorias Técnicas

- [ ] Implementar autorização completa com `TaskPolicy`
- [ ] Adicionar cache para consultas frequentes
- [ ] Implementar filas para e-mails
- [ ] Adicionar testes E2E com Playwright/Cypress
- [ ] Melhorar acessibilidade (ARIA, navegação por teclado)
- [ ] Otimizar bundle size do frontend
- [ ] Implementar lazy loading de componentes
- [ ] Adicionar service worker para PWA
- [ ] Melhorar SEO com meta tags dinâmicas
- [ ] Implementar internacionalização (i18n)

---

## 📝 Notas Adicionais

### Decisões de Design

1. **Inertia.js**: Escolhido para evitar duplicação de lógica entre backend e frontend, mantendo validação no Laravel.

2. **Vue 3 + TypeScript**: Para melhor DX (Developer Experience) e type safety.

3. **SQLite por padrão**: Facilita desenvolvimento local, mas suporta MySQL/PostgreSQL.

4. **vue-sonner**: Biblioteca moderna e acessível para toasts, melhor que alternativas.

5. **Reka UI**: Componentes acessíveis e customizáveis, baseados em Radix UI.

### Problemas Conhecidos

- Toasts podem aparecer duplicados em alguns cenários (solução: adicionar guarda no composable)
- Modal não reseta formulário ao fechar (melhoria futura)
- Filtros não persistem entre sessões (melhoria futura)

### Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature
3. Faça commit das mudanças
4. Push para a branch
5. Abra um Pull Request

---

## 📞 Suporte

Para dúvidas ou problemas:

- Abra uma issue no repositório
- Consulte a documentação do Laravel: https://laravel.com/docs
- Consulte a documentação do Inertia: https://inertiajs.com
- Consulte a documentação do Vue: https://vuejs.org

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

**Última atualização**: 2025

## Task Flow (Laravel + Inertia + Vue 3)

> 📚 **Documentação Completa**: Para uma documentação detalhada e completa do projeto, consulte [DOCUMENTACAO.md](./DOCUMENTACAO.md)

### Visão Geral

- **Stack**: Laravel 11, Inertia.js, Vue 3 (TypeScript), Vite, Laravel Fortify, SQLite, Tailwind (via app.css), `vue-sonner` para toasts.
- **Domínio**: Aplicação de tarefas com CRUD, filtros, paginação, completar e excluir; autenticação e configurações de usuário (perfil, senha, 2FA).

### Arquitetura

#### Backend (Laravel)

- **Rotas**:
    - `routes/web.php`: rotas principais, incluindo `dashboard` e o grupo autenticado para tarefas (`/tasks`).
    - `routes/auth.php`: login, registro, reset de senha, verificação de email (Fortify).
    - `routes/settings.php`: páginas de perfil, senha, aparência e 2FA.

- **Controladores** (`app/Http/Controllers`):
    - `TaskController`: lista, cria, mostra, edita, atualiza, completa e exclui tarefas. Retorna páginas Inertia e usa redirects com `flash` em operações de escrita.
    - `DashboardController`: fornece dados/estatísticas do usuário autenticado e renderiza `Dashboard`.
    - Pasta `Auth` (Fortify) e `Settings`: controladores para autenticação e páginas de configurações.

- **Middleware**:
    - `app/Http/Middleware/HandleInertiaRequests.php`: compartilha `props` globais com o front (ex.: `auth.user`, `sidebarOpen`, `flash`).

- **Requests (validação)**:
    - `StoreTaskRequest` e `UpdateTaskRequest`: regras de validação para criação/edição de tarefas.

- **Modelos**:
    - `Task`: `title`, `description`, `priority`, `due_date`, `status`, `user_id`. Relação `belongsTo(User)`.
    - `User`: integra Fortify/2FA e possui relação `hasMany(Task)`.

- **Banco de Dados**:
    - SQLite em `database/database.sqlite` (migrations e seeds disponíveis).

#### Frontend (Inertia + Vue 3 + TS)

- **Bootstrap**: `resources/js/app.ts` cria a app Inertia, registra `Toaster` (vue-sonner) e monta a aplicação.
- **SSR**: `resources/js/ssr.ts` (opcional) com `@inertiajs/vue3/server`.
- **Layouts**:
    - `resources/js/layouts/app/AppSidebarLayout.vue`: layout com sidebar, breadcrumb e `Toaster` global (posição top-right).
    - `resources/js/layouts/AppLayout.vue`: casca que delega para o layout sidebar.
- **Páginas** (`resources/js/pages`) principais:
    - `Dashboard.vue`: painel do usuário autenticado.
    - `tasks/Index.vue`: lista tarefas com filtros, paginação e modal para criar/editar.
    - `tasks/Show.vue`, `tasks/Create.vue`, `tasks/Edit.vue` (algumas mantidas para navegação direta).
    - Auth/Settings: páginas de login, registro, perfil, senha, 2FA.
- **Componentes** (exemplos):
    - `components/tasks/TaskList.vue`, `TaskItem.vue`, `TaskForm.vue`, `Modal.vue`, `TaskFilters.vue`, `TaskHeader.vue`.
    - Infra de layout: `AppShell.vue`, `AppSidebar.vue`, `AppContent.vue`, etc.
- **Composables**:
    - `useFlashMessages.ts`: observa `page.props.flash` e dispara toasts conforme mensagens de sucesso/erro/aviso/info.
    - `useTaskFilters.ts`: sincroniza filtros com a URL e a listagem.

### Funcionalidades

- **Autenticação**: login, registro, logout, recuperação de senha, verificação de e-mail, 2FA (via Fortify).
- **Tarefas**:
    - Listagem com filtros (status, prioridade, data), ordenação e paginação.
    - Criação e edição via modal dentro do `Index` (sem navegação para outra página).
    - Completar tarefa (`PATCH /tasks/{id}/complete`).
    - Exclusão de tarefa.
- **Feedback ao Usuário**:
    - Mensagens flash (success/error/warning/info) exibidas como toasts (`vue-sonner`).

### Fluxos e Implementação

#### Mensagens Flash (Backend → Frontend)

1. Operações de escrita retornam redirect com `with('success', '...')` (ou `error`).
2. `HandleInertiaRequests` compartilha `flash` nos props Inertia.
3. No front, `useFlashMessages`:
    - Executa no `onMounted` da página para mensagens iniciais.
    - Observa reativamente `page.props.flash` via `watch` para cenários onde a página não desmonta (ex.: usar modal em `tasks/Index.vue`).

Observação: este watcher evita o problema de toasts não aparecerem após criar/editar no modal, já que a rota não muda e a página permanece montada.

#### Tarefas (UI)

- `tasks/Index.vue` carrega lista + filtros + paginação.
- Botão “Nova Tarefa” abre `Modal` com `TaskForm`.
- `TaskForm` usa `useForm` (Inertia) e dispara `POST /tasks` ou `PATCH /tasks/{id}`. Ao sucesso, emite `saved` para o Index fechar o modal.
- Ações de completar/excluir usam `router.patch/delete` do Inertia, mantendo a experiência SPA.

### Estrutura do Projeto (resumo)

- `app/Http/Controllers`: controladores (Tasks, Dashboard, Auth, Settings).
- `app/Http/Middleware`: middleware Inertia para props compartilhados.
- `app/Http/Requests`: validações de tarefas e perfil/senha.
- `app/Models`: modelos `Task` e `User`.
- `resources/js`: front-end (layouts, páginas, componentes, composables, types, rotas helpers).
- `routes`: arquivos de rotas web, auth e settings.
- `database`: migrations, seeds e MySQL.
- `tests`: feature tests para auth, dashboard, settings e tarefas.

### Como Executar

1. Requisitos: PHP 8.2+, Composer, Node 18+, MySQL.
2. Backend
    - Instalar deps: `composer install`
    - Copiar `.env` e ajustar se necessário; garantir `DB_CONNECTION=mysql`
    - Configurar o MySQL user e password no env
    - Migrar e popular: `php artisan migrate --seed`
    - Servir: `php artisan serve`
3. Frontend
    - Instalar deps: `npm install`
    - Rodar em dev: `npm run dev`
    - Acessar: `http://localhost:8000` (ou conforme porta do servidor PHP)

### Testes

- Rodar suite: `php artisan test` (ou `vendor\bin\pest`/`vendor\bin\phpunit`).
- Cobrem: autenticação (inclui 2FA), dashboard protegido, update de perfil/senha e cenários de tarefas (filtros, completar/excluir).

### Decisões de Projeto

- **Inertia** para experiência SPA sem duplicar validação/roteamento em duas stacks distintas.
- **Fortify** para prover autenticação e 2FA com segurança e padrões Laravel.
- **Vue + TS** para tipagem e DX; `script setup` simplifica componentes.
- **`vue-sonner`** para toasts acessíveis e consistentes, integrados ao layout via `Toaster`.

### Extensões Futuras

- Autorizações em `TaskPolicy` (por dono) e uso nas actions do `TaskController`.
- Melhorias de UX no modal (ex.: foco, reset de formulário ao abrir/fechar, mensagens inline).
- Evitar toasts duplicados adicionando guarda simples no composable (memorizar último `flash` processado) ou limpando flash após exibição no front.
- Filtros adicionais (texto livre por descrição, intervalo de criação) e views salvas.

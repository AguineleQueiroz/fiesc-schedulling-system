# FIESC Scheduling System

Sistema web para gerenciamento de agendamentos de atendimentos e disponibilidade de agenda, desenvolvido como parte do processo seletivo **01873/2026 — Desenvolvedor Full Stack Pleno (SENAI/SC — Direção Regional)**.

---

## Como rodar o projeto

**Requisito único:** Docker e Docker Compose instalados na máquina.

```bash
# 1. Copie o arquivo de ambiente
cp .env.example .env

# 2. Suba os containers (Laravel + MySQL)
./vendor/bin/sail up -d

# 3. Execute as migrations e o seeder do usuário admin
./vendor/bin/sail artisan migrate --seed

# 4. Instale as dependências JS e compile os assets
./vendor/bin/sail npm install && ./vendor/bin/sail npm run build

# 5. Acesse a aplicação
# http://localhost
```

### Credenciais do administrador padrão

| Campo | Valor |
|-------|-------|
| E-mail | `admin@fiesc.local` |
| Senha | `admin1234` |

> O usuário é criado pelo `AdminUserSeeder`. Para recriar do zero: `./vendor/bin/sail artisan migrate:fresh --seed`

---

## Stack utilizada

| Camada | Tecnologia | Justificativa |
|--------|-----------|---------------|
| Backend | Laravel 12 / PHP 8.3 | Framework maduro, scaffolding de autenticação pronto via Breeze, ampla adoção no mercado |
| Banco de dados | MySQL 8 | Suficiente para o escopo; PostgreSQL seria over-engineering neste caso |
| Frontend | Blade + JavaScript vanilla modular | O escopo não justifica Vue/React; usar JS puro demonstra diretamente os "conhecimentos sólidos de JS" exigidos pelo edital (RQNF1) |
| Autenticação | Laravel Breeze (stack Blade + Alpine) | Scaffolding pronto de login/logout; Alpine.js é utilizado apenas onde o Breeze já o emprega (ex.: dropdown de menu) |
| Containerização | Docker via Laravel Sail | Garante que o avaliador sobe o projeto com poucos comandos, sem precisar configurar PHP/MySQL localmente |
| Build de assets | Vite | Padrão do Laravel 12; múltiplos entry points declarados, um por página com interação JS |

### Configuração simplificada de drivers

`SESSION_DRIVER=file`, `CACHE_STORE=file` e `QUEUE_CONNECTION=sync` foram escolhidos para evitar dependência de tabelas ou serviços extras (Redis, Memcached) desnecessários para o escopo desta aplicação.

---

## Arquitetura

### Backend

```
app/
├── Enums/
│   ├── UserRole.php            # admin | attendant
│   └── AppointmentStatus.php  # scheduled | cancelled
├── Models/
│   ├── User.php                # helpers: isAdmin(), isAttendant(), canEdit()
│   ├── Availability.php
│   └── Appointment.php
├── Policies/
│   ├── UserPolicy.php          # controla quem cria, edita e exclui usuários
│   └── AvailabilityPolicy.php  # somente admin gerencia disponibilidade
├── Http/
│   ├── Controllers/
│   │   ├── UserController.php
│   │   ├── AvailabilityController.php
│   │   └── AppointmentController.php # slots, criação, cancelamento e listagem
│   └── Requests/
│       ├── StoreUserRequest.php
│       ├── UpdateUserRequest.php
│       ├── StoreAvailabilityRequest.php
│       ├── UpdateAvailabilityRequest.php
│       └── StoreAppointmentRequest.php
├── Services/
│   ├── UserService.php           # criação, edição, exclusão e listagem de usuários
│   ├── AvailabilityService.php   # CRUD de disponibilidade + validação de sobreposição
│   └── AppointmentService.php    # cálculo de slots livres, criação e cancelamento
└── Repositories/
    └── AppointmentRepository.php # queries complexas/reutilizáveis de agendamentos
```

**Padrões aplicados:** FormRequest (validação e autorização de entrada), Service (regras de negócio), Policy (autorização de recursos), Repository (aplicado pontualmente apenas onde a query é não trivial — cálculo de horários livres e filtragem de agendamentos por perfil).

#### Padrão de camadas — decisão arquitetural

A divisão de responsabilidades segue uma hierarquia estrita:

```
Controller  →  Service  →  Eloquent (direto)
                       →  Repository  (apenas queries complexas/reutilizáveis)
```

**Controller:** exclusivamente coordenação HTTP — recebe request, delega ao Service, retorna response. Nunca toca Eloquent diretamente.

**Service:** contém a regra de negócio e pode usar Eloquent diretamente para operações simples (create, update, delete de um único modelo). Também injeta o Repository quando a query é complexa ou precisa ser reutilizada em mais de um lugar.

**Repository (Query Object):** encapsula apenas as queries que têm complexidade real — filtragem condicional por perfil, joins implícitos via relacionamentos, ou queries chamadas de mais de um Service. Não existe interface de Repository: a simplificação é intencional dado o escopo do projeto. Criar interfaces abstratas sem múltiplas implementações concretas seria over-engineering para uma aplicação de processo seletivo.

**Mapeamento Service → agregado de domínio:**

| Service | Model dono | Responsabilidade |
|---------|-----------|-----------------|
| `UserService` | `User` | CRUD + regra de role |
| `AvailabilityService` | `Availability` | CRUD + validação de overlap |
| `AppointmentService` | `Appointment` | Cálculo de slots livres, criação, cancelamento |

`Schedule` não é um agregado — não existe tabela `schedules`. O conceito de "agenda" é apenas o nome que a UI dá à junção visual de `Availability` e `Appointment`. Por isso não existe `ScheduleService`: o cálculo de slots livres pertence ao `AppointmentService`, que lê `Availability` diretamente (cross-model read, sem injetar `AvailabilityService` — evita acoplamento entre services), e delega ao Repository apenas a query de agendamentos já existentes.

Exemplos concretos desta divisão:
- `AppointmentRepository::listForUser()` — filtra por role (admin vê todos, atendente vê só os seus) → Repository justificado pela regra condicional + reutilização.
- `AppointmentRepository::scheduledForAttendantOnDate()` — usada por `AppointmentService::availableSlots()` para calcular slots livres; query com critério de data e atendente → Repository justificado pela especificidade.
- `AvailabilityService::hasOverlap()` — query Eloquent inline no Service, pois é a própria regra de negócio (validação de sobreposição); não há reutilização em outro Service.
- `AppointmentService::create()` / `cancel()` — Eloquent direto no Service; operações triviais que não justificam Repository.

### Frontend

```
resources/js/
├── app.js                    # entry global: Alpine.js
├── bootstrap.js              # configuração do Axios (CSRF, headers)
├── core/
│   ├── http.js               # wrapper de fetch (CSRF, parse de erro, status codes)
│   ├── notify.js             # notificações de sucesso/erro ao usuário (RQNF3)
│   └── dom.js                # helpers: clearErrors, showErrors, formData
├── modules/
│   ├── users/
│   │   ├── api.js            # chamadas HTTP do módulo de usuários
│   │   ├── users.index.js    # lógica da listagem (modal de exclusão, AJAX delete)
│   │   └── users.form.js     # lógica do formulário (criação e edição via AJAX)
│   ├── availability/
│   │   ├── api.js
│   │   └── availability.form.js  # CRUD de disponibilidade em página única
│   └── appointments/
│       ├── api.js
│       ├── appointments.create.js    # busca de slots + criação de agendamento
│       └── appointments.list.js      # cancelamento de agendamento com modal
```

**Princípio:** nenhuma Blade contém `<script>` com lógica de negócio. As views entregam apenas HTML com atributos `data-*` e um `@vite([...])` apontando para o entry point da página. Toda interação dinâmica parte dos módulos JS.

### Endpoints

| Método | URI | Descrição |
|--------|-----|-----------|
| `GET` | `/users` | Listagem de usuários (Blade) |
| `POST` | `/users` | Criar usuário (JSON 201) |
| `PUT` | `/users/{id}` | Editar usuário (JSON 200) |
| `DELETE` | `/users/{id}` | Excluir usuário (JSON 204) |
| `GET` | `/availabilities` | Listagem de disponibilidades (Blade) |
| `GET` | `/availabilities/attendant/{id}` | Disponibilidades por atendente (JSON) |
| `POST` | `/availabilities` | Cadastrar disponibilidade (JSON 201) |
| `PUT` | `/availabilities/{id}` | Editar disponibilidade (JSON 200) |
| `DELETE` | `/availabilities/{id}` | Excluir disponibilidade (JSON 204) |
| `GET` | `/appointments` | Listagem de agendamentos (Blade) |
| `GET` | `/appointments/create` | Formulário de novo agendamento (Blade) |
| `GET` | `/appointments/available-slots` | Slots disponíveis para atendente + data (JSON) |
| `POST` | `/appointments` | Criar agendamento (JSON 201) |
| `DELETE` | `/appointments/{id}` | Cancelar agendamento (JSON 204) |

Todos os endpoints retornam HTTP status codes coerentes em cenários de sucesso e erro (200, 201, 204, 400, 401, 403, 404, 422), conforme exigido pelo RQNF2.

---

## Decisões sobre pontos não especificados no edital

Os itens abaixo não estavam detalhados nos requisitos. A interpretação adotada em cada caso está documentada aqui para transparência — e deve ser considerada parte da avaliação de **Análise e Síntese** e **Planejamento e Organização**.

### 1. Contradição aparente no RQF1.1 — "lista igual para todos" vs. "atendente vê apenas listagem"

**Interpretação adotada:** o *conteúdo* da lista é o mesmo para todos os perfis (mesmos usuários, mesmas colunas). O que varia são as *ações disponíveis*: admin enxerga botões de editar qualquer usuário e de excluir; atendente enxerga apenas o botão de editar o próprio registro.

### 2. Risco de escalonamento de privilégio na edição do próprio perfil

O edital permite que o atendente edite o próprio usuário, mas não especifica se o campo "Tipo de Usuário" é editável neste contexto. Permitir que o atendente altere o próprio role para `admin` seria uma falha grave de segurança.

**Decisão:** a edição do campo `role` é bloqueada sempre que o usuário autenticado está editando a si mesmo, independentemente do seu perfil. Apenas um admin pode alterar o role de *outro* usuário. Isso é tratado em `UserService::update()` e reforçado na view `users/edit.blade.php`, que oculta o campo `role` quando o usuário edita a si mesmo.

### 3. Troca de senha fora do fluxo de edição de usuário

O requisito 1.3 (Edição de Usuários) especifica que a tela de edição deve reaproveitar os mesmos campos da tela de inserção (1.2), exceto e-mail e senha. Essa exclusão foi interpretada como delimitada ao escopo do formulário de edição de dados cadastrais — não como uma restrição de que a senha seja imutável no sistema como um todo.

Essa distinção é necessária porque o edital não descreve nenhum outro mecanismo de alteração de senha (como recuperação por e-mail), e a aplicação não possui um serviço de envio de e-mail configurado. Sem um fluxo alternativo, o usuário ficaria permanentemente preso à senha definida em sua criação, o que comprometeria a usabilidade do sistema — um dos critérios de avaliação explicitamente listados no edital.

**Decisão:** foi implementada uma tela de autoatendimento ("Alterar Senha"), isolada do CRUD de usuários, onde o usuário logado pode alterar exclusivamente a própria senha mediante confirmação da senha atual. Essa tela:

- não faz parte do formulário de edição de usuário do RQF1;
- não permite que um administrador altere a senha de terceiros;
- não expõe nenhum campo de senha no formulário de edição de usuário (1.3), que permanece restrito a Nome e Tipo de Usuário, conforme a leitura literal do requisito.

Essa separação de responsabilidades — edição de dados cadastrais (RQF1) versus segurança da própria conta (funcionalidade complementar) — foi a forma encontrada de atender tanto à regra explícita do edital quanto à exigência implícita de usabilidade, dado que nenhum requisito determina que a senha deva permanecer imutável após a criação do usuário.

### 4. Granularidade dos slots de agendamento

O edital não define de quanto em quanto tempo os horários devem ser oferecidos na consulta de disponibilidade.

**Decisão:** 30 minutos, configurável em `config/scheduling.php` (`slot_duration_minutes`). Alterar o valor nesse arquivo impacta automaticamente o cálculo de todos os slots gerados pelo `ScheduleService`.

### 5. Tela de criação efetiva do agendamento

O RQF2 detalha o cadastro de disponibilidade e a consulta de horários livres, mas não a tela onde o agendamento é de fato criado.

**Decisão:** foi implementado o fluxo completo em `/schedule/create`: seleção de atendente → seleção de data → carregamento dinâmico (via AJAX) dos slots livres para aquele atendente naquele dia → preenchimento de dados do cliente (nome e telefone, como mock) → confirmação. O slot escolhido é imediatamente bloqueado para novas consultas.

### 6. Cancelamento de agendamento

A introdução do documento menciona cancelamento, mas os RQFs não o detalham.

**Decisão:** a listagem de agendamentos (`/schedule`) exibe um botão "Cancelar" para cada agendamento com status `scheduled`. A ação chama `DELETE /appointments/{id}` via AJAX, muda o status para `cancelled` no banco e atualiza a linha na tabela sem recarregar a página. O horário fica disponível novamente para novos agendamentos.

### 7. Sobreposição de janelas de disponibilidade

O edital exige apenas "hora final > hora inicial", sem tratar janelas conflitantes do mesmo atendente no mesmo dia.

**Decisão:** o `AvailabilityService::hasOverlap()` valida, antes de criar ou editar qualquer janela, se o intervalo informado se sobrepõe a alguma janela já cadastrada para o mesmo atendente no mesmo dia. Em caso de conflito, retorna HTTP 422 com mensagem descritiva.

### 8. Garantia de existência de um usuário administrador

O edital não especifica como o primeiro admin é criado, já que o cadastro público foi removido.

**Decisão:** o `AdminUserSeeder` cria o usuário `admin@fiesc.local` na primeira execução de `migrate --seed`. O seeder usa `firstOrCreate`, portanto é idempotente e seguro de rodar múltiplas vezes.

### 9. Remoção de fluxos do Breeze fora do escopo

O scaffolding do Laravel Breeze inclui por padrão: registro público, recuperação de senha por e-mail, verificação de e-mail, confirmação de senha e exclusão da própria conta. Todos exigem configuração de mailer real e fogem do escopo dos requisitos.

**Decisão:** esses fluxos foram completamente removidos — controllers, views, rotas e testes associados. Foram mantidos apenas: login, logout e troca de senha autenticada (`PUT /password`).

---

## Regras de negócio por perfil

| Ação | Admin | Atendente |
|------|-------|-----------|
| Listar usuários | ✅ | ✅ |
| Criar usuário | ✅ | ❌ |
| Editar qualquer usuário | ✅ | ❌ |
| Editar o próprio usuário | ✅ (sem alterar o próprio role) | ✅ (sem alterar o próprio role) |
| Excluir usuário (exceto si mesmo) | ✅ | ❌ |
| Gerenciar disponibilidade | ✅ | ❌ (somente visualiza) |
| Criar agendamento | ✅ | ✅ |
| Ver agendamentos | ✅ (todos) | ✅ (apenas os próprios) |
| Cancelar agendamento | ✅ (qualquer) | ✅ (apenas os próprios) |

---

## Testes automatizados

```bash
./vendor/bin/sail artisan test
```

A suite cobre os fluxos de autenticação (login com credenciais válidas e inválidas, logout) e troca de senha. Para rodar em modo verboso:

```bash
./vendor/bin/sail artisan test --verbose
```

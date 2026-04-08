# Guild Engine — Propuesta Arquitectónica (MDA)

## Tabla de contenidos

1. [Overview](#1-overview)
2. [Model Map](#2-model-map)
3. [Enums](#3-enums)
4. [Estructura de carpetas](#4-estructura-de-carpetas)
5. [Componentes por dominio](#5-componentes-por-dominio)
6. [Use Cases](#6-use-cases)
7. [Application Services](#7-application-services)
8. [Permission Checking](#8-permission-checking)
9. [Flujos clave](#9-flujos-clave)
10. [Decisiones de diseño](#10-decisiones-de-diseño)

---

## 1. Overview

**Guild Engine** es una API REST en Laravel que centraliza la gestión de gremios de videojuegos: miembros, roles, eventos, DKP, donaciones e integración con Discord.

**Stack**:
- Laravel (API, sin Blade)
- Laravel Passport (OAuth2, Password Grant)
- MySQL
- Laravel Queues (para notificaciones Discord asíncronas)

**Arquitectura**: Model Domain Architecture (MDA) — ver `MDA.md`.

---

## 2. Model Map

Todos los modelos viven en `app/Models/Main/` (una sola conexión de base de datos).

### User
**Tabla**: `users`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| password | string | bcrypt |
| created_at / updated_at | timestamp | |

**Relaciones**:
- `hasMany(GuildMember::class)` — participa en varios gremios
- `hasMany(DkpTransaction::class, 'target_user_id')` — DKP recibido
- `hasMany(DkpTransaction::class, 'actor_user_id')` — DKP otorgado
- `hasMany(Donation::class)` — donaciones registradas
- `hasMany(AuditLog::class, 'actor_user_id')` — acciones auditadas
- `hasMany(EventRsvp::class)` — RSVPs a eventos

---

### Guild
**Tabla**: `guilds`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | string | |
| description | text nullable | |
| game | string | |
| is_public | boolean | default true |
| leader_user_id | FK → users | denormalizado para consultas rápidas |
| dkp_currency_name | string | default "DKP" (HU-22) |
| discord_webhook_url | string nullable | (HU-30) |
| discord_advance_notice_minutes | integer nullable | (HU-31) |
| created_at / updated_at | timestamp | |

**Relaciones**:
- `hasMany(GuildMember::class)`
- `hasMany(GuildRole::class)`
- `hasMany(Event::class)`
- `hasMany(DkpTransaction::class)`
- `hasMany(Donation::class)`
- `hasMany(AuditLog::class)`
- `belongsTo(User::class, 'leader_user_id')`

---

### GuildMember
**Tabla**: `guild_members`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| user_id | FK → users | |
| guild_role_id | FK → guild_roles | rol activo |
| status | enum | ver GuildMemberStatus |
| invited_by_user_id | FK → users nullable | solo para invitaciones |
| joined_at | timestamp nullable | se asigna al activar |
| created_at / updated_at | timestamp | |

**Unique**: `(guild_id, user_id)`

---

### GuildRole
**Tabla**: `guild_roles`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| name | string | ej: "Líder", "Oficial", "Miembro" |
| is_system | boolean | true = no se puede eliminar |
| created_at / updated_at | timestamp | |

**Relaciones**:
- `belongsTo(Guild::class)`
- `hasMany(GuildMember::class)`
- `belongsToMany(Permission::class, 'guild_role_permissions')`

Los roles de sistema creados automáticamente al crear un gremio son: **Líder**, **Oficial**, **Miembro**. El rol Líder es inmutable (no se pueden editar sus permisos).

---

### Permission
**Tabla**: `permissions`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| slug | string unique | ej: `manage_events`, `manage_dkp` |
| name | string | nombre legible, ej: "Gestionar eventos" |
| created_at | timestamp | |

> Tabla de catálogo, **solo lectura en runtime**. Sus filas son fijas y se poblan en un seeder (`PermissionSeeder`). No existe Repository con `create()` público — los permisos no se crean desde la aplicación, solo se asignan a roles.

**Relaciones**:
- `belongsToMany(GuildRole::class, 'guild_role_permissions')`

---

### guild_role_permissions *(pivot)*
**Tabla**: `guild_role_permissions`

| Campo | Tipo | Notas |
|---|---|---|
| guild_role_id | FK → guild_roles | |
| permission_id | FK → permissions | |

**PRIMARY KEY**: `(guild_role_id, permission_id)`

> Sin timestamps ni columnas extra. La relación es pura asignación.

---

### Event
**Tabla**: `events`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| created_by_user_id | FK → users | |
| title | string | |
| description | text nullable | |
| starts_at | datetime | |
| max_attendees | integer nullable | solo informativo (HU-18) |
| status | enum | ver EventStatus |
| discord_notified_creation | boolean | default false |
| discord_reminder_sent_at | timestamp nullable | |
| created_at / updated_at | timestamp | |

---

### EventRsvp
**Tabla**: `event_rsvps`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| event_id | FK → events | |
| user_id | FK → users | |
| response | enum | ver RsvpResponse |
| attended | boolean nullable | registrado post-evento (HU-19) |
| created_at / updated_at | timestamp | |

**Unique**: `(event_id, user_id)`

---

### DkpTransaction
**Tabla**: `dkp_transactions`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| target_user_id | FK → users | miembro que recibe/pierde DKP |
| actor_user_id | FK → users | quien realiza la acción |
| amount | integer | positivo = otorgar, negativo = descontar |
| balance_after | integer | snapshot del saldo resultante |
| reason | string | obligatorio |
| created_at | timestamp | sin updated_at — registro inmutable |

---

### DkpBalance
**Tabla**: `dkp_balances`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| user_id | FK → users | |
| balance | integer | total materializado |
| updated_at | timestamp | |

**Unique**: `(guild_id, user_id)`

> Saldo materializado: se actualiza atómicamente con cada `DkpTransaction`. Evita recalcular `SUM()` en cada lectura y sirve de guarda para la regla "no puede bajar de 0".

---

### Donation
**Tabla**: `donations`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| user_id | FK → users | donante |
| amount | integer | moneda del juego |
| note | string nullable | |
| status | enum | ver DonationStatus |
| reviewed_by_user_id | FK → users nullable | |
| reviewed_at | timestamp nullable | |
| created_at / updated_at | timestamp | |

---

### AuditLog
**Tabla**: `audit_logs`

| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| guild_id | FK → guilds | |
| actor_user_id | FK → users | quien realizó la acción |
| target_user_id | FK → users nullable | afectado |
| event_type | string | ej: `dkp.granted`, `donation.approved` |
| payload | JSON | snapshot completo del contexto |
| created_at | timestamp | sin updated_at — registro inmutable |

> Append-only. El `payload` captura el estado completo en el momento de la acción (montos, motivos, saldo antes/después), por lo que no requiere joins para reconstruir el historial.

---

## 3. Enums

```php
// app/Enums/GuildMemberStatus.php
enum GuildMemberStatus: string {
    case Active         = 'active';
    case PendingRequest = 'pending_request';
    case PendingInvite  = 'pending_invite';
    case Rejected       = 'rejected';
    case Kicked         = 'kicked';
    case Left           = 'left';
}

// app/Enums/EventStatus.php
enum EventStatus: string {
    case Scheduled  = 'scheduled';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';
}

// app/Enums/DonationStatus.php
enum DonationStatus: string {
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}

// app/Enums/RsvpResponse.php
enum RsvpResponse: string {
    case Confirmed = 'confirmed';
    case Declined  = 'declined';
    case Tentative = 'tentative';
}

// app/Enums/GuildPermission.php
enum GuildPermission: string {
    case ManageEvents        = 'manage_events';
    case ApproveMembers      = 'approve_members';
    case InviteMembers       = 'invite_members';
    case KickMembers         = 'kick_members';
    case ManageDkp           = 'manage_dkp';
    case ManageDonations     = 'manage_donations';
    case RegisterAttendance  = 'register_attendance';
    case ViewAuditLog        = 'view_audit_log';
    case ManageRoles         = 'manage_roles';
    case TransferLeadership  = 'transfer_leadership';
}
```

**Permisos por rol de sistema** (HU-13):

| Permiso                  | Líder | Oficial | Miembro |
|--------------------------|:-----:|:-------:|:-------:|
| manage_events            | ✓     | ✓       |         |
| approve_members          | ✓     | ✓       |         |
| invite_members           | ✓     | ✓       |         |
| kick_members             | ✓     | ✓       |         |
| manage_dkp               | ✓     | ✓       |         |
| manage_donations         | ✓     | ✓       |         |
| register_attendance      | ✓     | ✓       |         |
| view_audit_log           | ✓     | ✓       |         |
| manage_roles             | ✓     |         |         |
| transfer_leadership      | ✓     |         |         |

---

## 4. Estructura de carpetas

```
app/
│
├── Actions/
│   ├── User/
│   │   └── CreateUserAction.php
│   ├── Guild/
│   │   ├── CreateGuildAction.php
│   │   └── UpdateGuildAction.php
│   ├── GuildMember/
│   │   ├── CreateMemberRequestAction.php
│   │   ├── CreateMemberInviteAction.php
│   │   ├── ApproveMemberAction.php
│   │   ├── RejectMemberAction.php
│   │   ├── KickMemberAction.php
│   │   └── UpdateMemberRoleAction.php
│   ├── GuildRole/
│   │   ├── CreateGuildRoleAction.php
│   │   └── SyncRolePermissionsAction.php
│   ├── Event/
│   │   ├── CreateEventAction.php
│   │   ├── CancelEventAction.php
│   │   └── CompleteEventAction.php
│   ├── EventRsvp/
│   │   ├── UpsertEventRsvpAction.php
│   │   └── RegisterAttendanceAction.php
│   ├── DkpTransaction/
│   │   ├── GrantDkpAction.php
│   │   └── DeductDkpAction.php
│   ├── DkpBalance/
│   │   └── UpdateDkpBalanceAction.php
│   ├── Donation/
│   │   ├── CreateDonationAction.php
│   │   ├── ApproveDonationAction.php
│   │   └── RejectDonationAction.php
│   └── AuditLog/
│       └── CreateAuditLogAction.php
│
├── ApplicationServices/
│   ├── Auth/
│   │   └── RegisterApplicationService.php
│   ├── Guild/
│   │   └── CreateGuildApplicationService.php
│   ├── GuildMember/
│   │   └── TransferLeadershipApplicationService.php
│   ├── Event/
│   │   └── CreateEventApplicationService.php
│   ├── DkpTransaction/
│   │   ├── GrantDkpApplicationService.php
│   │   └── DeductDkpApplicationService.php
│   └── Donation/
│       └── ReviewDonationApplicationService.php
│
├── DTO/
│   ├── User/
│   │   └── CreateUserDTO.php
│   ├── Guild/
│   │   ├── CreateGuildDTO.php
│   │   └── UpdateGuildDTO.php
│   ├── GuildMember/
│   │   ├── CreateMemberRequestDTO.php
│   │   ├── CreateMemberInviteDTO.php
│   │   └── UpdateMemberRoleDTO.php
│   ├── GuildRole/
│   │   ├── CreateGuildRoleDTO.php
│   │   └── UpdateGuildRoleDTO.php
│   ├── Event/
│   │   ├── CreateEventDTO.php
│   │   └── RegisterAttendanceDTO.php
│   ├── EventRsvp/
│   │   └── UpsertRsvpDTO.php
│   ├── DkpTransaction/
│   │   ├── GrantDkpDTO.php
│   │   └── DeductDkpDTO.php
│   └── Donation/
│       ├── CreateDonationDTO.php
│       └── ReviewDonationDTO.php
│
├── Enums/
│   ├── GuildMemberStatus.php
│   ├── EventStatus.php
│   ├── DonationStatus.php
│   ├── RsvpResponse.php
│   └── GuildPermission.php
│
├── Exceptions/
│   ├── InsufficientDkpException.php
│   ├── MemberAlreadyExistsException.php
│   ├── CannotKickLeaderException.php
│   ├── CannotLeaveAsLeaderException.php
│   ├── InsufficientPermissionsException.php
│   ├── EventAlreadyCancelledException.php
│   └── DonationNotPendingException.php
│
├── Finders/
│   ├── UserFinder.php
│   ├── GuildFinder.php
│   ├── GuildMemberFinder.php
│   ├── GuildRoleFinder.php
│   ├── PermissionFinder.php
│   ├── EventFinder.php
│   ├── EventRsvpFinder.php
│   ├── DkpTransactionFinder.php
│   ├── DkpBalanceFinder.php
│   ├── DonationFinder.php
│   └── AuditLogFinder.php
│
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── GuildController.php
│   │   ├── GuildMemberController.php
│   │   ├── GuildRoleController.php
│   │   ├── EventController.php
│   │   ├── EventRsvpController.php
│   │   ├── DkpController.php
│   │   ├── DonationController.php
│   │   └── AuditLogController.php
│   ├── Requests/
│   │   ├── Auth/
│   │   │   ├── RegisterRequest.php
│   │   │   └── LoginRequest.php
│   │   ├── Guild/
│   │   │   ├── CreateGuildRequest.php
│   │   │   └── UpdateGuildRequest.php
│   │   ├── GuildMember/
│   │   │   └── UpdateMemberRoleRequest.php
│   │   ├── GuildRole/
│   │   │   ├── CreateGuildRoleRequest.php
│   │   │   └── UpdateGuildRoleRequest.php
│   │   ├── Event/
│   │   │   └── CreateEventRequest.php
│   │   ├── EventRsvp/
│   │   │   ├── UpsertRsvpRequest.php
│   │   │   └── RegisterAttendanceRequest.php
│   │   ├── Dkp/
│   │   │   ├── GrantDkpRequest.php
│   │   │   └── DeductDkpRequest.php
│   │   └── Donation/
│   │       ├── CreateDonationRequest.php
│   │       └── ReviewDonationRequest.php
│   └── Resources/
│       ├── UserResource.php
│       ├── GuildResource.php
│       ├── GuildPublicProfileResource.php
│       ├── GuildMemberResource.php
│       ├── GuildRoleResource.php
│       ├── EventResource.php
│       ├── EventRsvpResource.php
│       ├── DkpTransactionResource.php
│       ├── DkpBalanceResource.php
│       ├── DonationResource.php
│       └── AuditLogResource.php
│
├── Jobs/
│   ├── SendDiscordEventCreatedNotificationJob.php
│   └── SendDiscordEventReminderJob.php
│
├── Models/
│   └── Main/
│       ├── User.php
│       ├── Guild.php
│       ├── GuildMember.php
│       ├── GuildRole.php
│       ├── Permission.php
│       ├── Event.php
│       ├── EventRsvp.php
│       ├── DkpTransaction.php
│       ├── DkpBalance.php
│       ├── Donation.php
│       └── AuditLog.php
│
├── Queries/
│   ├── GuildQueries.php
│   ├── GuildMemberQueries.php
│   ├── EventQueries.php
│   ├── DkpTransactionQueries.php
│   └── DonationQueries.php
│
├── Repositories/
│   ├── UserRepository.php
│   ├── GuildRepository.php
│   ├── GuildMemberRepository.php
│   ├── GuildRoleRepository.php
│   ├── GuildRolePermissionRepository.php
│   ├── EventRepository.php
│   ├── EventRsvpRepository.php
│   ├── DkpTransactionRepository.php
│   ├── DkpBalanceRepository.php
│   ├── DonationRepository.php
│   └── AuditLogRepository.php
│
├── Services/
│   ├── GuildPermissionGate.php
│   ├── UserService.php
│   ├── GuildService.php
│   ├── GuildMemberService.php
│   ├── GuildRoleService.php
│   ├── EventService.php
│   ├── EventRsvpService.php
│   ├── DkpTransactionService.php
│   ├── DkpBalanceService.php
│   ├── DonationService.php
│   └── AuditLogService.php
│
└── UseCases/
    ├── Auth/
    │   └── RegisterUserProcess.php
    ├── Guild/
    │   └── CreateGuildProcess.php
    ├── GuildMember/
    │   ├── JoinGuildProcess.php
    │   ├── InviteMemberProcess.php
    │   ├── ApproveMemberProcess.php
    │   ├── RejectMemberProcess.php
    │   ├── KickMemberProcess.php
    │   └── TransferLeadershipProcess.php
    ├── Event/
    │   └── CreateEventProcess.php
    ├── DkpTransaction/
    │   ├── GrantDkpProcess.php
    │   └── DeductDkpProcess.php
    └── Donation/
        └── ReviewDonationProcess.php
```

---

## 5. Componentes por dominio

### 5.1 User

**UserFinder**
- `findById(int $id): ?User`
- `findByEmail(string $email): ?User`

**UserRepository**
- `create(array $data): User`

**Actions**
- `CreateUserAction` — acepta `CreateUserDTO`, hashea contraseña, llama `UserRepository::create()`

**UserService**
- `findById(int $id): ?User`
- `findByEmail(string $email): ?User`
- `create(CreateUserDTO $dto): User`

**DTOs**: `CreateUserDTO` (name, email, password)

---

### 5.2 Guild

**GuildFinder**
- `findById(int $id): ?Guild`
- `findByIdOrFail(int $id): Guild`

**GuildQueries**
- `searchPublicGuilds(?string $name, ?string $game, int $perPage): LengthAwarePaginator` — HU-05, búsqueda parcial por nombre y filtro por juego
- `getPublicProfile(int $guildId): array` — HU-06, vista pública con conteo de miembros

**GuildRepository**
- `create(array $data): Guild`
- `update(Guild $guild, array $data): Guild`

**Actions**
- `CreateGuildAction(CreateGuildDTO $dto): Guild`
- `UpdateGuildAction(Guild $guild, UpdateGuildDTO $dto): Guild`

**GuildService**
- `findByIdOrFail(int $id): Guild`
- `searchPublic(?string $name, ?string $game, int $perPage): LengthAwarePaginator`
- `getPublicProfile(int $guildId): array`
- `create(CreateGuildDTO $dto): Guild`
- `update(Guild $guild, UpdateGuildDTO $dto): Guild`

**DTOs**: `CreateGuildDTO` (name, description, game, is_public, creator_user_id), `UpdateGuildDTO` (name, description, game, is_public, dkp_currency_name, discord_webhook_url, discord_advance_notice_minutes)

---

### 5.3 GuildMember

**GuildMemberFinder**
- `findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember`
- `findByGuildAndUser(int $guildId, int $userId): ?GuildMember`
- `findPendingRequestsByGuild(int $guildId): Collection`
- `findActiveMembersByGuild(int $guildId): Collection`
- `findById(int $id): ?GuildMember`

**GuildMemberQueries**
- `getActiveMembersWithRoles(int $guildId): Collection` — lista de miembros con nombre de rol para visualización

**GuildMemberRepository**
- `create(array $data): GuildMember`
- `update(GuildMember $member, array $data): GuildMember`

**Actions**
- `CreateMemberRequestAction(CreateMemberRequestDTO $dto): GuildMember` — status=pending_request
- `CreateMemberInviteAction(CreateMemberInviteDTO $dto): GuildMember` — status=pending_invite
- `ApproveMemberAction(GuildMember $member): GuildMember` — status=active, joined_at=now
- `RejectMemberAction(GuildMember $member): GuildMember` — status=rejected
- `KickMemberAction(GuildMember $member): GuildMember` — status=kicked
- `UpdateMemberRoleAction(GuildMember $member, int $roleId): GuildMember`

**GuildMemberService**
- `findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember`
- `findPendingRequests(int $guildId): Collection`
- `getActiveMembersWithRoles(int $guildId): Collection`
- `requestJoin(CreateMemberRequestDTO $dto): GuildMember`
- `invite(CreateMemberInviteDTO $dto): GuildMember`
- `approve(GuildMember $member): GuildMember`
- `reject(GuildMember $member): GuildMember`
- `kick(GuildMember $member): GuildMember`
- `updateRole(GuildMember $member, int $roleId): GuildMember`

---

### 5.4 GuildRole + Permission

**GuildRoleFinder**
- `findById(int $id): ?GuildRole`
- `findByGuild(int $guildId): Collection`
- `findLeaderRoleByGuild(int $guildId): GuildRole`
- `findOfficialRoleByGuild(int $guildId): GuildRole`
- `findDefaultMemberRoleByGuild(int $guildId): GuildRole`

**PermissionFinder**
- `findAll(): Collection` — catálogo completo de permisos disponibles
- `findBySlug(string $slug): ?Permission`
- `findByIds(array $ids): Collection`
- `findBySlugs(array $slugs): Collection`

**GuildRoleRepository**
- `create(array $data): GuildRole`
- `update(GuildRole $role, array $data): GuildRole`

**GuildRolePermissionRepository** *(pivot)*
- `sync(GuildRole $role, array $permissionIds): void` — delega a `$role->permissions()->sync($permissionIds)`

**Actions**
- `CreateGuildRoleAction(CreateGuildRoleDTO $dto): GuildRole` — crea el rol y sincroniza permisos iniciales via `SyncRolePermissionsAction`
- `SyncRolePermissionsAction(GuildRole $role, array $permissionIds): void` — llama `GuildRolePermissionRepository::sync()`

**GuildRoleService**
- `findByGuild(int $guildId): Collection` — carga `permissions` eager por defecto
- `findById(int $id): GuildRole`
- `createDefaultRoles(int $guildId): void` — crea Líder, Oficial y Miembro; sincroniza permisos predefinidos (HU-13) usando `PermissionFinder::findBySlugs()`
- `createCustomRole(CreateGuildRoleDTO $dto): GuildRole` — HU-14
- `updatePermissions(GuildRole $role, UpdateGuildRoleDTO $dto): void` — HU-15, lanza excepción si el rol es Líder (`$role->is_system && $role->name === 'Líder'`); llama `SyncRolePermissionsAction`

**DTOs actualizados**:
- `CreateGuildRoleDTO` (guild_id, name, permission_ids: `int[]`)
- `UpdateGuildRoleDTO` (permission_ids: `int[]`)

---

### 5.5 Event

**EventFinder**
- `findById(int $id): ?Event`
- `findByIdOrFail(int $id): Event`
- `findScheduledWithoutReminderSent(): Collection` — utilizado por el job de recordatorio

**EventQueries**
- `getGuildEventsWithStatus(int $guildId, int $perPage): LengthAwarePaginator` — HU-21, listado con estado calculado

**EventRepository**
- `create(array $data): Event`
- `update(Event $event, array $data): Event`

**EventRsvpFinder**
- `findByEventAndUser(int $eventId, int $userId): ?EventRsvp`
- `findByEvent(int $eventId): Collection`

**EventRsvpRepository**
- `create(array $data): EventRsvp`
- `update(EventRsvp $rsvp, array $data): EventRsvp`

**Actions**
- `CreateEventAction(CreateEventDTO $dto): Event`
- `CancelEventAction(Event $event): Event` — status=cancelled
- `CompleteEventAction(Event $event): Event` — status=completed
- `UpsertEventRsvpAction(UpsertRsvpDTO $dto): EventRsvp` — crea o actualiza el RSVP
- `RegisterAttendanceAction(RegisterAttendanceDTO $dto): void` — actualiza `attended` en bulk sobre EventRsvp

**EventService**
- `findByIdOrFail(int $id): Event`
- `getGuildEvents(int $guildId, int $perPage): LengthAwarePaginator`
- `create(CreateEventDTO $dto): Event`
- `cancel(Event $event): Event` — guarda: el evento debe estar en `scheduled`
- `submitRsvp(UpsertRsvpDTO $dto): EventRsvp` — guarda: evento `scheduled` y en el futuro
- `registerAttendance(RegisterAttendanceDTO $dto): void` — guarda: evento `completed`

**EventRsvpService**
- `findByEvent(int $eventId): Collection`
- `upsertRsvp(UpsertRsvpDTO $dto): EventRsvp`
- `registerAttendance(RegisterAttendanceDTO $dto): void`

---

### 5.6 DKP

**DkpTransactionFinder**
- `findByGuildAndUser(int $guildId, int $userId, int $perPage): LengthAwarePaginator`

**DkpBalanceFinder**
- `findByGuildAndUser(int $guildId, int $userId): ?DkpBalance`
- `findOrCreateByGuildAndUser(int $guildId, int $userId): DkpBalance`

**DkpTransactionQueries**
- `getTransactionHistoryWithActors(int $guildId, int $userId, int $perPage): LengthAwarePaginator` — HU-25, join con users para mostrar nombre del actor

**DkpTransactionRepository**
- `create(array $data): DkpTransaction`

**DkpBalanceRepository**
- `create(array $data): DkpBalance`
- `incrementBalance(DkpBalance $balance, int $amount): DkpBalance`
- `decrementBalance(DkpBalance $balance, int $amount): DkpBalance`

**Actions**
- `GrantDkpAction(GrantDkpDTO $dto): DkpTransaction` — amount positivo
- `DeductDkpAction(DeductDkpDTO $dto): DkpTransaction` — amount negativo
- `UpdateDkpBalanceAction` — actualiza fila materializada en `dkp_balances`

**DkpTransactionService**
- `getHistoryWithActors(int $guildId, int $userId, int $perPage): LengthAwarePaginator`
- `grant(GrantDkpDTO $dto): DkpTransaction`
- `deduct(DeductDkpDTO $dto): DkpTransaction`

**DkpBalanceService**
- `getBalance(int $guildId, int $userId): DkpBalance`
- `getOrCreate(int $guildId, int $userId): DkpBalance`
- `increment(int $guildId, int $userId, int $amount): DkpBalance`
- `decrement(int $guildId, int $userId, int $amount): DkpBalance` — lanza `InsufficientDkpException` si balance < amount

---

### 5.7 Donation

**DonationFinder**
- `findById(int $id): ?Donation`
- `findByIdOrFail(int $id): Donation`
- `findPendingByGuild(int $guildId): Collection`

**DonationQueries**
- `getApprovedDonationsWithDonors(int $guildId, int $perPage): LengthAwarePaginator` — HU-28, join con users

**DonationRepository**
- `create(array $data): Donation`
- `update(Donation $donation, array $data): Donation`

**Actions**
- `CreateDonationAction(CreateDonationDTO $dto): Donation`
- `ApproveDonationAction(Donation $donation, int $reviewerUserId): Donation`
- `RejectDonationAction(Donation $donation, int $reviewerUserId): Donation`

**DonationService**
- `create(CreateDonationDTO $dto): Donation`
- `approve(Donation $donation, int $reviewerUserId): Donation` — guarda: status debe ser `pending`
- `reject(Donation $donation, int $reviewerUserId): Donation` — guarda: status debe ser `pending`
- `getApprovedHistory(int $guildId, int $perPage): LengthAwarePaginator`
- `findPendingByGuild(int $guildId): Collection`

---

### 5.8 AuditLog

**AuditLogFinder**
- `findByGuild(int $guildId, int $perPage): LengthAwarePaginator` — HU-29

**AuditLogRepository**
- `create(array $data): AuditLog`

**Actions**
- `CreateAuditLogAction::handle(int $guildId, int $actorUserId, ?int $targetUserId, string $eventType, array $payload): AuditLog`

**AuditLogService**
- `log(int $guildId, int $actorUserId, ?int $targetUserId, string $eventType, array $payload): AuditLog`
- `getByGuild(int $guildId, int $perPage): LengthAwarePaginator`

**Tipos de eventos de auditoría**:
- `dkp.granted`, `dkp.deducted`
- `donation.created`, `donation.approved`, `donation.rejected`

---

## 6. Use Cases

Los Use Cases coordinan operaciones sobre **múltiples modelos** con lógica de negocio transversal.

### 6.1 RegisterUserProcess
**Dominos cruzados**: User + Passport (token OAuth2)
- Crea el usuario via `UserService`
- Emite un access token via Passport

### 6.2 CreateGuildProcess
**Dominios cruzados**: Guild + GuildRole (×3) + GuildMember
- `GuildService::create()` — crea el gremio
- `GuildRoleService::createDefaultRoles()` — crea los 3 roles de sistema (Líder, Oficial, Miembro)
- `GuildMemberService::requestJoin()` + `approve()` — agrega al creador como miembro activo con rol Líder

**Justificación**: crear un gremio implica 3 operaciones cross-model que deben ser atómicas. Si alguna falla, no debe existir ni gremio ni roles ni membresía parciales.

### 6.3 JoinGuildProcess
**Dominios cruzados**: Guild + GuildMember
- Verifica que el gremio sea público y acepte solicitudes (`GuildService`)
- Verifica que el usuario no sea ya miembro (`GuildMemberFinder`)
- Crea la solicitud pendiente (`GuildMemberService::requestJoin()`)

### 6.4 InviteMemberProcess
**Dominios cruzados**: User + GuildMember
- Verifica que el usuario invitado exista (`UserFinder`)
- Verifica que no sea ya miembro del gremio (`GuildMemberFinder`)
- Crea la invitación pendiente (`GuildMemberService::invite()`)

### 6.5 ApproveMemberProcess
**Dominios cruzados**: GuildMember + GuildRole
- Aprueba la solicitud (`GuildMemberService::approve()`)
- Asigna el rol Miembro por defecto (`GuildRoleFinder::findDefaultMemberRoleByGuild()` + `UpdateMemberRoleAction`)

### 6.6 RejectMemberProcess
**Dominio**: GuildMember
- Rechaza la solicitud o invitación (`GuildMemberService::reject()`)
> Aunque opera sobre un solo dominio, se mantiene como Use Case por consistencia con Approve y para extensibilidad futura.

### 6.7 KickMemberProcess
**Dominios cruzados**: GuildMember + validación de liderazgo
- Verifica que el miembro a expulsar no sea el Líder actual (`Guild.leader_user_id`)
- Ejecuta la expulsión (`GuildMemberService::kick()`)

### 6.8 TransferLeadershipProcess
**Dominios cruzados**: Guild + GuildMember (×2) + GuildRole
- Actualiza `Guild.leader_user_id` al nuevo líder
- Asigna rol Líder al nuevo líder (`UpdateMemberRoleAction`)
- Asigna rol Oficial al ex-líder (`UpdateMemberRoleAction`)

**Justificación**: tres escrituras cross-model que deben ser atómicas; si alguna falla el liderazgo no puede quedar en estado inconsistente.

### 6.9 CreateEventProcess
**Dominios cruzados**: Event + Jobs de Discord
- Crea el evento (`EventService::create()`)
- Después del commit, despacha `SendDiscordEventCreatedNotificationJob` y `SendDiscordEventReminderJob` (con delay calculado)

### 6.10 GrantDkpProcess
**Dominios cruzados**: DkpBalance + DkpTransaction + AuditLog
- `DkpBalanceService::getOrCreate()` — obtiene o inicializa el saldo
- `DkpBalanceService::increment()` — actualiza el saldo materializado
- `DkpTransactionService::grant()` — crea el registro de transacción (con `balance_after`)
- `AuditLogService::log()` — registra la acción en el log

### 6.11 DeductDkpProcess
**Dominios cruzados**: DkpBalance + DkpTransaction + AuditLog
- `DkpBalanceService::getOrCreate()` — obtiene el saldo
- `DkpBalanceService::decrement()` — valida que balance >= amount y descuenta (lanza `InsufficientDkpException` si no)
- `DkpTransactionService::deduct()` — crea el registro de transacción (amount negativo)
- `AuditLogService::log()` — registra la acción

### 6.12 ReviewDonationProcess
**Dominios cruzados**: Donation + AuditLog
- Verifica que la donación esté en estado `pending` (lanza `DonationNotPendingException` si no)
- `DonationService::approve()` o `DonationService::reject()` según la decisión
- `AuditLogService::log()` — registra la revisión

---

## 7. Application Services

Los Application Services envuelven Use Cases con responsabilidades de infraestructura: transacciones, dispatch de jobs post-commit, y mapeo de excepciones.

### RegisterApplicationService
- Envuelve `RegisterUserProcess` en una transacción DB
- Emite el access token de Passport y lo retorna junto al usuario

### CreateGuildApplicationService
- `DB::beginTransaction()` → `CreateGuildProcess::execute()` → `DB::commit()`
- Guild + 3 roles + membresía deben crearse o fallar juntos

### TransferLeadershipApplicationService
- `DB::beginTransaction()` → `TransferLeadershipProcess::execute()` → `DB::commit()`
- Garantiza que el cambio de liderazgo sea atómico

### CreateEventApplicationService
- `DB::beginTransaction()` → `CreateEventProcess::execute()` → `DB::commit()`
- **Post-commit** (fuera de la transacción):
  - Si el gremio tiene webhook configurado → `SendDiscordEventCreatedNotificationJob::dispatch()`
  - Si hay anticipación configurada → `SendDiscordEventReminderJob::dispatch()->delay($reminderAt)`

### GrantDkpApplicationService / DeductDkpApplicationService
- `DB::beginTransaction()` → `GrantDkpProcess` / `DeductDkpProcess` → `DB::commit()`
- `DeductDkpApplicationService` captura `InsufficientDkpException` y retorna HTTP 422

### ReviewDonationApplicationService
- `DB::beginTransaction()` → `ReviewDonationProcess::execute()` → `DB::commit()`
- Captura `DonationNotPendingException` y retorna HTTP 409

---

## 8. Permission Checking

`GuildPermissionGate` (`app/Services/GuildPermissionGate.php`) es un helper transversal, **no es un Service de dominio**. No opera sobre Finders ni Repositories propios; es una clase de guardia inyectable.

Con el sistema ACL, el gate consulta la relación `GuildRole → permissions` (cargada via eager loading) en lugar de un JSON plano. La colección de `Permission` ya está normalizada en la DB.

```php
class GuildPermissionGate
{
    public function authorize(GuildMember $member, GuildPermission $permission): void
    {
        // $member debe venir con role.permissions eager-loaded
        $hasPermission = $member->role->permissions
            ->contains('slug', $permission->value);

        if (!$hasPermission) {
            throw new InsufficientPermissionsException();
        }
    }
}
```

**Eager loading requerido** — el `GuildMemberFinder` debe cargar la relación antes de que llegue al gate:

```php
// GuildMemberFinder.php
public function findActiveByGuildAndUser(int $guildId, int $userId): ?GuildMember
{
    return GuildMember::with('role.permissions')
        ->where('guild_id', $guildId)
        ->where('user_id', $userId)
        ->where('status', GuildMemberStatus::Active)
        ->first();
}
```

**Patrón de uso en controllers** (sin cambios respecto al anterior):

```php
public function grant(GrantDkpRequest $request, Guild $guild): JsonResponse
{
    $actorMember = $this->memberFinder->findActiveByGuildAndUser($guild->id, auth()->id());
    $this->permissionGate->authorize($actorMember, GuildPermission::ManageDkp);

    $dto = new GrantDkpDTO(...);
    return $this->grantDkpApplicationService->handle($dto);
}
```

El gate se llama en el controller **antes** de delegar al Application Service / Use Case. Los Use Cases permanecen libres de lógica de autenticación.

---

## 9. Flujos clave

### 9.1 Crear gremio (HU-03)

```
POST /guilds
  GuildController::store(CreateGuildRequest)
    → CreateGuildApplicationService::handle(CreateGuildDTO)
        → DB::beginTransaction()
        → CreateGuildProcess::execute(CreateGuildDTO)
            → GuildService::create(dto)                         [Guild creado]
                → CreateGuildAction → GuildRepository::create()
            → GuildRoleService::createDefaultRoles(guild_id)    [3 roles + ACL sincronizado]
                → PermissionFinder::findBySlugs([...]) por cada rol
                → CreateGuildRoleAction ×3 → GuildRoleRepository::create() ×3
                → SyncRolePermissionsAction ×3 → GuildRolePermissionRepository::sync() ×3
            → GuildRoleFinder::findLeaderRoleByGuild(guild_id)
            → GuildMemberService::requestJoin() + approve()     [Creador como Líder activo]
                → CreateMemberRequestAction + ApproveMemberAction + UpdateMemberRoleAction
        → DB::commit()
    → GuildResource
```

---

### 9.2 Otorgar DKP (HU-23)

```
POST /guilds/{guild}/members/{member}/dkp/grant
  DkpController::grant(GrantDkpRequest, Guild, GuildMember $target)
    → GuildMemberFinder::findActiveByGuildAndUser(guild_id, auth()->id()) → $actorMember
    → GuildPermissionGate::authorize($actorMember, GuildPermission::ManageDkp)
    → GrantDkpApplicationService::handle(GrantDkpDTO)
        → DB::beginTransaction()
        → GrantDkpProcess::execute(GrantDkpDTO)
            → DkpBalanceService::getOrCreate(guild_id, target_user_id)
            → DkpBalanceService::increment(guild_id, target_user_id, amount)
                → UpdateDkpBalanceAction → DkpBalanceRepository::incrementBalance()
            → DkpTransactionService::grant(dto)
                → GrantDkpAction → DkpTransactionRepository::create([..., balance_after])
            → AuditLogService::log('dkp.granted', payload)
                → CreateAuditLogAction → AuditLogRepository::create()
        → DB::commit()
    → DkpTransactionResource
```

---

### 9.3 Crear evento con Discord (HU-17 + HU-32 + HU-33)

```
POST /guilds/{guild}/events
  EventController::store(CreateEventRequest, Guild)
    → GuildPermissionGate::authorize($member, GuildPermission::ManageEvents)
    → CreateEventApplicationService::handle(CreateEventDTO)
        → DB::beginTransaction()
        → CreateEventProcess::execute(CreateEventDTO)
            → EventService::create(dto)
                → CreateEventAction → EventRepository::create()
        → DB::commit()

        [Post-commit — ya no dentro de la transacción]
        → SI guild->discord_webhook_url:
            → SendDiscordEventCreatedNotificationJob::dispatch($event->id, $webhookUrl)
            → SI guild->discord_advance_notice_minutes:
                → $reminderAt = $event->starts_at->subMinutes($advanceMinutes)
                → SendDiscordEventReminderJob::dispatch($event->id)->delay($reminderAt)

    → EventResource
```

Los jobs re-consultan el evento al ejecutarse y verifican que siga en `scheduled` antes de enviar la notificación. Si el evento fue cancelado entre la creación y el momento del recordatorio, el job no envía nada.

---

### 9.4 Revisar donación (HU-27)

```
PATCH /guilds/{guild}/donations/{donation}/review
  DonationController::review(ReviewDonationRequest, Guild, Donation)
    → GuildPermissionGate::authorize($member, GuildPermission::ManageDonations)
    → ReviewDonationApplicationService::handle(ReviewDonationDTO)
        → DB::beginTransaction()
        → ReviewDonationProcess::execute(ReviewDonationDTO)
            → DonationFinder::findByIdOrFail(id)
            → Guarda: status !== 'pending' → DonationNotPendingException
            → DonationService::approve($donation, reviewerUserId)
              ó DonationService::reject($donation, reviewerUserId)
                → ApproveDonationAction / RejectDonationAction → DonationRepository::update()
            → AuditLogService::log('donation.approved', payload)
                → CreateAuditLogAction → AuditLogRepository::create()
        → DB::commit()
    → DonationResource
```

---

## 10. Decisiones de diseño

### 10.1 DkpBalance materializado
En lugar de calcular `SUM(dkp_transactions.amount)` en cada lectura, la tabla `dkp_balances` mantiene el total corriente. Se actualiza atómicamente con cada `DkpTransaction` dentro de la misma transacción. Esto da lecturas O(1) del saldo y sirve de guarda para la regla "no puede bajar de 0" (HU-24), que se verifica en `DkpBalanceService::decrement()` antes de escribir. El campo `balance_after` en `DkpTransaction` permite reconstruir el historial completo sin recomputar.

### 10.2 AuditLog como log de eventos append-only
El modelo `AuditLog` no tiene `updated_at` y su Repository solo expone `create()`. El campo `payload` JSON captura el estado completo en el momento de la acción (montos, motivos, saldo antes/después, notas de donación). Esto hace que cada registro de auditoría sea autocontenido — no requiere joins para reconstruir qué pasó. `AuditLogService::log()` se llama desde Use Cases (no desde Services), asegurando que siempre esté dentro de la misma transacción que la operación que disparó el registro.

### 10.3 Sistema ACL con tabla pivot (guild_role_permissions)
Los permisos se gestionan mediante un sistema ACL normalizado en base de datos. La tabla `permissions` actúa como catálogo fijo (poblado por `PermissionSeeder`, nunca modificado en runtime). La tabla pivot `guild_role_permissions` vincula roles a permisos con una relación `belongsToMany`. Esto permite:

- **Consultas tipadas**: `$role->permissions->contains('slug', $slug)` en lugar de `in_array()` sobre JSON
- **Integridad referencial**: FK garantiza que no existan slugs inválidos en el pivot
- **Visibilidad**: cualquier herramienta de DB puede inspeccionar qué permisos tiene cada rol sin parsear JSON
- **Extensibilidad**: agregar columnas al pivot (ej: `granted_at`, `granted_by`) es directo

El enum `GuildPermission` se mantiene como contrato tipado en PHP para los slugs, pero la fuente de verdad en runtime es la tabla `permissions`. El `PermissionSeeder` debe poblar los 10 permisos con los slugs correspondientes al enum antes de ejecutar cualquier test o migración de datos.

La asignación/reasignación de permisos usa `sync()` de Eloquent a través de `GuildRolePermissionRepository::sync()` y `SyncRolePermissionsAction`, que reemplaza todos los permisos del rol en una sola operación atómica. Los roles de sistema tienen `is_system=true`; el rol Líder además tiene sus permisos bloqueados por `GuildRoleService::updatePermissions()`.

### 10.4 Cero dependencias cross-model en Services
Ningún Service inyecta otro Service. Si una operación requiere dos dominios, existe un Use Case. Esta convención es la más importante de MDA en este proyecto: si durante el desarrollo se encuentra un Service que necesita datos de otro dominio, la lógica debe moverse a un Use Case. Los Use Cases son el punto de orquestación correcto.

### 10.5 Discord via jobs queued post-commit
Las llamadas al webhook de Discord ocurren en `SendDiscordEventCreatedNotificationJob` y `SendDiscordEventReminderJob`. Se despachan **después** de `DB::commit()` desde el Application Service. Esto garantiza que un fallo de Discord nunca revierta la creación del evento. El job de recordatorio usa `delay()` nativo de Laravel Queue — no requiere cron adicional, solo `queue:work`. Los jobs re-consultan el evento y verifican `status === 'scheduled'` antes de enviar, por lo que eventos cancelados no generan recordatorios.

### 10.6 GuildMember status enum en tabla única
El ciclo de vida completo de una membresía (solicitud → activo → expulsado/salió) se representa con el enum `status` en una única tabla. Los registros no se eliminan, solo cambian de estado. Esto preserva el historial de DKP y donaciones de ex-miembros (HU-10, HU-11). Las transiciones válidas están implícitas en los guards de los Actions y Use Cases.

### 10.7 leader_user_id denormalizado en Guild
`Guild.leader_user_id` es una FK denormalizada que permite verificar rápidamente "¿es este usuario el líder?" sin join. Se actualiza atómicamente junto con los cambios de rol en `TransferLeadershipProcess` dentro de una única transacción. Esta denormalización es un trade-off aceptable: la consistencia está garantizada por la transacción, y el beneficio es eliminar un join en cada chequeo de liderazgo.

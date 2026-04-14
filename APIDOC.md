# API Documentation — Guild Engine

Base URL: `/api`

Todos los endpoints protegidos requieren el header:
```
Authorization: Bearer <token>
```

Los errores de validación retornan `422` con el siguiente formato:
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "campo": ["Descripción del error"]
  }
}
```

---

## Índice

- [Autenticación](#autenticación)
- [Guilds](#guilds)
- [Miembros](#miembros)
- [Roles de guild](#roles-de-guild)
- [Eventos](#eventos)
- [DKP](#dkp)
- [Donaciones](#donaciones)
- [Log de auditoría](#log-de-auditoría)
- [Referencia de enums](#referencia-de-enums)
- [Respuestas de error comunes](#respuestas-de-error-comunes)

> **Nota:** Todos los recursos con listado tienen su endpoint `GET /{id}` correspondiente para consulta individual.

---

## Autenticación

### `POST /auth/register`

Registra un nuevo usuario y retorna un token de acceso.

**Autenticación:** No requerida

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `name` | string | Sí | max:255 |
| `email` | string | Sí | formato email, único en la tabla `users` |
| `password` | string | Sí | min:8, debe coincidir con `password_confirmation` |
| `password_confirmation` | string | Sí | debe coincidir con `password` |

**Respuesta `201 Created`:**
```json
{
  "user": {
    "id": 1,
    "name": "Zangles",
    "email": "zangles@example.com",
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

---

### `POST /auth/login`

Autentica un usuario y retorna un token de acceso.

**Autenticación:** No requerida

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `email` | string | Sí | formato email |
| `password` | string | Sí | — |

**Respuesta `200 OK`:**
```json
{
  "user": {
    "id": 1,
    "name": "Zangles",
    "email": "zangles@example.com",
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9..."
}
```

**Respuesta `401 Unauthorized`:**
```json
{
  "message": "Invalid credentials."
}
```

---

### `POST /auth/logout`

Revoca el token de acceso actual.

**Autenticación:** Requerida

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "message": "Logged out successfully."
}
```

---

## Guilds

### `GET /guilds`

Lista las guilds con soporte para búsqueda y paginación.

**Autenticación:** No requerida

**Query Parameters:**

| Parámetro | Tipo | Requerido | Descripción |
|---|---|---|---|
| `name` | string | No | Filtra por nombre de guild |
| `game` | string | No | Filtra por juego |
| `per_page` | integer | No | Resultados por página (default: `15`) |

**Respuesta `200 OK` (paginada):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Los Dragones",
      "description": "Una guild épica",
      "game": "World of Warcraft",
      "is_public": true,
      "leader_user_id": 1,
      "dkp_currency_name": "DKP",
      "discord_webhook_url": null,
      "discord_advance_notice_minutes": null,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 }
}
```

---

### `GET /guilds/{guild}`

Retorna el perfil público de una guild.

**Autenticación:** No requerida

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "name": "Los Dragones",
  "description": "Una guild épica",
  "game": "World of Warcraft",
  "members_count": 42,
  "created_at": "2025-01-01T00:00:00.000000Z"
}
```

---

### `GET /me/guilds`

Lista todas las guilds en las que el usuario autenticado tiene membresía activa, con relaciones cargadas.

**Autenticación:** Requerida

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
[
  {
    "id": 5,
    "guild_id": 1,
    "user_id": 2,
    "guild_role_id": 3,
    "status": "active",
    "invited_by_user_id": null,
    "joined_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "user": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." },
    "role": { "id": 3, "guild_id": 1, "name": "Miembro", "is_system": true, "permissions": ["is_guild_member"], "created_at": "..." },
    "guild": { "id": 1, "name": "Los Dragones", ... }
  }
]
```

---

### `POST /guilds`

Crea una nueva guild. El usuario creador queda asignado automáticamente como **Líder**.

**Autenticación:** Requerida

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `name` | string | Sí | max:255 |
| `description` | string\|null | No | texto libre |
| `game` | string | Sí | max:255 |
| `is_public` | boolean | No | default: `true` |

**Respuesta `201 Created`:**
```json
{
  "id": 1,
  "name": "Los Dragones",
  "description": null,
  "game": "World of Warcraft",
  "is_public": true,
  "leader_user_id": 2,
  "dkp_currency_name": "DKP",
  "discord_webhook_url": null,
  "discord_advance_notice_minutes": null,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "updated_at": "2025-01-01T00:00:00.000000Z"
}
```

---

### `PUT /guilds/{guild}`

Actualiza los datos de una guild existente.

**Autenticación:** Requerida

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `name` | string | Sí | max:255 |
| `description` | string\|null | No | texto libre |
| `game` | string | Sí | max:255 |
| `is_public` | boolean | No | default: `true` |
| `dkp_currency_name` | string | Sí | max:50 |
| `discord_webhook_url` | string\|null | No | URL válida |
| `discord_advance_notice_minutes` | integer\|null | No | min:1 |

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "name": "Los Dragones",
  "description": "Una guild épica",
  "game": "World of Warcraft",
  "is_public": true,
  "leader_user_id": 2,
  "dkp_currency_name": "Puntos de Honor",
  "discord_webhook_url": "https://discord.com/api/webhooks/...",
  "discord_advance_notice_minutes": 30,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "updated_at": "2025-01-02T00:00:00.000000Z"
}
```

---

## Miembros

Todos los endpoints de esta sección tienen el prefijo `/guilds/{guild}/`.

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

---

### `GET /guilds/{guild}/me`

Retorna el contexto del usuario autenticado como miembro de la guild: su rol, estado y permisos.

**Autenticación:** Requerida

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "role": "Miembro",
  "status": "active",
  "permissions": ["is_guild_member"]
}
```

**Respuesta `403 Forbidden`** (si no es miembro activo):
```json
{
  "message": "No eres miembro activo de este guild."
}
```

---

### `GET /guilds/{guild}/members/{member}`

Retorna el detalle de una membresía individual.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 3,
  "status": "active",
  "invited_by_user_id": null,
  "joined_at": "2025-01-01T00:00:00.000000Z",
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." },
  "role": { "id": 3, "guild_id": 1, "name": "Miembro", "is_system": true, "permissions": ["is_guild_member"], "created_at": "..." }
}
```

---

### `GET /guilds/{guild}/members`

Lista todos los miembros activos de la guild con sus roles.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
[
  {
    "id": 5,
    "guild_id": 1,
    "user_id": 2,
    "guild_role_id": 3,
    "status": "active",
    "invited_by_user_id": null,
    "joined_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "user": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." },
    "role": { "id": 3, "guild_id": 1, "name": "Miembro", "is_system": true, "permissions": ["is_guild_member"], "created_at": "..." },
    "guild": { "id": 1, "name": "Los Dragones", ... }
  }
]
```

---

### `POST /guilds/{guild}/join`

Solicita unirse a una guild. Crea una membresía con estado `pending_request`.

**Autenticación:** Requerida

**Body:** Ninguno

**Respuesta `201 Created`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 3,
  "status": "pending_request",
  "invited_by_user_id": null,
  "joined_at": null,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { ... },
  "guild": { ... }
}
```

**Respuesta `422 Unprocessable Entity`** (si ya existe una membresía):
```json
{
  "message": "El usuario ya tiene una membresía en esta guild."
}
```

---

### `POST /guilds/{guild}/invite`

Invita a un usuario a la guild. Crea una membresía con estado `pending_invite`.

**Autenticación:** Requerida | **Permiso:** `invite_members`

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `user_id` | integer | Sí | debe existir en la tabla `users` |

**Respuesta `201 Created`:**
```json
{
  "id": 6,
  "guild_id": 1,
  "user_id": 3,
  "guild_role_id": 3,
  "status": "pending_invite",
  "invited_by_user_id": 2,
  "joined_at": null,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { ... },
  "guild": { ... }
}
```

---

### `POST /guilds/{guild}/members/{member}/approve`

Aprueba la solicitud de ingreso de un miembro. Cambia su estado a `active`.

**Autenticación:** Requerida | **Permiso:** `approve_members`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 3,
  "status": "active",
  "invited_by_user_id": null,
  "joined_at": "2025-01-02T00:00:00.000000Z",
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { ... },
  "guild": { ... }
}
```

---

### `POST /guilds/{guild}/members/{member}/reject`

Rechaza la solicitud de ingreso de un miembro. Cambia su estado a `rejected`.

**Autenticación:** Requerida | **Permiso:** `approve_members`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 3,
  "status": "rejected",
  "invited_by_user_id": null,
  "joined_at": null,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { ... },
  "guild": { ... }
}
```

---

### `POST /guilds/{guild}/members/{member}/kick`

Expulsa a un miembro de la guild. Cambia su estado a `kicked`.

**Autenticación:** Requerida | **Permiso:** `kick_members`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 3,
  "status": "kicked",
  "invited_by_user_id": null,
  "joined_at": "2025-01-01T00:00:00.000000Z",
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { ... },
  "guild": { ... }
}
```

**Respuesta `422 Unprocessable Entity`** (si se intenta expulsar al Líder):
```json
{
  "message": "No puedes expulsar al líder de la guild."
}
```

---

### `PATCH /guilds/{guild}/members/{member}/role`

Cambia el rol de un miembro dentro de la guild.

**Autenticación:** Requerida | **Permiso:** `manage_roles`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `guild_role_id` | integer | Sí | debe existir en la tabla `guild_roles` |

**Respuesta `200 OK`:**
```json
{
  "id": 5,
  "guild_id": 1,
  "user_id": 2,
  "guild_role_id": 4,
  "status": "active",
  "invited_by_user_id": null,
  "joined_at": "2025-01-01T00:00:00.000000Z",
  "created_at": "2025-01-01T00:00:00.000000Z",
  "user": { ... },
  "role": { "id": 4, "name": "Oficial", ... },
  "guild": { ... }
}
```

---

### `POST /guilds/{guild}/transfer-leadership`

Transfiere el liderazgo de la guild a otro miembro activo.

**Autenticación:** Requerida | **Permiso:** `transfer_leadership`

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `user_id` | integer | Sí | debe existir en la tabla `users` |

**Respuesta `200 OK`:** Retorna la guild actualizada con el nuevo `leader_user_id`.
```json
{
  "id": 1,
  "name": "Los Dragones",
  "description": "Una guild épica",
  "game": "World of Warcraft",
  "is_public": true,
  "leader_user_id": 3,
  "dkp_currency_name": "DKP",
  "discord_webhook_url": null,
  "discord_advance_notice_minutes": null,
  "created_at": "2025-01-01T00:00:00.000000Z",
  "updated_at": "2025-01-02T00:00:00.000000Z"
}
```

---

## Roles de guild

### `GET /guilds/{guild}/roles`

Lista todos los roles de la guild, incluyendo los roles del sistema.

**Autenticación:** Requerida | **Permiso:** `manage_roles`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
[
  {
    "id": 1,
    "guild_id": 1,
    "name": "Líder",
    "is_system": true,
    "permissions": [
      "is_guild_member", "invite_members", "approve_members", "kick_members",
      "manage_roles", "manage_events", "register_attendance", "manage_dkp",
      "manage_donations", "view_audit_log", "transfer_leadership"
    ],
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  {
    "id": 2,
    "guild_id": 1,
    "name": "Oficial",
    "is_system": true,
    "permissions": ["is_guild_member", "invite_members", "approve_members", "kick_members", "manage_events", "register_attendance", "manage_dkp", "manage_donations", "view_audit_log"],
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  {
    "id": 3,
    "guild_id": 1,
    "name": "Miembro",
    "is_system": true,
    "permissions": ["is_guild_member"],
    "created_at": "2025-01-01T00:00:00.000000Z"
  }
]
```

---

### `POST /guilds/{guild}/roles`

Crea un rol personalizado en la guild.

**Autenticación:** Requerida | **Permiso:** `manage_roles`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `name` | string | Sí | max:100 |
| `permission_slugs` | array | No | array de strings; cada elemento debe ser un permiso válido (ver [Referencia de enums](#referencia-de-enums)) |

**Respuesta `201 Created`:**
```json
{
  "id": 10,
  "guild_id": 1,
  "name": "Recluta",
  "is_system": false,
  "permissions": ["is_guild_member", "register_attendance"],
  "created_at": "2025-01-02T00:00:00.000000Z"
}
```

---

### `GET /guilds/{guild}/roles/{role}`

Retorna el detalle de un rol individual.

**Autenticación:** Requerida | **Permiso:** `manage_roles`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `role` | integer | ID del rol |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 10,
  "guild_id": 1,
  "name": "Recluta",
  "is_system": false,
  "permissions": ["is_guild_member", "register_attendance"],
  "created_at": "2025-01-02T00:00:00.000000Z"
}
```

---

### `PUT /guilds/{guild}/roles/{role}`

Actualiza los permisos de un rol existente. Los permisos del rol **Líder** no pueden modificarse.

**Autenticación:** Requerida | **Permiso:** `manage_roles`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `role` | integer | ID del rol |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `permission_slugs` | array | Sí | array de strings; cada elemento debe ser un permiso válido (ver [Referencia de enums](#referencia-de-enums)) |

**Respuesta `200 OK`:**
```json
{
  "id": 10,
  "guild_id": 1,
  "name": "Recluta",
  "is_system": false,
  "permissions": ["is_guild_member"],
  "created_at": "2025-01-02T00:00:00.000000Z"
}
```

---

## Eventos

### `GET /guilds/{guild}/events`

Lista los eventos de la guild de forma paginada.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body:** Ninguno

**Respuesta `200 OK` (paginada):**
```json
{
  "data": [
    {
      "id": 1,
      "guild_id": 1,
      "created_by_user_id": 2,
      "title": "Raid Nocturna",
      "description": "Raid semanal al castillo",
      "starts_at": "2025-02-01T20:00:00.000000Z",
      "max_attendees": 20,
      "status": "scheduled",
      "created_at": "2025-01-15T00:00:00.000000Z",
      "updated_at": "2025-01-15T00:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

### `GET /guilds/{guild}/events/{event}`

Retorna el detalle de un evento individual.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `event` | integer | ID del evento |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "created_by_user_id": 2,
  "title": "Raid Nocturna",
  "description": "Raid semanal al castillo",
  "starts_at": "2025-02-01T20:00:00.000000Z",
  "max_attendees": 20,
  "status": "scheduled",
  "created_at": "2025-01-15T00:00:00.000000Z",
  "updated_at": "2025-01-15T00:00:00.000000Z"
}
```

---

### `POST /guilds/{guild}/events`

Crea un nuevo evento en la guild. Dispara una notificación a Discord via queue.

**Autenticación:** Requerida | **Permiso:** `manage_events`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `title` | string | Sí | max:255 |
| `description` | string\|null | No | texto libre |
| `starts_at` | datetime | Sí | fecha futura (después del momento actual), formato ISO 8601 |
| `max_attendees` | integer\|null | No | min:1 |

**Respuesta `201 Created`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "created_by_user_id": 2,
  "title": "Raid Nocturna",
  "description": "Raid semanal al castillo",
  "starts_at": "2025-02-01T20:00:00.000000Z",
  "max_attendees": 20,
  "status": "scheduled",
  "created_at": "2025-01-15T00:00:00.000000Z",
  "updated_at": "2025-01-15T00:00:00.000000Z"
}
```

---

### `POST /guilds/{guild}/events/{event}/cancel`

Cancela un evento. Cambia su estado a `cancelled`.

**Autenticación:** Requerida | **Permiso:** `manage_events`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `event` | integer | ID del evento |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "created_by_user_id": 2,
  "title": "Raid Nocturna",
  "description": "Raid semanal al castillo",
  "starts_at": "2025-02-01T20:00:00.000000Z",
  "max_attendees": 20,
  "status": "cancelled",
  "created_at": "2025-01-15T00:00:00.000000Z",
  "updated_at": "2025-01-15T10:00:00.000000Z"
}
```

---

### `PUT /guilds/{guild}/events/{event}/rsvp`

Confirma, declina o marca como tentativo el RSVP del usuario autenticado para un evento. Crea o actualiza el registro existente.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `event` | integer | ID del evento |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `response` | string | Sí | enum: `confirmed`, `declined`, `tentative` |

**Respuesta `200 OK`:**
```json
{
  "id": 3,
  "event_id": 1,
  "user_id": 2,
  "response": "confirmed",
  "attended": null,
  "created_at": "2025-01-16T00:00:00.000000Z",
  "updated_at": "2025-01-16T00:00:00.000000Z"
}
```

---

### `POST /guilds/{guild}/events/{event}/attendance`

Registra la asistencia real de una lista de usuarios a un evento.

**Autenticación:** Requerida | **Permiso:** `register_attendance`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `event` | integer | ID del evento |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `attendances` | array | Sí | arreglo de objetos de asistencia |
| `attendances[].user_id` | integer | Sí | debe existir en la tabla `users` |
| `attendances[].attended` | boolean | Sí | `true` si asistió, `false` si no |

**Ejemplo de body:**
```json
{
  "attendances": [
    { "user_id": 2, "attended": true },
    { "user_id": 3, "attended": false }
  ]
}
```

**Respuesta `200 OK`:**
```json
{
  "message": "Attendance registered successfully."
}
```

---

## DKP

Los endpoints de DKP usan el ID de la **membresía** (`guild_members.id`) como parámetro `{member}`, no el `user_id`.

### `GET /guilds/{guild}/members/{member}/dkp/balance`

Retorna el balance actual de DKP de un miembro.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "guild_id": 1,
  "user_id": 2,
  "balance": 450,
  "updated_at": "2025-01-20T00:00:00.000000Z"
}
```

---

### `GET /guilds/{guild}/members/{member}/dkp/history`

Retorna el historial paginado de transacciones de DKP de un miembro.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body:** Ninguno

**Respuesta `200 OK` (paginada):**
```json
{
  "data": [
    {
      "id": 10,
      "guild_id": 1,
      "target_user_id": 2,
      "actor_user_id": 1,
      "amount": 100,
      "balance_after": 450,
      "reason": "Completó la raid nocturna",
      "created_at": "2025-01-20T00:00:00.000000Z",
      "actor": { "id": 1, "name": "Líder", "email": "lider@example.com", "created_at": "..." }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

### `POST /guilds/{guild}/members/{member}/dkp/grant`

Otorga DKP a un miembro.

**Autenticación:** Requerida | **Permiso:** `manage_dkp`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `amount` | integer | Sí | min:1 |
| `reason` | string | Sí | max:255 |

**Respuesta `201 Created`:**
```json
{
  "id": 11,
  "guild_id": 1,
  "target_user_id": 2,
  "actor_user_id": 1,
  "amount": 50,
  "balance_after": 500,
  "reason": "Ayudó en el evento del gremio",
  "created_at": "2025-01-21T00:00:00.000000Z",
  "actor": { "id": 1, "name": "Líder", "email": "lider@example.com", "created_at": "..." }
}
```

---

### `POST /guilds/{guild}/members/{member}/dkp/deduct`

Deduce DKP de un miembro. Falla si el balance resultante sería negativo.

**Autenticación:** Requerida | **Permiso:** `manage_dkp`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `member` | integer | ID de la membresía (`guild_members.id`) |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `amount` | integer | Sí | min:1 |
| `reason` | string | Sí | max:255 |

**Respuesta `201 Created`:**
```json
{
  "id": 12,
  "guild_id": 1,
  "target_user_id": 2,
  "actor_user_id": 1,
  "amount": 100,
  "balance_after": 400,
  "reason": "Penalización por ausencia",
  "created_at": "2025-01-22T00:00:00.000000Z",
  "actor": { "id": 1, "name": "Líder", "email": "lider@example.com", "created_at": "..." }
}
```

**Respuesta `422 Unprocessable Entity`** (saldo insuficiente):
```json
{
  "message": "Saldo DKP insuficiente."
}
```

---

## Donaciones

### `GET /guilds/{guild}/donations`

Lista las donaciones en estado `pending` de la guild.

**Autenticación:** Requerida | **Permiso:** `manage_donations`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
[
  {
    "id": 1,
    "guild_id": 1,
    "user_id": 2,
    "amount": 500,
    "note": "Para la reparación del banco de la guild",
    "status": "pending",
    "reviewed_by_user_id": null,
    "reviewed_at": null,
    "created_at": "2025-01-10T00:00:00.000000Z",
    "updated_at": "2025-01-10T00:00:00.000000Z",
    "donor": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." }
  }
]
```

---

### `GET /guilds/{guild}/donations/history`

Lista el historial paginado de donaciones aprobadas de la guild.

**Autenticación:** Requerida

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body:** Ninguno

**Respuesta `200 OK` (paginada):**
```json
{
  "data": [
    {
      "id": 1,
      "guild_id": 1,
      "user_id": 2,
      "amount": 500,
      "note": "Para la reparación del banco de la guild",
      "status": "approved",
      "reviewed_by_user_id": 1,
      "reviewed_at": "2025-01-11T00:00:00.000000Z",
      "created_at": "2025-01-10T00:00:00.000000Z",
      "updated_at": "2025-01-11T00:00:00.000000Z",
      "donor": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." }
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

### `POST /guilds/{guild}/donations`

Registra una nueva donación pendiente de revisión.

**Autenticación:** Requerida | **Permiso:** `is_guild_member`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `amount` | integer | Sí | min:1 |
| `note` | string\|null | No | max:500 |

**Respuesta `201 Created`:**
```json
{
  "id": 2,
  "guild_id": 1,
  "user_id": 2,
  "amount": 200,
  "note": null,
  "status": "pending",
  "reviewed_by_user_id": null,
  "reviewed_at": null,
  "created_at": "2025-01-12T00:00:00.000000Z",
  "updated_at": "2025-01-12T00:00:00.000000Z",
  "donor": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." }
}
```

---

### `GET /guilds/{guild}/donations/{donation}`

Retorna el detalle de una donación individual.

**Autenticación:** Requerida | **Permiso:** `manage_donations` — o ser el donante de la donación

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `donation` | integer | ID de la donación |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "user_id": 2,
  "amount": 500,
  "note": "Para la reparación del banco de la guild",
  "status": "pending",
  "reviewed_by_user_id": null,
  "reviewed_at": null,
  "created_at": "2025-01-10T00:00:00.000000Z",
  "updated_at": "2025-01-10T00:00:00.000000Z",
  "donor": { "id": 2, "name": "Zangles", "email": "zangles@example.com", "created_at": "..." }
}
```

---

### `PATCH /guilds/{guild}/donations/{donation}/review`

Aprueba o rechaza una donación. Solo aplica sobre donaciones en estado `pending`.

**Autenticación:** Requerida | **Permiso:** `manage_donations`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `donation` | integer | ID de la donación |

**Body (JSON):**

| Campo | Tipo | Requerido | Reglas |
|---|---|---|---|
| `decision` | string | Sí | enum: `approved`, `rejected` |

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "user_id": 2,
  "amount": 500,
  "note": "Para la reparación del banco de la guild",
  "status": "approved",
  "reviewed_by_user_id": 1,
  "reviewed_at": "2025-01-11T00:00:00.000000Z",
  "created_at": "2025-01-10T00:00:00.000000Z",
  "updated_at": "2025-01-11T00:00:00.000000Z",
  "donor": { ... }
}
```

**Respuesta `409 Conflict`** (si la donación no está en estado `pending`):
```json
{
  "message": "La donación ya fue procesada."
}
```

---

## Log de auditoría

### `GET /guilds/{guild}/audit-log`

Retorna el historial paginado de acciones registradas en la guild.

**Autenticación:** Requerida | **Permiso:** `view_audit_log`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |

**Body:** Ninguno

**Respuesta `200 OK` (paginada):**
```json
{
  "data": [
    {
      "id": 1,
      "guild_id": 1,
      "actor_user_id": 1,
      "target_user_id": 2,
      "event_type": "member_kicked",
      "payload": { "reason": "Conducta inapropiada" },
      "created_at": "2025-01-15T00:00:00.000000Z"
    }
  ],
  "links": { ... },
  "meta": { ... }
}
```

---

### `GET /guilds/{guild}/audit-log/{log}`

Retorna el detalle de una entrada individual del log de auditoría.

**Autenticación:** Requerida | **Permiso:** `view_audit_log`

**Path Parameters:**

| Parámetro | Tipo | Descripción |
|---|---|---|
| `guild` | integer | ID de la guild |
| `log` | integer | ID de la entrada del log |

**Body:** Ninguno

**Respuesta `200 OK`:**
```json
{
  "id": 1,
  "guild_id": 1,
  "actor_user_id": 1,
  "target_user_id": 2,
  "event_type": "member_kicked",
  "payload": { "reason": "Conducta inapropiada" },
  "created_at": "2025-01-15T00:00:00.000000Z"
}
```

---

## Referencia de enums

### Permisos de guild (`permission_slugs`)

| Valor | Descripción |
|---|---|
| `is_guild_member` | Acceso básico como miembro |
| `invite_members` | Invitar usuarios a la guild |
| `approve_members` | Aprobar o rechazar solicitudes de ingreso |
| `kick_members` | Expulsar miembros |
| `manage_roles` | Crear y modificar roles |
| `manage_events` | Crear y cancelar eventos |
| `register_attendance` | Registrar asistencia a eventos |
| `manage_dkp` | Otorgar y deducir DKP |
| `manage_donations` | Revisar donaciones |
| `view_audit_log` | Ver el log de auditoría |
| `transfer_leadership` | Transferir el liderazgo de la guild |

### Estado de miembro (`status`)

| Valor | Descripción |
|---|---|
| `active` | Miembro activo |
| `pending_request` | Solicitud de ingreso pendiente |
| `pending_invite` | Invitación pendiente de aceptar |
| `rejected` | Solicitud rechazada |
| `kicked` | Expulsado |
| `left` | Abandonó la guild |

### Estado de evento (`status`)

| Valor | Descripción |
|---|---|
| `scheduled` | Programado |
| `completed` | Completado |
| `cancelled` | Cancelado |

### Respuesta RSVP (`response`)

| Valor | Descripción |
|---|---|
| `confirmed` | Confirmado |
| `declined` | Declinado |
| `tentative` | Tentativo |

### Estado de donación (`status`)

| Valor | Descripción |
|---|---|
| `pending` | Pendiente de revisión |
| `approved` | Aprobada |
| `rejected` | Rechazada |

---

## Respuestas de error comunes

| Código | Descripción |
|---|---|
| `401 Unauthorized` | Token inválido, expirado o no provisto |
| `403 Forbidden` | Sin permiso suficiente en la guild |
| `404 Not Found` | Recurso no encontrado |
| `409 Conflict` | Estado inválido para la operación (ej: donación ya procesada) |
| `422 Unprocessable Entity` | Error de validación o de negocio (ej: DKP insuficiente, miembro ya existe) |

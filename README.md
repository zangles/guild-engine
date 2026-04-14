<p align="center">
  <img src="GuildEngineLogo.png" alt="Guild Engine Logo" width="1024">
</p>

# Guild Engine

API REST para la gestión de guilds de videojuegos. Construida con **Laravel 13** y **Laravel Passport** (OAuth2).

Funcionalidades principales: gestión de miembros y roles, eventos con RSVP, sistema de DKP, donaciones, log de auditoría e integración con Discord.

> Para instrucciones de instalación y configuración del entorno, ver [INSTALL.md](INSTALL.md).
> Para la documentación detallada de cada endpoint (parámetros, tipos, respuestas), ver [APIDOC.md](APIDOC.md).

---

## Autenticación

La API usa **OAuth2 Password Grant** via Laravel Passport. Todos los endpoints protegidos requieren el header:

```
Authorization: Bearer <token>
```

### Registrar usuario

```
POST /api/auth/register
```

```json
{
  "name": "Zangles",
  "email": "zangles@example.com",
  "password": "secret",
  "password_confirmation": "secret"
}
```

### Login

```
POST /api/auth/login
```

```json
{
  "email": "zangles@example.com",
  "password": "secret"
}
```

Responde con `access_token` y `token_type`.

### Logout

```
POST /api/auth/logout
```

Requiere autenticación. Revoca el token actual.

---

## Guilds

| Método | Endpoint | Descripción | Auth |
|---|---|---|---|
| GET | `/api/guilds` | Listar todas las guilds | No |
| GET | `/api/guilds/{guild}` | Ver detalle de una guild | No |
| POST | `/api/guilds` | Crear una guild | Sí |
| PUT | `/api/guilds/{guild}` | Actualizar una guild | Sí |

Al crear una guild, el usuario creador queda como **Líder** automáticamente.

---

## Miembros

Todos los endpoints de miembros tienen el prefijo `/api/guilds/{guild}/`.

| Método | Endpoint | Descripción | Permiso requerido |
|---|---|---|---|
| GET | `me` | Ver tu propio contexto como miembro | `is_guild_member` |
| GET | `members` | Listar miembros de la guild | `is_guild_member` |
| POST | `join` | Solicitar unirse a la guild | — |
| POST | `invite` | Invitar a un usuario | `invite_members` |
| POST | `members/{member}/approve` | Aprobar solicitud de ingreso | `approve_members` |
| POST | `members/{member}/reject` | Rechazar solicitud | `approve_members` |
| POST | `members/{member}/kick` | Expulsar a un miembro | `kick_members` |
| PATCH | `members/{member}/role` | Cambiar el rol de un miembro | `manage_roles` |
| POST | `transfer-leadership` | Transferir liderazgo | `transfer_leadership` |

---

## Roles de guild

| Método | Endpoint | Descripción | Permiso requerido |
|---|---|---|---|
| GET | `/api/guilds/{guild}/roles` | Listar roles | `is_guild_member` |
| POST | `/api/guilds/{guild}/roles` | Crear rol | `manage_roles` |
| PUT | `/api/guilds/{guild}/roles/{role}` | Actualizar rol | `manage_roles` |

### Roles del sistema

Cada guild tiene tres roles predefinidos que no pueden eliminarse:

| Rol | Permisos |
|---|---|
| **Líder** | Todos los permisos |
| **Oficial** | Todos excepto `manage_roles` y `transfer_leadership` |
| **Miembro** | Solo `is_guild_member` |

Los permisos del rol Líder no pueden modificarse.

### Permisos disponibles

| Permiso | Descripción |
|---|---|
| `is_guild_member` | Acceso básico como miembro |
| `invite_members` | Invitar usuarios a la guild |
| `approve_members` | Aprobar o rechazar solicitudes |
| `kick_members` | Expulsar miembros |
| `manage_roles` | Crear y modificar roles |
| `manage_events` | Crear y cancelar eventos |
| `register_attendance` | Registrar asistencia a eventos |
| `manage_dkp` | Otorgar y deducir DKP |
| `manage_donations` | Revisar donaciones |
| `view_audit_log` | Ver el log de auditoría |
| `transfer_leadership` | Transferir el liderazgo de la guild |

---

## Eventos

| Método | Endpoint | Descripción | Permiso requerido |
|---|---|---|---|
| GET | `/api/guilds/{guild}/events` | Listar eventos | `is_guild_member` |
| POST | `/api/guilds/{guild}/events` | Crear evento | `manage_events` |
| POST | `/api/guilds/{guild}/events/{event}/cancel` | Cancelar evento | `manage_events` |
| POST | `/api/guilds/{guild}/events/{event}/attendance` | Registrar asistencia | `register_attendance` |
| PUT | `/api/guilds/{guild}/events/{event}/rsvp` | Confirmar/actualizar RSVP | `is_guild_member` |

Al crear un evento se despacha una notificación a Discord vía queue (después del commit).

---

## DKP (Dragon Kill Points)

| Método | Endpoint | Descripción | Permiso requerido |
|---|---|---|---|
| GET | `/api/guilds/{guild}/members/{member}/dkp/balance` | Ver balance DKP | `is_guild_member` |
| GET | `/api/guilds/{guild}/members/{member}/dkp/history` | Historial de transacciones | `is_guild_member` |
| POST | `/api/guilds/{guild}/members/{member}/dkp/grant` | Otorgar DKP | `manage_dkp` |
| POST | `/api/guilds/{guild}/members/{member}/dkp/deduct` | Deducir DKP | `manage_dkp` |

El balance se mantiene materializado en `dkp_balances`. Cada transacción guarda el `balance_after` para reconstrucción histórica. Deducir más DKP del disponible retorna un error `422`.

---

## Donaciones

| Método | Endpoint | Descripción | Permiso requerido |
|---|---|---|---|
| GET | `/api/guilds/{guild}/donations` | Donaciones pendientes | `is_guild_member` |
| GET | `/api/guilds/{guild}/donations/history` | Historial de donaciones | `is_guild_member` |
| POST | `/api/guilds/{guild}/donations` | Registrar donación | `is_guild_member` |
| PATCH | `/api/guilds/{guild}/donations/{donation}/review` | Aprobar/rechazar donación | `manage_donations` |

---

## Log de auditoría

```
GET /api/guilds/{guild}/audit-log
```

Permiso requerido: `view_audit_log`

Retorna un historial de acciones realizadas en la guild (creación de eventos, cambios de rol, DKP, etc.).

---

## Respuestas de error comunes

| Código | Descripción |
|---|---|
| `401` | Token inválido o no provisto |
| `403` | Sin permiso suficiente en la guild |
| `404` | Recurso no encontrado |
| `422` | Error de validación o negocio (ej: DKP insuficiente, miembro ya existe) |

---

## Arquitectura

El proyecto sigue el patrón **MDA** (Model-Driven Architecture):

```
Controllers → GuildPermissionGate → Services → Repositories → Models
```

- Los **Finders** encapsulan queries de búsqueda.
- Los **Repositories** manejan persistencia.
- Los **Services** contienen lógica de dominio.
- Las transacciones de base de datos ocurren en los **ApplicationServices**.

Ver [MDA.md](MDA.md) para más detalle arquitectónico.

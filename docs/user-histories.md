# Historias de Usuario — Guild Engine Core

## ÉPICO 1 — Autenticación

**HU-01** — Como usuario, quiero registrarme con email y contraseña para acceder a la plataforma.
- Email único en el sistema
- Contraseña almacenada con hash
- Retorna un access token OAuth2 (Laravel Passport) al completar el registro

**HU-02** — Como usuario registrado, quiero iniciar sesión con email y contraseña para obtener acceso autenticado.
- Utiliza el flujo Password Grant de OAuth2 (Laravel Passport)
- Retorna access token y refresh token
- Error descriptivo si las credenciales son incorrectas

---

## ÉPICO 2 — Gremios

**HU-03** — Como usuario autenticado, quiero crear un gremio indicando nombre, descripción y juego, para centralizar la gestión de mi comunidad.
- El creador queda automáticamente como Líder
- El gremio puede configurarse como público o privado (a elección del líder)

**HU-04** — Como líder, quiero editar el nombre, descripción, juego y visibilidad de mi gremio.

**HU-05** — Como cualquier usuario, quiero buscar gremios por nombre o juego para encontrar comunidades.
- Búsqueda parcial por nombre
- Filtro por juego

**HU-06** — Como cualquier usuario, quiero ver el perfil público de un gremio con su nombre, descripción, juego, cantidad de miembros y si está abierto a nuevos integrantes.

---

## ÉPICO 3 — Membresía

**HU-07** — Como usuario, quiero solicitar unirme a un gremio público para participar en él.
- Crea una solicitud en estado pendiente
- Solo aplica si el gremio acepta solicitudes

**HU-08** — Como miembro con permiso, quiero invitar directamente a un usuario registrado para que se una al gremio.
- El usuario invitado puede aceptar o rechazar

**HU-09** — Como miembro con permiso, quiero aprobar o rechazar solicitudes de membresía pendientes.

**HU-10** — Como miembro, quiero abandonar un gremio voluntariamente.
- Su historial de DKP y donaciones se conserva
- El Líder no puede abandonar sin antes transferir el liderazgo

**HU-11** — Como miembro con permiso, quiero expulsar a un miembro del gremio.
- No se puede expulsar al Líder
- El historial del miembro se conserva

**HU-12** — Como Líder, quiero transferir el liderazgo a otro miembro para poder retirarme del gremio.
- El Líder anterior queda con rol Oficial

---

## ÉPICO 4 — Roles y permisos

**HU-13** — Como Líder, quiero que el gremio tenga por defecto tres roles (Líder, Oficial, Miembro) con permisos predefinidos.

Permisos por defecto:

| Permiso                   | Líder | Oficial | Miembro |
|---------------------------|:-----:|:-------:|:-------:|
| Gestionar eventos         | ✓     | ✓       |         |
| Aprobar miembros          | ✓     | ✓       |         |
| Invitar miembros          | ✓     | ✓       |         |
| Expulsar miembros         | ✓     | ✓       |         |
| Gestionar DKP             | ✓     | ✓       |         |
| Gestionar donaciones      | ✓     | ✓       |         |
| Registrar asistencia real | ✓     | ✓       |         |
| Ver log de auditoría      | ✓     | ✓       |         |
| Crear/editar roles        | ✓     |         |         |
| Transferir liderazgo      | ✓     |         |         |

**HU-14** — Como Líder, quiero crear roles personalizados con un subconjunto de los permisos disponibles.

**HU-15** — Como Líder, quiero editar los permisos de un rol existente (excepto el rol Líder, que es inmutable).

**HU-16** — Como Líder, quiero asignar un rol a un miembro del gremio.

---

## ÉPICO 5 — Eventos

**HU-17** — Como miembro con permiso, quiero crear un evento con fecha, hora y cupos máximos para organizar actividades del gremio.
- Si hay webhook configurado, se notifica al canal de Discord

**HU-18** — Como miembro, quiero confirmar, rechazar o marcarme como tentativo en un evento (RSVP).
- Solo mientras el evento no haya ocurrido ni esté cancelado
- El cupo máximo no bloquea el RSVP (solo es informativo por ahora)

**HU-19** — Como miembro con permiso, quiero registrar la asistencia real de los participantes de un evento ya ocurrido.

**HU-20** — Como miembro con permiso, quiero cancelar un evento.
- El evento queda marcado como cancelado, no se elimina

**HU-21** — Como miembro, quiero ver el listado de eventos del gremio con su estado (próximo, ocurrido, cancelado).

---

## ÉPICO 6 — DKP

**HU-22** — Como Líder, quiero configurar el sistema de DKP del gremio definiendo el nombre de la moneda (ej: "Puntos", "Tokens", "DKP").

**HU-23** — Como miembro con permiso, quiero otorgar DKP a uno o varios miembros registrando el motivo (ej: asistencia a raid, kill de boss).
- Queda registrado en el log de auditoría con actor, receptor, cantidad y motivo

**HU-24** — Como miembro con permiso, quiero descontar DKP a un miembro registrando el motivo.
- El saldo no puede quedar por debajo de 0

**HU-25** — Como miembro, quiero ver mi saldo actual de DKP y mi historial de cambios dentro del gremio.

---

## ÉPICO 7 — Donaciones

**HU-26** — Como miembro, quiero registrar una donación de moneda del juego al gremio indicando la cantidad.
- Queda en estado pendiente hasta ser aprobada

**HU-27** — Como miembro con permiso, quiero aprobar o rechazar una donación pendiente.
- Queda registrado en el log de auditoría con actor y resultado

**HU-28** — Como miembro, quiero ver el historial de donaciones aprobadas del gremio.

---

## ÉPICO 8 — Log de auditoría

**HU-29** — Como miembro con permiso, quiero ver el log de auditoría del gremio con todos los cambios de DKP y donaciones, incluyendo quién realizó la acción, sobre quién, cuándo y el detalle del cambio.

---

## ÉPICO 9 — Integración Discord

**HU-30** — Como Líder, quiero configurar una URL de webhook de Discord para que el gremio reciba notificaciones automáticas.

**HU-31** — Como Líder, quiero configurar con cuánta anticipación se envía la notificación de proximidad de cada evento (ej: 30 min, 1 hora, 1 día antes).

**HU-32** — Como miembro del gremio, quiero que cuando se cree un evento se envíe automáticamente una notificación al canal de Discord configurado.

**HU-33** — Como miembro del gremio, quiero recibir una notificación en Discord con la anticipación configurada antes de que comience un evento.
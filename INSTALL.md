# Instalación — Guild Engine

## Requisitos

| Herramienta | Versión mínima |
|---|---|
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js / NPM | 18+ |
| Base de datos | SQLite (por defecto) o MySQL 8+ |

---

## Instalación rápida (un solo comando)

```bash
composer run setup
```

Este script ejecuta automáticamente los pasos 1–5 de abajo. Luego seguí con el paso 6 (Passport).

---

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone <url-del-repo>
cd guild-engine
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Configurar el entorno

```bash
cp .env.example .env
php artisan key:generate
```

El proyecto usa **SQLite por defecto** (`database/database.sqlite`). Si preferís MySQL, editá `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=guild_engine
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar migraciones

```bash
php artisan migrate
```

### 5. Instalar dependencias JS y compilar assets

```bash
npm install
npm run build
```

### 6. Configurar Laravel Passport

```bash
php artisan passport:install
```

Cuando pida el nombre del cliente personal, ingresá `users`:

```
What should we name the personal access client? [Laravel Personal Access Client]:
> users
```

---

## Levantar el servidor

**Modo producción / simple:**

```bash
php artisan serve
```

La API queda disponible en `http://localhost:8000`.

**Modo desarrollo** (servidor + queue worker + logs + Vite en paralelo):

```bash
composer run dev
```

---

## Tests

```bash
composer run test
```

---

## Variables de entorno relevantes

| Variable | Descripción | Default |
|---|---|---|
| `APP_ENV` | Entorno (`local`, `production`) | `local` |
| `APP_KEY` | Clave de cifrado (generada con `key:generate`) | — |
| `DB_CONNECTION` | Driver de base de datos (`sqlite`, `mysql`) | `sqlite` |
| `QUEUE_CONNECTION` | Driver de colas (para notificaciones Discord) | `database` |

Para notificaciones de Discord, configurá también el webhook en `config/services.php` o mediante variables de entorno adicionales según la integración implementada.

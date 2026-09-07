# BilleterIA — Backend (API REST)

> API REST del proyecto BilleterIA. Construida con **Laravel 12** + **Sanctum** + **Google Gemini AI**.

---

## Requisitos Previos

- PHP 8.2+
- Composer 2.x
- PostgreSQL (producción) o SQLite (desarrollo local)
- API Key de [Google Gemini](https://aistudio.google.com/app/apikey)
- Credenciales de [Google OAuth 2.0](https://console.cloud.google.com)

---

## Instalación

```bash
# 1. Instalar dependencias
composer install

# 2. Crear el fichero de entorno
cp .env.example .env

# 3. Generar clave de aplicación
php artisan key:generate

# 4. Editar .env con tus credenciales (ver abajo)

# 5. Ejecutar migraciones + seeders (incluye usuario evaluador)
php artisan migrate --seed

# 6. Crear enlace para almacenamiento de avatares
php artisan storage:link

# 7. Arrancar el servidor
php artisan serve
```

> API disponible en `http://localhost:8000`

---

## Variables de Entorno Clave

```env
APP_NAME=BilleterIA
APP_URL=http://localhost:8000

# SQLite para local (sin configuración adicional):
DB_CONNECTION=sqlite

# PostgreSQL para producción:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=billeteria
# DB_USERNAME=postgres
# DB_PASSWORD=tu_password

# Google OAuth
GOOGLE_CLIENT_ID=tu_client_id
GOOGLE_CLIENT_SECRET=tu_client_secret

# Google Gemini AI
GEMINI_API_KEY=tu_api_key
```

---

## Endpoints de la API

### Autenticación (pública)
| Método | Ruta | Descripción |
|--------|------|-------------|
| `POST` | `/api/register` | Registro con email + contraseña |
| `POST` | `/api/login` | Login con email + contraseña |
| `POST` | `/api/google-login` | Login con Google OAuth |

### Autenticación (privada — Bearer token)
| Método | Ruta | Descripción |
|--------|------|-------------|
| `POST` | `/api/logout` | Cerrar sesión |
| `GET` | `/api/user` | Datos del usuario autenticado |
| `POST` | `/api/user/avatar` | Subir avatar |

### Transacciones (privada)
| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/transactions` | Listar transacciones |
| `POST` | `/api/transactions` | Crear transacción |
| `GET` | `/api/transactions/{id}` | Ver transacción |
| `PUT` | `/api/transactions/{id}` | Editar transacción |
| `DELETE` | `/api/transactions/{id}` | Eliminar transacción |

### Objetivos de Ahorro (privada)
| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/saving-goals` | Listar objetivos |
| `POST` | `/api/saving-goals` | Crear objetivo |
| `GET` | `/api/saving-goals/{id}` | Ver objetivo |
| `PUT` | `/api/saving-goals/{id}` | Editar objetivo |
| `DELETE` | `/api/saving-goals/{id}` | Eliminar objetivo |

### Dashboard (privada)
| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/dashboard` | Resumen financiero del usuario |

### Coach IA
| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| `POST` | `/api/coach/chat` | ✅ | Chat con Billetín (datos reales) |
| `GET` | `/api/coach/history` | ✅ | Historial de conversación |
| `POST` | `/api/demo/coach/chat` | ❌ | Chat demo (datos de ejemplo) |

### Logros
| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/achievements` | Todos los logros |
| `GET` | `/api/users/{id}/achievements` | Logros del usuario |

### Categorías
| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/api/categories` | Todas las categorías |

---

## Usuario de Prueba

Creado automáticamente con `php artisan migrate --seed`:

| Campo | Valor |
|-------|-------|
| Email | `profesor@billeteria.com` |
| Contraseña | `evaluador123` |

Para crearlo sin re-migrar: `php artisan db:seed --class=EvaluatorSeeder`

---

## Base de Datos — Modelos Principales

```
users               → id, nickname, email, password, avatar
transactions        → id, user_id, category_id, type, title, amount, transaction_date
saving_goals        → id, user_id, title, target_amount, current_amount, target_date, priority, emoji
coach_messages      → id, user_id, message, response, mood
coach_preferences   → id, user_id, preferred_name, conversation_style, coach_background
achievements        → id, title, description, icon
achievement_user    → user_id, achievement_id (pivot)
categories          → id, name, type
```

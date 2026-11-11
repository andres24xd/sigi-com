# SIGI-COM

Sistema de Información para la Gestión Integral de Inventarios, Compras y Ventas para
negocios comerciales minoristas.

Proyecto académico de **Electiva Profesional II — Ingeniería de Sistemas (UNIAJS)**,
construido con Laravel 12, MariaDB y Blade + Tailwind CSS.

## Integrantes

- Andrés Felipe Sánchez Acuña
- Luis José Villadiego Pineda
- Carlos Andrés Ortega Cadrazco

**Docente:** Guillermo Antonio González Márquez

## Módulos

1. **Catálogo e inventario** — categorías y productos con stock, precios y alertas.
2. **Proveedores y compras** — directorio, compras con detalle y aumento automático de stock.
3. **Clientes y ventas** — directorio, ventas con detalle, consumidor final y descuento de stock.
4. **Reportes** — historial por fecha, cajero, cliente y proveedor, más inventario y stock bajo.
5. **Usuarios y roles** — estructura de datos lista; login y permisos en la siguiente fase.

## Stack

| Componente | Versión |
| --- | --- |
| Laravel | 12.x |
| PHP | 8.2+ |
| MariaDB / MySQL | 10.4+ |
| Blade + Tailwind CSS | 4.x |
| Vite | 7.x |
| PHPUnit | 11.x |

## Requisitos

- PHP 8.2 o superior con `pdo_mysql`
- Composer 2.x
- Node.js 20+ y npm
- MariaDB o MySQL en ejecución (por ejemplo, XAMPP)

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configura la base de datos en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigi_com_db
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos y carga los datos de prueba:

```sql
CREATE DATABASE sigi_com_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

```bash
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

La aplicación queda disponible en <http://127.0.0.1:8000>.

## Credenciales de prueba

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@sigicom.com` | `admin123` |
| Cajero | `cajero@sigicom.com` | `cajero123` |

El sistema exige iniciar sesión en `/login`. Cada rol entra a un panel distinto y solo ve
las secciones que le corresponden.

## Roles y permisos

| Módulo | Administrador | Cajero |
| --- | --- | --- |
| Panel | Resumen general del negocio | Resumen de caja y ventas propias |
| Categorías y productos | Crear, editar y eliminar | Solo consultar |
| Proveedores | Total | Sin acceso |
| Compras | Total | Sin acceso |
| Clientes | Total | Total |
| Ventas | Total | Total |
| Reportes | Todos | Solo sus propias ventas |

Las restricciones se aplican en el servidor con el middleware `role`, no solo ocultando
enlaces en la interfaz. Un acceso no permitido responde con la pantalla «Acceso restringido».

## Rutas principales

| URI | Descripción |
| --- | --- |
| `/login` | Inicio de sesión |
| `/` | Panel (general o de cajero según el rol) |
| `/inventario/categorias` | CRUD de categorías |
| `/inventario/productos` | CRUD de productos |
| `/terceros/proveedores` | CRUD de proveedores |
| `/terceros/clientes` | CRUD de clientes |
| `/operaciones/compras` | Registro y consulta de compras |
| `/operaciones/ventas` | Registro y consulta de ventas |
| `/reportes` | Reportes de ventas, compras e inventario |

Cada entidad maestra cuenta con listado, creación, detalle, edición y eliminación. Los
formularios usan `@csrf` y `@method()`, la validación vive en Form Requests y las respuestas
incluyen mensajes flash de éxito o error.

## Pruebas

```bash
php artisan test
```

La suite cubre esquema y seeder, autenticación y permisos por rol, rutas y verbos HTTP,
CRUD completo, validaciones, retención de datos con `old()`, control de stock y reportes.

## Estructura relevante

```
app/Http/Controllers/     Controladores por módulo
app/Http/Middleware/      Middleware de roles
app/Http/Requests/        Validación con Form Requests
app/Models/              Modelos Eloquent y relaciones
database/migrations/     Esquema de las 9 tablas del dominio
database/seeders/        Datos de prueba coherentes
resources/views/         Vistas Blade, layout y componentes
routes/web.php           Rutas nombradas y agrupadas
tests/Feature/           Pruebas de integración
docs/                    Informes de las actividades
```

## Documentación

- [`docs/INFORME_ACTIVIDAD3.md`](docs/INFORME_ACTIVIDAD3.md) — rutas, controladores y vistas.

## Licencia

Proyecto académico. Uso educativo.

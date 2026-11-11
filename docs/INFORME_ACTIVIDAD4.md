# Actividad Complementaria 4

## Operaciones CRUD completas, validación e integración Eloquent–Vista

**Sistema:** SIGI-COM — Sistema de Gestión Integral de Inventarios, Compras y Ventas  
**Curso:** Electiva Profesional II — Ingeniería de Sistemas  
**Integrantes:** Andrés Felipe Sánchez Acuña · Luis José Villadiego Pineda · Carlos Andrés Ortega Cadrazco  
**Docente:** Guillermo Antonio González Márquez  
**Fecha:** 11 de noviembre de 2026  
**Repositorio:** <https://github.com/andres24xd/sigi-com>  
**Modalidad:** Opción A — Monolito Laravel con Blade + Tailwind CSS

---

## 1. Objetivo

Completar la integración entre las vistas Blade, los controladores y los modelos Eloquent,
de modo que las entidades principales del sistema ejecuten el ciclo CRUD completo
(crear, consultar en detalle, editar y eliminar) con validación en el servidor,
retención de datos (`old()`) y retroalimentación al usuario mediante mensajes de sesión.

---

## 2. Alcance implementado

| Entidad | Crear | Consultar | Editar | Eliminar |
| --- | --- | --- | --- | --- |
| Categorías | ✅ | ✅ (nueva pantalla de detalle) | ✅ | ✅ |
| Productos | ✅ | ✅ (detalle + movimientos) | ✅ | ✅ |
| Proveedores | ✅ | ✅ (detalle + historial de compras) | ✅ | ✅ |
| Clientes | ✅ | ✅ (detalle + historial de ventas) | ✅ | ✅ |
| Compras | ✅ | ✅ | — | ✅ (revierte stock) |
| Ventas | ✅ | ✅ | — | ✅ (revierte stock) |

Compras y ventas no se editan a propósito: modificar un documento histórico rompería la
trazabilidad del inventario (RNF02). Para corregirlas se elimina el documento y se registra
uno nuevo.

### 2.1 Pantallas de detalle agregadas

- `GET /inventario/categorias/{categoria}` → datos de la categoría y sus productos.
- `GET /inventario/productos/{producto}` → precios, stock, valor en inventario y los
  últimos movimientos de compra y venta.
- `GET /terceros/proveedores/{proveedor}` → contacto, total comprado e historial de compras.
- `GET /terceros/clientes/{cliente}` → contacto, total comprado e historial de ventas.

Cada pantalla incluye acciones de volver, editar y eliminar, y enlaces a los documentos
relacionados.

### 2.2 Rutas

El proyecto registra **43 rutas** nombradas. Los cuatro catálogos usan `Route::resource()`
completo (7 rutas cada uno): `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`.
Los verbos HTTP quedan así:

| Operación | Método | Ejemplo |
| --- | --- | --- |
| Listar | `GET` | `/inventario/productos` |
| Crear (formulario) | `GET` | `/inventario/productos/create` |
| Guardar | `POST` | `/inventario/productos` |
| Ver detalle | `GET` | `/inventario/productos/{producto}` |
| Editar (formulario) | `GET` | `/inventario/productos/{producto}/edit` |
| Actualizar | `PUT/PATCH` | `/inventario/productos/{producto}` |
| Eliminar | `DELETE` | `/inventario/productos/{producto}` |

Los formularios usan `@csrf`, `@method('PUT')` y `@method('DELETE')`; nunca se envía un
verbo distinto por `GET`.

---

## 3. Validación de datos

La validación vive en **Form Requests** (`app/Http/Requests`), no en el controlador:

| Form Request | Reglas principales |
| --- | --- |
| `CategoriaRequest` | `nombre` requerido, texto, máximo 100, único (ignora el propio registro al editar) |
| `ProductoRequest` | `nombre` requerido; `categoria_id` requerido, entero y existente; `precio_venta` numérico entre 0 y 99 999 999; `stock_actual` entero mayor o igual a 0 |
| `ProveedorRequest` | `nombre` requerido, máximo 150; `telefono` opcional con formato válido de 7 a 20 caracteres |
| `ClienteRequest` | `nombre` requerido, máximo 150; `telefono` opcional con formato válido de 7 a 20 caracteres |
| `StoreCompraRequest` | proveedor existente, fecha válida y al menos una línea con producto existente, cantidad y precio válidos |
| `StoreVentaRequest` | cliente opcional existente, fecha válida, líneas con producto existente y stock suficiente |

Los mensajes están traducidos al español en `lang/es/validation.php` y los nombres de los
campos también (`attributes`), por lo que el usuario lee «El campo categoría es obligatorio»
y no el nombre técnico de la columna.

### 3.1 Visualización de errores y retención de datos

- El layout incluye `partials/flash.blade.php`, que muestra un bloque con **todos** los
  errores de validación cuando `$errors->any()`.
- Los componentes `x-input` y `x-select` muestran el mensaje puntual de cada campo con
  `@error($name)` y pintan el borde en rojo.
- Los campos usan `old($name, $valor)`; si la validación falla, el formulario conserva lo
  que el usuario había escrito. Los `select` conservan la opción elegida con `@selected()`.

---

## 4. Persistencia y retroalimentación

### 4.1 Flujo de cada operación

```
Formulario Blade
   │ POST / PUT / DELETE  (+ @csrf y @method)
   ▼
Form Request  ──►  si falla, redirige atrás con errores y old()
   │ válido
   ▼
Controlador  ──►  Modelo Eloquent (create / update / delete)
   │
   ▼
redirect()->route('...index')  +  with('success', '...')
```

- **Crear y editar** redirigen al listado con `with('success', ...)`.
- **Eliminar** redirige al listado con confirmación. Si la entidad tiene registros
  relacionados que la protegen, se vuelve atrás con `with('error', ...)`:

| Entidad | Protección |
| --- | --- |
| Categoría con productos | No se elimina; mensaje de error |
| Producto con compras o ventas | No se elimina; mensaje de error |
| Proveedor con compras | No se elimina; mensaje de error |
| Cliente con ventas | Se elimina y sus ventas pasan a «consumidor final» (`nullOnDelete`) |

- Las confirmaciones de borrado piden confirmación en el navegador antes de enviar el
  `DELETE`.
- Compras y ventas se guardan dentro de `DB::transaction()` con `lockForUpdate()`, y su
  eliminación revierte el stock.

---

## 5. Pruebas automatizadas

La suite completa queda en **35 pruebas y 169 aserciones**, todas aprobadas:

```powershell
php artisan test
```

| Archivo | Pruebas | Cobertura |
| --- | --- | --- |
| `Actividad2Test.php` | 6 | Esquema, seeder, stock, totales y relaciones |
| `Actividad3Test.php` | 10 | Rutas, verbos, compras, ventas y reportes |
| `Actividad4Test.php` | 18 | Detalle, edición, eliminación, protecciones, validación y `old()` |
| `ExampleTest.php` | 1 | Humo de la aplicación |

Casos nuevos destacados:

- Las cuatro pantallas de detalle responden `200` y muestran el registro.
- Actualización por `PUT` de categoría, producto, proveedor y cliente.
- Eliminación por `DELETE` de las cuatro entidades.
- Bloqueo de eliminación con mensaje de error y registro intacto.
- Eliminación de cliente con ventas: las ventas quedan con `cliente_id = null`.
- Validación de categoria inexistente, precio negativo, stock no numérico, nombre repetido
  y teléfono inválido, comprobando que el dato no se guarda y que `old()` conserva el valor.

---

## 6. Cómo ejecutar el proyecto

```powershell
cd sigi-com
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
php artisan serve
```

Credenciales sembradas:

| Rol | Correo | Contraseña |
| --- | --- | --- |
| Administrador | `admin@sigicom.com` | `admin123` |
| Cajero | `cajero@sigicom.com` | `cajero123` |

> El login se implementa en la siguiente fase; por ahora las rutas están abiertas.

---

## 7. Conclusión

El CRUD queda completo e integrado en las cuatro entidades maestras, con consulta detallada,
edición y eliminación protegida. La validación en servidor impide datos corruptos, los
mensajes de error se muestran en la interfaz, los valores escritos se conservan y cada
operación termina con una redirección y un mensaje de confirmación. La suite automatizada
respalda el comportamiento y deja la base lista para la fase de autenticación y roles.

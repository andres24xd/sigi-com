# Actividad Complementaria 3

## Enrutamiento, controladores y vistas del dominio

**Sistema:** SIGI-COM — Sistema de Gestión Integral de Inventarios, Compras y Ventas  
**Curso:** Electiva Profesional II — Ingeniería de Sistemas  
**Integrantes:** Andrés Felipe Sánchez Acuña · Luis José Villadiego Pineda · Carlos Andrés Ortega Cadrazco  
**Docente:** Guillermo Antonio González Márquez  
**Fecha:** 6 de noviembre de 2026  
**Repositorio:** <https://github.com/andres24xd/sigi-com>  
**Modalidad:** Opción A — Monolito con Blade + Tailwind CSS

---

## 1. Objetivo y alcance

Implementar la capa de navegación y presentación de SIGI-COM sobre Laravel 12, usando
rutas nombradas y agrupadas, controladores con los verbos HTTP correctos y vistas Blade
dinámicas con un layout maestro y componentes reutilizables.

La entrega cubre los cinco módulos del sistema:

1. Autenticación y usuarios (estructura preparada; el login se implementa en la siguiente fase).
2. Catálogo e inventario: categorías y productos.
3. Proveedores y abastecimiento: proveedores, compras y detalle de compra.
4. Clientes y punto de venta: clientes, ventas y detalle de venta.
5. Consultas y reportes: resumen, ventas, compras e inventario.

---

## 2. Tecnologías utilizadas

| Componente | Versión / detalle |
| --- | --- |
| Laravel | 12.69.2 |
| PHP | 8.2.12 |
| MariaDB | 10.4.32 (XAMPP 8.2.12) |
| Composer | 2.11.1 |
| Node.js / npm | 24.21.0 / 11.19.0 |
| Tailwind CSS | 4.x con Vite 7 |
| Blade | Layout maestro, partials y componentes anónimos |
| JavaScript | Vanilla JS para las líneas dinámicas de compras y ventas |
| Pruebas | PHPUnit 11 + Laravel TestCase (18 pruebas) |
| Capturas | Brave Browser (Chromium) headless + Playwright |

---

## 3. Arquitectura MVC implementada

```
Navegador
   │  HTTP (GET / POST / PUT / DELETE)
   ▼
routes/web.php  ──►  Controladores (app/Http/Controllers)
   │                        │
   │                        ├── Form Requests (validación)
   │                        ├── Modelos Eloquent (datos y relaciones)
   │                        └── DB::transaction() (compras y ventas)
   ▼                        │
Vistas Blade  ◄─────────────┘
   │
   ├── layouts/app.blade.php
   ├── partials/ (sidebar, topbar, flash, iconos)
   └── components/ (stat-card, badge, input, select, empty-state…)
```

La lógica de negocio de compras y ventas está encapsulada en transacciones: si falla una
línea, no se guarda la cabecera ni se mueve el stock.

---

## 4. Rutas

### 4.1 Organización por grupos

| Prefijo | Nombre base | Entidades | Propósito |
| --- | --- | --- | --- |
| `/` | `dashboard` | Panel | Indicadores y accesos rápidos |
| `/inventario` | `inventario.` | Categorías, productos | Catálogo y existencias |
| `/operaciones` | `operaciones.` | Compras, ventas | Movimientos de entrada y salida |
| `/terceros` | `terceros.` | Proveedores, clientes | Directorios de contacto |
| `/reportes` | `reportes.` | Ventas, compras, inventario | Consultas y filtros |

Todas las rutas tienen nombre (`route()`), se agrupan por módulo con `prefix()` y `name()`,
y usan los verbos HTTP adecuados: `GET` para consultas, `POST` para crear, `PUT/PATCH`
para actualizar y `DELETE` para eliminar. Los catálogos usan `Route::resource()`.

### 4.2 Tabla completa de rutas

| Método | URI | Nombre | Acción |
| --- | --- | --- | --- |
| GET/HEAD | `/` | `dashboard` | `DashboardController@index` |
| GET/HEAD | `inventario/categorias` | `inventario.categorias.index` | `CategoriaController@index` |
| POST | `inventario/categorias` | `inventario.categorias.store` | `CategoriaController@store` |
| GET/HEAD | `inventario/categorias/create` | `inventario.categorias.create` | `CategoriaController@create` |
| GET/HEAD | `inventario/categorias/{categoria}/edit` | `inventario.categorias.edit` | `CategoriaController@edit` |
| PUT/PATCH | `inventario/categorias/{categoria}` | `inventario.categorias.update` | `CategoriaController@update` |
| DELETE | `inventario/categorias/{categoria}` | `inventario.categorias.destroy` | `CategoriaController@destroy` |
| GET/HEAD | `inventario/productos` | `inventario.productos.index` | `ProductoController@index` |
| POST | `inventario/productos` | `inventario.productos.store` | `ProductoController@store` |
| GET/HEAD | `inventario/productos/create` | `inventario.productos.create` | `ProductoController@create` |
| GET/HEAD | `inventario/productos/{producto}/edit` | `inventario.productos.edit` | `ProductoController@edit` |
| PUT/PATCH | `inventario/productos/{producto}` | `inventario.productos.update` | `ProductoController@update` |
| DELETE | `inventario/productos/{producto}` | `inventario.productos.destroy` | `ProductoController@destroy` |
| GET/HEAD | `operaciones/compras` | `operaciones.compras.index` | `CompraController@index` |
| POST | `operaciones/compras` | `operaciones.compras.store` | `CompraController@store` |
| GET/HEAD | `operaciones/compras/create` | `operaciones.compras.create` | `CompraController@create` |
| GET/HEAD | `operaciones/compras/{compra}` | `operaciones.compras.show` | `CompraController@show` |
| DELETE | `operaciones/compras/{compra}` | `operaciones.compras.destroy` | `CompraController@destroy` |
| GET/HEAD | `operaciones/ventas` | `operaciones.ventas.index` | `VentaController@index` |
| POST | `operaciones/ventas` | `operaciones.ventas.store` | `VentaController@store` |
| GET/HEAD | `operaciones/ventas/create` | `operaciones.ventas.create` | `VentaController@create` |
| GET/HEAD | `operaciones/ventas/{venta}` | `operaciones.ventas.show` | `VentaController@show` |
| DELETE | `operaciones/ventas/{venta}` | `operaciones.ventas.destroy` | `VentaController@destroy` |
| GET/HEAD | `terceros/proveedores` | `terceros.proveedores.index` | `ProveedorController@index` |
| POST | `terceros/proveedores` | `terceros.proveedores.store` | `ProveedorController@store` |
| GET/HEAD | `terceros/proveedores/create` | `terceros.proveedores.create` | `ProveedorController@create` |
| GET/HEAD | `terceros/proveedores/{proveedor}/edit` | `terceros.proveedores.edit` | `ProveedorController@edit` |
| PUT/PATCH | `terceros/proveedores/{proveedor}` | `terceros.proveedores.update` | `ProveedorController@update` |
| DELETE | `terceros/proveedores/{proveedor}` | `terceros.proveedores.destroy` | `ProveedorController@destroy` |
| GET/HEAD | `terceros/clientes` | `terceros.clientes.index` | `ClienteController@index` |
| POST | `terceros/clientes` | `terceros.clientes.store` | `ClienteController@store` |
| GET/HEAD | `terceros/clientes/create` | `terceros.clientes.create` | `ClienteController@create` |
| GET/HEAD | `terceros/clientes/{cliente}/edit` | `terceros.clientes.edit` | `ClienteController@edit` |
| PUT/PATCH | `terceros/clientes/{cliente}` | `terceros.clientes.update` | `ClienteController@update` |
| DELETE | `terceros/clientes/{cliente}` | `terceros.clientes.destroy` | `ClienteController@destroy` |
| GET/HEAD | `reportes` | `reportes.index` | `ReporteController@index` |
| GET/HEAD | `reportes/ventas` | `reportes.ventas` | `ReporteController@ventas` |
| GET/HEAD | `reportes/compras` | `reportes.compras` | `ReporteController@compras` |
| GET/HEAD | `reportes/inventario` | `reportes.inventario` | `ReporteController@inventario` |

Total: **39 rutas** registradas (`php artisan route:list --except-vendor`).

---

## 5. Controladores

| Controlador | Métodos | Responsabilidad |
| --- | --- | --- |
| `DashboardController` | `index` | Indicadores del día y del mes, stock bajo, últimas ventas, gráfica de 7 días y top de productos. |
| `CategoriaController` | `index`, `create`, `store`, `edit`, `update`, `destroy` | CRUD de categorías. Impide eliminar una categoría con productos. |
| `ProductoController` | `index`, `create`, `store`, `edit`, `update`, `destroy` | CRUD del catálogo, búsqueda por nombre, filtro por categoría y estado del stock. Impide borrar productos con movimientos. |
| `ProveedorController` | `index`, `create`, `store`, `edit`, `update`, `destroy` | CRUD de proveedores con conteo y total comprado. Impide borrar proveedores con compras. |
| `ClienteController` | `index`, `create`, `store`, `edit`, `update`, `destroy` | CRUD de clientes con conteo y total vendido. Al eliminar, las ventas quedan como consumidor final. |
| `CompraController` | `index`, `create`, `store`, `show`, `destroy` | Registro transaccional de compras, cálculo del total, incremento de stock y reversión al eliminar. |
| `VentaController` | `index`, `create`, `store`, `show`, `destroy` | Registro transaccional de ventas, precio tomado del catálogo, validación de stock, descuento y restauración al eliminar. |
| `ReporteController` | `index`, `ventas`, `compras`, `inventario` | Reportes con filtros por fecha, cajero, cliente, proveedor, categoría y estado. |

### Validación

Se usan Form Requests para desacoplar la validación:

- `CategoriaRequest` (nombre obligatorio y único, ignorando el registro en edición).
- `ProductoRequest` (categoría existente, precio numérico y stock entero no negativo).
- `ProveedorRequest` y `ClienteRequest`.
- `StoreCompraRequest` y `StoreVentaRequest` (cabecera + arreglo de detalles, productos distintos y cantidades positivas).

Los mensajes están en español mediante `lang/es/validation.php`.

### Integridad de las operaciones

- Compras y ventas se ejecutan dentro de `DB::transaction()`.
- Las ventas usan `lockForUpdate()` para evitar sobreventa en concurrencia.
- El stock se modifica únicamente como consecuencia de un detalle de compra o venta.
- Al eliminar una compra se valida que el stock alcance para revertirla; si no, se rechaza.

---

## 6. Vistas y componentes

### Layout maestro

`resources/views/layouts/app.blade.php` define la estructura común: `<head>`, carga de
Vite, sprite de iconos, sidebar, topbar, contenedor de mensajes flash, `@yield('content')`
y `@stack('scripts')`.

### Partials

- `partials/sidebar.blade.php`: navegación agrupada por módulos y estado activo con `routeIs()`.
- `partials/topbar.blade.php`: breadcrumb, fecha en español, acceso rápido a nueva venta y usuario.
- `partials/flash.blade.php`: mensajes de éxito, error y resumen de validación.
- `partials/icons.blade.php`: sprite SVG con los iconos del sistema.

### Componentes Blade reutilizables

| Componente | Uso |
| --- | --- |
| `<x-page-header>` | Título, descripción y acciones de cada pantalla. |
| `<x-stat-card>` | Tarjetas de indicadores con icono y tono. |
| `<x-badge>` | Estados de stock, roles y conteos. |
| `<x-input>` / `<x-select>` | Campos con label, valor antiguo y error de validación. |
| `<x-empty-state>` | Estados vacíos consistentes. |
| `<x-nav-link>` / `<x-icon>` | Navegación e iconografía. |

### Vistas por módulo

- `dashboard.blade.php`
- `inventario/categorias/{index,create,edit,_form}`
- `inventario/productos/{index,create,edit,_form}`
- `operaciones/compras/{index,create,show}`
- `operaciones/ventas/{index,create,show}`
- `terceros/proveedores/{index,create,edit,_form}`
- `terceros/clientes/{index,create,edit,_form}`
- `reportes/{index,ventas,compras,inventario,_tabs}`

---

## 7. Diseño visual y responsive

- **Tailwind CSS 4** compilado con Vite; paleta `brand` (verde esmeralda) definida en `@theme`.
- Tipografía **Instrument Sans** con fallback al sistema.
- Sidebar oscuro fijo en escritorio y deslizable en móvil (JavaScript + overlay).
- Tarjetas, tablas, badges y formularios con estados de foco, hover y error.
- Tablas con desplazamiento horizontal controlado y columnas secundarias ocultas en móvil.
- Se verificó con Playwright que `documentElement.scrollWidth === clientWidth` (390 px) en
  el panel, productos, ventas, formulario de venta y reporte de ventas: **sin desbordamiento horizontal**.
- Capturas móviles a 390 × 844 px.

---

## 8. Funcionalidad dinámica

### Formularios de compra y venta

Ambos formularios permiten agregar y quitar líneas sin recargar la página. El JavaScript:

1. Genera una fila nueva con el producto, cantidad, precio y subtotal.
2. Reindexa los nombres `detalles[n][...]` para que Laravel los reciba como arreglo.
3. Calcula el subtotal y el total en tiempo real con `Intl.NumberFormat('es-CO')`.
4. En ventas, toma el precio del catálogo, muestra el stock disponible y bloquea productos agotados.
5. En compras, permite digitar el costo unitario.

Ejemplo verificado en navegador: 3 unidades de Aceite Premier (12.500) + 2 unidades de
Agua Cristal (2.000) = **$ 41.500**, con stock disponible mostrado en cada línea.

### Servidor

El precio de venta se toma del producto en el servidor (no se confía en el valor enviado
por el navegador). Si la cantidad supera el stock, la transacción se revierte y se devuelve
un error de validación.

---

## 9. Evidencia de navegación

### 9.1 Panel de control

![Panel de control](capturas/01-dashboard.png)

*Indicadores del día, gráfica de ventas de los últimos 7 días, últimas ventas, alertas de
stock y valor del inventario.*

### 9.2 Inventario

![Categorías](capturas/02-categorias.png)

*Listado de categorías con conteo de productos y acciones de edición/eliminación.*

![Productos](capturas/03-productos.png)

*Catálogo con búsqueda, filtro por categoría y estado, stock y valorización.*

![Nuevo producto](capturas/04-producto-form.png)

*Formulario de creación con validación y ayuda contextual.*

![Editar producto](capturas/04b-producto-edit.png)

*Edición del producto y zona de eliminación protegida.*

### 9.3 Compras

![Compras](capturas/05-compras.png)

*Historial de compras con filtros por proveedor y fechas, y resumen de valores.*

![Registrar compra](capturas/06-compra-form.png)

*Formulario con líneas dinámicas, costo unitario y total calculado.*

![Detalle de compra](capturas/07-compra-detalle.png)

*Comprobante con productos, cantidades, subtotales y total.*

### 9.4 Ventas

![Ventas](capturas/08-ventas.png)

*Historial de ventas con filtros por cliente, cajero y fechas.*

![Nueva venta](capturas/09-venta-form.png)

*Punto de venta: cliente opcional (consumidor final) y fecha/hora.*

![Venta dinámica](capturas/09b-venta-form-dinamica.png)

*Dos líneas agregadas en tiempo real, precio de catálogo, stock disponible y total $ 41.500.*

![Detalle de venta](capturas/10-venta-detalle.png)

*Comprobante de venta con cliente, cajero y detalle de productos.*

### 9.5 Terceros

![Proveedores](capturas/11-proveedores.png)

*Directorio de proveedores con compras y total abastecido.*

![Clientes](capturas/12-clientes.png)

*Directorio de clientes con ventas y total comprado.*

### 9.6 Reportes

![Reportes](capturas/13-reportes.png)

*Resumen general, evolución mensual, ventas por categoría y productos más vendidos.*

![Reporte de ventas](capturas/14-reporte-ventas.png)

*Filtros por rango de fechas, cajero y cliente; gráfica diaria y detalle paginado.*

![Reporte de inventario](capturas/15-reporte-inventario.png)

*Existencias, valorización y estado por producto.*

### 9.7 Vista móvil (390 px)

![Panel móvil](capturas/16-mobile-dashboard.png)

*Panel completo en móvil, sin desbordamiento horizontal.*

![Productos móvil](capturas/18-mobile-productos.png)

*Tabla adaptada: se ocultan columnas secundarias y se conserva la acción de edición.*

![Ventas móvil](capturas/19-mobile-ventas.png)

*Historial adaptado a tres columnas; el número de venta abre el comprobante.*

![Formulario de venta móvil](capturas/17-mobile-venta-form.png)

*Formulario apilado en una columna, con etiquetas por campo.*

---

## 10. Pruebas y verificación

### Comandos ejecutados

```powershell
php artisan migrate:fresh --seed
php artisan route:list --except-vendor
php artisan test
npm run build
```

### Resultados

| Verificación | Resultado |
| --- | --- |
| Migraciones y seeder | Sin errores; 9 tablas del dominio pobladas |
| Rutas registradas | 39 rutas nombradas y agrupadas |
| Respuesta HTTP de las vistas | 23 URLs verificadas con estado 200 |
| Pruebas automatizadas | **18 pruebas aprobadas, 89 aserciones** |
| Formulario dinámico | Total y subtotales correctos en navegador |
| Responsive | `scrollWidth = clientWidth = 390` en 5 vistas |
| Build de assets | Vite compila CSS y JS sin errores |

### Pruebas de la Actividad 3

- Las rutas principales responden.
- Las rutas están nombradas y usan los verbos correctos.
- Se puede crear una categoría.
- No se permite repetir el nombre de una categoría.
- Se puede crear un producto.
- Una compra aumenta el stock y calcula el total.
- Una venta descuenta el stock y usa el precio del catálogo.
- No se puede vender más stock del disponible.
- Eliminar una compra revierte el stock.
- Los reportes aceptan filtros de fecha.

---

## 11. Instalación y ejecución

```powershell
# 1. Iniciar Apache y MySQL desde XAMPP

# 2. En la carpeta del proyecto
composer install
copy .env.example .env
php artisan key:generate

# 3. Crear la base
# CREATE DATABASE sigi_com_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 4. Migrar, poblar y compilar
php artisan migrate:fresh --seed
npm install
npm run build

# 5. Servir
php artisan serve
# http://127.0.0.1:8000
```

Credenciales sembradas: `admin@sigicom.com / admin123` y `cajero@sigicom.com / cajero123`
(el login se integrará en la siguiente actividad).

---

## 12. Conclusiones

- La navegación quedó centralizada en rutas nombradas y agrupadas por módulo, lo que facilita
  el mantenimiento y la generación de URLs con `route()`.
- Los controladores delegan la validación en Form Requests y protegen las operaciones
  transaccionales de compras y ventas.
- El layout maestro y los componentes Blade mantienen una identidad visual consistente en
  las 21 pantallas del sistema.
- El diseño es responsivo: se verificó en 1440 px y 390 px, corrigiendo el desbordamiento
  horizontal y adaptando las tablas.
- La aplicación queda lista para la siguiente fase: autenticación, roles y permisos.

---

## Anexo: estructura de archivos relevante

```
app/
  Http/
    Controllers/
      DashboardController.php
      CategoriaController.php
      ProductoController.php
      ProveedorController.php
      ClienteController.php
      CompraController.php
      VentaController.php
      ReporteController.php
    Requests/
      CategoriaRequest.php  ProductoRequest.php
      ProveedorRequest.php  ClienteRequest.php
      StoreCompraRequest.php  StoreVentaRequest.php
  Support/helpers.php
resources/
  css/app.css
  js/app.js
  views/
    layouts/app.blade.php
    partials/{sidebar,topbar,flash,icons}.blade.php
    components/{icon,nav-link,page-header,stat-card,badge,input,select,empty-state}.blade.php
    dashboard.blade.php
    inventario/  operaciones/  terceros/  reportes/
routes/web.php
lang/es/{validation,pagination}.php
tests/Feature/{Actividad2Test,Actividad3Test,ExampleTest}.php
docs/
  INFORME_ACTIVIDAD3.md
  INFORME_ACTIVIDAD3.docx
  capturas/*.png
```

# Actividad Complementaria 4 — Informe resumen

**Sistema:** SIGI-COM — Gestión Integral de Inventarios, Compras y Ventas  
**Curso:** Electiva Profesional II — Ingeniería de Sistemas (UNIAJS)  
**Integrantes:** Andrés Felipe Sánchez Acuña · Luis José Villadiego Pineda · Carlos Andrés Ortega Cadrazco  
**Docente:** Guillermo Antonio González Márquez  
**Fecha:** 11 de noviembre de 2026  
**Repositorio:** <https://github.com/andres24xd/sigi-com>

## Objetivo

Integrar las vistas y controladores con los modelos Eloquent para ejecutar el ciclo CRUD
completo (crear, consultar en detalle, editar y eliminar) con validación en el servidor,
retención de datos y retroalimentación al usuario.

## Alcance implementado

| Entidad | Crear | Detalle | Editar | Eliminar |
| --- | --- | --- | --- | --- |
| Categorías | ✅ | ✅ | ✅ | ✅ |
| Productos | ✅ | ✅ | ✅ | ✅ |
| Proveedores | ✅ | ✅ | ✅ | ✅ |
| Clientes | ✅ | ✅ | ✅ | ✅ |
| Compras | ✅ | ✅ | — | ✅ revierte stock |
| Ventas | ✅ | ✅ | — | ✅ revierte stock |

- Formularios con `@csrf`, `@method('PUT')` y `@method('DELETE')`.
- Cuatro pantallas nuevas de detalle con información del registro e historial relacionado.
- Compras y ventas no se editan para no romper la trazabilidad del inventario.

## Validación y experiencia de usuario

- Reglas en Form Requests: requeridos, texto, longitud máxima, enteros, numéricos,
  existencias (`exists`) y unicidad (`unique`).
- Errores visibles en un bloque general y por campo; mensajes en español.
- Retención de datos con `old()` y `@selected()` tras un fallo de validación.
- Redirección con mensaje flash de éxito después de cada operación.
- Borrados protegidos con mensaje de error: categoría con productos, producto con
  movimientos y proveedor con compras.
- Eliminar un cliente conserva sus ventas como consumidor final.

## Verificación

| Comprobación | Resultado |
| --- | --- |
| Pruebas específicas de la entrega | 18 aprobadas (`Actividad4Test`) |
| Suite del proyecto | 47 pruebas, 220 aserciones |
| Rutas del panel al cierre de la entrega | 43 nombradas |
| Assets | `npm run build` sin errores |
| Vistas | Blade compiladas correctamente |

**Commits de la entrega:**

```
a0faab3  feat: completa el CRUD con consulta detallada de las entidades maestras
7b5612a  feat: refuerza la validacion y agrega pruebas de CRUD, errores y datos old
2ccd8f4  docs: actualiza README y agrega informe de la Actividad 4
```

## Enlace del repositorio

**GitHub:** <https://github.com/andres24xd/sigi-com>

El repositorio incluye el código, las migraciones, los seeders, las pruebas automatizadas y
los informes de las actividades.

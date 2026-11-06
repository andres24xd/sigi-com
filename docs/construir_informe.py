"""Construye el informe de la Actividad 3 en formato .docx."""

from pathlib import Path

from docx import Document
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Inches, Pt, RGBColor

BASE = Path(__file__).resolve().parent
CAPTURAS = BASE / "capturas"

BRAND = RGBColor(5, 150, 105)
BRAND_DARK = RGBColor(6, 95, 70)
SLATE = RGBColor(51, 65, 85)
MUTED = RGBColor(100, 116, 139)


def fondo_celda(celda, color_hex: str) -> None:
    tc_pr = celda._tc.get_or_add_tcPr()
    shd = OxmlElement("w:shd")
    shd.set(qn("w:val"), "clear")
    shd.set(qn("w:color"), "auto")
    shd.set(qn("w:fill"), color_hex)
    tc_pr.append(shd)


def agregar_numero_pagina(parrafo) -> None:
    run = parrafo.add_run()
    inicio = OxmlElement("w:fldChar")
    inicio.set(qn("w:fldCharType"), "begin")
    instruccion = OxmlElement("w:instrText")
    instruccion.set(qn("xml:space"), "preserve")
    instruccion.text = "PAGE"
    fin = OxmlElement("w:fldChar")
    fin.set(qn("w:fldCharType"), "end")
    run._r.append(inicio)
    run._r.append(instruccion)
    run._r.append(fin)


def titulo(doc, texto: str, nivel: int = 1):
    p = doc.add_heading(texto, level=nivel)
    for run in p.runs:
        run.font.color.rgb = BRAND_DARK if nivel == 1 else BRAND
        run.font.name = "Calibri"
    p.paragraph_format.space_before = Pt(14 if nivel == 1 else 10)
    p.paragraph_format.space_after = Pt(6)
    return p


def parrafo(doc, texto: str, *, size: float = 10.5, color=SLATE, bold=False, italic=False):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.12
    run = p.add_run(texto)
    run.font.size = Pt(size)
    run.font.color.rgb = color
    run.bold = bold
    run.italic = italic
    return p


def vineta(doc, texto: str):
    p = doc.add_paragraph(texto, style="List Bullet")
    p.paragraph_format.space_after = Pt(2)
    for run in p.runs:
        run.font.size = Pt(10)
        run.font.color.rgb = SLATE
    return p


def codigo(doc, texto: str):
    tabla = doc.add_table(rows=1, cols=1)
    tabla.alignment = WD_TABLE_ALIGNMENT.CENTER
    celda = tabla.cell(0, 0)
    fondo_celda(celda, "F1F5F9")
    p = celda.paragraphs[0]
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(4)
    run = p.add_run(texto)
    run.font.name = "Consolas"
    run.font.size = Pt(8.5)
    run.font.color.rgb = RGBColor(30, 41, 59)
    doc.add_paragraph().paragraph_format.space_after = Pt(2)


def tabla(doc, encabezados, filas, *, anchos=None, size: float = 8.8):
    t = doc.add_table(rows=1, cols=len(encabezados))
    t.style = "Table Grid"
    t.alignment = WD_TABLE_ALIGNMENT.CENTER
    t.autofit = True

    for i, encabezado in enumerate(encabezados):
        celda = t.rows[0].cells[i]
        fondo_celda(celda, "065F46")
        p = celda.paragraphs[0]
        run = p.add_run(encabezado)
        run.bold = True
        run.font.size = Pt(size)
        run.font.color.rgb = RGBColor(255, 255, 255)

    for fila in filas:
        celdas = t.add_row().cells
        for i, valor in enumerate(fila):
            p = celdas[i].paragraphs[0]
            run = p.add_run(str(valor))
            run.font.size = Pt(size)
            run.font.color.rgb = SLATE

    if anchos:
        for fila in t.rows:
            for i, ancho in enumerate(anchos):
                fila.cells[i].width = Inches(ancho)

    doc.add_paragraph().paragraph_format.space_after = Pt(2)
    return t


def imagen(doc, nombre: str, leyenda: str, *, ancho: float = 6.3):
    ruta = CAPTURAS / nombre
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(6)
    p.paragraph_format.space_after = Pt(2)
    p.add_run().add_picture(str(ruta), width=Inches(ancho))

    c = doc.add_paragraph()
    c.alignment = WD_ALIGN_PARAGRAPH.CENTER
    c.paragraph_format.space_after = Pt(10)
    run = c.add_run(leyenda)
    run.font.size = Pt(8.5)
    run.italic = True
    run.font.color.rgb = MUTED


def imagen_lado_a_lado(doc, izquierda, derecha, leyenda_izq, leyenda_der):
    t = doc.add_table(rows=2, cols=2)
    t.alignment = WD_TABLE_ALIGNMENT.CENTER

    for i, (nombre, leyenda) in enumerate([(izquierda, leyenda_izq), (derecha, leyenda_der)]):
        celda = t.cell(0, i)
        p = celda.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.add_run().add_picture(str(CAPTURAS / nombre), width=Inches(2.45))

        c = t.cell(1, i)
        p = c.paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        run = p.add_run(leyenda)
        run.font.size = Pt(8)
        run.italic = True
        run.font.color.rgb = MUTED

    doc.add_paragraph().paragraph_format.space_after = Pt(4)


def main() -> None:
    doc = Document()

    # Configuración general
    styles = doc.styles
    normal = styles["Normal"]
    normal.font.name = "Calibri"
    normal.font.size = Pt(10.5)
    normal.font.color.rgb = SLATE

    section = doc.sections[0]
    section.top_margin = Inches(0.85)
    section.bottom_margin = Inches(0.8)
    section.left_margin = Inches(0.9)
    section.right_margin = Inches(0.9)

    # Encabezado y pie
    header = section.header.paragraphs[0]
    header.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run = header.add_run("SIGI-COM · Actividad Complementaria 3")
    run.font.size = Pt(8)
    run.font.color.rgb = MUTED

    footer = section.footer.paragraphs[0]
    footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = footer.add_run("Página ")
    run.font.size = Pt(8)
    run.font.color.rgb = MUTED
    agregar_numero_pagina(footer)

    # Portada
    for _ in range(3):
        doc.add_paragraph()

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("ACTIVIDAD COMPLEMENTARIA 3")
    run.bold = True
    run.font.size = Pt(11)
    run.font.color.rgb = BRAND

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(8)
    run = p.add_run("Enrutamiento, controladores y\nvistas del dominio")
    run.bold = True
    run.font.size = Pt(28)
    run.font.color.rgb = RGBColor(15, 23, 42)

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(12)
    run = p.add_run("SIGI-COM · Sistema de Gestión Integral de Inventarios, Compras y Ventas")
    run.font.size = Pt(12)
    run.font.color.rgb = SLATE

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(40)
    run = p.add_run("Andrés Felipe Sánchez Acuña\nLuis José Villadiego Pineda\nCarlos Andrés Ortega Cadrazco")
    run.font.size = Pt(11)
    run.font.color.rgb = SLATE

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(18)
    run = p.add_run("Electiva Profesional II · Ingeniería de Sistemas\nDocente: Guillermo Antonio González Márquez\n6 de noviembre de 2026")
    run.font.size = Pt(10)
    run.font.color.rgb = MUTED

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(24)
    run = p.add_run("https://github.com/andres24xd/sigi-com")
    run.font.size = Pt(9.5)
    run.font.color.rgb = BRAND_DARK
    run.underline = True

    doc.add_page_break()

    # 1. Objetivo
    titulo(doc, "1. Objetivo y alcance")
    parrafo(
        doc,
        "Implementar la capa de navegación y presentación de SIGI-COM sobre Laravel 12, "
        "usando rutas nombradas y agrupadas, controladores con los verbos HTTP correctos y "
        "vistas Blade dinámicas con un layout maestro y componentes reutilizables.",
    )
    parrafo(doc, "La entrega cubre los cinco módulos del sistema:", bold=True)
    for texto in [
        "Autenticación y usuarios (estructura preparada; el login se integra en la siguiente fase).",
        "Catálogo e inventario: categorías y productos.",
        "Proveedores y abastecimiento: proveedores, compras y detalle de compra.",
        "Clientes y punto de venta: clientes, ventas y detalle de venta.",
        "Consultas y reportes: resumen, ventas, compras e inventario.",
    ]:
        vineta(doc, texto)

    # 2. Tecnologías
    titulo(doc, "2. Tecnologías utilizadas")
    tabla(
        doc,
        ["Componente", "Versión / detalle"],
        [
            ("Laravel", "12.69.2"),
            ("PHP", "8.2.12"),
            ("MariaDB", "10.4.32 (XAMPP 8.2.12)"),
            ("Composer", "2.11.1"),
            ("Node.js / npm", "24.21.0 / 11.19.0"),
            ("Tailwind CSS", "4.x con Vite 7"),
            ("Blade", "Layout maestro, partials y componentes anónimos"),
            ("JavaScript", "Vanilla JS para líneas dinámicas de compras y ventas"),
            ("Pruebas", "PHPUnit 11 · 18 pruebas"),
            ("Capturas", "Brave Browser headless + Playwright"),
        ],
        anchos=[2.0, 4.4],
    )

    # 3. Arquitectura
    titulo(doc, "3. Arquitectura MVC implementada")
    codigo(
        doc,
        "Navegador\n"
        "   │ HTTP (GET / POST / PUT / DELETE)\n"
        "   ▼\n"
        "routes/web.php ──► Controladores (app/Http/Controllers)\n"
        "   │                    ├── Form Requests (validación)\n"
        "   │                    ├── Modelos Eloquent (datos y relaciones)\n"
        "   │                    └── DB::transaction() (compras y ventas)\n"
        "   ▼                    │\n"
        "Vistas Blade ◄──────────┘\n"
        "   ├── layouts/app.blade.php\n"
        "   ├── partials/ (sidebar, topbar, flash, iconos)\n"
        "   └── components/ (stat-card, badge, input, select, empty-state…)",
    )
    parrafo(
        doc,
        "La lógica de compras y ventas está encapsulada en transacciones: si falla una línea, "
        "no se guarda la cabecera ni se mueve el stock.",
    )

    # 4. Rutas
    titulo(doc, "4. Rutas")
    parrafo(
        doc,
        "Las rutas se agrupan por módulo con prefix() y name(), tienen nombre para usar route() "
        "y emplean los verbos HTTP adecuados. Los catálogos usan Route::resource().",
    )
    tabla(
        doc,
        ["Prefijo", "Nombre base", "Entidades", "Propósito"],
        [
            ("/", "dashboard", "Panel", "Indicadores y accesos rápidos"),
            ("/inventario", "inventario.", "Categorías, productos", "Catálogo y existencias"),
            ("/operaciones", "operaciones.", "Compras, ventas", "Entradas y salidas de inventario"),
            ("/terceros", "terceros.", "Proveedores, clientes", "Directorios de contacto"),
            ("/reportes", "reportes.", "Ventas, compras, inventario", "Consultas y filtros"),
        ],
        anchos=[1.1, 1.2, 1.8, 2.3],
    )

    parrafo(doc, "Tabla completa de rutas (39 registros):", bold=True, size=10)
    rutas = [
        ("GET/HEAD", "/", "dashboard", "DashboardController@index"),
        ("GET/HEAD", "inventario/categorias", "inventario.categorias.index", "CategoriaController@index"),
        ("POST", "inventario/categorias", "inventario.categorias.store", "CategoriaController@store"),
        ("GET/HEAD", "inventario/categorias/create", "inventario.categorias.create", "CategoriaController@create"),
        ("GET/HEAD", "inventario/categorias/{categoria}/edit", "inventario.categorias.edit", "CategoriaController@edit"),
        ("PUT/PATCH", "inventario/categorias/{categoria}", "inventario.categorias.update", "CategoriaController@update"),
        ("DELETE", "inventario/categorias/{categoria}", "inventario.categorias.destroy", "CategoriaController@destroy"),
        ("GET/HEAD", "inventario/productos", "inventario.productos.index", "ProductoController@index"),
        ("POST", "inventario/productos", "inventario.productos.store", "ProductoController@store"),
        ("GET/HEAD", "inventario/productos/create", "inventario.productos.create", "ProductoController@create"),
        ("GET/HEAD", "inventario/productos/{producto}/edit", "inventario.productos.edit", "ProductoController@edit"),
        ("PUT/PATCH", "inventario/productos/{producto}", "inventario.productos.update", "ProductoController@update"),
        ("DELETE", "inventario/productos/{producto}", "inventario.productos.destroy", "ProductoController@destroy"),
        ("GET/HEAD", "operaciones/compras", "operaciones.compras.index", "CompraController@index"),
        ("POST", "operaciones/compras", "operaciones.compras.store", "CompraController@store"),
        ("GET/HEAD", "operaciones/compras/create", "operaciones.compras.create", "CompraController@create"),
        ("GET/HEAD", "operaciones/compras/{compra}", "operaciones.compras.show", "CompraController@show"),
        ("DELETE", "operaciones/compras/{compra}", "operaciones.compras.destroy", "CompraController@destroy"),
        ("GET/HEAD", "operaciones/ventas", "operaciones.ventas.index", "VentaController@index"),
        ("POST", "operaciones/ventas", "operaciones.ventas.store", "VentaController@store"),
        ("GET/HEAD", "operaciones/ventas/create", "operaciones.ventas.create", "VentaController@create"),
        ("GET/HEAD", "operaciones/ventas/{venta}", "operaciones.ventas.show", "VentaController@show"),
        ("DELETE", "operaciones/ventas/{venta}", "operaciones.ventas.destroy", "VentaController@destroy"),
        ("GET/HEAD", "terceros/proveedores", "terceros.proveedores.index", "ProveedorController@index"),
        ("POST", "terceros/proveedores", "terceros.proveedores.store", "ProveedorController@store"),
        ("GET/HEAD", "terceros/proveedores/create", "terceros.proveedores.create", "ProveedorController@create"),
        ("GET/HEAD", "terceros/proveedores/{proveedor}/edit", "terceros.proveedores.edit", "ProveedorController@edit"),
        ("PUT/PATCH", "terceros/proveedores/{proveedor}", "terceros.proveedores.update", "ProveedorController@update"),
        ("DELETE", "terceros/proveedores/{proveedor}", "terceros.proveedores.destroy", "ProveedorController@destroy"),
        ("GET/HEAD", "terceros/clientes", "terceros.clientes.index", "ClienteController@index"),
        ("POST", "terceros/clientes", "terceros.clientes.store", "ClienteController@store"),
        ("GET/HEAD", "terceros/clientes/create", "terceros.clientes.create", "ClienteController@create"),
        ("GET/HEAD", "terceros/clientes/{cliente}/edit", "terceros.clientes.edit", "ClienteController@edit"),
        ("PUT/PATCH", "terceros/clientes/{cliente}", "terceros.clientes.update", "ClienteController@update"),
        ("DELETE", "terceros/clientes/{cliente}", "terceros.clientes.destroy", "ClienteController@destroy"),
        ("GET/HEAD", "reportes", "reportes.index", "ReporteController@index"),
        ("GET/HEAD", "reportes/ventas", "reportes.ventas", "ReporteController@ventas"),
        ("GET/HEAD", "reportes/compras", "reportes.compras", "ReporteController@compras"),
        ("GET/HEAD", "reportes/inventario", "reportes.inventario", "ReporteController@inventario"),
    ]
    tabla(doc, ["Método", "URI", "Nombre", "Acción"], rutas, anchos=[0.85, 1.9, 1.9, 1.75], size=7.6)

    # 5. Controladores
    titulo(doc, "5. Controladores")
    tabla(
        doc,
        ["Controlador", "Métodos", "Responsabilidad"],
        [
            ("DashboardController", "index", "Indicadores, gráfica de 7 días, últimas ventas, stock bajo y top de productos."),
            ("CategoriaController", "CRUD", "Categorías. Impide eliminar una categoría con productos."),
            ("ProductoController", "CRUD", "Catálogo, búsqueda, filtros y protección de productos con movimientos."),
            ("ProveedorController", "CRUD", "Proveedores con conteo y total comprado; no elimina si tiene compras."),
            ("ClienteController", "CRUD", "Clientes con conteo y total vendido; al eliminar, las ventas quedan como consumidor final."),
            ("CompraController", "index, create, store, show, destroy", "Registro transaccional, total, incremento de stock y reversión."),
            ("VentaController", "index, create, store, show, destroy", "Precio de catálogo, validación de stock, descuento y restauración."),
            ("ReporteController", "index, ventas, compras, inventario", "Reportes con filtros de fecha, usuario, cliente, proveedor, categoría y estado."),
        ],
        anchos=[1.35, 1.55, 3.5],
        size=8.4,
    )
    parrafo(
        doc,
        "La validación se delega en Form Requests (CategoriaRequest, ProductoRequest, "
        "ProveedorRequest, ClienteRequest, StoreCompraRequest y StoreVentaRequest) y los "
        "mensajes están en español mediante lang/es/validation.php.",
    )

    # 6. Vistas
    titulo(doc, "6. Vistas y componentes")
    parrafo(
        doc,
        "El layout maestro layouts/app.blade.php integra Vite, el sprite de iconos, la barra "
        "lateral, la barra superior, los mensajes flash y el contenido de cada vista.",
    )
    tabla(
        doc,
        ["Componente", "Uso"],
        [
            ("<x-page-header>", "Título, descripción y acciones de cada pantalla."),
            ("<x-stat-card>", "Tarjetas de indicadores con icono y tono."),
            ("<x-badge>", "Estados de stock, roles y conteos."),
            ("<x-input> / <x-select>", "Campos con label, valor antiguo y error de validación."),
            ("<x-empty-state>", "Estados vacíos consistentes."),
            ("<x-nav-link> / <x-icon>", "Navegación e iconografía."),
        ],
        anchos=[1.8, 4.6],
        size=9,
    )

    # 7. Responsive
    titulo(doc, "7. Diseño visual y responsive")
    for texto in [
        "Tailwind CSS 4 compilado con Vite y paleta brand (verde esmeralda) definida en @theme.",
        "Tipografía Instrument Sans con fallback al sistema.",
        "Sidebar oscuro fijo en escritorio y deslizable en móvil mediante JavaScript y overlay.",
        "Tablas con desplazamiento horizontal controlado y columnas secundarias ocultas en móvil.",
        "Verificación con Playwright: scrollWidth = clientWidth = 390 px en panel, productos, ventas, formulario de venta y reporte de ventas.",
    ]:
        vineta(doc, texto)

    # 8. Dinámica
    titulo(doc, "8. Funcionalidad dinámica")
    parrafo(
        doc,
        "Los formularios de compra y venta permiten agregar y quitar líneas sin recargar. "
        "El JavaScript reindexa los nombres detalles[n][...], calcula subtotales y total con "
        "Intl.NumberFormat('es-CO'), muestra el stock disponible y bloquea productos agotados. "
        "En el servidor, el precio de venta se toma del catálogo y la operación se ejecuta "
        "dentro de DB::transaction() con lockForUpdate() para evitar sobreventa.",
    )
    parrafo(
        doc,
        "Prueba en navegador: 3 unidades de Aceite Premier (12.500) + 2 unidades de Agua "
        "Cristal (2.000) = $ 41.500, con stock disponible en cada línea.",
        italic=True,
    )

    doc.add_page_break()

    # 9. Evidencia
    titulo(doc, "9. Evidencia de navegación")

    parrafo(doc, "9.1 Panel de control", bold=True, size=10)
    imagen(doc, "01-dashboard.png", "Panel con indicadores del día, gráfica de 7 días, últimas ventas y alertas de stock.")

    parrafo(doc, "9.2 Inventario", bold=True, size=10)
    imagen(doc, "02-categorias.png", "Listado de categorías con conteo de productos.")
    imagen(doc, "03-productos.png", "Catálogo con búsqueda, filtros, stock y valorización.")

    parrafo(doc, "9.3 Compras", bold=True, size=10)
    imagen(doc, "05-compras.png", "Historial de compras con filtros y resumen de valores.")
    imagen(doc, "06-compra-form.png", "Formulario de compra con líneas dinámicas y total calculado.")
    imagen(doc, "07-compra-detalle.png", "Comprobante de compra con productos, cantidades y total.")

    parrafo(doc, "9.4 Ventas", bold=True, size=10)
    imagen(doc, "08-ventas.png", "Historial de ventas con filtros por cliente, cajero y fechas.")
    imagen(doc, "09b-venta-form-dinamica.png", "Dos líneas agregadas en tiempo real; total $ 41.500.")
    imagen(doc, "10-venta-detalle.png", "Comprobante de venta con cliente, cajero y detalle.")

    parrafo(doc, "9.5 Terceros", bold=True, size=10)
    imagen(doc, "11-proveedores.png", "Directorio de proveedores con compras y total abastecido.")
    imagen(doc, "12-clientes.png", "Directorio de clientes con ventas y total comprado.")

    parrafo(doc, "9.6 Reportes", bold=True, size=10)
    imagen(doc, "13-reportes.png", "Resumen general, evolución mensual y ventas por categoría.")
    imagen(doc, "14-reporte-ventas.png", "Reporte de ventas con filtros, gráfica diaria y detalle paginado.")
    imagen(doc, "15-reporte-inventario.png", "Reporte de inventario con existencias y valorización.")

    parrafo(doc, "9.7 Vista móvil (390 px)", bold=True, size=10)
    imagen_lado_a_lado(
        doc,
        "20-mobile-dashboard-viewport.png",
        "22-mobile-venta-form-viewport.png",
        "Panel en móvil, sin desbordamiento horizontal.",
        "Formulario de venta apilado en una columna.",
    )

    doc.add_page_break()

    # 10. Pruebas
    titulo(doc, "10. Pruebas y verificación")
    tabla(
        doc,
        ["Verificación", "Resultado"],
        [
            ("Migraciones y seeder", "Sin errores; 9 tablas del dominio pobladas"),
            ("Rutas registradas", "39 rutas nombradas y agrupadas"),
            ("Respuesta HTTP de las vistas", "23 URLs verificadas con estado 200"),
            ("Pruebas automatizadas", "18 pruebas aprobadas, 89 aserciones"),
            ("Formulario dinámico", "Total y subtotales correctos en navegador"),
            ("Responsive", "scrollWidth = clientWidth = 390 px en 5 vistas"),
            ("Build de assets", "Vite compila CSS y JS sin errores"),
        ],
        anchos=[2.6, 3.8],
        size=9,
    )
    parrafo(doc, "Pruebas de la Actividad 3:", bold=True)
    for texto in [
        "Las rutas principales responden.",
        "Las rutas están nombradas y usan los verbos correctos.",
        "Se puede crear una categoría y no se permite repetir su nombre.",
        "Se puede crear un producto.",
        "Una compra aumenta el stock y calcula el total.",
        "Una venta descuenta el stock y usa el precio del catálogo.",
        "No se puede vender más stock del disponible.",
        "Eliminar una compra revierte el stock.",
        "Los reportes aceptan filtros de fecha.",
    ]:
        vineta(doc, texto)

    # 11. Instalación
    titulo(doc, "11. Instalación y ejecución")
    codigo(
        doc,
        "composer install\n"
        "copy .env.example .env\n"
        "php artisan key:generate\n"
        "php artisan migrate:fresh --seed\n"
        "npm install\n"
        "npm run build\n"
        "php artisan serve\n"
        "# http://127.0.0.1:8000",
    )

    # 12. Conclusiones
    titulo(doc, "12. Conclusiones")
    for texto in [
        "La navegación quedó centralizada en rutas nombradas y agrupadas por módulo.",
        "Los controladores delegan la validación en Form Requests y protegen las operaciones transaccionales.",
        "El layout maestro y los componentes Blade mantienen una identidad visual consistente.",
        "El diseño es responsivo: se verificó en 1440 px y 390 px, corrigiendo el desbordamiento horizontal.",
        "La aplicación queda lista para la siguiente fase: autenticación, roles y permisos.",
    ]:
        vineta(doc, texto)

    salida = BASE / "INFORME_ACTIVIDAD3.docx"
    doc.save(salida)
    print(f"Informe generado: {salida}")


if __name__ == "__main__":
    main()

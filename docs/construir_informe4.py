"""Genera el informe resumido de la Actividad 4 en PDF."""

from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle
from reportlab.lib.units import mm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    HRFlowable,
    KeepTogether,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

BASE = Path(__file__).resolve().parent
OUT = BASE / "INFORME_ACTIVIDAD4.pdf"

FUENTES = Path(r"C:\Windows\Fonts")
pdfmetrics.registerFont(TTFont("Segoe", str(FUENTES / "segoeui.ttf")))
pdfmetrics.registerFont(TTFont("Segoe-Bold", str(FUENTES / "segoeuib.ttf")))
pdfmetrics.registerFont(TTFont("Consolas", str(FUENTES / "consola.ttf")))
pdfmetrics.registerFont(TTFont("Consolas-Bold", str(FUENTES / "consolab.ttf")))

BRAND = colors.HexColor("#059669")
BRAND_DARK = colors.HexColor("#047857")
BRAND_DEEP = colors.HexColor("#065f46")
SLATE = colors.HexColor("#1e293b")
SLATE_DARK = colors.HexColor("#0f172a")
MUTED = colors.HexColor("#64748b")
BORDER = colors.HexColor("#e2e8f0")
LIGHT = colors.HexColor("#f8fafc")
MINT = colors.HexColor("#ecfdf5")
MINT_BORDER = colors.HexColor("#a7f3d0")
DARK_BG = colors.HexColor("#0f172a")

REPO = "https://github.com/andres24xd/sigi-com"

estilos = {
    "titulo": ParagraphStyle(
        "titulo",
        fontName="Segoe-Bold",
        fontSize=19,
        leading=23,
        textColor=SLATE_DARK,
        spaceAfter=4,
    ),
    "subtitulo": ParagraphStyle(
        "subtitulo",
        fontName="Segoe",
        fontSize=9.5,
        leading=13,
        textColor=MUTED,
        spaceAfter=12,
    ),
    "h2": ParagraphStyle(
        "h2",
        fontName="Segoe-Bold",
        fontSize=10.5,
        leading=13,
        textColor=BRAND_DARK,
        spaceBefore=6,
        spaceAfter=2,
    ),
    "cuerpo": ParagraphStyle(
        "cuerpo",
        fontName="Segoe",
        fontSize=9.4,
        leading=13.6,
        textColor=SLATE,
        alignment=TA_JUSTIFY,
        spaceAfter=6,
    ),
    "nota": ParagraphStyle(
        "nota",
        fontName="Segoe",
        fontSize=8,
        leading=11,
        textColor=MUTED,
        alignment=TA_JUSTIFY,
        spaceAfter=4,
    ),
    "celda": ParagraphStyle(
        "celda",
        fontName="Segoe",
        fontSize=8.6,
        leading=11.4,
        textColor=SLATE,
    ),
    "celda_centro": ParagraphStyle(
        "celda_centro",
        fontName="Segoe",
        fontSize=8.6,
        leading=11.4,
        textColor=SLATE,
        alignment=TA_CENTER,
    ),
    "th": ParagraphStyle(
        "th",
        fontName="Segoe-Bold",
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#334155"),
    ),
    "th_centro": ParagraphStyle(
        "th_centro",
        fontName="Segoe-Bold",
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#334155"),
        alignment=TA_CENTER,
    ),
    "ok": ParagraphStyle(
        "ok",
        fontName="Segoe-Bold",
        fontSize=8.6,
        leading=11.4,
        textColor=BRAND_DARK,
        alignment=TA_CENTER,
    ),
    "bullet": ParagraphStyle(
        "bullet",
        fontName="Segoe",
        fontSize=8.5,
        leading=12,
        textColor=SLATE,
        leftIndent=9,
        bulletIndent=1,
        spaceAfter=3,
    ),
    "metric_num": ParagraphStyle(
        "metric_num",
        fontName="Segoe-Bold",
        fontSize=15,
        leading=17,
        textColor=SLATE_DARK,
        alignment=TA_CENTER,
    ),
    "metric_lbl": ParagraphStyle(
        "metric_lbl",
        fontName="Segoe",
        fontSize=6.8,
        leading=8.5,
        textColor=MUTED,
        alignment=TA_CENTER,
    ),
    "codigo": ParagraphStyle(
        "codigo",
        fontName="Consolas",
        fontSize=7.4,
        leading=11,
        textColor=colors.HexColor("#d1fae5"),
    ),
    "repo_url": ParagraphStyle(
        "repo_url",
        fontName="Segoe-Bold",
        fontSize=11,
        leading=14,
        textColor=BRAND_DEEP,
    ),
    "repo_lbl": ParagraphStyle(
        "repo_lbl",
        fontName="Segoe-Bold",
        fontSize=7,
        leading=9,
        textColor=BRAND_DARK,
    ),
}


def titulo_seccion(texto: str):
    return [
        Paragraph(texto.upper(), estilos["h2"]),
        HRFlowable(width="100%", thickness=0.8, color=colors.HexColor("#d1fae5"), spaceBefore=1, spaceAfter=6),
    ]


def fila_th(textos, centro_desde=1):
    celdas = []
    for i, texto in enumerate(textos):
        estilo = estilos["th"] if i < centro_desde else estilos["th_centro"]
        celdas.append(Paragraph(texto, estilo))
    return celdas


def construir() -> None:
    doc = SimpleDocTemplate(
        str(OUT),
        pagesize=A4,
        leftMargin=16 * mm,
        rightMargin=16 * mm,
        topMargin=19 * mm,
        bottomMargin=16 * mm,
        title="Actividad 4 — SIGI-COM",
        author="Andrés Sánchez · Luis Villadiego · Carlos Ortega",
        subject="Operaciones CRUD completas, validación e integración Eloquent–Vista",
    )

    ancho = A4[0] - 32 * mm

    def decorar(canvas, documento):
        canvas.saveState()
        ancho_pagina, alto = A4
        canvas.setStrokeColor(BRAND)
        canvas.setLineWidth(1.2)
        canvas.line(16 * mm, alto - 12 * mm, ancho_pagina - 16 * mm, alto - 12 * mm)
        canvas.setFont("Segoe", 7.2)
        canvas.setFillColor(MUTED)
        canvas.drawString(16 * mm, 8 * mm, "SIGI-COM · Actividad Complementaria 4")
        canvas.drawRightString(
            ancho_pagina - 16 * mm,
            8 * mm,
            f"11 de noviembre de 2026 · Página {documento.page}",
        )
        canvas.restoreState()

    historia = []

    # Encabezado de marca
    marca = Table(
        [[
            Paragraph(
                '<font name="Segoe-Bold" size="13" color="#0f172a">SIGI-COM</font><br/>'
                '<font name="Segoe" size="7.8" color="#64748b">Inventario, compras y ventas</font>',
                estilos["celda"],
            ),
            Paragraph(
                '<font name="Segoe-Bold" size="7.2" color="#047857">ACTIVIDAD COMPLEMENTARIA 4</font>',
                ParagraphStyle("badge", fontName="Segoe-Bold", fontSize=7.2, alignment=2),
            ),
        ]],
        colWidths=[ancho * 0.55, ancho * 0.45],
        style=TableStyle([
            ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
            ("LEFTPADDING", (0, 0), (-1, -1), 0),
            ("RIGHTPADDING", (0, 0), (-1, -1), 0),
            ("TOPPADDING", (0, 0), (-1, -1), 0),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 0),
        ]),
    )
    historia.append(marca)
    historia.append(Spacer(1, 10))

    historia.append(Paragraph("Operaciones CRUD completas, validación e integración Eloquent–Vista", estilos["titulo"]))
    historia.append(Paragraph(
        "Informe resumen de la entrega · Sistema de Gestión Integral de Inventarios, Compras y Ventas",
        estilos["subtitulo"],
    ))

    # Datos generales
    datos = [
        ("Integrantes", "Andrés Felipe Sánchez Acuña · Luis José Villadiego Pineda · Carlos Andrés Ortega Cadrazco"),
        ("Curso / docente", "Electiva Profesional II — Ingeniería de Sistemas (UNIAJS) · Guillermo Antonio González Márquez"),
        ("Fecha", "11 de noviembre de 2026"),
        ("Repositorio", f'<a href="{REPO}" color="#047857">{REPO}</a>'),
    ]
    tabla_datos = Table(
        [[Paragraph(f"<b>{etiqueta}</b>", estilos["celda"]), Paragraph(valor, estilos["celda"])] for etiqueta, valor in datos],
        colWidths=[32 * mm, ancho - 32 * mm],
        style=TableStyle([
            ("BACKGROUND", (0, 0), (0, -1), LIGHT),
            ("GRID", (0, 0), (-1, -1), 0.5, BORDER),
            ("VALIGN", (0, 0), (-1, -1), "TOP"),
            ("LEFTPADDING", (0, 0), (-1, -1), 6),
            ("RIGHTPADDING", (0, 0), (-1, -1), 6),
            ("TOPPADDING", (0, 0), (-1, -1), 4),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ]),
    )
    historia.append(tabla_datos)
    historia.append(Spacer(1, 8))

    # 1. Objetivo
    historia += titulo_seccion("1. Objetivo")
    historia.append(Paragraph(
        "Integrar las vistas Blade, los controladores y los modelos Eloquent para ejecutar el ciclo "
        "CRUD completo —crear, consultar en detalle, editar y eliminar— con validación estricta en el "
        "servidor, retención de los datos escritos y confirmación visible para el usuario.",
        estilos["cuerpo"],
    ))

    # 2. Alcance
    historia += titulo_seccion("2. Alcance implementado")
    filas = [
        fila_th(["Entidad", "Crear", "Detalle", "Editar", "Eliminar", "Observación"]),
    ]
    entidades = [
        ("Categorías", "Detalle con productos asociados."),
        ("Productos", "Detalle con movimientos de compra y venta."),
        ("Proveedores", "Detalle con historial de compras."),
        ("Clientes", "Detalle con historial de ventas."),
        ("Compras", "Al eliminar se revierte el stock."),
        ("Ventas", "Al eliminar se revierte el stock."),
    ]
    for nombre, observacion in entidades:
        editable = nombre not in {"Compras", "Ventas"}
        filas.append([
            Paragraph(f"<b>{nombre}</b>", estilos["celda"]),
            Paragraph("Sí", estilos["ok"]),
            Paragraph("Sí", estilos["ok"]),
            Paragraph("Sí" if editable else "—", estilos["ok"] if editable else estilos["celda_centro"]),
            Paragraph("Sí", estilos["ok"]),
            Paragraph(observacion, estilos["celda"]),
        ])
    tabla_alcance = Table(
        filas,
        colWidths=[27 * mm, 13 * mm, 15 * mm, 14 * mm, 16 * mm, ancho - 85 * mm],
        repeatRows=1,
        style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#f1f5f9")),
            ("GRID", (0, 0), (-1, -1), 0.5, BORDER),
            ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
            ("LEFTPADDING", (0, 0), (-1, -1), 5),
            ("RIGHTPADDING", (0, 0), (-1, -1), 5),
            ("TOPPADDING", (0, 0), (-1, -1), 4),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ]),
    )
    historia.append(tabla_alcance)
    historia.append(Paragraph(
        "Compras y ventas no se editan a propósito: modificar un documento histórico rompería la "
        "trazabilidad del inventario. Se eliminan y se registran de nuevo.",
        estilos["nota"],
    ))

    # 3. Validación y UX
    historia += titulo_seccion("3. Validación y experiencia de usuario")
    izquierda = [
        Paragraph("• Validación en <b>Form Requests</b>: campos requeridos, texto, longitud máxima, enteros, numéricos, existencias y unicidad.", estilos["bullet"]),
        Paragraph("• Errores mostrados en un bloque general y junto a cada campo, con mensajes en español.", estilos["bullet"]),
        Paragraph("• Retención de datos con <b>old()</b> y <b>@selected()</b> cuando falla la validación.", estilos["bullet"]),
    ]
    derecha = [
        Paragraph("• Redirección con <b>flash message</b> de éxito tras crear, editar o eliminar.", estilos["bullet"]),
        Paragraph("• Borrados protegidos con mensaje de error: categoría con productos, producto con movimientos y proveedor con compras.", estilos["bullet"]),
        Paragraph("• Formularios con <b>@csrf</b>, <b>@method('PUT')</b> y <b>@method('DELETE')</b>.", estilos["bullet"]),
    ]
    tabla_ux = Table(
        [[izquierda, derecha]],
        colWidths=[ancho / 2, ancho / 2],
        style=TableStyle([
            ("VALIGN", (0, 0), (-1, -1), "TOP"),
            ("LEFTPADDING", (0, 0), (0, 0), 0),
            ("RIGHTPADDING", (0, 0), (0, 0), 8),
            ("LEFTPADDING", (1, 0), (1, 0), 8),
            ("RIGHTPADDING", (1, 0), (1, 0), 0),
            ("TOPPADDING", (0, 0), (-1, -1), 0),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 0),
        ]),
    )
    historia.append(tabla_ux)

    # 4. Flujo CRUD
    historia += titulo_seccion("4. Flujo de una operación CRUD")

    def paso(numero: str, titulo: str, detalle: str):
        return Table(
            [[
                Paragraph(
                    f'<font name="Segoe-Bold" size="11" color="#047857">{numero}</font>',
                    ParagraphStyle("n", fontName="Segoe-Bold", fontSize=11, alignment=TA_CENTER),
                ),
                Paragraph(
                    f'<font name="Segoe-Bold" size="8.2" color="#0f172a">{titulo}</font><br/>'
                    f'<font name="Segoe" size="7.2" color="#64748b">{detalle}</font>',
                    estilos["celda"],
                ),
            ]],
            colWidths=[8 * mm, 30 * mm],
            style=TableStyle([
                ("BACKGROUND", (0, 0), (-1, -1), LIGHT),
                ("BOX", (0, 0), (-1, -1), 0.6, BORDER),
                ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
                ("LEFTPADDING", (0, 0), (0, 0), 3),
                ("RIGHTPADDING", (0, 0), (0, 0), 0),
                ("LEFTPADDING", (1, 0), (1, 0), 1),
                ("RIGHTPADDING", (1, 0), (1, 0), 4),
                ("TOPPADDING", (0, 0), (-1, -1), 4),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
            ]),
        )

    flecha = Paragraph(
        '<font name="Segoe-Bold" size="12" color="#10b981">→</font>',
        ParagraphStyle("flecha", fontName="Segoe-Bold", fontSize=12, alignment=TA_CENTER),
    )

    tabla_flujo = Table(
        [[
            paso("1", "Formulario", "Blade con @csrf y @method"),
            flecha,
            paso("2", "Validación", "Form Request en servidor"),
            flecha,
            paso("3", "Persistencia", "Modelo Eloquent"),
            flecha,
            paso("4", "Respuesta", "redirect + flash"),
        ]],
        colWidths=[38 * mm, 6 * mm, 38 * mm, 6 * mm, 38 * mm, 6 * mm, 38 * mm],
        hAlign="LEFT",
        style=TableStyle([
            ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
            ("LEFTPADDING", (0, 0), (-1, -1), 0),
            ("RIGHTPADDING", (0, 0), (-1, -1), 0),
            ("TOPPADDING", (0, 0), (-1, -1), 0),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 0),
        ]),
    )
    historia.append(tabla_flujo)

    historia.append(PageBreak())

    # 5. Trazabilidad
    historia += titulo_seccion("5. Trazabilidad de requisitos")
    trazabilidad = [
        ("CRUD y persistencia Eloquent", "Route::resource + modelos Eloquent", "4 entidades con crear, detalle, editar y eliminar"),
        ("Peticiones POST / PUT / DELETE", "@csrf y @method en cada formulario", "Rutas y verbos verificados con pruebas"),
        ("Validación y retención de datos", "6 Form Requests + componentes de campo", "18 pruebas con errores y old() conservado"),
        ("Redirección y retroalimentación", "redirect()->route()->with()", "Mensajes flash de éxito y error"),
    ]
    filas_traza = [fila_th(["Requisito", "Implementación", "Evidencia"], centro_desde=3)]
    for requisito, implementacion, evidencia in trazabilidad:
        filas_traza.append([
            Paragraph(f"<b>{requisito}</b>", estilos["celda"]),
            Paragraph(implementacion, estilos["celda"]),
            Paragraph(evidencia, estilos["celda"]),
        ])
    historia.append(Table(
        filas_traza,
        colWidths=[52 * mm, 62 * mm, ancho - 114 * mm],
        repeatRows=1,
        style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#f1f5f9")),
            ("GRID", (0, 0), (-1, -1), 0.5, BORDER),
            ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
            ("LEFTPADDING", (0, 0), (-1, -1), 5),
            ("RIGHTPADDING", (0, 0), (-1, -1), 5),
            ("TOPPADDING", (0, 0), (-1, -1), 4),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ]),
    ))

    # 6. Verificación
    historia += titulo_seccion("6. Verificación")
    metricas = [
        ("18", "Pruebas de la Actividad 4"),
        ("47 / 220", "Pruebas / aserciones de la suite"),
        ("43", "Rutas del panel al cierre"),
        ("OK", "Build de assets y vistas Blade"),
    ]
    tabla_metricas = Table(
        [[
            Table(
                [[Paragraph(numero, estilos["metric_num"])], [Paragraph(etiqueta, estilos["metric_lbl"])]],
                colWidths=[(ancho - 18) / 4],
                style=TableStyle([
                    ("BOX", (0, 0), (-1, -1), 0.6, BORDER),
                    ("BACKGROUND", (0, 0), (-1, -1), LIGHT),
                    ("TOPPADDING", (0, 0), (-1, 0), 7),
                    ("BOTTOMPADDING", (0, 1), (-1, 1), 7),
                    ("LEFTPADDING", (0, 0), (-1, -1), 4),
                    ("RIGHTPADDING", (0, 0), (-1, -1), 4),
                ]),
            )
            for numero, etiqueta in metricas
        ]],
        colWidths=[(ancho - 18) / 4 + 2] * 4,
        hAlign="LEFT",
        style=TableStyle([
            ("LEFTPADDING", (0, 0), (-1, -1), 0),
            ("RIGHTPADDING", (0, 0), (-1, -1), 6),
            ("TOPPADDING", (0, 0), (-1, -1), 0),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 0),
            ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
        ]),
    )
    historia.append(tabla_metricas)
    historia.append(Spacer(1, 10))

    historia.append(Paragraph("<b>Commits de la entrega</b>", estilos["cuerpo"]))
    commits = [
        "a0faab3  feat: completa el CRUD con consulta detallada de las entidades maestras",
        "7b5612a  feat: refuerza la validacion y agrega pruebas de CRUD, errores y datos old",
        "2ccd8f4  docs: actualiza README y agrega informe de la Actividad 4",
    ]
    tabla_commits = Table(
        [[Paragraph(f"<font name='Consolas' size='7.4' color='#d1fae5'>{linea}</font>", estilos["codigo"])] for linea in commits],
        colWidths=[ancho],
        style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, -1), DARK_BG),
            ("LEFTPADDING", (0, 0), (-1, -1), 9),
            ("RIGHTPADDING", (0, 0), (-1, -1), 9),
            ("TOPPADDING", (0, 0), (-1, 0), 7),
            ("BOTTOMPADDING", (0, -1), (-1, -1), 7),
            ("TOPPADDING", (0, 1), (-1, -1), 0),
            ("BOTTOMPADDING", (0, 0), (-1, -2), 0),
        ]),
    )
    historia.append(tabla_commits)

    # Resultados breves
    historia.append(Spacer(1, 8))
    historia.append(Paragraph(
        "Las 18 pruebas de la Actividad 4 cubren las pantallas de detalle, la edición por PUT, "
        "la eliminación por DELETE, las protecciones de integridad, la validación de datos y la "
        "retención de valores con old(). El build de Tailwind y Vite y la compilación de todas las "
        "vistas Blade finalizaron sin errores.",
        estilos["cuerpo"],
    ))

    # 7. Conclusión
    historia += titulo_seccion("7. Conclusión")
    historia.append(Paragraph(
        "El CRUD quedó completo e integrado en las cuatro entidades maestras: cada operación "
        "persiste con Eloquent, valida en el servidor, conserva los datos ante errores y confirma "
        "el resultado con un mensaje flash. Las protecciones de integridad impiden borrar registros "
        "con historial y la suite automatizada respalda el comportamiento entregado.",
        estilos["cuerpo"],
    ))

    # 8. Enlace
    historia += titulo_seccion("8. Enlace del repositorio")
    caja_repo = Table(
        [
            [Paragraph("GITHUB", estilos["repo_lbl"])],
            [Paragraph(f'<a href="{REPO}" color="#065f46">{REPO}</a>', estilos["repo_url"])],
            [Paragraph("Código fuente, migraciones, seeders, pruebas automatizadas e informes.", estilos["nota"])],
        ],
        colWidths=[ancho],
        style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, -1), MINT),
            ("BOX", (0, 0), (-1, -1), 0.8, MINT_BORDER),
            ("LEFTPADDING", (0, 0), (-1, -1), 11),
            ("RIGHTPADDING", (0, 0), (-1, -1), 11),
            ("TOPPADDING", (0, 0), (-1, 0), 9),
            ("BOTTOMPADDING", (0, -1), (-1, -1), 9),
            ("TOPPADDING", (0, 1), (-1, -1), 2),
            ("BOTTOMPADDING", (0, 0), (-1, -2), 1),
        ]),
    )
    historia.append(caja_repo)

    doc.build(historia, onFirstPage=decorar, onLaterPages=decorar)
    print(f"PDF generado: {OUT}")


if __name__ == "__main__":
    construir()

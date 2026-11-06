@push('scripts')
    <script>
        (() => {
            const esVenta = @json($esVenta);
            const productos = @json($productosData);
            const contenedor = document.getElementById('detalles');
            const btnAgregar = document.getElementById('agregar-linea');
            const totalEl = document.getElementById('total');
            const form = document.getElementById('form-operacion');

            if (!contenedor || !btnAgregar || !totalEl) {
                return;
            }

            const formato = (valor) => '$ ' + new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(Number(valor) || 0);
            const esc = (texto) => String(texto)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;');

            const opciones = (seleccionado) => {
                let html = '<option value="">Seleccione un producto</option>';

                productos.forEach((producto) => {
                    const agotado = esVenta && producto.stock <= 0;
                    const texto = esVenta
                        ? `${producto.nombre} · stock ${producto.stock}`
                        : `${producto.nombre} · ${producto.categoria}`;
                    const selected = String(producto.id) === String(seleccionado) ? ' selected' : '';
                    const disabled = agotado ? ' disabled' : '';

                    html += `<option value="${producto.id}" data-precio="${producto.precio}" data-stock="${producto.stock}"${selected}${disabled}>${esc(texto)}</option>`;
                });

                return html;
            };

            const crearLinea = (index) => {
                const div = document.createElement('div');
                div.className = 'detalle-linea grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-3 sm:grid-cols-12 sm:items-end';
                div.innerHTML = `
                    <div class="sm:col-span-5">
                        <span class="detalle-label">Producto</span>
                        <select name="detalles[${index}][producto_id]" class="input producto">${opciones('')}</select>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="detalle-label">Cantidad</span>
                        <input type="number" min="1" step="1" name="detalles[${index}][cantidad]" value="1" class="input cantidad">
                        <p class="stock-hint mt-1 hidden text-xs text-slate-500"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="detalle-label">${esVenta ? 'Precio' : 'Costo unitario'}</span>
                        <input type="number" min="0" step="1" name="detalles[${index}][precio_unitario]" value="" class="input precio" ${esVenta ? 'readonly' : ''} placeholder="${esVenta ? 'Precio' : 'Costo'}">
                    </div>
                    <div class="sm:col-span-2">
                        <span class="detalle-label">Subtotal</span>
                        <div class="subtotal flex h-[42px] items-center rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-800">$ 0</div>
                    </div>
                    <div class="sm:col-span-1 flex justify-end">
                        <button type="button" class="btn-icon btn-icon-danger quitar" title="Quitar producto">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" aria-hidden="true">
                                <use href="#i-trash"></use>
                            </svg>
                        </button>
                    </div>
                `;

                return div;
            };

            const actualizarTotal = () => {
                let total = 0;

                contenedor.querySelectorAll('.detalle-linea').forEach((linea) => {
                    const cantidad = Number(linea.querySelector('.cantidad')?.value) || 0;
                    const precio = Number(linea.querySelector('.precio')?.value) || 0;
                    total += cantidad * precio;

                    const subtotal = linea.querySelector('.subtotal');
                    if (subtotal) {
                        subtotal.textContent = formato(cantidad * precio);
                    }
                });

                totalEl.textContent = formato(total);
            };

            const reindexar = () => {
                contenedor.querySelectorAll('.detalle-linea').forEach((linea, index) => {
                    linea.querySelector('.producto').name = `detalles[${index}][producto_id]`;
                    linea.querySelector('.cantidad').name = `detalles[${index}][cantidad]`;
                    linea.querySelector('.precio').name = `detalles[${index}][precio_unitario]`;
                });
            };

            const enlazar = (linea) => {
                const select = linea.querySelector('.producto');
                const cantidad = linea.querySelector('.cantidad');
                const precio = linea.querySelector('.precio');
                const quitar = linea.querySelector('.quitar');
                const hint = linea.querySelector('.stock-hint');

                const aplicarProducto = () => {
                    const opcion = select.selectedOptions[0];

                    if (esVenta) {
                        const precioProducto = Number(opcion?.dataset.precio) || 0;
                        const stock = Number(opcion?.dataset.stock) || 0;

                        precio.value = opcion?.value ? precioProducto : '';

                        if (opcion?.value) {
                            cantidad.max = stock;
                            hint.textContent = `Disponible: ${stock} u.`;
                            hint.classList.remove('hidden');
                        } else {
                            cantidad.removeAttribute('max');
                            hint.classList.add('hidden');
                        }
                    }

                    actualizarTotal();
                };

                select?.addEventListener('change', aplicarProducto);
                cantidad?.addEventListener('input', actualizarTotal);
                precio?.addEventListener('input', actualizarTotal);

                quitar?.addEventListener('click', () => {
                    if (contenedor.querySelectorAll('.detalle-linea').length === 1) {
                        select.value = '';
                        cantidad.value = 1;
                        precio.value = '';
                        cantidad.removeAttribute('max');
                        hint?.classList.add('hidden');
                        actualizarTotal();

                        return;
                    }

                    linea.remove();
                    reindexar();
                    actualizarTotal();
                });

                aplicarProducto();
            };

            contenedor.querySelectorAll('.detalle-linea').forEach(enlazar);

            btnAgregar.addEventListener('click', () => {
                const linea = crearLinea(contenedor.querySelectorAll('.detalle-linea').length);
                contenedor.appendChild(linea);
                enlazar(linea);
                reindexar();
                actualizarTotal();
            });

            form?.addEventListener('submit', () => {
                const boton = form.querySelector('button[type="submit"]');
                if (boton) {
                    boton.disabled = true;
                    boton.textContent = 'Guardando...';
                }
            });

            actualizarTotal();
        })();
    </script>
@endpush

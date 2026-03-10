/* ======================================================
   INVENTARIO - PRODUCTOS JS (VERSIÓN ESTÉTICA ORIGINAL)
====================================================== */

let selectedProducts = []

/* ======================================================
   CHECKBOX SELECTION
====================================================== */

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked')
    const bar = document.getElementById('bulkActionsBar')
    const count = document.getElementById('selectedCount')
    const modalCount = document.getElementById('modalSelectedCount')

    selectedProducts = []

    checkboxes.forEach(cb => {
        selectedProducts.push({
            id: cb.value,
            nombre: cb.dataset.nombre,
            precio: parseFloat(cb.dataset.precio) || 0,
            costo: parseFloat(cb.dataset.costo) || 0,
            stock: parseFloat(cb.dataset.stock) || 0,
            margen: parseFloat(cb.dataset.margen) || 0
        })
    })

    if (count) count.innerText = selectedProducts.length

    if (modalCount) {
        modalCount.innerText = selectedProducts.length
    }

    if (selectedProducts.length > 0) {
        if (bar) {
            bar.classList.remove('hidden')
            bar.classList.add('flex')
        }
    } else {
        if (bar) {
            bar.classList.add('hidden')
            bar.classList.remove('flex')
        }
    }

    const modal = document.getElementById('modalPrecioMasivo');
    if (modal && !modal.classList.contains('hidden')) {
        renderBulkList()
        actualizarPreviewGlobal()
        sincronizarSelect2ConSeleccion()
    }
}

/* ======================================================
   SELECT ALL
====================================================== */

function toggleAllCheckboxes(master) {
    const checkboxes = document.querySelectorAll('.product-checkbox')
    checkboxes.forEach(cb => {
        cb.checked = master.checked
    })
    updateSelectedCount()
}

function selectAll() {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = true)
    updateSelectedCount()
}

function deselectAll() {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false)
    updateSelectedCount()
}

/* ======================================================
   FUNCIONES PARA SELECT2
====================================================== */

function initSelect2() {
    if (typeof jQuery !== 'undefined' && $('#selectProductos').length) {
        const $select = $('#selectProductos');

        if ($select.data('select2')) {
            $select.select2('destroy');
        }

        $select.select2({
            placeholder: 'Buscar y agregar productos...',
            width: '100%',
            allowClear: true,
            dropdownParent: $('#modalPrecioMasivo'),
            language: {
                noResults: () => "No se encontraron productos",
                searching: () => "Buscando..."
            }
        });

        $select.on('select2:select', function(e) {
            const data = e.params.data;
            if (data && data.id) {
                agregarProductoDesdeSelect2(data);
                $(this).val(null).trigger('change');
            }
        });

        $select.on('select2:unselect', function(e) {
            const data = e.params.data;
            if (data && data.id) {
                quitarProductoDeLista(data.id);
            }
        });
    }
}

function agregarProductoDesdeSelect2(data) {
    const checkbox = document.querySelector(`.product-checkbox[value="${data.id}"]`);
    if (checkbox) {
        checkbox.checked = true;
    }
    updateSelectedCount();
}

function quitarProductoDeLista(productId) {
    const checkbox = document.querySelector(`.product-checkbox[value="${productId}"]`);
    if (checkbox) {
        checkbox.checked = false;
    }

    selectedProducts = selectedProducts.filter(p => p.id != productId);

    renderBulkList();
    actualizarPreviewGlobal();
    sincronizarSelect2ConSeleccion();

    const count = document.getElementById('selectedCount');
    const modalCount = document.getElementById('modalSelectedCount');
    if (count) count.innerText = selectedProducts.length;
    if (modalCount) modalCount.innerText = selectedProducts.length;
}

function sincronizarSelect2ConSeleccion() {
    if (!$('#selectProductos').length || typeof jQuery === 'undefined') return;
    const select = $('#selectProductos');
    const selectedIds = selectedProducts.map(p => p.id.toString());
    select.val(selectedIds).trigger('change');
}

function clearProductList() {
    deselectAll();
    if ($('#selectProductos').length && typeof jQuery !== 'undefined') {
        $('#selectProductos').val(null).trigger('change');
    }
}

/* ======================================================
   MODAL BULK
====================================================== */

function openBulkPriceModal() {
    const modal = document.getElementById('modalPrecioMasivo')
    modal.classList.remove('hidden')
    modal.classList.add('flex')

    if(document.getElementById('valorOperacion')) document.getElementById('valorOperacion').value = '';

    renderBulkList()
    actualizarPreviewGlobal()
    actualizarSimboloPorCampo()

    if (typeof jQuery !== 'undefined') {
        setTimeout(() => {
            initSelect2()
            sincronizarSelect2ConSeleccion()
        }, 100)
    }
}

function closeBulkPriceModal() {
    const modal = document.getElementById('modalPrecioMasivo')
    modal.classList.add('hidden')
}

/* ======================================================
   RENDER PRODUCT LIST - ESTÉTICA ORIGINAL
====================================================== */

function renderBulkList() {
    const container = document.getElementById('bulkProductList')
    if (!container) return

    container.innerHTML = ''

    if (selectedProducts.length === 0) {
        container.innerHTML = `
        <div class="p-4 text-center text-gray-400 text-sm">
            No hay productos seleccionados
        </div>`
        return
    }

    selectedProducts.forEach((p) => {
        const campo = document.getElementById('campoActualizar')?.value || 'precio'
        const tipo = document.getElementById('tipoOperacion')?.value || 'fijo'
        const valor = parseFloat(document.getElementById('valorOperacion')?.value) || 0

        let previewText = ''
        if (valor > 0 && !isNaN(valor)) {
            const nuevoValor = calcularPreview(p, campo, tipo, valor)
            previewText = generarPreviewText(campo, nuevoValor)
        }

        const html = `
        <div class="p-3 flex flex-col gap-1 border-b border-gray-100 last:border-b-0">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-semibold text-gray-900">${escapeHtml(p.nombre)}</span>
                        <button type="button" onclick="quitarProductoDeLista('${p.id}')"
                            class="text-gray-400 hover:text-red-600 transition p-1 -mt-1 -mr-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="text-xs text-gray-500">
                        Precio: $${formatNumber(p.precio)} |
                        Costo: $${formatNumber(p.costo)} |
                        Stock: ${p.stock} |
                        Margen: ${formatNumber(p.margen)}%
                    </div>
                    ${previewText ? `<div class="preview text-xs text-blue-600 mt-2">${previewText}</div>` : ''}
                </div>
            </div>
        </div>`
        container.insertAdjacentHTML('beforeend', html)
    })
}

function eliminarProductoDeLista(productId) {
    quitarProductoDeLista(productId);
}

/* --- UTILIDADES --- */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div')
    div.textContent = text
    return div.innerHTML
}

function formatNumber(num) {
    if (isNaN(num)) return '0.00'
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

/* ======================================================
   PREVIEW DINAMICO - ESTÉTICA ORIGINAL
====================================================== */

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();

    const inputs = ['campoActualizar', 'tipoOperacion', 'valorOperacion']
    inputs.forEach(id => {
        const el = document.getElementById(id)
        if (el) {
            el.addEventListener('input', () => {
                if (!document.getElementById('modalPrecioMasivo').classList.contains('hidden')) {
                    renderBulkList();
                    actualizarPreviewGlobal();
                }
            });
            el.addEventListener('change', () => {
                if (!document.getElementById('modalPrecioMasivo').classList.contains('hidden')) {
                    actualizarSimboloPorCampo();
                    renderBulkList();
                    actualizarPreviewGlobal();
                }
            });
        }
    });
});

function calcularPreview(producto, campo, tipo, valor) {
    if (!valor || isNaN(valor)) return null

    let base = 0;
    if (campo === 'precio') base = producto.precio;
    else if (campo === 'precio_costo') base = producto.costo;
    else if (campo === 'stock') base = producto.stock;
    else if (campo === 'margen_ganancia') base = producto.margen;

    const valorNum = parseFloat(valor);
    let result = base;

    if (tipo === "fijo") result = valorNum;
    else if (tipo === "sumar") result = base + (base * valorNum / 100);
    else if (tipo === "restar") result = base - (base * valorNum / 100);

    return campo === 'stock' ? Math.round(result) : result;
}

function generarPreviewText(campo, valor) {
    if (valor === null) return 'Esperando cambios...'

    const textos = {
        'precio': `Nuevo precio: <b>$${formatNumber(valor)}</b>`,
        'precio_costo': `Nuevo costo: <b>$${formatNumber(valor)}</b>`,
        'stock': `Nuevo stock: <b>${valor}</b>`,
        'margen_ganancia': `Nuevo margen: <b>${formatNumber(valor)}%</b>`
    }
    return textos[campo] || `Nuevo valor: ${valor}`
}

function actualizarPreviewGlobal() {
    const preview = document.getElementById('previewOperation')
    const valor = document.getElementById('valorOperacion')?.value
    if (!preview) return

    if (!valor || selectedProducts.length === 0) {
        preview.innerHTML = `Seleccione productos y valores para ver los cambios`;
        return
    }

    preview.innerHTML = `Se actualizarán <span class="font-bold">${selectedProducts.length}</span> productos`;
}

function actualizarSimboloPorCampo() {
    const simbolo = document.getElementById('valorSimbolo')
    const campo = document.getElementById('campoActualizar')?.value
    if (simbolo) {
        if (campo === 'stock') {
            simbolo.textContent = '#'
        } else if (campo === 'margen_ganancia') {
            simbolo.textContent = '%'
        } else {
            simbolo.textContent = '$'
        }
    }
}

/* ======================================================
   SUBMIT BULK UPDATE
====================================================== */

function submitBulkUpdate() {
    const campo = document.getElementById('campoActualizar').value
    const tipo = document.getElementById('tipoOperacion').value
    const valor = document.getElementById('valorOperacion').value

    if (selectedProducts.length === 0) {
        showValidationMessage('Debes seleccionar al menos un producto');
        return
    }

    if (!valor || valor === '') {
        showValidationMessage('Debes ingresar un valor');
        return
    }

    const valorNum = parseFloat(valor)
    if (isNaN(valorNum) || valorNum < 0) {
        showValidationMessage('El valor debe ser un número válido');
        return
    }

    if (campo === 'stock' && !Number.isInteger(valorNum)) {
        showValidationMessage('El stock debe ser un número entero');
        return
    }

    const btn = event.target
    const original = btn.innerHTML
    btn.innerHTML = 'Actualizando...';
    btn.disabled = true;

    fetch('/productos/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json', // Importante para recibir errores en JSON
            'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
        },
        body: JSON.stringify({
            productos: selectedProducts.map(p => p.id),
            campo: campo,
            tipo: tipo,
            valor: valorNum
        })
    })
    .then(async res => {
        const data = await res.json();

        // Si la respuesta no es 2xx, lanzamos el error con el mensaje del servidor
        if (!res.ok) {
            throw new Error(data.message || 'Error en la actualización');
        }
        return data;
    })
    .then(data => {
        if (data.success) {
            // Suponiendo que tienes esta función para el estilo verde/éxito
            if (typeof showSuccessMessage === 'function') {
                showSuccessMessage(data.message || 'Productos actualizados correctamente');
            } else {
                alert(data.message || 'Productos actualizados correctamente');
            }
            setTimeout(() => location.reload(), 1000);
        } else {
            throw new Error(data.message || 'Error al actualizar');
        }
    })
    .catch(err => {
        // Mostramos el mensaje de error en tu contenedor de validación
        showValidationMessage(err.message);

        // Restauramos el botón para que el usuario pueda corregir y reintentar
        btn.innerHTML = original;
        btn.disabled = false;
    });
}

function showValidationMessage(msg) {
    const div = document.getElementById('validationMessage');
    const txt = document.getElementById('validationText');
    if(div && txt) {
        txt.innerText = msg;
        div.classList.remove('hidden');

        setTimeout(() => {
            div.classList.add('hidden');
        }, 5000);
    }
}

function showSuccessMessage(msg) {
    const n = document.createElement('div');
    n.className = 'fixed top-4 right-4 bg-green-600 text-black px-6 py-3 rounded-xl shadow-lg z-50';
    n.innerHTML = `
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${msg}</span>
        </div>
    `;
    document.body.appendChild(n);

    setTimeout(() => n.remove(), 3000);
}

/* ======================================================
   INIT
====================================================== */

document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount()

    if (typeof jQuery !== 'undefined') {
        initSelect2()
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-checkbox')) {
            updateSelectedCount()
        }
    })
})

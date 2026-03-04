// Funciones para selección múltiple
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll(".product-checkbox");
    const selected = Array.from(checkboxes).filter((cb) => cb.checked);
    const count = selected.length;

    document.getElementById("selectedCount").textContent = count;
    document.getElementById("bulkSelectedCount").textContent = count;

    // Mostrar/ocultar barra de acciones masivas
    const bulkBar = document.getElementById("bulkActionsBar");
    if (count > 0) {
        bulkBar.classList.remove("hidden");
        bulkBar.classList.add("flex");
    } else {
        bulkBar.classList.add("hidden");
        bulkBar.classList.remove("flex");
    }

    // Guardar IDs seleccionados en el input oculto
    const selectedIds = selected.map((cb) => cb.value).join(",");
    document.getElementById("selectedProductosInput").value = selectedIds;
}

function toggleAllCheckboxes(checkbox) {
    const checkboxes = document.querySelectorAll(".product-checkbox");
    checkboxes.forEach((cb) => {
        cb.checked = checkbox.checked;
    });
    updateSelectedCount();
}

function selectAll() {
    const checkboxes = document.querySelectorAll(".product-checkbox");
    checkboxes.forEach((cb) => (cb.checked = true));
    updateSelectedCount();

    // Actualizar checkbox principal si existe
    const mainCheckbox = document.querySelector('thead input[type="checkbox"]');
    if (mainCheckbox) mainCheckbox.checked = true;
}

function deselectAll() {
    const checkboxes = document.querySelectorAll(".product-checkbox");
    checkboxes.forEach((cb) => (cb.checked = false));
    updateSelectedCount();

    // Actualizar checkbox principal si existe
    const mainCheckbox = document.querySelector('thead input[type="checkbox"]');
    if (mainCheckbox) mainCheckbox.checked = false;
}

// Funciones para modales
function openBulkPriceModal() {
    const selected = Array.from(
        document.querySelectorAll(".product-checkbox:checked"),
    );
    if (selected.length === 0) {
        alert("Selecciona al menos un producto");
        return;
    }

    document.getElementById("modalPrecioMasivo").classList.remove("hidden");
    document.getElementById("modalPrecioMasivo").classList.add("flex");

    // Actualizar vista previa
    updatePreview();
}

function closeBulkPriceModal() {
    document.getElementById("modalPrecioMasivo").classList.add("hidden");
    document.getElementById("modalPrecioMasivo").classList.remove("flex");
}

function togglePriceInputs() {
    const tipo = document.getElementById("tipoActualizacion").value;

    document.getElementById("inputFijo").classList.add("hidden");
    document.getElementById("inputPorcentaje").classList.add("hidden");

    if (tipo === "fijo") {
        document.getElementById("inputFijo").classList.remove("hidden");
    } else {
        document.getElementById("inputPorcentaje").classList.remove("hidden");
    }

    updatePreview();
}

function updatePreview() {
    const tipo = document.getElementById("tipoActualizacion").value;
    const selected = Array.from(
        document.querySelectorAll(".product-checkbox:checked"),
    );
    const preview = document.getElementById("previewMessage");

    if (selected.length === 0) {
        preview.textContent = "Selecciona productos para ver vista previa";
        return;
    }

    const preciosActuales = selected.map((cb) =>
        parseFloat(cb.dataset.precio || 0),
    );
    const promedio =
        preciosActuales.reduce((a, b) => a + b, 0) / preciosActuales.length;

    switch (tipo) {
        case "fijo":
            preview.textContent = `Se establecerá un precio fijo para ${selected.length} productos`;
            break;
        case "porcentaje_aumento":
            preview.textContent = `Se aumentará el precio en X% (promedio actual: $${promedio.toFixed(2)})`;
            break;
        case "porcentaje_disminucion":
            preview.textContent = `Se disminuirá el precio en X% (promedio actual: $${promedio.toFixed(2)})`;
            break;
    }
}


function openModal(id, costo, nombre) {
    document.getElementById("modalCosto").classList.remove("hidden");
    document.getElementById("modalCosto").classList.add("flex");
    document.getElementById("inputCosto").value = costo;
    document.getElementById("modalProductName").innerText = nombre;
    document.getElementById("formCosto").action = `/productos/${id}`;
}

function closeModal() {
    document.getElementById("modalCosto").classList.add("hidden");
    document.getElementById("modalCosto").classList.remove("flex");
}

// Escuchar cambios en los inputs de precio para actualizar preview
document.addEventListener("DOMContentLoaded", function () {
    const inputs = document.querySelectorAll(
        'input[name="precio_fijo"], input[name="porcentaje"]',
    );
    inputs.forEach((input) => {
        input.addEventListener("input", updatePreview);
    });
});

        // Funciones existentes
function cambiarVista(vista) {
    const url = new URL(window.location.href);
    url.searchParams.set("vista", vista);
    window.location.href = url.toString();
}

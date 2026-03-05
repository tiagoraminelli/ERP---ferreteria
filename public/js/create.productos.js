const costoInput = document.getElementById("precio_costo");
const margenInput = document.getElementById("margen_ganancia");
const precioInput = document.getElementById("precio");

function calcularPrecio() {
    let costo = parseFloat(costoInput.value) || 0;
    let margen = parseFloat(margenInput.value) || 0;
    let precioFinal = costo + costo * (margen / 100);
    precioInput.value = precioFinal.toFixed(2);
}

costoInput.addEventListener("input", calcularPrecio);
margenInput.addEventListener("input", calcularPrecio);

// Calcular al cargar la página
document.addEventListener("DOMContentLoaded", calcularPrecio);

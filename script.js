document.addEventListener("DOMContentLoaded", function () {
    const periodosInput = document.getElementById("periodos");
    const demandaTable = document.getElementById("demanda-table");
    const metodoSelect = document.getElementById("metodo");

    periodosInput.addEventListener("input", function () {
        const periodos = parseInt(periodosInput.value, 10);
        demandaTable.innerHTML = ""; // Limpia la tabla existente

        for (let i = 1; i <= periodos; i++) {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${i}</td>
                <td><input type="number" name="demanda[]" required></td>
                <td></td>
            `;
            demandaTable.appendChild(row);
        }
    });

    metodoSelect.addEventListener("change", function () {
        const selectedMetodo = metodoSelect.value;

        // Ocultar todos los campos adicionales
        document.getElementById("n_promedio_movil_simple").style.display = "none";
        document.getElementById("promedio-movil-ponderado").style.display = "none";
        document.getElementById("alfa_suavizado_exponencial_simple").style.display = "none";
        document.getElementById("suavizado_exponencial_doble").style.display = "none";
        document.getElementById("winters").style.display = "none";
        document.getElementById("regresionlineal").style.display = "none";

        if (selectedMetodo === "promedio_movil_simple") {
            // Mostrar el campo adicional "N" solo si se selecciona Promedio Móvil Simple
            document.getElementById("n_promedio_movil_simple").style.display = "block";
        } else if (selectedMetodo === "promedio_movil_ponderado") {
            // Mostrar los campos adicionales para Promedio Móvil Ponderado solo si se selecciona ese método
            document.getElementById("promedio-movil-ponderado").style.display = "block";
        } else if (selectedMetodo === "suavizado_exponencial_simple") {
            // Mostrar el campo adicional "Alpha" solo si se selecciona Suavizado Exponencial Simple
            document.getElementById("alfa_suavizado_exponencial_simple").style.display = "block";
        } else if (selectedMetodo === "suavizado_exponencial_doble") {
            // Mostrar el campo adicional "Alpha" solo si se selecciona Suavizado Exponencial Simple
            document.getElementById("suavizado_exponencial_doble").style.display = "block";
        } else if (selectedMetodo === "metodo_winters") {
            // Mostrar el campo adicional "Alpha" solo si se selecciona Suavizado Exponencial Simple
            document.getElementById("winters").style.display = "block";
        }  else if (selectedMetodo === "regresion_lineal") {
            // Mostrar el campo adicional "Alpha" solo si se selecciona Suavizado Exponencial Simple
            document.getElementById("regresionlineal").style.display = "block";
        } 
        // Agregar lógica para otros métodos según sea necesario
    });
});

<html>

</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $numPeriodos = $_POST["periodos"];
    $metodo = $_POST["metodo"];

    // Recuperar la demanda para cada periodo
    $demanda = array();
    for ($i = 1; $i <= $numPeriodos; $i++) {
        $demanda[] = $_POST["demanda_periodo_" . $i];
    }

    // Realizar los cálculos según el método seleccionado
    $pronosticos = calcularPronosticos($metodo, $numPeriodos, $demanda);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener los valores del formulario
        $a = isset($_POST['a_regresionlineal']) ? floatval($_POST['a_regresionlineal']) : 0;
        $b = isset($_POST['b_regresionlineal']) ? floatval($_POST['b_regresionlineal']) : 0;
        $periodos = isset($_POST['periodos']) ? intval($_POST['periodos']) : 0;

        // Mostrar el número de períodos
        echo "Períodos: $periodos<br>";

        // Calcular el resultado de la regresión lineal para cada período
        // for ($n = 1; $n <= $periodos; $n++) {
        //     $resultado = $a + ($b * $n);
        //     echo "Resultado para el período $n: $resultado<br>";
        // }
    }
}

function calcularPronosticos($metodo, $numPeriodos, $demanda)
{
    $pronosticos = array();

    for ($i = 0; $i < $numPeriodos; $i++) {
        $pronosticos[] = calcularPronostico($metodo, $demanda, $i);
    }

    return $pronosticos;
}

function calcularPronostico($metodo, $demanda, $indice)
{
    $indice = max($indice, 0);
    $n = intval($_POST["n_promedio_movil_simple"]);
    if ($metodo === "n_promedio_movil_simple") {
        if ($indice < $n) {
            return ""; // No se pueden calcular pronósticos para los primeros $n periodos
        }
        $indice = max($indice, 0);

        // Calcula el promedio de las últimas "n" demandas sin contar la última
        $suma = 0;
        for ($i = $indice - $n; $i < $indice; $i++) {
            $suma += $demanda[$i];
        }

        $pronostico = $suma / $n; // Calcula el promedio móvil simple

        return $pronostico; // Valor calculado según el método
        // }elseif ($metodo === "promedio-movil-ponderado") {
        //     // Lógica para promedio móvil ponderado
        //     // ...
        //     return $pronostico; // Valor calculado según el método
        // }elseif ($metodo === "alfa_suavizado_exponencial_simple") {
        //     // Lógica para promedio móvil ponderado
        //     // ...
        //     return $pronostico; // Valor calculado según el método
        // }elseif ($metodo === "suavizado_exponencial_doble") {
        //     // Lógica para promedio móvil ponderado
        //     // ...
        //     return $pronostico; // Valor calculado según el método
        // }elseif ($metodo === "winters") {
        //     // Lógica para promedio móvil ponderado
        //     // ...
        //     return $pronostico; // Valor calculado según el método
        // }elseif ($metodo === "regresionlineal") {
        //     // Lógica para promedio móvil ponderado
        //     // ...
        //     return $pronostico; // Valor calculado según el método

        // return ""; // Método no reconocido

    }
}
?>
<table>
    <tr>
        <th>Periodo</th>
        <th>Demanda</th>
        <th>Pronóstico</th>
        <th>Regresion Lineal</th>
    </tr>
    <?php
    for ($i = 0; $i < $numPeriodos; $i++) {
        echo "<tr>";
        echo "<td>" . ($i + 1) . "</td>"; // Mostrar el periodo
        echo "<td>" . $demanda[$i] . "</td>"; // Mostrar la demanda
        echo "<td>" . $pronosticos[$i] . "</td>";
        $resultado = $a + ($b * ($i + 1));
        echo "<td>" . "$resultado<br>" . "</td>"; // Mostrar el pronóstico

        echo "</tr>";
    }
    ?>

</table>
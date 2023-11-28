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

    /*Recuperar alfa para suavizado exponencial simple*/
    $alfa = floatval($_POST["alfa_suavizado_exponencial_simple"]);

    /*Recuperar los valores para el promedio móvil ponderado*/
    $t1 = floatval($_POST["t1_promedio_movil_ponderado"]);
    $t2 = floatval($_POST["t2_promedio_movil_ponderado"]);
    $t3 = floatval($_POST["t3_promedio_movil_ponderado"]);

    /*Recuperar los valores para el suavizado exponencial doble*/
    $alfad = floatval($_POST["alfa_suavizado_exponencial_doble"]);
    $beta = floatval($_POST["beta_suavizado_exponencial_doble"]);
    $rho = floatval($_POST["RHO_suavizado_exponencial_doble"]);



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
        return $pronostico; 

    } elseif ($metodo === "promedio_movil_ponderado") { // Asegurarse de que hay suficientes datos para calcular el promedio móvil ponderado


            if ($indice < 3) {
                return "";
            }
    
            // Calcular el promedio móvil ponderado para los tres últimos elementos
            $pronostico = ($t1 * $demanda[$indice - 1] + $t2 * $demanda[$indice - 2] + $t3 * $demanda[$indice - 3]) / ($t1 + $t2 + $t3);
            return $pronostico;

    }elseif ($metodo === "suavizado_exponencial_simple") { 

        if ($indice === 0) {
            return $demanda[0]; // El primer pronóstico es igual a la primera demanda
        }

        $pronostico = $demanda[0];

        for ($i = 1; $i <= $indice; $i++) {
            $pronostico = $alfa * $demanda[$i - 1] + (1 - $alfa) * $pronostico;
        }
        return $pronostico; // Valor calculado según el método
    }elseif ($metodo === "suavizado_exponencial_doble") { 
        
        $at = $demanda[0];
        $tend = 0;
        $at_ant = $at;

        for ($i = 0; $i < $indice && $i < count($demanda); $i++) {
            $at_temp = $at;
            $at = $alfad * $demanda[$i] + (1 - $alfad) * ($at_temp + $tend);
            $tend = $beta * ($at - $at_ant) + (1 - $beta) * $tend;

            $at_ant = $at;
        }

        if ($indice == 0) {
            return ""; // No se genera un pronóstico para el primer periodo
        } else {
            return $at + $rho * $tend;
        }
}}
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
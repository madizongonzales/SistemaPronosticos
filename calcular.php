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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_periodos = $_POST['periodos'];
    $alpha = $_POST['alpha_winters'];
    $beta = $_POST['beta_winters'];
    $gamma = $_POST['gamma_winters'];
    $L = $_POST['L'];

    // Inicializar arrays para almacenar datos
    $demanda = [];
    $At = [];
    $Tt = [];
    $Atw = [];
    $Ttw = [];
    $St = [];
    $Winters = [];
    $error = [];

    // Obtener demanda introducida
    for ($i = 1; $i <= $num_periodos; $i++) {
        $demanda[$i] = $_POST["demanda_periodo_$i"];
    }

    // Calcular con el método Winters
    // Inicializar primeros valores
    $At[1] = $demanda[1];
    $Tt[1] = 0;
    $Atw[1] = $demanda[1];
    $Ttw[1] = 0;
    $St[1] = 1; // Comienza en 
    $Winters[1] = 0; // Fórmula inicial

    // Calcular At, Tt, Atw, Ttw, St y Winters
    for ($i = 2; $i <= $num_periodos; $i++) {
        // At
        $At[$i] = $alpha * $demanda[$i] + (1 - $alpha) * ($At[$i - 1] + $Tt[$i - 1]);

        // Tt
        $Tt[$i] = $beta * ($At[$i] - $At[$i - 1]) + (1 - $beta) * $Tt[$i - 1];

        // Atw
        if ($i <= $L) {
            $Atw[$i] = $alpha * ($demanda[$i] / 1) + (1 - $alpha) * ($Atw[$i - 1] + $Ttw[$i - 1]);
        } else {
            $Atw[$i] = $alpha * ($demanda[$i] / $St[$i - $L]) + (1 - $alpha) * ($Atw[$i - 1] + $Ttw[$i - 1]);
        }

        // Ttw
        $Ttw[$i] = $beta * ($Atw[$i] - $Atw[$i - 1]) + (1 - $beta) * $Ttw[$i - 1];

        // St
        if ($i <= $L) {
            $St[$i] = $gamma * ($demanda[$i] / $Atw[$i]) + (1 - $gamma) * 1; // Siguientes L-1 valores
        } else {
            $St[$i] = $gamma * ($demanda[$i] / $Atw[$i]) + (1 - $gamma) * $St[$i - $L]; // Resto de valores
        }

        // Winters
        if ($i <= $L) {
            $Winters[$i] = ($Atw[$i - 1] + 1 * $Ttw[$i - 1]) * 1;
        } else {
            $Winters[$i] = ($Atw[$i - 1] + 1 * $Ttw[$i - 1]) * $St[$i - $L];
        }

        // Calcular error
        // $error[$i] = $demanda[$i] - $St[$i];
    }
    // $pronostico = $Winters[$num_periodos];
    // return $pronostico;
}

function calcularPronosticos($metodo, $numPeriodos, $demanda)
{
    $pronosticos = array();

    for ($i = 0; $i < $numPeriodos; $i++) {
        $pronosticos[] = calcularPronostico($metodo, $demanda, $i);
    }

    return $pronosticos;
}

function calcularPronostico($metodo, $demanda, $indice,)
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

    //Rucupero datos para Winters
    // $numPeriodos = $_POST['periodos'];
    $alpha = $_POST['alpha_winters'];
    $betaw = $_POST['beta_winters'];
    $gamma = $_POST['gamma_winters'];
    $L = $_POST['L'];


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
    } elseif ($metodo === "suavizado_exponencial_simple") {

        if ($indice === 0) {
            return $demanda[0]; // El primer pronóstico es igual a la primera demanda
        }

        $pronostico = $demanda[0];

        for ($i = 1; $i <= $indice; $i++) {
            $pronostico = $alfa * $demanda[$i - 1] + (1 - $alfa) * $pronostico;
        }
        return $pronostico; // Valor calculado según el método

    } elseif ($metodo === "suavizado_exponencial_doble") {

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
    } elseif ($metodo === "regresion_lineal2") {
        $a = isset($_POST['a_regresionlineal']) ? floatval($_POST['a_regresionlineal']) : 0;  // Supongamos que calcularA y calcularB son funciones que calculan los valores de 'a' y 'b' respectivamente.
        $b = isset($_POST['b_regresionlineal']) ? floatval($_POST['b_regresionlineal']) : 0;
    }
}
?>

<table border="1">
    <tr>
        <th>Periodo</th>
        <th>Demanda</th>
        <th>Pronóstico</th>
        <th>Regresion Lineal</th>
        <th>Winters</th>
        <th>At</th>
        <th>Tt</th>
        <th>Atw</th>
        <th>Ttw</th>
        <th>St</th>
    </tr>

    <?php
    for ($i = 0; $i < $numPeriodos; $i++) {
        echo "<tr>";
        echo "<td>" . ($i + 1) . "</td>"; // Mostrar el periodo
        echo "<td>" . $demanda[$i + 1] . "</td>"; // Mostrar la demanda
        echo "<td>" . $pronosticos[$i] . "</td>";
        $RL = $a + ($b * ($i + 1));
        echo "<td>" . round($RL) . "<br>" . "</td>"; // Mostrar el pronóstico
        echo "<td>" . round($Winters[$i + 1]) . "</td>";
        echo "<td>{$At[$i + 1]}</td>";
        echo "<td>{$Tt[$i + 1]}</td>";
        echo "<td>{$Atw[$i + 1]}</td>";
        echo "<td>{$Ttw[$i + 1]}</td>";
        echo "<td>{$St[$i + 1]}</td>";
        echo "</tr>";
    }

    ?>

</table>
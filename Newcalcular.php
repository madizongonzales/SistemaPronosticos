<?php

//promedio movil simple
function calcularPromedioMovilSimple($demanda, $n, $num_periodos)
{
    $promedio_movil_simple = [];

    for ($i = 1; $i <= $num_periodos; $i++) {
        if ($i <= $n || !isset($demanda[$i])) {
            $promedio_movil_simple[$i] = '';
        } else {
            // Aplicar la fórmula a partir del período $n + 1
            $suma = 0;
            for ($j = 1; $j <= $n; $j++) {
                $suma += $demanda[$i - $j];
            }

            // Asignar el resultado al índice correcto
            $indice_resultado = $i - $n;
            $promedio_movil_simple[$indice_resultado] = ($n > 0) ? $suma / $n : '';
        }
    }

    return $promedio_movil_simple;
}

//promedio movil ponderado
function calcularPromedioMovilPonderado($demanda, $np, $t1, $t2, $t3, $num_periodos)
{
    $Pmponderado = [];

    for ($i = 1; $i <= $num_periodos; $i++) {
        $Pmponderado[$i] = '';

        if ($i >= $np && isset($demanda[$i - 1]) && isset($demanda[$i - 2]) && isset($demanda[$i - 3])) {
            // Calcular el promedio móvil ponderado solo si la suma de los pesos no es cero
            $pesos_suma = $t1 + $t2 + $t3;
            if ($pesos_suma != 0) {
                $Pmponderado[$i] = ($t1 * $demanda[$i - 1] + $t2 * $demanda[$i - 2] + $t3 * $demanda[$i - 3]) / $pesos_suma;
            }
        }
    }

    return $Pmponderado;
}

//regresion lineal
function calcularRegresionLineal($a, $b, $periodos)
{
    $resultados = [];

    for ($n = 1; $n <= $periodos; $n++) {
        $resultado = $a + ($b * $n);
        $resultados[] = $resultado;
    }

    return $resultados;
}

//metodo de winters
function calcularWinters($periodos, $alpha, $beta, $gamma, $L, $demanda)
{
    // $At = [];
    // $Tt = [];
    // $Atw = [];
    // $Ttw = [];
    // $St = [];
    // $Winters = [];

    // // Inicializar primeros valores
    // $At[1] = $demanda[1];
    // $Tt[1] = 0;
    // $Atw[1] = $demanda[1];
    // $Ttw[1] = 0;
    // $St[1] = 1; // Comienza en 
    // $Winters[1] = 0; // Fórmula inicial

    // // Calcular At, Tt, Atw, Ttw, St y Winters
    // for ($i = 2; $i <= $periodos; $i++) {
    //     // At
    //     $At[$i] = $alpha * $demanda[$i] + (1 - $alpha) * ($At[$i - 1] + $Tt[$i - 1]);

    //     // Tt
    //     $Tt[$i] = $beta * ($At[$i] - $At[$i - 1]) + (1 - $beta) * $Tt[$i - 1];

    //     // Atw
    //     if ($i <= $L) {
    //         $Atw[$i] = $alpha * ($demanda[$i] / 1) + (1 - $alpha) * ($Atw[$i - 1] + $Ttw[$i - 1]);
    //     } else {
    //         $Atw[$i] = $alpha * ($demanda[$i] / $St[$i - $L]) + (1 - $alpha) * ($Atw[$i - 1] + $Ttw[$i - 1]);
    //     }

    //     // Ttw
    //     $Ttw[$i] = $beta * ($Atw[$i] - $Atw[$i - 1]) + (1 - $beta) * $Ttw[$i - 1];

    //     // St
    //     if ($i <= $L) {
    //         $St[$i] = $gamma * ($demanda[$i] / $Atw[$i]) + (1 - $gamma) * 1; // Siguientes L-1 valores
    //     } else {
    //         $St[$i] = $gamma * ($demanda[$i] / $Atw[$i]) + (1 - $gamma) * $St[$i - $L]; // Resto de valores
    //     }

    //     // Winters
    //     if ($i <= $L) {
    //         $Winters[$i] = ($Atw[$i - 1] + 1 * $Ttw[$i - 1]) * 1;
    //     } else {
    //         $Winters[$i] = ($Atw[$i - 1] + 1 * $Ttw[$i - 1]) * $St[$i - $L];
    //     }

    //     //Calcular error
    //     //$error[$i] = $demanda[$i] - $St[$i];
    // }

    // return $Winters;
}

//suavizado exponencial doble
function calcularSuavizadoExponencialDoble($alfa, $beta, $rho, $demanda, $periodo)
{

    $at = $demanda[1];
    $tend = 0;
    $at_ant = $at;

    for ($i = 1; $i < $periodo && $i < count($demanda); $i++) {
        $at_temp = $at;
        $at = $alfa * $demanda[$i] + (1 - $alfa) * ($at_temp + $tend);
        $tend = $beta * ($at - $at_ant) + (1 - $beta) * $tend;

        $at_ant = $at;
    }

    if ($periodo == $rho) {
        return ""; // No se genera un pronóstico para el primer periodo
    } else {
        return $at + $rho * $tend;
    }
}

//suavizado exponencial simple
function calcularSuavizadoExponencialSimple($alfaSES, $demandaSES, $periodo)
{
    $SES = $demandaSES[1]; // El primer pronóstico es igual a la primera demanda

    for ($i = 2; $i <= $periodo; $i++) {
        $SES = $alfaSES * $demandaSES[$i - 1] + (1 - $alfaSES) * $SES;
    }

    return $SES; // Valor calculado según el método
}

//suavizado exponencial simple
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los valores para el suavizado exponencial simple
    $alfaSES = floatval($_POST["alfa_suavizado_exponencial_simple"]);
    $num_periodos = $_POST['periodos'];

    // Obtener demanda introducida
    $demandaSES = [];
    for ($i = 1; $i <= $num_periodos; $i++) {
        $demandaSES[$i] = $_POST["demanda_periodo_$i"];
    }

    // Calcular con el método de suavizado exponencial simple
    $resultadosSES = [];
    for ($i = 1; $i <= $num_periodos; $i++) {
        $resultadosSES[$i] = calcularSuavizadoExponencialSimple($alfaSES, $demandaSES, $i);
    }
}

//suavizado exponencial doble
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los valores para el suavizado exponencial doble
    $num_periodos = $_POST['periodos'];
    $alfa = floatval($_POST["alfa_suavizado_exponencial_doble"]);
    $beta = floatval($_POST["beta_suavizado_exponencial_doble"]);
    $rho = floatval($_POST["RHO_suavizado_exponencial_doble"]);

    // Obtener demanda introducida
    $demanda = [];
    for ($i = 1; $i <= $num_periodos; $i++) {
        $demanda[$i] = $_POST["demanda_periodo_$i"];
    }

    // Calcular con el método de suavizado exponencial doble
    $resultadosSuavizadoDoble = [];
    for ($i = 1; $i <= $num_periodos; $i++) {
        $resultadosSuavizadoDoble[$i] = calcularSuavizadoExponencialDoble($alfa, $beta, $rho, $demanda, $i);
    }
}


//Metodo de Winters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_periodos = $_POST['periodos'];
    $alpha = $_POST['alpha_winters'];
    $beta = $_POST['beta_winters'];
    $gamma = $_POST['gamma_winters'];
    $L = $_POST['L'];

    // Obtener demanda introducida
    $demanda = [];
    for ($i = 1; $i <= $num_periodos; $i++) {
        $demanda[$i] = $_POST["demanda_periodo_$i"];
    }

    // Calcular con el método Winters
    $resultadosWinters = calcularWinters($num_periodos, $alpha, $beta, $gamma, $L, $demanda);
}




//Metodo regresion lineal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $a = isset($_POST['a_regresionlineal']) ? floatval($_POST['a_regresionlineal']) : 0;
    $b = isset($_POST['b_regresionlineal']) ? floatval($_POST['b_regresionlineal']) : 0;
    $periodos = isset($_POST['periodos']) ? intval($_POST['periodos']) : 0;

    // Mostrar el número de períodos
    echo "Períodos: $periodos<br>";

    // Calcular el resultado de la regresión lineal utilizando la función
    $resultadosRegresionLineal = calcularRegresionLineal($a, $b, $periodos);
}

//metodo promedio movil simple
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $n = intval($_POST["n_promedio_movil_simple"]);
    $num_periodos = intval($_POST['periodos']);

    if ($num_periodos < $n) {
        echo "No hay suficientes periodos para calcular el promedio móvil simple.";
        exit;
    }

    $demanda = [];

    for ($i = 1; $i <= $num_periodos; $i++) {
        $demanda[$i] = floatval($_POST["demanda_periodo_$i"]);
    }

    $promedio_movil_simple = calcularPromedioMovilSimple($demanda, $n, $num_periodos);
}

//promedio movil ponderado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $np = intval($_POST["n_promedio_movil_ponderado"]);
    $t1 = floatval($_POST["t1_promedio_movil_ponderado"]);
    $t2 = floatval($_POST["t2_promedio_movil_ponderado"]);
    $t3 = floatval($_POST["t3_promedio_movil_ponderado"]);

    $Pmponderado = calcularPromedioMovilPonderado($demanda, $np, $t1, $t2, $t3, $num_periodos);
}


?>

<table border="1px">
    <tr>
        <th>Periodo</th>
        <th>Demanda</th>
        <th>Promedio movil simple</th>
        <th>Promedio movil ponderado</th>
        <th>Suavizado Exponencial Simple</th>
        <th>Regresion Lineal</th>
        <th>Suavizado Exponensial doble</th>
        <th>winters</th>
    </tr>

    <?php
    for ($i = 1; $i <= $num_periodos; $i++) {
        echo "<tr>";
        echo "<td>" . $i . "</td>";
        echo "<td>" . $demanda[$i] . "</td>";
        // Verificar si el índice existe en $promedio_movil_simple
        $pms = $i - $n;
        echo '<td>' . (isset($promedio_movil_simple[$pms]) ? round(floatval($promedio_movil_simple[$pms]), 2) : '') . '</td>';
        echo '<td>' . ($Pmponderado[$i] !== '' ? round($Pmponderado[$i]) : '') . '</td>';
        echo "<td>" . ($resultadosSES[$i] !== '' ? round($resultadosSES[$i]) : '') . "</td>";
        echo '<td>' . (isset($resultadosRegresionLineal[$i - 1]) ? round($resultadosRegresionLineal[$i - 1]) : '') . '</td>';
        echo "<td>" . ($resultadosSuavizadoDoble[$i] !== '' ? round($resultadosSuavizadoDoble[$i]) : '') . "</td>";
        echo '<td>' . (isset($resultadosWinters[$i]) ? round($resultadosWinters[$i]) : '') . '</td>';
        echo "</tr>";
    }

    ?>
</table>
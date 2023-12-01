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
function calcularPromedioMovilPonderado($demanda, $t1, $t2, $t3, $num_periodos)
{
    $Pmponderado = [];

    for ($i = 1; $i <= $num_periodos; $i++) {
        $Pmponderado[$i] = '';

        if ($i >= 3 && isset($demanda[$i - 1]) && isset($demanda[$i - 2]) && isset($demanda[$i - 3])) {
            // Calcular el promedio móvil ponderado solo si la suma de los pesos no es cero
            $pesos_suma = $t1 + $t2 + $t3;
            if ($pesos_suma != 0) {
                $Pmponderado[$i] = ($t1 * $demanda[$i - 1] + $t2 * $demanda[$i - 2] + $t3 * $demanda[$i - 3]) / $pesos_suma;
            }
        }
    }

    return $Pmponderado;
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
function calcularWinters($periodosw, $alphaw, $betaw, $gammaw, $Lw, $demandaw)
{

    $At = $Tt = $Atw = $Ttw = $St = $Winters = [];

    // Inicializar primeros valores
    $At[1] = intval($demandaw[1]);
    $Tt[1] = 0;
    $Atw[1] = intval($demandaw[1]);
    $Ttw[1] = 0;
    $St[1] = 1; // Comienza en 1 como número entero
    $Winters[1] = 0; // Fórmula inicial

    // Calcular At, Tt, Atw, Ttw, St y Winters
    for ($i = 2; $i <= $periodosw; $i++) {
        // At
        $At[$i] = intval($alphaw * intval($demandaw[$i]) + (1 - $alphaw) * (intval($At[$i - 1]) + intval($Tt[$i - 1])));

        // Tt
        $Tt[$i] = intval($betaw * (intval($At[$i]) - intval($At[$i - 1])) + (1 - $betaw) * intval($Tt[$i - 1]));

        // Atw
        if ($i <= $Lw) {
            $Atw[$i] = intval($alphaw * (intval($demandaw[$i]) / 1) + (1 - $alphaw) * (intval($Atw[$i - 1]) + intval($Ttw[$i - 1])));
        } else {
            $St[$i - $Lw] = isset($St[$i - $Lw]) ? intval($St[$i - $Lw]) : 1;
            $Atw[$i] = intval($alphaw * (intval($demandaw[$i]) / intval($St[$i - $Lw])) + (1 - $alphaw) * (intval($Atw[$i - 1]) + intval($Ttw[$i - 1])));
        }

        // Ttw
        $Ttw[$i] = intval($betaw * (intval($Atw[$i]) - intval($Atw[$i - 1])) + (1 - $betaw) * intval($Ttw[$i - 1]));

        // St
        if ($i <= $Lw) {
            $St[$i] = intval($gammaw * (intval($demandaw[$i]) / intval($Atw[$i])) + (1 - $gammaw) * 1); // Siguientes L-1 valores
        } else {
            $St[$i] = intval($gammaw * (intval($demandaw[$i]) / intval($Atw[$i])) + (1 - $gammaw) * intval($St[$i - $Lw])); // Resto de valores
        }

        // Winters
        if ($i <= $Lw) {
            $Winters[$i] = intval((intval($Atw[$i - 1]) + 1 * intval($Ttw[$i - 1])) * 1);
        } else {
            $Winters[$i] = intval((intval($Atw[$i - 1]) + 1 * intval($Ttw[$i - 1])) * intval($St[$i - $Lw]));
        }

        //Calcular error
        //$error[$i] = intval($demanda[$i]) - intval($St[$i]);
    }

    return $Winters;
}



/************************************************************/

// Variables para acumular las diferencias de desviaciones medias absolutas
$diferencias_acumuladas_pm_simple = 0;
$diferencias_acumuladas_pm_ponderado = 0;
$diferencias_acumuladas_suavizado_simple = 0;
$diferencias_acumuladas_regresion_lineal = 0;
$diferencias_acumuladas_suavizado_doble = 0;
$diferencias_acumuladas_winters = 0;


// Variables para acumular los errores cuadráticos medios
$errores_cuadraticos_pm_simple = 0;
$errores_cuadraticos_pm_ponderado = 0;
$errores_cuadraticos_suavizado_simple = 0;
$errores_cuadraticos_regresion_lineal = 0;
$errores_cuadraticos_suavizado_doble = 0;

// Variables para acumular los errores porcentuales medios absolutos
$errores_porcentuales_pm_simple = 0;
$errores_porcentuales_pm_ponderado = 0;
$errores_porcentuales_suavizado_simple = 0;
$errores_porcentuales_regresion_lineal = 0;
$errores_porcentuales_suavizado_doble = 0;



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

    // Calcular la desviación media absoluta
    for ($i = $n + 1; $i <= $num_periodos; $i++) {
        if (isset($promedio_movil_simple[$i - $n])) {
            $diferencia = abs(floatval($demanda[$i]) - floatval($promedio_movil_simple[$i - $n]));
            $diferencias_acumuladas_pm_simple += $diferencia;
        }
    }

    // Mostrar desviación media absoluta para cada método
    if ($diferencias_acumuladas_pm_simple > 0) {
        $promedio_diferencias_pm_simple = $diferencias_acumuladas_pm_simple / (count($demanda) - max($n, 3));
    }

    /*Erro cuadratico*/

    // Calcular el error cuadrático medio para el promedio móvil simple
    for ($i = $n + 1; $i <= $num_periodos; $i++) {
        if (isset($promedio_movil_simple[$i - $n])) {
            $diferencia = floatval($demanda[$i]) - floatval($promedio_movil_simple[$i - $n]);
            $errores_cuadraticos_pm_simple += pow($diferencia, 2);
        }
    }

    // Mostrar error cuadrático medio para el promedio móvil simple
    if ($errores_cuadraticos_pm_simple > 0) {
        $ecm_pm_simple = $errores_cuadraticos_pm_simple / (count($demanda) - max($n, 3));
    }

    /*Error porcentual medio absoluto*/

    // Calcular el error porcentual medio absoluto para el promedio móvil simple
    for ($i = $n + 1; $i <= $num_periodos; $i++) {
        if (isset($promedio_movil_simple[$i - $n]) && $demanda[$i] != 0) {
            $diferencia = abs(floatval($demanda[$i]) - floatval($promedio_movil_simple[$i - $n])) / $demanda[$i];
            $errores_porcentuales_pm_simple += $diferencia;
        }
    }

    // Mostrar error porcentual medio absoluto para el promedio móvil simple
    if ($errores_porcentuales_pm_simple > 0) {
        $mape_pm_simple = ($errores_porcentuales_pm_simple / (count($demanda) - max($n, 3))) * 100;
    }
}


//promedio movil ponderado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $t1 = floatval($_POST["t1_promedio_movil_ponderado"]);
    $t2 = floatval($_POST["t2_promedio_movil_ponderado"]);
    $t3 = floatval($_POST["t3_promedio_movil_ponderado"]);

    $Pmponderado = calcularPromedioMovilPonderado($demanda, $t1, $t2, $t3, $num_periodos);

    // Calcular la desviación media absoluta para el promedio móvil ponderado
    for ($i = 1; $i <= $num_periodos; $i++) {
        if ($i >= 3 && isset($demanda[$i - 1]) && isset($demanda[$i - 2]) && isset($demanda[$i - 3])) {
            $diferencia = abs(floatval($demanda[$i]) - floatval($Pmponderado[$i]));
            $diferencias_acumuladas_pm_ponderado += $diferencia;
        }
    }

    if ($diferencias_acumuladas_pm_ponderado > 0 && is_array($demanda)) {
        $promedio_diferencias = $diferencias_acumuladas_pm_ponderado / (count($demanda) - max($n, 3));
    }

    /*Erro cuadratico*/

    // Calcular el error cuadrático medio para el promedio móvil ponderado
    for ($i = 1; $i <= $num_periodos; $i++) {
        if ($i >= 3 && isset($demanda[$i - 1]) && isset($demanda[$i - 2]) && isset($demanda[$i - 3])) {
            $diferencia = floatval($demanda[$i]) - floatval($Pmponderado[$i]);
            $errores_cuadraticos_pm_ponderado += pow($diferencia, 2);
        }
    }

    // Mostrar error cuadrático medio para el promedio móvil ponderado
    if ($errores_cuadraticos_pm_ponderado > 0 && is_array($demanda)) {
        $ecm_pm_ponderado = $errores_cuadraticos_pm_ponderado / (count($demanda) - max($n, 3));
    }

    /*Error porcentual medio absoluto*/

    // Calcular el error porcentual medio absoluto para el promedio móvil ponderado
    for ($i = 1; $i <= $num_periodos; $i++) {
        if ($i >= 3 && isset($demanda[$i - 1]) && isset($demanda[$i - 2]) && isset($demanda[$i - 3]) && $demanda[$i] != 0) {
            $diferencia = abs(floatval($demanda[$i]) - floatval($Pmponderado[$i])) / $demanda[$i];
            $errores_porcentuales_pm_ponderado += $diferencia;
        }
    }

    // Mostrar error porcentual medio absoluto para el promedio móvil ponderado
    if ($errores_porcentuales_pm_ponderado > 0 && is_array($demanda)) {
        $mape_pm_ponderado = ($errores_porcentuales_pm_ponderado / (count($demanda) - max($n, 3))) * 100;
    }
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

    // Calcular desviación media absoluta para el suavizado exponencial simple
    for ($i = 1; $i <= $num_periodos; $i++) {
        if (isset($resultadosSES[$i])) {
            $diferencia = abs($demanda[$i] - $resultadosSES[$i]);
            $diferencias_acumuladas_suavizado_simple += $diferencia;
        }
    }
    if ($diferencias_acumuladas_suavizado_simple > 0 && is_array($demanda)) {
        $promedio_diferencias_suavizado_simple = $diferencias_acumuladas_suavizado_simple / ($num_periodos - 1);
    }

    /*Erro cuadratico*/

    // Calcular el error cuadrático medio para el suavizado exponencial simple
    for ($i = 1; $i <= $num_periodos; $i++) {
        if (isset($resultadosSES[$i])) {
            $diferencia = $demanda[$i] - $resultadosSES[$i];
            $errores_cuadraticos_suavizado_simple += pow($diferencia, 2);
        }
    }

    // Mostrar error cuadrático medio para el suavizado exponencial simple
    if ($errores_cuadraticos_suavizado_simple > 0 && is_array($demanda)) {
        $ecm_suavizado_simple = $errores_cuadraticos_suavizado_simple / ($num_periodos - 1);
    }

    /*Error porcentual medio absoluto*/

    // Calcular el error porcentual medio absoluto para el suavizado exponencial simple
    for ($i = 1; $i <= $num_periodos; $i++) {
        if (isset($resultadosSES[$i]) && $demanda[$i] != 0) {
            $diferencia = abs($demanda[$i] - $resultadosSES[$i]) / $demanda[$i];
            $errores_porcentuales_suavizado_simple += $diferencia;
        }
    }

    // Mostrar error porcentual medio absoluto para el suavizado exponencial simple
    if ($errores_porcentuales_suavizado_simple > 0 && is_array($demanda)) {
        $mape_suavizado_simple = ($errores_porcentuales_suavizado_simple / ($num_periodos - 1)) * 100;
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
    $diferencias_acumuladas_suavizado_doble = 0; // Asegurar que la variable se inicialice correctamente

    for ($i = 2; $i <= $num_periodos; $i++) {
        if ($i >= $rho) {
            $resultadosSuavizadoDoble[$i] = calcularSuavizadoExponencialDoble($alfa, $beta, $rho, $demanda, $i);

            // Calcular la desviación media absoluta solo si existe el resultado
            if (isset($resultadosSuavizadoDoble[$i]) && isset($demanda[$i])) {
                $diferencia = abs(floatval($demanda[$i]) - floatval($resultadosSuavizadoDoble[$i]));
                $diferencias_acumuladas_suavizado_doble += $diferencia;
            }
        }
    }
    // Calcular la desviación media absoluta
    if ($diferencias_acumuladas_suavizado_doble > 0 && is_array($demanda)) {
        $promedio_diferencias_suavizado_doble = $diferencias_acumuladas_suavizado_doble / ($num_periodos - $rho);
    }


    /*Erro cuadratico*/

    // Método suavizado exponencial doble
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // ...

        // Calcular el error cuadrático medio para el suavizado exponencial doble
        for ($i = 2; $i <= $num_periodos; $i++) {
            if ($i >= $rho && isset($resultadosSuavizadoDoble[$i]) && isset($demanda[$i])) {
                $diferencia = $demanda[$i] - $resultadosSuavizadoDoble[$i];
                $errores_cuadraticos_suavizado_doble += pow($diferencia, 2);
            }
        }

        // Mostrar error cuadrático medio para el suavizado exponencial doble
        if ($errores_cuadraticos_suavizado_doble > 0 && is_array($demanda)) {
            $ecm_suavizado_doble = $errores_cuadraticos_suavizado_doble / ($num_periodos - $rho);
        }
    }

    /*Error porcentual medio absoluto*/

    // Calcular el error porcentual medio absoluto para el suavizado exponencial doble
    for ($i = 2; $i <= $num_periodos; $i++) {
        if ($i >= $rho && isset($resultadosSuavizadoDoble[$i]) && isset($demanda[$i]) && $demanda[$i] != 0) {
            $diferencia = abs($demanda[$i] - $resultadosSuavizadoDoble[$i]) / $demanda[$i];
            $errores_porcentuales_suavizado_doble += $diferencia;
        }
    }

    // Mostrar error porcentual medio absoluto para el suavizado exponencial doble
    if ($errores_porcentuales_suavizado_doble > 0 && is_array($demanda)) {
        $mape_suavizado_doble = ($errores_porcentuales_suavizado_doble / ($num_periodos - $rho)) * 100;
    }
}


//Metodo regresion lineal
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $a = isset($_POST['a_regresionlineal']) ? floatval($_POST['a_regresionlineal']) : 0;
    $b = isset($_POST['b_regresionlineal']) ? floatval($_POST['b_regresionlineal']) : 0;
    $periodos = isset($_POST['periodos']) ? intval($_POST['periodos']) : 0;

    // Calcular con el método de regresión lineal
    $resultadosRegresionLineal = calcularRegresionLineal($a, $b, $periodos);

    // Calcular la desviación media absoluta
    $diferencias_acumuladas_regresion_lineal = 0; // Asegurar que la variable se inicialice correctamente

    for ($i = 1; $i <= $num_periodos; $i++) {
        // Calcular la desviación media absoluta
        if (isset($resultadosRegresionLineal[$i - 1]) && isset($demanda[$i])) {
            $diferencia = abs($demanda[$i] - $resultadosRegresionLineal[$i - 1]);
            $diferencias_acumuladas_regresion_lineal += $diferencia;
        }
    }

    if ($diferencias_acumuladas_regresion_lineal > 0 && is_array($demanda)) {
        $promedio_diferencias_regresion_lineal = $diferencias_acumuladas_regresion_lineal / $num_periodos;
    }

    // Calcular el error cuadrático medio para la regresión lineal
    for ($i = 1; $i <= $num_periodos; $i++) {
        if (isset($resultadosRegresionLineal[$i - 1]) && isset($demanda[$i])) {
            $diferencia = $demanda[$i] - $resultadosRegresionLineal[$i - 1];
            $errores_cuadraticos_regresion_lineal += pow($diferencia, 2);
        }
    }

    // Mostrar error cuadrático medio para la regresión lineal
    if ($errores_cuadraticos_regresion_lineal > 0 && is_array($demanda)) {
        $ecm_regresion_lineal = $errores_cuadraticos_regresion_lineal / $num_periodos;
    }

    /*Error porcentual medio absoluto*/

    // Calcular el error porcentual medio absoluto para la regresión lineal
    for ($i = 1; $i <= $num_periodos; $i++) {
        if (isset($resultadosRegresionLineal[$i - 1]) && isset($demanda[$i]) && $demanda[$i] != 0) {
            $diferencia = abs($demanda[$i] - $resultadosRegresionLineal[$i - 1]) / $demanda[$i];
            $errores_porcentuales_regresion_lineal += $diferencia;
        }
    }

    // Mostrar error porcentual medio absoluto para la regresión lineal
    if ($errores_porcentuales_regresion_lineal > 0 && is_array($demanda)) {
        $mape_regresion_lineal = ($errores_porcentuales_regresion_lineal / (count($demanda))) * 100;
    }
}

// Método de Winters
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["metodo"]) && $_POST["metodo"] === "winters") {
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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mostrar el número de períodos
    echo "Períodos: $periodos<br>";
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
        echo '<td>' . (isset($promedio_movil_simple[$pms]) ? round(floatval($promedio_movil_simple[$pms])) : '') . '</td>';
        echo '<td>' . ($Pmponderado[$i] !== '' ? round($Pmponderado[$i]) : '') . '</td>';
        echo "<td>" . ($resultadosSES[$i] !== '' ? round($resultadosSES[$i]) : '') . "</td>";
        echo '<td>' . (isset($resultadosRegresionLineal[$i - 1]) ? round($resultadosRegresionLineal[$i - 1]) : '') . '</td>';
        echo "<td>" . (isset($resultadosSuavizadoDoble[$i]) ? round($resultadosSuavizadoDoble[$i]) : '') . "</td>";
        echo '<td>' . (isset($resultadosWinters[$i]) ? round($resultadosWinters[$i]) : '') . '</td>';
        echo "</tr>";
    }



    ?>
</table>

/*//</?php* /*/ <!-- // Mostrar tabla de errores echo "<table border='1px'>" ; echo "<tr>" ; echo "<th></th>" ; echo "<th>Promedio Móvil Simple</th>" ; echo "<th>Promedio Móvil Ponderado</th>" ; echo "<th>Suavizado Exponencial Simple</th>" ; echo "<th>Regresión Lineal</th>" ; echo "<th>Suavizado Exponencial Doble</th>" ; echo "<th>Winters</th>" ; // Agrega más columnas según sea necesario echo "</tr>" ; echo "<tr>" ; echo "<td>MAD</td>" ; echo "<td>" . ($promedio_diferencias_pm_simple ?? '' ) . "</td>" ; echo "<td>" . ($promedio_diferencias ?? '' ) . "</td>" ; echo "<td>" . ($promedio_diferencias_suavizado_simple ?? '' ) . "</td>" ; echo "<td>" . ($promedio_diferencias_regresion_lineal ?? '' ) . "</td>" ; echo "<td>" . ($promedio_diferencias_suavizado_doble ?? '' ) . "</td>" ; echo "<td>" . ($promedio_diferencias_winters ?? '' ) . "</td>" ; // Agrega más celdas con los valores de MAD para cada método echo "</tr>" ; echo "<tr>" ; echo "<td>MSE</td>" ; echo "<td>" . ($ecm_pm_simple ?? '' ) . "</td>" ; echo "<td>" . ($ecm_pm_ponderado ?? '' ) . "</td>" ; echo "<td>" . ($ecm_suavizado_simple ?? '' ) . "</td>" ; echo "<td>" . ($ecm_regresion_lineal ?? '' ) . "</td>" ; echo "<td>" . ($ecm_suavizado_doble ?? '' ) . "</td>" ; echo "<td>" . ($ecm_winters ?? '' ) . "</td>" ; // Agrega más celdas con los valores de MSE para cada método echo "</tr>" ; echo "<tr>" ; echo "<td>MAPE</td>" ; echo "<td>" . ($mape_pm_simple ?? '' ) . "</td>" ; echo "<td>" . ($mape_pm_ponderado ?? '' ) . "</td>" ; echo "<td>" . ($mape_suavizado_simple ?? '' ) . "</td>" ; echo "<td>" . ($mape_regresion_lineal ?? '' ) . "</td>" ; echo "<td>" . ($mape_suavizado_doble ?? '' ) . "</td>" ; echo "<td>" . ($mape_winters ?? '' ) . "</td>" ; // Agrega más celdas con los valores de MAPE para cada método echo "</tr>" ; // Agrega más filas según sea necesario echo "</table>" ; -->
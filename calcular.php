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

//regresion lineal
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

    } elseif ($metodo === "suavizado_exponencial_simple") {

        if ($indice === 0) {
            return $demanda[0]; // El primer pronóstico es igual a la primera demanda
        }

        $pronostico = $demanda[0];

        for ($i = 1; $i <= $indice; $i++) {
            $pronostico = $alfa * $demanda[$i - 1] + (1 - $alfa) * $pronostico;
        }
        return $pronostico; // Valor calculado según el método
    }
}

//Metodo winters
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
        //como inidico a una suma i en su posicion 0


        // Calcular error
        $error[$i] = $demanda[$i] - $St[$i];
    }
}
?>
<table border="1">
    <tr>
        <th>Periodo</th>
        <th>Demanda</th>
        <th>Pronóstico</th>
        <th>Regresion Lineal</th>
    </tr>
    <!-- <tr>
        <th>Número</th>
        <th>Demanda</th>
        <th>Winters</th>
        <th>At</th>
        <th>Tt</th>
        <th>Atw</th>
        <th>Ttw</th>
        <th>St</th>
    </tr> -->
    <?php
    for ($i = 0; $i < $numPeriodos; $i++) {
        echo "<tr>";
        echo "<td>" . ($i + 1) . "</td>"; // Mostrar el periodo
        echo "<td>" . $demanda[$i + 1] . "</td>"; // Mostrar la demanda
        echo "<td>" . $pronosticos[$i] . "</td>";
        $RL = $a + ($b * ($i + 1));
        echo "<td>" . "$RL<br>" . "</td>"; // Mostrar el pronóstico
        echo "</tr>";
    }
    // for ($i = 1; $i <= $num_periodos; $i++) {
    //     echo "<tr>";
    //     echo "<td>$i</td>";
    //     echo "<td>{$demanda[$i]}</td>";
    //     echo "<td>" . round($Winters[$i]) . "</td>";
    //     echo "<td>{$At[$i]}</td>";
    //     echo "<td>{$Tt[$i]}</td>";
    //     echo "<td>{$Atw[$i]}</td>";
    //     echo "<td>{$Ttw[$i]}</td>";
    //     echo "<td>{$St[$i]}</td>";
    //     echo "</tr>";
    // }
    ?>

</table>
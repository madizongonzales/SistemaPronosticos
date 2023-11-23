<!DOCTYPE html>
<html>

<head>
    <title>Resultado del Pronóstico</title>
</head>

<body>
    <h1>Resultado del Pronóstico</h1>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $demandas = $_POST["demanda"];
        $n = intval($_POST["n_promedio_movil_simple"]); // Obtener el valor de "n" desde el formulario
        $t1 = floatval($_POST["t1_promedio_movil_ponderado"]);
        $t2 = floatval($_POST["t2_promedio_movil_ponderado"]);
        $t3 = floatval($_POST["t3_promedio_movil_ponderado"]);
        // Validar que haya suficientes datos para el cálculo
        if (count($demandas) < $n + 1) {
            echo "<p>Error: No hay suficientes datos para el cálculo.</p>";
        } else {
            for ($i = 0; $i < count($demandas); $i++) {
                $periodo = $i + 1;
                $demanda = $demandas[$i];
                if ($n != null) {
                    $pronostico = ($i >= $n) ? calcularPromedioMovilSimple($demandas, $i, $n) : "";
                    echo "<tr><td>$periodo</td><td>$demanda</td><td>$pronostico</td></tr>";
                } else if ($t1 != null) {
                    $pronostico = ($i >= $n) ? calcularPromedioMovilPonderado($demandas, $i, $t1, $t2, $t3) : "";
                    echo "<tr><td>$periodo</td><td>$demanda</td><td>$pronostico</td></tr>";
                }
                // Calcula el promedio móvil simple para los períodos a partir del "n+1"

            }
        }
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
            // for ($i = 1; $i <= $periodos; $i++) {
            //     $resultado = $a + ($b * $i);
            //     echo "Resultado para el período $i: $resultado<br>";
            // }
        }
    }

    function calcularPromedioMovilSimple($demandas, $indice, $n)
    {
        // Asegura que el índice no sea negativo
        $indice = max($indice, 0);

        // Calcula el promedio de las últimas "n+1" demandas sin contar la última
        $suma = 0;
        for ($i = $indice - $n; $i < $indice; $i++) {
            $suma += $demandas[$i];
        }

        return $suma / $n; // Calcula el promedio de las últimas "n+1" demandas
    }
    function calcularPromedioMovilPonderado($demandas, $indice, $t1, $t2, $t3)
    {
        // Asegura que el índice no sea negativo
        $indice = max($indice, 0);

        if ($indice < 3) {
            return ""; // No se pueden calcular pronósticos para los primeros 3 periodos
        }

        // Calcula el promedio ponderado de los últimos 3 periodos
        $pronostico = ($demandas[$indice - 1] * $t1 + $demandas[$indice - 2] * $t2 + $demandas[$indice - 3] * $t3);

        return $pronostico;
    }

    ?>
    <table>
        <tr>
            <th>Periodo</th>

            <th>Demanda</th>
            <th>Promedio Móvil Simple</th>
            <th>Regresion Lineal</th>
            <td> <?php for ($i = 1; $i <= $periodos; $i++) {
                        $resultado = round($a + ($b * $i));
                        echo "$resultado<br>";
                    } ?>
            </td>
        </tr>

    </table>
</body>

</html>
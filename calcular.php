<!DOCTYPE html>
<html>
<head>
    <title>Resultado del Pronóstico</title>
</head>
<body>
    <h1>Resultado del Pronóstico</h1>
    <table>
        <tr>
            <th>Periodo</th>
            <th>Demanda</th>
            <th>Pronostico</th>
        </tr>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $periodos=$_POST["periodos"];
        $demandas = $_POST["demanda"];
        $metodo = $_POST["metodo"]; // Obtener el método seleccionado desde el formulario

        $n = intval($_POST["n_promedio_movil_simple"]);
        $t1 = floatval($_POST["t1_promedio_movil_ponderado"]);
        $t2 = floatval($_POST["t2_promedio_movil_ponderado"]);
        $t3 = floatval($_POST["t3_promedio_movil_ponderado"]);

        if (count($demandas) < max($n, 3)) {
            echo "<p>Error: No hay suficientes datos para el cálculo.</p>";
        } else {
            for ($i = 0; $i < count($demandas); $i++) {
                $periodo = $i + 1;
                $demanda = $demandas[$i];

                // Calcular el pronóstico en función del método seleccionado
                $pronostico = calcularPronostico($demandas, $i, $metodo, $t1, $t2, $t3, $n);

                echo "<tr><td>$periodo</td><td>$demanda</td><td>$pronostico</td></tr>";
            }
        }
    }
    function calcularPronostico($demandas, $indice, $metodo, $t1, $t2, $t3, $n) {
        $indice = max($indice, 0);

        if ($metodo === "promedio_movil_simple") {
            if ($indice < $n) {
                return ""; // No se pueden calcular pronósticos para los primeros $n periodos
            }
            $indice = max($indice, 0);
    
            // Calcula el promedio de las últimas "n+1" demandas sin contar la última
            $suma = 0;
            for ($i = $indice - $n; $i < $indice; $i++) {
                $suma += $demandas[$i];
                echo $demandas[$i];
            }
        
            return $suma / $n; // Calcula el promedio de las últimas "n+1" demandas
        } elseif ($metodo === "promedio_movil_ponderado") {
            if ($indice < 3) {
                return ""; // No se pueden calcular pronósticos para los primeros 3 periodos
            }
        
            $indice = max($indice, 0);
    
            // Calcula el promedio de las últimas "n+1" demandas sin contar la última
            $pron = null;
            for ($i = $indice - 2; $i < $indice; $i++) {
                
                    for ($j = 1; $j <= 3; $j++) {
                        $pron += $demandas[$i]*${"t$j"};
                        echo $demandas[$i];

                    }
                
            }
        
            return $pron; // Calcula el promedio de las últimas "n+1" demandas
        }
        
        return ""; // Método no reconocido
        
    }

    
    ?>
    </table>
</body>
</html>

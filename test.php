<?php
date_default_timezone_set('America/Santiago'); // Ajusta a la zona horaria de Santiago de Chile

function getDateTimeRange($dayOverride = 1) {
    $currentDay = $dayOverride ?? date('N'); // 1 (para lunes) hasta 7 (para domingo)
    $currentTime = time();

    if ($currentDay == 1) { // Es lunes
        $startTime = strtotime('last Friday 21:00');
        $endTime = strtotime('today 06:00');
    } else { // De martes a viernes
        $startTime = strtotime('yesterday 21:00');
        $endTime = strtotime('today 06:00');
    }

    return [
        'start' => date('Y-m-d H:i:s', $startTime),
        'end' => date('Y-m-d H:i:s', $endTime)
    ];
}

// Para probar, establece manualmente el día (1 para lunes, 2 para martes, etc.)
// Pasa null para usar el día actual automáticamente
$dayOverride = null; // Cambia a 1, 2, 3, etc. para probar diferentes días
$timeRange = getDateTimeRange($dayOverride);

echo "Fecha de inicio: " . $timeRange['start'] . "\n";
echo "Fecha de término: " . $timeRange['end'] . "\n";
?>

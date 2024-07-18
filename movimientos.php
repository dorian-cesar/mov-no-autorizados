<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de tener la librería PHPMailer instalada


date_default_timezone_set('America/Santiago'); // Ajusta a la zona horaria de Santiago de Chile

include "conexion.php";

function token()
{

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/user/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{"login":"controlveh@masgps.com","password":"Control_2024","dealer_id":10004282,"locale":"es","hash":null}',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json, text/plain, */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Cookie: _ga=GA1.2.728367267.1665672802; _gid=GA1.2.343013326.1670594107; locale=es; _gat=1',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Mobile Safari/537.36'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    //echo $response;

    $jsonHash = json_decode($response);

    return $jsonHash->hash;

    return $jsonHash['hash'];
};

$hash = token();

if (!$hash) {
    sendEmail();
    exit('Token function did not return a value.');
}


function sendEmail() {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Cambia esto por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'mailer.wit@gmail.com'; // Cambia esto por tu correo electrónico
    $mail->Password = 'qzyuwykitiekjsku'; // Cambia esto por tu contraseña
    $mail->SMTPSecure = 'tls'; // O 'ssl' si es necesario
    $mail->Port = 587; // Puerto SMTP

    $mail->setFrom('desarrollo.wit@gmail.com', 'Desarrollo Wit');
   
    $mail->addAddress('dorian.celu@gmail.com', 'Dorian G');
    $mail->addAddress('itorres@wit.la', 'Israel T.');
   // $mail->setFrom('your-email@example.com', 'Mailer');
   // $mail->addAddress('recipient@example.com', 'Recipient'); // Añadir destinatario

    $mail->isHTML(true);
    $mail->Subject = 'Movimientos-No-Autorizados';
    $mail->Body    = 'No se logro obtener  Token (hash)';

    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}



include "list.php";



$currentDay = date('N'); // 1 (para lunes) hasta 7 (para domingo)


if ($currentDay >= 1 && $currentDay <= 5) {

    function getDateTimeRange()
    {
        $currentDay = date('N'); // 1 (para lunes) hasta 7 (para domingo)
        // $currentDay = date('N'); // 1 (para lunes) hasta 7 (para domingo)
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

    $timeRange = getDateTimeRange();
    echo "Fecha de inicio: " . $timeRange['start'] . "\n";
    echo "Fecha de término: " . $timeRange['end'] . "\n";

    $from = $timeRange['start'];
    $to = $timeRange['end'];

    //$hash = 'ad39ddd21a18bd863a51a47c814d3f17';

    $hash = token();

    $fromUrl = urlencode($from);

    $toUrl = urlencode($to);
    // $trackers = urlencode("[10180690,10182419,10182728,10188754,10189478,10191084,10192115,10196194,10196198,10196241,10196252,10196259,10196465,10196550,10202082,10204598,10204620,10205706,10208019,10208020,10208021,10208345,10209395,10209398,10209407,10209411,10209418,10209428,10209431,10209877,10210547,10212544,10212549,10212550,10212630,10213010,10213516,10213527,10213540,10214696,10214697,10214700,10214701,10214704,10215925,10218214,10218463,10218469,10219406,10222354,10222356,10222361,10222383,10223941,10224569,10225580,10225853,10225870,10235806,10235832,10245566,10245575,10245627,10245641,10245689,10245929,10245930,10245946,10247485,10247868,10252677,10254109]");
    $trackers = $idss;
    $title = urlencode('Informe de viaje');

    $time_filter = urlencode('{"from":"00:00:00","to":"23:59:59","weekdays":[1,2,3,4,5,6,7]}');
    $plugin = urlencode('{"hide_empty_tabs":true,"plugin_id":4,"show_seconds":true,"include_summary_sheet":true,"include_summary_sheet_only":false,"split":true,"show_idle_duration":true,"show_coordinates":true,"filter":true,"group_by_driver":false}');

    $cadena = 'hash=' . $hash . '&title=' . $title . '&trackers=' . $trackers . '&from=' . $fromUrl . '&to=' . $toUrl . '&time_filter=' . $time_filter . '&plugin=' . $plugin;

    //goto Reporte;

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/generate',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $cadena,
        CURLOPT_HTTPHEADER => array(
            'Accept: */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Cookie: _ga=GA1.2.252064098.1712071785; _gid=GA1.2.2133610961.1718040990; locale=es; session_key=eb222bd854326e0e6ad9d50124b05c6b; _ga_XXFQ02HEZ2=GS1.2.1718140122.21.1.1718140646.0.0.0',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/pro/applications/reports/index.html?newuiwrap=1',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Mobile Safari/537.36'
        ),
    ));
    echo
    $informe = curl_exec($curl);


    curl_close($curl);


    $json = json_decode($informe);

    $reporte = $json->id;

    Reporte:
    //$reporte = 1951325;

    //patente=$.report.sheets.[3].header;
    //dia=$.report.sheets.[3].sections[0].data[0].header
    //$.report.sheets.[2].sections[0].data[0].rows[]
    // to_lat=$.report.sheets.[2].sections[0].data[0].rows[0].to_lat.v
    // to_lng=$.report.sheets.[2].sections[0].data[0].rows[0].to_lng.v
    // from_lat=$.report.sheets.[2].sections[0].data[0].rows[0].from_lat
    // from_lng=$.report.sheets.[2].sections[0].data[0].rows[0].from_lng

    // to_address=$.report.sheets.[2].sections[0].data[0].rows[0].to_address.v
    // to_coord=$.report.sheets.[2].sections[0].data[0].rows[0].to_address.location

    // from_address=$.report.sheets.[2].sections[0].data[0].rows[0].from_address.v
    // from_coord=$.report.sheets.[2].sections[0].data[0].rows[0].from_address.location

    //velcidad promedio=$.report.sheets.[2].sections[0].data[0].rows[0].avg_speed.v
    //velcocidad_max=$.report.sheets.[2].sections[0].data[0].rows[0].max_speed.v

    //distancia=$.report.sheets.[2].sections[0].data[0].rows[0].length.v
    //duraccion=$.report.sheets.[2].sections[0].data[0].rows[0].time.v

    sleep(10);

    $curl2 = curl_init();

    curl_setopt_array($curl2, array(
        CURLOPT_URL => 'http://www.trackermasgps.com/api-v2/report/tracker/retrieve',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'hash=' . $hash . '&report_id=' . $reporte . '',
        CURLOPT_HTTPHEADER => array(
            'Accept: */*',
            'Accept-Language: es-419,es;q=0.9,en;q=0.8',
            'Connection: keep-alive',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Cookie: _ga=GA1.2.728367267.1665672802; _gid=GA1.2.183718605.1679328823; locale=es; session_key=cf290712c61924284913e1af01cfaded; check_audit=cf290712c61924284913e1af01cfaded; date_format=m-d-Y; date_format_moment=MM-DD-YYYY',
            'Origin: http://www.trackermasgps.com',
            'Referer: http://www.trackermasgps.com/pro/applications/reports/index.html?newuiwrap=1',
            'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36'
        ),
    ));


    $response2 = curl_exec($curl2);

    curl_close($curl2);

    $json2 = json_decode($response2);




    foreach ($json2->report->sheets as $bus) {


        $patente = $bus->header;
        echo "<br>";
        if ($patente != 'Período Resumen') {
            echo
            $patente = $bus->header;
            $dia = $bus->sections[0]->data[0]->header;

            $fechaString = substr($dia, 0, 10);

            // Convertir la fecha al formato Y-m-d
            $fechaDateTime = DateTime::createFromFormat('d/m/Y', $fechaString);
            $fecha = $fechaDateTime->format('Y-m-d');

            foreach ($bus->sections[0]->data[0]->rows as $filas) {

                if (($filas->from_address->v) != "") {

                    $from_address = $filas->from_address->v;
                    $from_address = str_replace("'", " ", $from_address);
                    $to_address = $filas->to_address->v;
                    $to_address = str_replace("'", " ", $to_address);
                    $avg_speed = $filas->avg_speed->v;
                    $max_speed = $filas->max_speed->v;
                    $distancia = $filas->length->v;
                    $duracion = $filas->time->v;
                    $coordenadas = $filas->to_address->location;
                    $lat = $coordenadas->lat;
                    $lng = $coordenadas->lng;

                    echo
                    $qry = "INSERT INTO `masgps`.`mov_no_aut` (`patente`, `from_address`, `to_address`, `avg_speed`, `max_speed`, `distancia`, `duracion`,`lat`,`lng`,`dia`)
             VALUES ('$patente', '$from_address', '$to_address', $avg_speed, $max_speed, $distancia, '$duracion', '$lat', '$lng','$fecha')";

                    echo "<br>";

                    $resutaldo = mysqli_query($mysqli, $qry);
                }
            }
        }
    }
} else {
    echo "hoy es Sabado o Domingo . No se ejecuta el procedimiento";
}

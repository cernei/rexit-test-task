<?php

function csvGenerator($filePath, $delimiter = ','): Generator
{
    $file = fopen($filePath, 'r');
    if ($file) {
        while (($data = fgetcsv($file, 1000, $delimiter)) !== false) {
            yield $data;
        }
        fclose($file);
    } else {
        echo "Error opening the file.";
    }
}

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');

    return json_encode($data);
}
function getDateByYearsPastFromNow($years) {
    $currentDate = new DateTime();
    $birthDate = $currentDate->sub(new DateInterval('P' . $years . 'Y'));

    return $birthDate->format('Y-m-d');
}
function outputCsv($name, $data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $name . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $fp = fopen('php://output', 'w');

    foreach ($data as $row) {
        fputcsv($fp, $row);
    }
    fclose($fp);

    return true;
}
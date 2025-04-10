<?php

namespace App\Services;

class CsvExportService {
    public static function generateCsv(array $header,array $rows){
        $temps = [];
        array_push($temps, $header);
        foreach ($rows as $row){
            array_push($temps, $row);
        }
        $stream = fopen('php://temp', 'r+b');

        foreach ($temps as $temp){
            fputcsv($stream, $temp);
        }
        rewind($stream);
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        return mb_convert_encoding($csv,'SJIS-win', 'UTF-8');

    }
}

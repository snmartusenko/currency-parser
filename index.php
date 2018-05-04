<?php
//This is aPHP script example on how SOURCE_URL can be parsed
//option allow_url_fopen=On (default)

const SOURCE_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist-90d.xml?72837b35f8ca90ddb9015cbd378b93d5';
const SEPARATOR = "\t"; //tab CHR(09)
const CURRENCY = 'USD';
const YEAR = 2018;
const MONTH = 3;  //(integer) number of month

//start and end date for parsing (unix time)
$begin = strtotime('01-' . MONTH . '-' . YEAR);
$end = strtotime("+1 month", $begin) - 1;

echo "current_time = ", date('Y-m-d H:i:s'), "</br>";
echo 'start_date = ' . date('Y-m-d H:i:s', $begin), "</br>";
echo 'end_date = ' . date('Y-m-d H:i:s', $end), "</br>";
echo "</br>";


//get XML data
$XML = simplexml_load_file(SOURCE_URL);
$days = $XML->Cube->Cube;

if (!$days || !$len = $days->count()) {
    throw new Exception('Cannot get XML data');
}

//create output file
$fileName = 'output_EUR_' . CURRENCY . '_' . date('M_Y', $begin) . '.csv';
$file = fopen($fileName, 'w+');


//parse and write file
for ($i = $len - 1; $i >= 0; $i--) {

    $day = $days[$i];
    $unixDay = strtotime($day['time']);


    //find all records for set month
    if ($unixDay >= $begin && $unixDay <= $end) {

        //find record with set currency
        foreach ($day as $row) {

            if ($row['currency'] == CURRENCY) {

                $dateString = date('d.m.Y', strtotime($day['time']));
                $record = $dateString . SEPARATOR . $row['currency'] . SEPARATOR . $row['rate'] . PHP_EOL;

                echo $record, "</br>";
                //write record to file
                file_put_contents($fileName, $record, FILE_APPEND);
            }
        }
    }
}

fclose($file);

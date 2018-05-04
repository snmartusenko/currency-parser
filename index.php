<?php

const SOURCE_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-hist-90d.xml?72837b35f8ca90ddb9015cbd378b93d5';
const CURRENCY = 'USD';
const YEAR = 2018;
const MONTH = 3;

$begin = strtotime('01-' . MONTH . '-' . YEAR);
$end = strtotime("+1 month", $begin) - 1;

echo "current_time = ", date('Y-m-d H:i:s'), "</br>";
echo 'start_date = ' . date('Y-m-d H:i:s', $begin), "</br>";
echo 'end_date = ' . date('Y-m-d H:i:s', $end), "</br>";


//This is aPHP script example on how SOURCE_URL can be parsed
//option allow_url_fopen=On (default)
$XML = simplexml_load_file(SOURCE_URL);

$days = $XML->Cube->Cube;
$len = $days->count();

$fileName = 'output_EUR_'.CURRENCY.'_'.date('M_Y', $begin).'.txt';
$file = fopen($fileName, 'w+');

for ($i = $len - 1; $i >= 0; $i--) {

    $day = $days[$i];
    $unixDay = strtotime($day['time']);

    //find the records in set month
    if ($unixDay >= $begin && $unixDay <= $end) {

//        echo $day['time'], "</br>";
        foreach ($day as $row) {

            if ($row['currency'] == CURRENCY) {
//                var_dump($row);

                $dateString = date('d.m.Y', strtotime($day['time']));
                $record = $dateString ."\t" .$row['currency'] ."\t" .$row['rate'] . PHP_EOL;

                echo $record, "</br>";
                file_put_contents($fileName, $record, FILE_APPEND);
            }
        }
    }
}

fclose($file);

/*
function StartElement($parser, $name, $attrs)
{
    if (!empty($attrs['RATE'])) {
        echo "1&euro;=" . $attrs['RATE'] . " " . $attrs['CURRENCY'] . "<br />";
    }
}

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "StartElement", "");
// for the following command you will need file_get_contents (PHP >= 4.3.0)
// and the config option allow_url_fopen=On (default)
$content = file_get_contents("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
xml_parse($xml_parser, $content);
xml_parser_free($xml_parser);
*/


/*
//This is a PHP(4/5) script example on how eurofxref-daily.xml can be parsed
//Read eurofxref-daily.xml file in memory
//For this command you will need the config
//option allow_url_fopen=On (default)
$XMLContent=file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
//the file is updated at around 16:00 CET

foreach($XMLContent as $line){
    if(preg_match("/currency='([[:alpha:]]+)'/",$line,$currencyCode)){
        if(preg_match("/rate='([[:graph:]]+)'/",$line,$rate)){
            //Output the value of 1EUR for a currency code
            echo'1&euro;='.$rate[1].' '.$currencyCode[1].'<br/>';
            //--------------------------------------------------
            //Here you can add your code for inserting
            //$rate[1] and $currencyCode[1] into your database
            //--------------------------------------------------
        }
    }
}
*/

<?

$file = fopen('log.txt', 'a');
foreach ($_REQUEST as $key => $val)
{
    fwrite($file, $key . ' => ' . $val . "\n");
}
fclose($file);
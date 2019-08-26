<?php
 date_default_timezone_set("Asia/Taipei");

 $host = 'localhost'; // <--  db address
 $user = 'root'; // <-- db user name
 $pass = 'a92015a92015'; // <-- password
 $db = 'webcam'; // db's name
 $table = 'snapshot'; // table you want to export
 $file = 'snapshot'; // csv name.

$link = mysql_connect($host, $user, $pass) or die("Can not connect." . mysql_error());
mysql_select_db($db) or die("Can not connect.");

$result = mysql_query("SHOW COLUMNS FROM ".$table."");
$i = 0;

if (mysql_num_rows($result) > 0) {
 while ($row = mysql_fetch_assoc($result)) {
  $csv_output .= "`".$row['Field']."`;";
  $i++;
 }
}
$csv_output =substr($csv_output,0,strlen($csv_output)-1);
$csv_output .= "\n";

$values = mysql_query("SELECT * FROM ".$table."");

while ($rowr = mysql_fetch_row($values)) {
 for ($j=0;$j<$i;$j++) {
  $csv_output .= "`".$rowr[$j]."`;";
 }
 $csv_output =substr($csv_output,0,strlen($csv_output)-1);
 $csv_output .= "\n";
}

$filename = $file;

header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header( "Content-disposition: filename=".$filename.".csv");

print $csv_output;

exit;
?>

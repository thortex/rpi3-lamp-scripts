<? 

print("{");

$dbhandle = new mysqli('localhost', 'webiopi', 'raspberry');
if ($dbhandle->connect_errno) {
   print('<p>Error 100: Databse connection error.');
   exit();
}

$dbhandle->select_db('i2c_env_sensors');
$query = "SELECT * FROM basics";
$result = $dbhandle->query($query);

if (!$result) {
   print('<p>Error 200: Database query error.');
   $dbhandle->close();
   exit();
}

while ($row = $result->fetch_assoc()) {
      print(
	     "{date:\"" . $row['date'] . 
      	     "\",temp:\"" . $row['temp'] . 
	     "\",pres:\"" . $row['pres'] . 
	     "\",humi:\"" . $row['humi'] . 
	     "\",lumi:\"" . $row['lumi'] . 
	     "\",cput:\"" . $row['cput'] . 
	     "\"}") ;
}

$result->free();
$dbhandle->close();

print("}");

?>


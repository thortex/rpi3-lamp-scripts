<? 

header('Content-Type: application/json; charset=utf-8');

$dbhandle = new mysqli('localhost', 'webiopi', 'raspberry');
if ($dbhandle->connect_errno) {
   print('<p>Error 100: Databse connection error.');
   exit();
}

$dbhandle->select_db('i2c_env_sensors');
$query = "SELECT * FROM basics";

if (($_GET['period'] != '' ) || ($_GET['start'] != '') || ($_GET['end'] != '')) {
   $where = "";
//   print("<p>period param:" . $_GET['period'] . "\n");
//   print("<p>start param:" . $_GET['start'] . "\n");
//   print("<p>end param:" . $_GET['end'] . "\n");   
     $freq = 60; // if 1 sampling per min
     $freq = 1;  // if 1 sampling per hour
     switch($_GET['period']) {
          case 'hourly':
	       $where = " (`id` %  $freq) = 0 ";
	       break;
          case 'daily':
	       $where = " (`id` % ($freq*24)) = 0 ";
	       break;
          case 'weekly':
	       $where = " (`id` % ($freq*24*7)) = 0 ";
	       break;
          case 'monthly':
	       $where = " (`id` % ($freq*24*30)) = 0 ";
	       break;
          case 'yearly':
	       $where = " (`id` % ($freq*24*30*12)) = 0 ";
	       break;
     }

     if ( $_GET['start'] != '') {
     	if ($where != '') {
	   $where .= ' AND ';
	}
	$where .= " `date` > '" . $_GET['start'] . "' ";
     }
     
     if ( $_GET['end'] != '') {
     	if ($where != '') {
	   $where .= ' AND ';
	}
	$where .= " `date` < '" . $_GET['end'] . " 23:59:59' ";
     }

     if ($where != '') {
     	$query .= " WHERE " . $where ;
     }
}

//print("query: " . $query) ;

$query .= " LIMIT 0, 100000";

$result = $dbhandle->query($query);

if (!$result) {
   print('<p>Error 200: Database query error.');
   $dbhandle->close();
   exit();
}

$line_cnt = 0;
print("[");
while ($row = $result->fetch_assoc()) {
      if ($line_cnt != 0) { print(','); }
      print(
	      '{"d":"' . $row['date'] . 
      	     '","t":' . $row['temp'] . 
	     ',"p":' . $row['pres'] . 
	     ',"h":' . $row['humi'] . 
	     ',"l":' . $row['lumi'] . 
	     ',"c":' . $row['cput'] . 
	     '}') ;
      $line_cnt++;
//      if ($line_cnt > 200) { break ; }
}
print("]");

$result->free();
$dbhandle->close();

?>


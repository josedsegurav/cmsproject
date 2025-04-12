<?php
    //  define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
    //  define('DB_USER','serveruser');
    //  define('DB_PASS','gorgonzola7!');      
     
    //  try {
    //      $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    //  } catch (PDOException $e) {
    //      print "Error: " . $e->getMessage();
    //      die();
    //  }
     
     // PHP Data Objects(PDO) Sample Code:
try {
    $db = new PDO("sqlsrv:server = tcp:cmsproject.database.windows.net,1433; Database = serverside", "serveruser", "gorgonzola7!");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

?>


<?php
     define('DB_DSN','mysql:host=localhost;dbname=server_project;charset=utf8');
     define('DB_USER','serveradmin');
     define('DB_PASS','admin1015!');     
     
     try {
         $db = new PDO(DB_DSN, DB_USER, DB_PASS);
     } catch (PDOException $e) {
         print "Error: " . $e->getMessage();
         die();
     }
 ?>
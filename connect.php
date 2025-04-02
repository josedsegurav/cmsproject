<?php
     define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
     define('DB_USER','serveruser');
     define('DB_PASS','gorgonzola7!');      
     
    //  try {
    //      $db = new PDO(DB_DSN, DB_USER, DB_PASS);
    //  } catch (PDOException $e) {
    //      print "Error: " . $e->getMessage();
    //      die();
    //  }
    $url = getenv('JAWSDB_MARIA_URL');
    $dbparts = parse_url($url);

    $hostname = $dbparts['ui0tj7jn8pyv9lp6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com'];
    $username = $dbparts['zoapdlyutui4hvsi'];
    $password = $dbparts['rrny49vc3cbjia2g'];
    $database = ltrim($dbparts['path'],'/');

    try {
        $conn = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
        }
    catch(PDOException $e)
        {
        echo "Connection failed: " . $e->getMessage();
        }
 ?>
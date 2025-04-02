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
    $url = getenv('mysql://zoapdlyutui4hvsi:rrny49vc3cbjia2g@ui0tj7jn8pyv9lp6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/m4e70yz4l1bqa3ks');
    $dbparts = parse_url($url);

    $hostname = $dbparts['host'];
    $username = $dbparts['user'];
    $password = $dbparts['pass'];
    $database = ltrim($dbparts['path'],'/');

    try {
        $db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully";
        }
    catch(PDOException $e)
        {
        echo "Connection failed: " . $e->getMessage();
        }
 ?>
<?php
session_start();

require('connect.php');

$title = "Browse Items";

// SQL query
$query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name 
        FROM items i JOIN categories c ON c.category_id = i.category_id";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$items = $statement->fetchAll();

if(!empty($_SESSION['message'])){
    $message = $_SESSION['message'];
    echo "<script>alert('{$message}')</script>";
    unset($_SESSION['message']);
}

// If statement to verify if there is id data coming from the GET action.
if(isset($_GET['id'])){
        // Sanitizing id data into a string.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name 
                    FROM items i 
                    JOIN categories c ON c.category_id = i.category_id 
                    WHERE i.category_id = :id
                    AND i.slug = :slug";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $categories = $statement->fetchAll();

        if (!$item) {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found or URL has been modified";
            exit;
        }

    // If the input form the GET is not an int, it is redirected to index.php.

}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <?php foreach ($items as $row): ?>
    <div>
        <div>
            <h2><a href="items/<?= $row['slug'] ?>"><?= $row['item_name'] ?></a></h2>
            <span>Created by <?= $row['author'] ?> on
                <?= date("F d, Y, g:i a", strtotime($row['date_created'])) ?></span>
        </div>
        <p>Category: <span><?= $row['category_name'] ?></span></p>

    </div>
    <?php endforeach ?>
    <script>
        var options = {
            closeOnScroll: true,
        };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    
    </script>
</body>

</html>
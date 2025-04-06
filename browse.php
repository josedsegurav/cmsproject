<?php
session_start();

require('connect.php');

$title = "Browse Items";

// SQL query
$query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname
        FROM items i 
        JOIN categories c ON c.category_id = i.category_id
        JOIN users u ON i.user_id = u.user_id";
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
if(isset($_GET['p'])){
        // Sanitizing id data into a string.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.user_id, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, c.category_slug, u.name, u.lastname  
        FROM items i 
        JOIN categories c ON c.category_id = i.category_id
        JOIN users u ON i.user_id = u.user_id 
        WHERE c.category_slug = :slug";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $browseCategories = $statement->fetchAll();

}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <?php if(!isset($_GET['p'])): ?>
    <?php foreach ($items as $item): ?>
        <?php include('listItemTemplate.php') ?>
    <?php endforeach ?>
    <?php elseif(isset($_GET['p']) && $browseCategories): ?>
        <?php foreach ($browseCategories as $item): ?>
            <?php include('listItemTemplate.php') ?>
    <?php endforeach ?>
    <?php elseif(isset($_GET['p']) && !$browseCategories): ?>
        <p>There are no items in that category.</p>
    <?php endif ?>
        <!-- Footer -->
        <?php include('footer.php'); ?>
    <script>
        var options = {
            closeOnScroll: true,
        };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    
    </script>
</body>

</html>
<?php
session_start();

require('connect.php');

$title = "Results";



if(isset($_GET['p'])){
        // Sanitizing id data into a string.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, c.category_slug  
        FROM items i 
        JOIN categories c ON c.category_id = i.category_id 
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
    <?php elseif(isset($_GET['p']) && $browseCategories): ?>
        <?php foreach ($browseCategories as $browseCategory): ?>
    <div>
        <div>
            <h2><a href="items/<?= $browseCategory['slug'] ?>"><?= $browseCategory['item_name'] ?></a></h2>
            <span>Created by <?= $browseCategory['author'] ?> on
                <?= date("F d, Y, g:i a", strtotime($browseCategory['date_created'])) ?></span>
        </div>
        <p>Category: <span><?= $browseCategory['category_name'] ?></span></p>

    </div>
    <?php endforeach ?>
    <?php elseif(isset($_GET['p']) && !$browseCategories): ?>
        <p>There are no items in that category.</p>
    <?php endif ?>
    <script>
        var options = {
            closeOnScroll: true,
        };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    
    </script>
</body>

</html>
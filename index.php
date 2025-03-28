<?php
session_start();

require('connect.php');

$title = "Home";

// SQL query
$query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, c.category_name FROM items i JOIN categories c ON c.category_id = i.category_id";
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

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <?php foreach ($items as $row): ?>
    <div>
        <div>
            <h2><a href="./"><?= $row['item_name'] ?></a></h2>
            <span>Created by <?= $row['author'] ?> on
                <?= date("F d, Y, g:i a", strtotime($row['date_created'])) ?></span>
        </div>
        <div class="image">
            <a href="./images/<?= $row['image'] ?>"><img src="./images/medium_<?= $row['image'] ?>"
                    alt="<?= $row['image'] ?>"></a>
        </div>
        <p>Description:</p>
        <span><?= $row['content'] ?></span>
        <p>Category: <span><?= $row['category_name'] ?></span></p>
        <a id="lbox" href="<?= $row['store_url'] ?>" target="_blank">Link of the store</a>

    </div>
    <?php endforeach ?>
    <script>
        var galleryOpts = {  arrowNavigation: false
        };
    new LuminousGallery(document.querySelectorAll(".image a"),galleryOpts);
    
    </script>
</body>

</html>
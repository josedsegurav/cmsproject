<?php
session_start();

require('connect.php');

$title = "Results";

function filterInput() {
    if (
        $_POST && 
        !empty($_POST['searchInput']) && 
        !(trim($_POST['searchInput']) == '')
        ){
        return true;
    }else{
        return false;
    }
}

if(filterInput()){
        // Sanitizing id data into a string.
        $search = filter_input(INPUT_POST, 'searchInput', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, c.category_slug  
        FROM items i 
        JOIN categories c ON c.category_id = i.category_id 
        WHERE i.item_name LIKE :search
        OR i.author LIKE :search";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $searchResults = $statement->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <?php if($searchResults): ?>
    <?php foreach ($searchResults as $result): ?>
    <div>
        <div>
            <h2><a href="items/<?= $result['slug'] ?>"><?= $result['item_name'] ?></a></h2>
            <span>Created by <?= $result['author'] ?> on
                <?= date("F d, Y, g:i a", strtotime($result['date_created'])) ?></span>
        </div>
        <p>Category: <span><?= $result['category_name'] ?></span></p>

    </div>
    <?php endforeach ?>
    <?php else: ?>
        <p>There are no items with that search.</p>
    <?php endif ?>
    <script>
        var options = {
            closeOnScroll: true,
        };

    new LuminousGallery(document.querySelectorAll(".image a"), options);
    
    </script>
</body>

</html>
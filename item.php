<?php
session_start();

require('connect.php');

$title = "Item - ";

if(isset($_GET['p'])){
    // If statemet to verify the input form the GET is an int.
        // Sanitizing id data into a number.
       // $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.user_id, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname
                    FROM items i 
                    JOIN categories c ON c.category_id = i.category_id
                    JOIN users u ON i.user_id = u.user_id 
                    WHERE i.slug = :slug";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $item = $statement->fetch();

        if (!$item) {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found or URL has been modified";
            exit;
        }else{
            $title .= " {$item['item_name']}";
        }

    // If the input form the GET is not an int, it is redirected to index.php.

}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <div>
        <div>
            <h2><a href="./"><?= $item['item_name'] ?></a></h2>
            <span>Created by <?= $item['name'] ?>  <?= $item['lastname'] ?> on
                <?= date("F d, Y, g:i a", strtotime($item['date_created'])) ?></span>
        </div>
        <div class="image">
            <a href="/webdev2/project/images/<?= $item['image'] ?>"><img
                    src="/webdev2/project/images/medium_<?= $item['image'] ?>" alt="<?= $item['image'] ?>"></a>
        </div>
        <p>Description:</p>
        <span><?= $item['content'] ?></span>
        <p>Category: <span><?= $item['category_name'] ?></span></p>
        <a id="lbox" href="<?= $item['store_url'] ?>" target="_blank">Link of the store</a>

    </div>
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
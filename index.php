<?php

require('connect.php');

// SQL query
$query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, c.category_name FROM items i JOIN categories c ON c.category_id = i.category_id";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$items = $statement->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
<?php foreach ($items as $row): ?>
                <div>
                    <div>
                    <h2><a href="./"><?= $row['item_name'] ?></a></h2>
                    <span>Created by <?= $row['author'] ?> on <?= date("F d, Y, g:i a", strtotime($row['date_created'])) ?></span>
                    <a href="edit.php?id=<?= $row['item_id'] ?>">edit item</a></p>
                    </div>
                    <img src="./images/medium_<?= $row['image'] ?>" />
                    <p>Description:</p>
                    <span><?= $row['content'] ?></span>
                    <p>Category: <span><?= $row['category_name'] ?></span></p>
                    <a href="<?= $row['store_url'] ?>" target="_blank">Link of the store</a>
                    
                </div>
            <?php endforeach ?>
</body>

</html>
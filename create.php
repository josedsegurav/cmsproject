<?php

    // Require authentication script to protect data manipulation from unauthorized users
    require 'authenticate.php';
    require('connect.php');

// SQL query
$query = "SELECT * FROM categories";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$categories = $statement->fetchAll();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>JS Blog - New Post</title>
</head>

<body>
    <div id="wrapper">
        <!-- Nav imported from php file  -->
        <h2>Add New Item</h2>
        <main>
            <form action="post.php" method="post">

                <label for="name">Item Name</label>
                <input id="name" type="text" name="name">
                <label for="author">Author Name</label>
                <input id="author" type="text" name="author">
                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file'>
                <input type='submit' name='submit' value='Upload Image'>
                <label for="content">Content</label>
                <textarea id="content" name="content"></textarea>
                <label for="category">Category</label>
                <select id="category" type="text" name="category">
                    <option>- Choose a Category -</option>
                    <?php foreach ($categories as $row): ?>
                    <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
                    <?php endforeach ?>
                </select>
                <label for="link">Link to buy it</label>
                <input id="link" type="text" name="link">
                <input type="submit" id="submit" name="create" value="Create Post">

            </form>
        </main>
    </div>
</body>

</html>
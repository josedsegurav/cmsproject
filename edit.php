<?php
require('connect.php');
require('authenticate.php');

session_start();

if(!empty($_SESSION['message'])){
    $message = $_SESSION['message'];
    echo "<script>alert('{$message}')</script>";
    unset($_SESSION['message']);
}

// SQL query
$category_query = "SELECT * FROM categories";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($category_query);
// Execution on the DB server.
$statement->execute();

$categories = $statement->fetchAll();

// If statement to verify if there is id data coming from the GET action.
if(isset($_GET['id'])){
    // If statemet to verify the input form the GET is an int.
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)){
        // Sanitizing id data into a number.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.author, i.content, i.category_id, i.store_url, i.image, i.date_created, c.category_name 
                    FROM items i 
                    JOIN categories c ON c.category_id = i.category_id 
                    WHERE i.item_id = :id";
        

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $row = $statement->fetch();

    // If the input form the GET is not an int, it is redirected to index.php.
    }else{
    header("Location: index.php");
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interiour Design Items - Update Item</title>
</head>

<body>
<nav>
    <h1><a href="../../">Interiour Design Items</a></h1>
    <ul>
        <li><a href="../../">Home</a></li>
        <li><a href="../../add">Add Item</a></li>
        <li><a href="../../items">Items List</a></li>
    </ul>
</nav>
    <div id="wrapper">
        <h2>Update Item</h2>
        <main>
            <form action="../process" enctype='multipart/form-data' method="post">
                <input type="hidden" id="id" name="id" value="<?= $row['item_id'] ?>">
                <label for="name">Item Name</label>
                <input id="name" type="text" name="name" value="<?= $row['item_name'] ?>" required>
                <label for="author">Author Name</label>
                <input id="author" type="text" name="author" value="<?= $row['author'] ?>">
                <img src="../../images/medium_<?= $row['image'] ?>" />
                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file'>
                <label for="content">Content</label>
                <textarea id="content" name="content" required><?= $row['content'] ?></textarea>
                <label for="category">Category</label>
                <select id="category" type="text" name="category" value="<?= $row['category_name'] ?>" required>
                    <?php foreach ($categories as $category): ?>
                    <option 
                    value="<?= $category['category_id'] ?>"
                    <?= ($category['category_id'] == $row['category_id']) ? 'selected' : '' ?>
                    >
                        <?= $category['category_name'] ?></option>
                    <?php endforeach ?>
                </select>
                <label for="link">Link to buy it</label>
                <input id="link" type="url" name="link" value="<?= $row['store_url'] ?>" required>
                <input type="submit" id="submit" name="update" value="Update Item">
                <input type="submit" id="delete" name="delete" value="Delete Item">
            </form>
        </main>
    </div>
</body>

</html>
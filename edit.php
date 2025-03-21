<?php
require('connect.php');
require('authenticate.php');

// If statement to verify if there is id data coming from the GET action.
if(isset($_GET['id'])){
    // If statemet to verify the input form the GET is an int.
    if(filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)){
        // Sanitizing id data into a number.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        // SQL query
        $query = "SELECT * FROM posts WHERE id = :id";

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
    <title>JS Blog - New Post</title>
</head>

<body>
    <div id="wrapper">
        <h2>Add New Item</h2>
        <main>
            <form action="process.php" enctype='multipart/form-data' method="post">
                <label for="name">Item Name</label>
                <input id="name" type="text" name="name" required>
                <label for="author">Author Name</label>
                <input id="author" type="text" name="author" required>
                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file' required>
                <label for="content">Content</label>
                <textarea id="content" name="content" required></textarea>
                <label for="category">Category</label>
                <select id="category" type="text" name="category" required>
                    <option>- Choose a Category -</option>
                    <?php foreach ($categories as $row): ?>
                    <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
                    <?php endforeach ?>
                </select>
                <label for="link">Link to buy it</label>
                <input id="link" type="url" name="link" required>
                <input type="submit" id="submit" name="create" value="Create Post">

            </form>
        </main>
    </div>
</body>

</html>
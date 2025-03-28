<?php
// Require authentication script to protect data manipulation from unauthorized users
require 'authenticate.php';
// Require database data
require('connect.php');
// If statement to verify a Session variable 'message' has a value, and send the content in a alert script.
session_start();
if(!empty($_SESSION['message'])){
    $message = $_SESSION['message'];
    echo "<script>alert('{$message}')</script>";
    unset($_SESSION['message']);
}
// Variable to add a name to the title in the html head tag
$title = "Update Item.";

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
    header("Location: /webdev2/project/");
    }
}

if(isset($_POST['createCategory'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO categories (category_name) 
    VALUES (:newCategory)";

    // A PDO::Statement is prepared from the query. 
    $statement = $db->prepare($query);
    // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
    $statement->bindValue(':newCategory', $newCategory, PDO::PARAM_STR);

    // Execution on the DB server.
    $statement->execute();

    header("Location: /webdev2/project/items/edit/{$id}");

}elseif (isset($_POST['cancelCreateCategory'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    // It is redirected to edit.php according to it's id data.
    header("Location: /webdev2/project/items/edit/{$id}");
}

?>

<!DOCTYPE html>
<html lang="en">
<!-- Include head tag from template -->
<?php include('htmlHead.php'); ?>

<body>
    <!-- Include nav tag from template -->
    <?php include('nav.php'); ?>
    <?php if(isset($_POST['addCategory'])): ?>
    <div>
        <!-- Delete confirmation form -->
        <form method="post">
            <fieldset>
                <label for="newCategory">New Category</label>
                <input type="hidden" id="id" name="id" value="<?= $id ?>">
                <input id="newCategory" type="text" name="newCategory">
                <input type="submit" id="createCategory" name="createCategory" value="Add Category">
                <input type="submit" id="cancelCreateCategory" name="cancelCreateCategory" value="Cancel">
            </fieldset>
        </form>
    </div>
    <?php else: ?>
    <div>
        <h2>Update Item</h2>
        <main>
            <form method="post">
                <input type="submit" id="addCategory" name="addCategory" value="Add Category">
            </form>
            <!-- Form sending the data to process.php -->
            <form action="/webdev2/project/items/process" enctype='multipart/form-data' method="post">
                <input type="hidden" id="id" name="id" value="<?= $row['item_id'] ?>">
                <label for="name">Item Name</label>
                <input id="name" type="text" name="name" value="<?= $row['item_name'] ?>" required>

                <label for="author">Author Name</label>
                <input id="author" type="text" name="author" value="<?= $row['author'] ?>">

                <img src="../../images/medium_<?= $row['image'] ?>" alt="<?= $row['image'] ?>">
                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file'>

                <label for="content">Content</label>
                <textarea id="content" name="content" rows="20" cols="50" required><?= $row['content'] ?></textarea>

                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="" disabled>- Choose a Category -</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category_id'] ?>"
                        <?= ($category['category_id'] == $row['category_id']) ? 'selected' : '' ?>>
                        <?= $category['category_name'] ?></option>
                    <?php endforeach ?>
                </select>

                <label for="newCategory">New Category</label>
                <input id="newCategory" type="text" name="newCategory">

                <label for="link">Link to buy it</label>
                <input id="link" type="text" name="link" value="<?= $row['store_url'] ?>" required>

                <input type="submit" id="submit" name="update" value="Update Item">
                <input type="submit" id="delete" name="delete" value="Delete Item">
            </form>
        </main>
    </div>
    <?php endif ?>
    <!-- Script to add the WYSIWYG editor. -->
    <script>
    var textarea = document.getElementById('content');
    sceditor.create(textarea, {
        format: 'bbcode',
        style: 'minified/themes/content/default.min.css',
        toolbarExclude: 'table,code,quote,horizontalrule,image,email,link,unlink,emoticon,youtube,date,time,ltr,rtl,print,maximize,source,font,size,color,removeformat,subscript,superscript'
    });
    </script>
</body>

</html>
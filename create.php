<?php

// Require authentication script to protect data manipulation from unauthorized users
 require 'authenticate.php';
 // Require database data
 require('connect.php');
// Variable to add a name to the title in the html head tag
$title = "Add Item";

// SQL query
$query = "SELECT * FROM categories";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();
$categories = $statement->fetchAll();

if(isset($_POST['createCategory'])){

    $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $query = "INSERT INTO categories (category_name) 
    VALUES (:newCategory)";

    // A PDO::Statement is prepared from the query. 
    $statement = $db->prepare($query);
    // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
    $statement->bindValue(':newCategory', $newCategory, PDO::PARAM_STR);

    // Execution on the DB server.
    $statement->execute();

    header("Location: /webdev2/project/add");

}elseif (isset($_POST['cancelCreateCategory'])) {
    // It is redirected to edit.php according to it's id data.
    header("Location: /webdev2/project/add");
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
                <input id="newCategory" type="text" name="newCategory">
                <input type="submit" id="createCategory" name="createCategory" value="Add Category">
                <input type="submit" id="cancelCreateCategory" name="cancelCreateCategory" value="Cancel">
            </fieldset>
        </form>
    </div>
    <?php else: ?>
    <div>
        <h2>Add New Item</h2>
        <main>
            <form method="post">
                <input type="submit" id="addCategory" name="addCategory" value="Add Category">
            </form>

            <!-- Form sending the data to process.php -->
            <form action="/webdev2/project/items/process" enctype='multipart/form-data' method="post">
                <label for="name">Item Name</label>
                <input id="name" type="text" name="name">

                <label for="author">Author Name</label>
                <input id="author" type="text" name="author">

                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file'>

                <label for="content">Content</label>
                <textarea id="content" name="content" rows="20" cols="50"></textarea>

                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="" disabled selected>- Choose a Category -</option>
                    <?php foreach ($categories as $row): ?>
                    <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
                    <?php endforeach ?>
                </select>

                <label for="link">Link to buy it</label>
                <input id="link" type="text" name="link">

                <input type="submit" id="submit" name="create" value="Create Item">

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
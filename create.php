<?php

    // Require authentication script to protect data manipulation from unauthorized users
    require 'authenticate.php';
    require('connect.php');

$title = "Add Item";

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

<?php include('htmlHead.php'); ?>

<body>
    <?php include('nav.php'); ?>
    <div id="wrapper">
        <h2>Add New Item</h2>
        <main>
            <form action="/webdev2/project/items/process" enctype='multipart/form-data' method="post">
                <label for="name">Item Name</label>
                <input id="name" type="text" name="name" required>
                <label for="author">Author Name</label>
                <input id="author" type="text" name="author" required>
                <label for='file'>Image File:</label>
                <input type='file' name='file' id='file' required>
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="20" cols="50" required></textarea>
                <label for="category">Category</label>
                <select id="category" name="category" required>
                    <option value="">- Choose a Category -</option>
                    <?php foreach ($categories as $row): ?>
                    <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
                    <?php endforeach ?>
                </select>
                <label for="link">Link to buy it</label>
                <input id="link" type="url" name="link" required>
                <input type="submit" id="submit" name="create" value="Create Item">

            </form>
        </main>
    </div>
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
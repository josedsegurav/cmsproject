<?php
session_start();

if(!empty($_SESSION['user'])){
    $user = $_SESSION['user'];
}else{
    header("Location: login");
}



// Require authentication script to protect data manipulation from unauthorized users
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

?>
<!DOCTYPE html>
<html lang="en">
<!-- Include head tag from template -->
<?php include('htmlHead.php'); ?>
<body>
    <!-- Include nav tag from template -->
    <?php include('nav.php'); ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="shadow-sm">
                    <div class="bg-white p-4">
                        <h2 class="mb-4">Add New <span class="text-warning">Item</span></h2>

                        <main>
                            <!-- Form sending the data to process.php -->
                            <form action="items/process" enctype='multipart/form-data' method="post">
                                <input type="hidden" id="userId" name="userId" value="<?= $user['user_id'] ?>">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Item Name</label>
                                    <input id="name" type="text" name="name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">Image File</label>
                                    <input type="file" name="file" id="file" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea id="content" name="content" rows="10" class="form-control"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <div class="input-group">
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="" disabled selected>- Choose a Category -</option>
                                            <?php foreach ($categories as $row): ?>
                                            <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?>
                                            </option>
                                            <?php endforeach ?>
                                        </select>
                                        <a class="btn btn-warning">Add New</a>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label">Link to buy it</label>
                                    <input id="link" type="text" name="link" class="form-control">
                                </div>

                                <div class="mb-4">
                                    <div class="border-light bg-light">

                                        <p class="small text-muted mb-3">
                                            You can specify your own link address. If you don't want to, you can
                                            leave the box unchecked and
                                            the link will default to the item's name.
                                        </p>

                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input name="slugCheck" id="slugCheck" type="checkbox"
                                                    class="form-check-input me-2">
                                                <label for="slugCheck" class="form-check-label">Permalink</label>
                                            </div>
                                            <input name="slug" id="slug" type="text" class="form-control">
                                        </div>

                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" id="submit" name="create" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Create Item
                                    </button>
                                </div>
                            </form>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <?php include('footer.php'); ?>
    <!-- Script to add the WYSIWYG editor. -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('content');
        sceditor.create(textarea, {
            format: 'bbcode',
            style: 'minified/themes/content/default.min.css',
            toolbarExclude: 'table,code,quote,horizontalrule,image,email,link,unlink,emoticon,youtube,date,time,ltr,rtl,print,maximize,source,font,size,color,removeformat,subscript,superscript'
        });
    });
    </script>
</body>

</html>
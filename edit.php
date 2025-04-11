<?php
session_start();

// Require database data
require('connect.php');
// If statement to verify a Session variable 'message' has a value, and send the content in a alert script.

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
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.user_id, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname 
                    FROM items i 
                    JOIN categories c ON c.category_id = i.category_id
                    JOIN users u ON i.user_id = u.user_id 
                    WHERE i.item_id = :id
                    AND i.slug = :slug";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $item = $statement->fetch();

        if (!$item) {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found or URL has been modified";
            exit;
        }

    // If the input form the GET is not an int, it is redirected to index.php.
    }else{
    header("Location: ");
    }
}

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
                        <h2 class="mb-4">Update <span class="text-warning">Item</span></h2>

                        <main>
                            <!-- Form sending the data to process.php -->
                            <form action="items/process" enctype='multipart/form-data' method="post">
                                <input type="hidden" id="id" name="id" value="<?= $item['item_id'] ?>">
                                <input type="hidden" id="userId" name="userId" value="<?= $item['user_id'] ?>">

                                <div class="mb-3">
                                    <label for="name" class="form-label">Item Name</label>
                                    <input id="name" type="text" name="name" value="<?= $item['item_name'] ?>"
                                        class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <?php if(!empty($item['image'])): ?>
                                    <img src="images/medium_<?= $item['image'] ?>"
                                        alt="<?= $item['image'] ?>" class="img-thumbnail mb-2">
                                    <?php else: ?>
                                    <?php endif ?>
                                    <label for="file" class="form-label">Image File</label>
                                    <input type="file" name="file" id="file" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea id="content" name="content" rows="10" class="form-control"
                                        required><?= $item['content'] ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <div class="input-group">
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="" disabled>- Choose a Category -</option>
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['category_id'] ?>"
                                                <?= ($category['category_id'] == $item['category_id']) ? 'selected' : '' ?>>
                                                <?= $category['category_name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <a href="dashboard/categories" class="btn btn-warning">Add New</a>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="link" class="form-label">Link to buy it</label>
                                    <input id="link" type="text" name="link" value="<?= $item['store_url'] ?>"
                                        class="form-control" required>
                                </div>

                                <div class="mb-4">
                                    <div class="border-light bg-light p-3">
                                        <p class="small text-muted mb-3">
                                            You can specify your own link address. If you don't want to, you can
                                            leave the box unchecked and the link will default to the item's name.
                                        </p>

                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <input name="slugCheck" id="slugCheck" type="checkbox"
                                                    class="form-check-input me-2" checked>
                                                <label for="slugCheck" class="form-check-label">Permalink</label>
                                            </div>
                                            <input name="slug" id="slug" type="text" class="form-control"
                                                value="<?= $item['slug'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" id="submit" name="update" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Item
                                    </button>
                                    <button type="button" id="delete" data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        class="btn btn-danger">
                                        <i class="fas fa-trash-alt me-2"></i>Delete Item
                                    </button>
                                </div>
                                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog"
                                    aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete this item? This action cannot be
                                                    undone.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                                <input type="submit" class="btn btn-danger" name="confirm"
                                                    value="Delete">
                                            </div>
                                        </div>
                                    </div>
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
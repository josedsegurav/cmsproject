<?php

require('connect.php');

function filterInput() {
    if (
        $_POST && 
        (!empty($_POST['newCategory']) && empty($_POST['updateCategoryName']) && !(trim($_POST['newCategory']) == '')) ||  
        (!empty($_POST['updateCategoryName']) && empty($_POST['newCategory'])) && !(trim($_POST['updateCategoryName']) == '')){
        return true;
    }else{
        return false;
    }
}

if(filterInput()){
    if(isset($_POST['createCategory'])){

        // Replacing spaces for dashes from name input to use it as a slug.
        $filterSlug = str_replace(" ", "-", $_POST['newCategory']);

        $newCategory = filter_input(INPUT_POST, 'newCategory', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $slug = filter_var($filterSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "INSERT INTO categories (category_name, category_slug) 
        VALUES (:newCategory, :slug)";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':newCategory', $newCategory, PDO::PARAM_STR);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        header("Location: /webdev2/project/dashboard/categories");

    }elseif(isset($_POST['updateCategory'])) {

        // Replacing spaces for dashes from name input to use it as a slug.
        $filterSlug = str_replace(" ", "-", $_POST['updateCategoryName']);
        $id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        $updateCategory = filter_input(INPUT_POST, 'updateCategoryName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $slug = filter_var($filterSlug, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "UPDATE categories 
                SET category_name = :updateCategory, category_slug = :slug 
                WHERE category_id = :id";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query. A PDO constant to verify the data is a string.
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':updateCategory', $updateCategory, PDO::PARAM_STR);
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        header("Location: /webdev2/project/dashboard/categories");
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<!-- Include head tag from template -->
<?php include('htmlHead.php'); ?>

<body>
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
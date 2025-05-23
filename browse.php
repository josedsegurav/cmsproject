<?php
session_start();

require('utils/functions.php');

    unsetRedirectSessions();

require('connect.php');

$title = "Browse Items";

// SQL query
$query =    "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, 
            c.category_name, u.name, u.lastname, COUNT(m.comment_id) AS comments_count 
            FROM serverside.items i 
            JOIN serverside.categories c ON c.category_id = i.category_id
            JOIN serverside.users u ON i.user_id = u.user_id
            LEFT JOIN serverside.comments m ON i.item_id = m.item_id
            GROUP BY i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, 
            c.category_name, u.name, u.lastname
            ORDER BY i.date_created DESC";

// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$items = $statement->fetchAll();

if(!empty($_SESSION['message'])){
    $message = $_SESSION['message'];
    echo "<script>alert('{$message}')</script>";
    unset($_SESSION['message']);
}
// If statement to verify if there is id data coming from the GET action.
if(isset($_GET['p'])){
        // Sanitizing id data into a string.
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
        // SQL query
        $query =   "SELECT i.item_id, i.item_name, i.user_id, i.content, i.category_id, i.store_url, i.image, i.date_created, i.slug, c.category_name, c.category_slug, u.name, u.lastname  
        FROM serverside.items i 
        JOIN serverside.categories c ON c.category_id = i.category_id
        JOIN serverside.users u ON i.user_id = u.user_id 
        WHERE c.category_slug = :slug";

        // A PDO::Statement is prepared from the query. 
        $statement = $db->prepare($query);
        // Bind the value of the id coming from the GET and sanitized into the query.
        $statement->bindValue(':slug', $slug, PDO::PARAM_STR);

        // Execution on the DB server.
        $statement->execute();

        // Get the data from the DB after the query was executed.
        $browseCategories = $statement->fetchAll();

        $_SESSION['categoryBrowse'] = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<?php include('htmlHead.php'); ?>

<body>
    <!-- Navigation -->
    <?php include('nav.php'); ?>

    <!-- Browse Header Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-5 fw-bold mb-3">Browse Interior Design Items</h1>
                    <p class="lead mb-4">Explore our curated collection of furniture, lighting, and décor for your home.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Items Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">

                <!-- Main Content Items -->
                
                    <?php if(!isset($_GET['p'])): ?>
                    <div class="row g-4">
                        <?php foreach ($items as $item): ?>
                            <?php include('listItemTemplate.php') ?>
                        <?php endforeach ?>
                    </div>
                    
                    <?php elseif(isset($_GET['p']) && $browseCategories): ?>
                    <div class="row g-4">
                        <?php foreach ($browseCategories as $item): ?>
                            <?php include('listItemTemplate.php') ?>
                        <?php endforeach ?>
                    </div>
                    
                    <?php elseif(isset($_GET['p']) && !$browseCategories): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>There are no items in that category.</span>
                    </div>
                    <?php endif ?>

                    <!-- Pagination
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav> -->
                
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('footer.php'); ?>

    <script>
        var options = {
            closeOnScroll: true,
        };

        new LuminousGallery(document.querySelectorAll(".image a"), options);
    </script>
</body>

</html>
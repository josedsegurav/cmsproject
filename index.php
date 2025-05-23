<?php
session_start();

require('utils/functions.php');

    unsetRedirectSessions();

require('connect.php');

$loginSuccess = false;
$logOutSuccess = false;

if(isset($_SESSION['loggedMessage'])){
    $loggedIn = $_SESSION['loggedMessage'];
    $loginSuccess = true;
    unset($_SESSION['loggedMessage']);
}



$currentPage = $_SERVER['PHP_SELF'];
$_SESSION['current_page'] = $currentPage;

$title = "Discover Trendy Design Pieces";

function getCountData($table, $db){

    $query = "SELECT COUNT(*) FROM $table";
    // A PDO::Statement is prepared from the query.
    $statement = $db->prepare($query);
    // Execution on the DB server.
    $statement->execute();
    $output = $statement->fetch();
    
    return $output;
}

$usersTotal = getCountData("serverside.users", $db);
$itemsTotal = getCountData("serverside.items", $db);
$commentsTotal = getCountData("serverside.comments", $db);

// SQL query
$query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname 
        FROM serverside.items i 
        JOIN serverside.categories c ON c.category_id = i.category_id
        JOIN serverside.users u ON i.user_id = u.user_id
        ORDER BY i.date_created DESC";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$items = $statement->fetchAll();

$itemsSliced = array_slice($items, 0, 3);



?>


<!DOCTYPE html>
<html lang="en">
<?php include('htmlHead.php'); ?>

<body>
    <!-- Navigation -->
    <?php include('nav.php'); ?>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Discover Beautiful Interior Design Items</h1>
                    <p class="lead mb-5">Explore trending furniture, lighting, and décor for your next design project.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <div class="row text-center g-4">
                <?php foreach($categoriesSliced as $row): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <a class="link-primary" href="browse/<?= $row['category_slug'] ?>"><h5><?= $row['category_name'] ?></h5></a>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <a class="link-primary" href="browse">
                                <h5>More</h5>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Items Section -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Recently Added</h2>
                <a href="browse" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row">
                <?php foreach($itemsSliced as $item): ?>
                <?php include('listItemTemplate.php') ?>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <!-- Community Stats Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Our Community</h2>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="stats-box">
                        <div class="stats-number"><?= $itemsTotal[0] ?>+</div>
                        <h5 class="mb-0">Design Items</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <div class="stats-number"><?= $usersTotal[0] ?>+</div>
                        <h5 class="mb-0">Active Users</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <div class="stats-number"><?= $commentsTotal[0] ?>+</div>
                        <h5 class="mb-0">Comments</h5>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="lead mb-4">Join our community of interior design enthusiasts!</p>
                <a href="signup" class="btn btn-primary">Sign Up Now</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2>Have an amazing design item?</h2>
                    <p class="lead">Share it with our community and help others discover beautiful pieces for their
                        projects.</p>
                    <a href="add" class="btn btn-primary">Upload Your Item</a>
                </div>
            </div>
        </div>
    </section>

    <div id="assistant-container"></div>

    <!-- Footer -->
    <?php include('footer.php'); ?>

    <script src="assistant.jsx"></script>

</body>

</html>
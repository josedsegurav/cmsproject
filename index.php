<?php
session_start();

require('connect.php');

$loginSuccess = false;
$logOutSuccess = false;

if(isset($_SESSION['loggedMessage'])){
    $loggedIn = $_SESSION['loggedMessage'];
    $loginSuccess = true;
    unset($_SESSION['loggedMessage']);
}

if(isset($_SESSION['loggedOutMessage'])){
    $loggedOut = $_SESSION['loggedOutMessage'];
    $logOutSuccess = true;
    unset($_SESSION['loggedOutMessage']);
}

$title = "Discover Trendy Design Pieces";

// SQL query
$query = "SELECT i.item_id, i.item_name, i.user_id, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name, u.name, u.lastname 
        FROM items i 
        JOIN categories c ON c.category_id = i.category_id
        JOIN users u ON i.user_id = u.user_id";
// A PDO::Statement is prepared from the query. 
$statement = $db->prepare($query);
// Execution on the DB server.
$statement->execute();

$items = $statement->fetchAll();



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
                    <p class="lead mb-5">Explore trending furniture, lighting, and décor for your next design project.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Browse by Category</h2>
            <div class="row text-center g-4">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-couch"></i></div>
                            <h5>Furniture</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-lightbulb"></i></div>
                            <h5>Lighting</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-paint-brush"></i></div>
                            <h5>Decor</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-tshirt"></i></div>
                            <h5>Textiles</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-box-open"></i></div>
                            <h5>Storage</h5>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="category-icon"><i class="fas fa-ellipsis-h"></i></div>
                            <h5>More</h5>
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
                <a href="#" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <img src="" class="card-img-top" alt="Armchair">
                        <div class="card-body">
                            <span class="category-pill">Furniture</span>
                            <h5 class="card-title">Velvet Armchair</h5>
                            <p class="card-text">Luxurious velvet armchair in rich emerald green.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">5 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <img src="" class="card-img-top" alt="Table Lamp">
                        <div class="card-body">
                            <span class="category-pill">Lighting</span>
                            <h5 class="card-title">Brass Table Lamp</h5>
                            <p class="card-text">Elegant brass lamp with adjustable shade.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">3 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <img src="" class="card-img-top" alt="Throw Pillows">
                        <div class="card-body">
                            <span class="category-pill">Textiles</span>
                            <h5 class="card-title">Boho Throw Pillows</h5>
                            <p class="card-text">Set of 3 handwoven pillows with fringe details.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">7 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <img src="" class="card-img-top" alt="Bookshelf">
                        <div class="card-body">
                            <span class="category-pill">Storage</span>
                            <h5 class="card-title">Modular Bookshelf</h5>
                            <p class="card-text">Customizable bookshelf system for any space.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">4 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
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
                        <div class="stats-number">1,250+</div>
                        <h5 class="mb-0">Design Items</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <div class="stats-number">450+</div>
                        <h5 class="mb-0">Active Users</h5>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-box">
                        <div class="stats-number">3,800+</div>
                        <h5 class="mb-0">Comments</h5>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="lead mb-4">Join our community of interior design enthusiasts!</p>
                <a href="#" class="btn btn-primary">Sign Up Now</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2>Have an amazing design item?</h2>
                    <p class="lead">Share it with our community and help others discover beautiful pieces for their projects.</p>
                    <a href="#" class="btn btn-primary">Upload Your Item</a>
                </div>
                <div class="col-lg-6">
                    <img src="" alt="Submit Item" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include('footer.php'); ?>   
    <?php if($loginSuccess || $logOutSuccess): ?>
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="successToast" class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-white text-dark border-bottom border-warning">
      <strong class="me-auto">
        <i class="fas fa-check-circle me-2 text-warning"></i>
        <span>Interior<span class="text-warning">Items</span></span>
      </strong>
      <small class="text-muted">just now</small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white">
      <div class="d-flex align-items-center">
        <i class="fas fa-user-check text-primary me-2"></i>
        <?php if($loginSuccess): ?>
        <?= $loggedIn ?>
        <?php elseif($logOutSuccess): ?>
            <?= $loggedOut ?>
            <?php endif ?>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var successToast = new bootstrap.Toast(document.getElementById('successToast'), {
      delay: 5000
    });
    successToast.show();
  });
</script>
<?php endif ?>
</body>
</html>
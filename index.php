<?php
session_start();

require('connect.php');

$title = "Discover Trendy Design Pieces";

// SQL query
$query = "SELECT i.item_id, i.item_name, i.author, i.content, i.store_url, i.image, i.date_created, i.slug, c.category_name 
        FROM items i JOIN categories c ON c.category_id = i.category_id";
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

    <!-- Featured Items Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="section-title mb-0">Featured Items</h2>
                <a href="#" class="btn btn-outline-primary">View All</a>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <span class="featured-badge">Featured</span>
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Modern Sofa">
                        <div class="card-body">
                            <span class="category-pill">Furniture</span>
                            <h5 class="card-title">Scandinavian Minimalist Sofa</h5>
                            <p class="card-text">Sleek, modern sofa with clean lines and comfort.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">12 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <span class="featured-badge">Featured</span>
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Pendant Light">
                        <div class="card-body">
                            <span class="category-pill">Lighting</span>
                            <h5 class="card-title">Geometric Pendant Light</h5>
                            <p class="card-text">Eye-catching pendant light with geometric design.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">8 comments</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <span class="featured-badge">Featured</span>
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Wall Decor">
                        <div class="card-body">
                            <span class="category-pill">Decor</span>
                            <h5 class="card-title">Abstract Wall Art Set</h5>
                            <p class="card-text">Colorful abstract art pieces to elevate any space.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                <small class="text-muted">15 comments</small>
                            </div>
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
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Armchair">
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
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Table Lamp">
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
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Throw Pillows">
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
                        <img src="/api/placeholder/600/400" class="card-img-top" alt="Bookshelf">
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
                    <img src="/api/placeholder/600/400" alt="Submit Item" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="text-white">Interior<span class="text-warning">Items</span></h4>
                    <p>A collaborative space to discover and share beautiful interior design items for your projects.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-pinterest-p"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 col-lg-2 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50">Home</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Browse Items</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Categories</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Submit Item</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-2 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Categories</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white-50">Furniture</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Lighting</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Decor</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Textiles</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50">Storage</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-4">
                    <h5 class="text-white mb-4">Stay Updated</h5>
                    <p class="text-white-50">Subscribe to our newsletter for the latest design trends and new items.</p>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your email">
                        <button class="btn btn-warning" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4 bg-light">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; 2025 Interior Items. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 me-3">Terms of Service</a>
                    <a href="#" class="text-white-50">Contact Us</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
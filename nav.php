<?php

$logged = false;
$adminUser = false;
$userLogged= false;


if(isset($_SESSION['user'])){
    $logged = true;
    $user = $_SESSION['user'];
    if($_SESSION['user']['role'] === "admin"){
        $adminUser = true;
    }elseif($_SESSION['user']['role'] === "user"){
        $userLogged = true;
    }
}

// SQL query
$navCategoryQuery = "SELECT * FROM categories";
// A PDO::Statement is prepared from the query. 
$navStatement = $db->prepare($navCategoryQuery);
// Execution on the DB server.
$navStatement->execute();
$navCategories = $navStatement->fetchAll();

$categoriesSliced = array_slice($navCategories, 0, 5);

if(isset($_POST['logOut'])){
    unset($_SESSION['user']);
    $userLogged = false;
    $_SESSION['loggedOutMessage'] = "You have successfully logged out!";
    header("Location: /webdev2/project/");
}

?>
<!-- Nav template -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/webdev2/project/">Interior<span class="text-warning">Items</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="/webdev2/project/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/webdev2/project/browse">Browse Items</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                        <?php foreach($navCategories as $navCategory): ?>
                        <li><a class="dropdown-item"
                                href="/webdev2/project/browse/<?= $navCategory['category_slug'] ?>"><?= $navCategory['category_name'] ?></a>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </li>
                <?php if($logged && $user['role'] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="/webdev2/project/items">Items List</a>
                </li>
                <?php endif ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex justify-content-center">
                    <form action="/webdev2/project/search" method="post" class="d-flex m-0">
                        <input id="searchInput" name="searchInput" type="text" class="form-control search-bar me-2"
                            placeholder="Search for items..." style="max-width: 500px;">
                        <select class="form-select search-bar me-2" id="category" name="category">
                            <option value="default" selected>All Categories</option>
                            <?php foreach ($navCategories as $category): ?>
                            <option value="<?= $category['category_id']?>"><?= $category['category_name']?></option>
                            <?php endforeach?>
                        </select>
                        <button type="submit" class="btn btn-warning"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <?php if($logged): ?>
                <div class="d-flex align-items-center gap-2">
                    <p class="mb-0">Hi, <?= $_SESSION['user']['name'] ?>!</p>
                    <a href="/webdev2/project/dashboard" class="btn btn-primary">Dashboard</a>
                    <form method="post" class="mb-0">
                        <input type="submit" id="logOut" name="logOut" class="btn btn-primary" value="Log Out">
                    </form>
                </div>
                <?php else: ?>
                <div class="d-flex">
                    <a href="/webdev2/project/login" class="btn btn-outline-primary me-2">Log In</a>
                    <a href="/webdev2/project/signup" class="btn btn-outline-primary">Sign Up</a>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</nav>
<?php

// SQL query
$navCategoryQuery = "SELECT * FROM categories";
// A PDO::Statement is prepared from the query. 
$navStatement = $db->prepare($navCategoryQuery);
// Execution on the DB server.
$navStatement->execute();
$navCategories = $navStatement->fetchAll();



?>
<!-- Nav template -->
<nav>
    <h1><a href="/webdev2/project/">Interiour Design Items</a></h1>
    <ul>
        <li><a href="/webdev2/project/">Home</a></li>
        <li><a href="/webdev2/project/browse">Browse Items</a></li>
        <li><a href="/webdev2/project/add">Add Item</a></li>
        <li><a href="/webdev2/project/items">Items List</a></li>
    </ul>
</nav>
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
                    <a class="nav-link dropdown-toggle" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                        Categories
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach($navCategories as $navCategory): ?>
                        <li><a class="dropdown-item"
                                href="/webdev2/project/browse/<?= $navCategory['category_slug'] ?>"><?= $navCategory['category_name'] ?></a>
                        </li>
                        <?php endforeach ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Submit Item</a>
                </li>
            </ul>
            <div class="d-flex gap-3">
                <div class="d-flex justify-content-center">
                    <form action="/webdev2/project/search" method="post" class="d-flex">
                        <input id="searchInput" name="searchInput" type="text" class="form-control search-bar me-2" placeholder="Search for items..."
                            style="max-width: 500px;">
                        <button class="btn btn-warning"><i class="fas fa-search"></i></button>
                        <form>
                </div>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-primary me-2">Log In</a>
                    <a href="#" class="btn btn-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</nav>
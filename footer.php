<footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="text-white">Interior<span class="text-warning">Items</span></h4>
                    <p>A collaborative space to discover and share beautiful interior design items for your projects.</p>
                    <!-- <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-pinterest-p"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div> -->
                </div>
                <div class="col-md-4 col-lg-2 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="
                        " class="text-white-50">Home</a></li>
                        <li class="mb-2"><a href="browse" class="text-white-50">Browse Items</a></li>
                        <li class="mb-2"><a href="add" class="text-white-50">Submit Item</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-2 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Categories</h5>
                    <ul class="list-unstyled">
                    <?php foreach($categoriesSliced as $row): ?>
                        <li class="mb-2"><a href="browse/<?= $row['category_slug'] ?>" class="text-white-50"><?= $row['category_name'] ?></a></li>
                        <?php endforeach ?>
                        <li class="mb-2"><a href="browse" class="text-white-50">More...</a></li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 mb-4 bg-light">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; 2025 Interior Items. All rights reserved.</p>
                </div>
                <!-- <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-white-50 me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 me-3">Terms of Service</a>
                    <a href="#" class="text-white-50">Contact Us</a>
                </div> -->
            </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.min.js" integrity="sha384-VQqxDN0EQCkWoxt/0vsQvZswzTHUVOImccYmSyhJTp7kGtPed0Qcx8rK9h9YEgx+" crossorigin="anonymous"></script>
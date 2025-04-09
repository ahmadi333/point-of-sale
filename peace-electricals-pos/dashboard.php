<?php include 'includes/db.php'; include 'includes/header.php'; ?>
    <h1 class="mb-4">Welcome to Peace Electricals POS</h1>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <?php
                    $result = $conn->query("SELECT SUM(quantity) as total FROM stock");
                    $row = $result->fetch_assoc();
                    ?>
                    <h5 class="card-title">Total Stock</h5>
                    <p class="card-text"><?php echo $row['total']; ?> items</p>
                </div>
            </div>
        </div>
        <!-- Add more cards for stats -->
    </div>
<?php include 'includes/footer.php'; ?>
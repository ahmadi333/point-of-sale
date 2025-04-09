<?php include 'includes/db.php'; include 'includes/header.php'; ?>
    <h1>Stock Management</h1>
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStockModal">Add Stock</button>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr><th>ID</th><th>Product Name</th><th>Quantity</th><th>Price</th></tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM stock");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['product_name']}</td><td>{$row['quantity']}</td><td>" . CURRENCY_SYMBOL . " " . number_format($row['price'], 2) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="stock.php">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Price (<?php echo CURRENCY_CODE; ?>)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $product_name = $_POST['product_name'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $conn->query("INSERT INTO stock (product_name, quantity, price) VALUES ('$product_name', $quantity, $price)");
        echo "<script>window.location.href='stock.php';</script>";
    }
    ?>
<?php include 'includes/footer.php'; ?>
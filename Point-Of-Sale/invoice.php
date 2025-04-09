<?php
include 'includes/db.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $products = $_POST['product_id'];
    $quantities = $_POST['quantity_sold'];
    $total_amount = 0;

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO invoices (customer_name, total_amount, invoice_date) VALUES (?, 0, NOW())");
        $stmt->bind_param("s", $customer_name);
        $stmt->execute();
        $invoice_id = $conn->insert_id;

        for ($i = 0; $i < count($products); $i++) {
            $product_id = $products[$i];
            $quantity_sold = $quantities[$i];

            $stock_query = "SELECT quantity, price FROM stock WHERE id = $product_id";
            $stock_result = $conn->query($stock_query);
            $stock = $stock_result->fetch_assoc();

            if ($stock['quantity'] >= $quantity_sold) {
                $unit_price = $stock['price'];
                $subtotal = $quantity_sold * $unit_price;
                $total_amount += $subtotal;

                $conn->query("UPDATE stock SET quantity = quantity - $quantity_sold WHERE id = $product_id");

                $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity_sold, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiidd", $invoice_id, $product_id, $quantity_sold, $unit_price, $subtotal);
                $stmt->execute();
            } else {
                throw new Exception("Not enough stock for product ID $product_id!");
            }
        }

        $conn->query("UPDATE invoices SET total_amount = $total_amount WHERE id = $invoice_id");

        $conn->commit();
        echo "<div class='alert alert-success mt-3'>Invoice #$invoice_id generated for $customer_name! Total: " . CURRENCY_SYMBOL . " " . number_format($total_amount, 2) . "</div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger mt-3'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<h1>Create Invoice</h1>
<form method="POST" action="invoice.php">
    <div class="row mb-3">
        <div class="col-12">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
        </div>
    </div>
    <div id="product-list">
        <div class="product-row mb-3 row">
            <div class="col-12 col-md-6 mb-2 mb-md-0">
                <label>Product</label>
                <select name="product_id[]" class="form-control" required>
                    <?php
                    $result = $conn->query("SELECT * FROM stock");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['product_name']} (Stock: {$row['quantity']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-8 col-md-4 mb-2 mb-md-0">
                <label>Quantity</label>
                <input type="number" name="quantity_sold[]" class="form-control" min="1" required>
            </div>
            <div class="col-4 col-md-2">
                <label class="d-none d-md-block"> </label>
                <button type="button" class="btn btn-danger remove-product w-100">Remove</button>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12 col-md-6">
            <button type="button" class="btn btn-primary w-100" id="add-product">Add More Product</button>
        </div>
        <div class="col-12 col-md-6 mt-2 mt-md-0">
            <button type="submit" class="btn btn-success w-100">Generate Invoice</button>
        </div>
    </div>
</form>
<style>
     .product-row{
align-items: end !important;
}
</style>
<script>
document.getElementById('add-product').addEventListener('click', function() {
    const productList = document.getElementById('product-list');
    const newRow = document.createElement('div');
    newRow.className = 'product-row mb-3 row';
    newRow.innerHTML = `
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <label>Product</label>
            <select name="product_id[]" class="form-control" required>
                <?php
                $result = $conn->query("SELECT * FROM stock");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['product_name']} (Stock: {$row['quantity']})</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-8 col-md-4 mb-2 mb-md-0">
            <label>Quantity</label>
            <input type="number" name="quantity_sold[]" class="form-control" min="1" required>
        </div>
        <div class="col-4 col-md-2">
            <label class="d-none d-md-block"> </label>
            <button type="button" class="btn btn-danger remove-product w-100">Remove</button>
        </div>
    `;
    productList.appendChild(newRow);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.product-row').remove();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
<?php include 'includes/db.php'; include 'includes/header.php'; ?>
    <h1>View All Invoices</h1>
    <?php
    $sql = "SELECT * FROM invoices ORDER BY invoice_date DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($invoice = $result->fetch_assoc()) {
            $invoice_id = $invoice['id'];
            ?>
            <div class="card mb-3">
                <div class="card-header">
                    Invoice #<?php echo $invoice_id; ?> - <?php echo $invoice['invoice_date']; ?>
                    <span class="float-end">Total: <?php echo CURRENCY_SYMBOL . " " . number_format($invoice['total_amount'], 2); ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Customer Name:</strong> <?php echo $invoice['customer_name']; ?></p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity Sold</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $items_sql = "SELECT invoice_items.*, stock.product_name 
                                              FROM invoice_items 
                                              JOIN stock ON invoice_items.product_id = stock.id 
                                              WHERE invoice_id = $invoice_id";
                                $items_result = $conn->query($items_sql);
                                while ($item = $items_result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $item['product_name'] . "</td>";
                                    echo "<td>" . $item['quantity_sold'] . "</td>";
                                    echo "<td>" . CURRENCY_SYMBOL . " " . number_format($item['unit_price'], 2) . "</td>";
                                    echo "<td>" . CURRENCY_SYMBOL . " " . number_format($item['subtotal'], 2) . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<div class='alert alert-info'>No invoices found.</div>";
    }
    ?>
<?php include 'includes/footer.php'; ?>
<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Supplier orders heading - shows orders containing supplier's products -->
<h1 class="h3 mb-3">Objednávky mých produktů</h1>

<!-- Display orders table if there are any orders -->
<?php if (!empty($orders)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <!-- Table headers -->
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Zákazník</th>
                <th>Stav</th>
                <th>Datum</th>
                <th>Detail</th>
            </tr>
            </thead>
            <!-- Loop through and display each order -->
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <!-- Order ID -->
                    <td><?= (int)$order['id'] ?></td>
                    <!-- Customer name who placed the order -->
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>

                    <!-- Order status with color-coded badge -->
                    <td>
                        <!-- Display status badge with appropriate styling -->
                        <?php
                        switch ($order['status']) {
                            case 'pending':
                                echo '<span class="badge bg-secondary">Čeká na potvrzení</span>';
                                break;
                            case 'confirmed':
                                echo '<span class="badge bg-warning text-dark">Potvrzeno</span>';
                                break;
                            case 'shipped':
                                echo '<span class="badge bg-info text-dark">Odesláno</span>';
                                break;
                            case 'canceled':
                                echo '<span class="badge bg-danger">Zrušeno</span>';
                                break;
                            case 'delivered':
                                echo '<span class="badge bg-success">Ukončeno</span>';
                                break;
                            default:
                                echo htmlspecialchars($order['status']);
                                break;
                        }
                        ?>
                    </td>

                    <!-- Order creation date -->
                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                    <!-- Link to view order details (supplier only sees their items) -->
                    <td>
                        <a href="index.php?action=supplier_order_detail&id=<?= (int)$order['id'] ?>"
                           class="btn btn-sm btn-outline-secondary">
                            🔍 Detail (moje položky)
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when no orders found -->
    <p>Nemáš žádné objednávky obsahující tvoje produkty.</p>
<?php endif; ?>

<!-- Link back to home page -->
<p class="mt-3">
    <a href="index.php" class="btn btn-secondary">← Zpět na hlavní stránku</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

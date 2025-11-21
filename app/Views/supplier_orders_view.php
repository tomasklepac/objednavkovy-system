<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Supplier orders heading - shows orders containing supplier's products -->
<h1 class="h3 mb-4"><i class="fas fa-box-open"></i> Objednávky mých produktů</h1>

<!-- Display orders table if there are any orders -->
<?php if (!empty($orders)): ?>
    <!-- Summary Stats -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="summary-card">
                <div class="summary-icon"><i class="fas fa-shopping-bag"></i></div>
                <div class="summary-content">
                    <h6>Objednávek k procesování</h6>
                    <h3><?= count($orders) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card highlight">
                <div class="summary-icon"><i class="fas fa-check-circle"></i></div>
                <div class="summary-content">
                    <h6>Zpracováno</h6>
                    <h3><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['delivered', 'completed', 'finished']))) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <!-- Table headers -->
            <thead>
            <tr>
                <th><i class="fas fa-hashtag"></i> ID</th>
                <th class="text-center"><i class="fas fa-user"></i> Zákazník</th>
                <th class="text-center"><i class="fas fa-circle"></i> Stav</th>
                <th class="text-center"><i class="fas fa-calendar"></i> Datum</th>
                <th class="text-center"><i class="fas fa-eye"></i> Detail</th>
            </tr>
            </thead>
            <!-- Loop through and display each order -->
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr class="cart-item-row">
                    <!-- Order ID -->
                    <td class="fw-600">#<?= (int)$order['id'] ?></td>
                    <!-- Customer name who placed the order -->
                    <td class="text-center">
                        <small><?= htmlspecialchars($order['customer_name']) ?></small>
                    </td>

                    <!-- Order status with color-coded badge -->
                    <td class="text-center">
                        <!-- Display status badge with appropriate styling -->
                        <?php
                        $statusConfig = [
                            'pending' => ['badge bg-secondary', 'Čeká na potvrzení'],
                            'confirmed' => ['badge bg-warning text-dark', 'Potvrzeno'],
                            'shipped' => ['badge bg-info text-white', 'Odesláno'],
                            'canceled' => ['badge bg-danger', 'Zrušeno'],
                            'delivered' => ['badge bg-success', 'Doručeno'],
                            'completed' => ['badge bg-success', 'Dokončeno'],
                            'finished' => ['badge bg-success', 'Hotovo'],
                        ];
                        
                        $status = $order['status'];
                        $config = $statusConfig[$status] ?? ['badge bg-secondary', $status];
                        echo '<span class="' . $config[0] . '">' . $config[1] . '</span>';
                        ?>
                    </td>

                    <!-- Order creation date -->
                    <td class="text-center"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                    <!-- Link to view order details (supplier only sees their items) -->
                    <td class="text-center">
                        <a href="index.php?action=supplier_order_detail&id=<?= (int)$order['id'] ?>"
                           class="btn btn-sm btn-quantity" title="Zobrazit detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Zpět na dashboard
        </a>
    </div>
<?php else: ?>
    <!-- Message when no orders found -->
    <div class="alert alert-info text-center" style="padding: 3rem;">
        <i class="fas fa-inbox" style="font-size: 2rem; color: #667eea;"></i>
        <p class="mt-3 mb-0">Nemáš žádné objednávky obsahující tvoje produkty.</p>
        <p class="text-muted">Kdy se pozdržíš, budou se zde zobrazovat.</p>
        <a href="index.php?action=my_products" class="btn btn-primary mt-3">
            <i class="fas fa-box"></i> Moje produkty
        </a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

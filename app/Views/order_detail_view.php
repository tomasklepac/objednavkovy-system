<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Order detail heading with order ID -->
<h1 class="h3 mb-3">Detail objednávky #<?= htmlspecialchars($_GET['id'] ?? '') ?></h1>

<!-- Display order status if order exists -->
<?php if (!empty($order)): ?>
    <p>
        <strong>Stav objednávky:</strong>
        <!-- Order status badge with color coding -->
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
    </p>
<?php endif; ?>

<!-- Display order items table if there are any items -->
<?php if (!empty($items)): ?>
    <div class="table-responsive mt-3">
        <table class="table table-striped table-hover align-middle">
            <!-- Table headers -->
            <thead class="table-light">
            <tr>
                <th>Produkt</th>
                <th>Množství</th>
                <th>Cena/ks</th>
                <th>Celkem</th>
            </tr>
            </thead>
            <!-- Loop through and display each order item -->
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <!-- Product name -->
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <!-- Ordered quantity -->
                    <td><?= (int)$item['quantity'] ?></td>
                    <!-- Unit price in CZK -->
                    <td><?= number_format($item['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Total for this line (price × quantity) -->
                    <td><?= number_format(($item['unit_price_cents'] * $item['quantity']) / 100, 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when order has no items -->
    <p>Tato objednávka neobsahuje žádné položky.</p>
<?php endif; ?>

<!-- Link back to orders list -->
<p class="mt-3">
    <a href="index.php?action=orders" class="btn btn-secondary">← Zpět na seznam objednávek</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

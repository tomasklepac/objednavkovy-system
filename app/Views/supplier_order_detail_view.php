<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Supplier order detail heading showing order ID and scope (only supplier's items) -->
<h1 class="h3 mb-3">Objednávka #<?= htmlspecialchars($_GET['id'] ?? '') ?> – moje položky</h1>

<!-- Display customer information if available -->
<?php if (!empty($customer)): ?>
    <p><strong>Zákazník:</strong> <?= htmlspecialchars($customer['name']) ?> (<?= htmlspecialchars($customer['email']) ?>)</p>
<?php endif; ?>

<!-- Display order items table if there are any items for this supplier -->
<?php if (!empty($items)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <!-- Table headers -->
            <thead class="table-light">
            <tr>
                <th>Produkt</th>
                <th>Množství</th>
                <th>Cena/ks</th>
                <th>Mezisoučet</th>
            </tr>
            </thead>
            <!-- Loop through and display each item belonging to this supplier -->
            <tbody>
            <?php
            // Calculate total for supplier's items only
            $subtotalCents = 0;
            foreach ($items as $it):
                // Calculate line total (unit price × quantity)
                $line = $it['unit_price_cents'] * $it['quantity'];
                $subtotalCents += $line;
                ?>
                <tr>
                    <!-- Product name -->
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <!-- Ordered quantity -->
                    <td><?= (int)$it['quantity'] ?></td>
                    <!-- Unit price in CZK -->
                    <td><?= number_format($it['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Line total (price × quantity) -->
                    <td><?= number_format($line / 100, 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            <!-- Total row showing sum of supplier's items only -->
            <tr>
                <td colspan="3" class="text-end fw-bold">Součet mých položek:</td>
                <td class="fw-bold"><?= number_format($subtotalCents / 100, 2, ',', ' ') ?> Kč</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when order has no items for this supplier -->
    <p>Tahle objednávka neobsahuje žádné tvoje položky.</p>
<?php endif; ?>

<!-- Link back to supplier orders list -->
<p class="mt-3">
    <a href="index.php?action=supplier_orders" class="btn btn-secondary">← Zpět na objednávky mých produktů</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

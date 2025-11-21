<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Supplier order detail heading showing order ID and scope (only supplier's items) -->
<h1 class="h3 mb-3">Objednávka #<?= htmlspecialchars($_GET['id'] ?? '') ?> – moje položky</h1>

<!-- Display customer information if available -->
<?php if (!empty($customer)): ?>
    <p><strong>Zákazník:</strong> <?= htmlspecialchars($customer['name']) ?> (<?= htmlspecialchars($customer['email']) ?>)</p>
<?php endif; ?>

<!-- Display order items table if there are any items for this supplier -->
<?php if (!empty($items)): ?>
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <!-- Table headers -->
            <thead>
            <tr>
                <th class="text-center">Produkt</th>
                <th class="text-center">Množství</th>
                <th class="text-center">Cena/ks</th>
                <th class="text-center">Mezisoučet</th>
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
                    <td class="text-center"><?= htmlspecialchars($it['name']) ?></td>
                    <!-- Ordered quantity -->
                    <td class="text-center"><?= (int)$it['quantity'] ?></td>
                    <!-- Unit price in CZK -->
                    <td class="text-center"><?= number_format($it['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Line total (price × quantity) -->
                    <td class="text-center"><?= number_format($line / 100, 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            <!-- Total row showing sum of supplier's items only -->
            <tr>
                <td colspan="3" class="text-end fw-bold">Součet mých položek:</td>
                <td class="text-center fw-bold"><?= number_format($subtotalCents / 100, 2, ',', ' ') ?> Kč</td>
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
    <a href="index.php?action=supplier_orders" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Zpět na objednávky mých produktů</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

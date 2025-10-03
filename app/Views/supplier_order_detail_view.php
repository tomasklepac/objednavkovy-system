<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Objednávka #<?= htmlspecialchars($_GET['id'] ?? '') ?> – moje položky</h1>

<?php if (!empty($customer)): ?>
    <p><strong>Zákazník:</strong> <?= htmlspecialchars($customer['name']) ?> (<?= htmlspecialchars($customer['email']) ?>)</p>
<?php endif; ?>

<?php if (!empty($items)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Produkt</th>
                <th>Množství</th>
                <th>Cena/ks</th>
                <th>Mezisoučet</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $subtotalCents = 0;
            foreach ($items as $it):
                $line = $it['unit_price_cents'] * $it['quantity'];
                $subtotalCents += $line;
                ?>
                <tr>
                    <td><?= htmlspecialchars($it['name']) ?></td>
                    <td><?= (int)$it['quantity'] ?></td>
                    <td><?= number_format($it['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <td><?= number_format($line / 100, 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end fw-bold">Součet mých položek:</td>
                <td class="fw-bold"><?= number_format($subtotalCents / 100, 2, ',', ' ') ?> Kč</td>
            </tr>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Tahle objednávka neobsahuje žádné tvoje položky.</p>
<?php endif; ?>

<p class="mt-3">
    <a href="index.php?action=supplier_orders" class="btn btn-secondary">← Zpět na objednávky mých produktů</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Tvůj košík</h1>

<?php if (!empty($_SESSION['cart'])): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>Název</th>
                <th>Cena (Kč)</th>
                <th>Množství</th>
                <th>Celkem</th>
                <th>Akce</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $id => $item):
                $subtotal = ($item['price_cents'] / 100) * $item['quantity'];
                $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <a href="index.php?action=decrease_from_cart&id=<?= (int)$id ?>" class="btn btn-sm btn-outline-secondary">➖</a>
                            <span><?= (int)$item['quantity'] ?></span>
                            <a href="index.php?action=increase_from_cart&id=<?= (int)$id ?>" class="btn btn-sm btn-outline-secondary">➕</a>
                        </div>
                    </td>
                    <td><?= number_format($subtotal, 2, ',', ' ') ?> Kč</td>
                    <td>
                        <a href="index.php?action=remove_from_cart&id=<?= (int)$id ?>"
                           onclick="return confirm('Opravdu smazat tento produkt z košíku?');"
                           class="btn btn-sm btn-outline-danger">🗑 Smazat</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Celkem</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2, ',', ' ') ?> Kč</strong></td>
            </tr>
            </tbody>
        </table>
    </div>

    <p>
        <a href="index.php?action=confirm_order" class="btn btn-success">✅ Pokračovat k potvrzení objednávky</a>
    </p>
<?php else: ?>
    <p>Tvůj košík je prázdný.</p>
<?php endif; ?>

<p><a href="index.php?action=products" class="btn btn-secondary">← Zpět k produktům</a></p>

<?php require __DIR__ . '/partials/footer.php'; ?>

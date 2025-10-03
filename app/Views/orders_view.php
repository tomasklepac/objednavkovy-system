<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Objednávky</h1>

<?php if (!empty($orders)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Stav</th>
                <th>Celkem</th>
                <th>Datum</th>
                <th>Detail</th>
                <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                    <th>Zákazník</th>
                    <th>Akce</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td>
                        <?php if ($order['status'] === 'delivered'): ?>
                            <span class="text-success fw-bold">✔ Ukončeno</span>
                        <?php elseif ($order['status'] === 'canceled'): ?>
                            <span class="text-danger fw-bold">❌ Zrušeno</span>
                        <?php else: ?>
                            <?= htmlspecialchars($order['status']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($order['total_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td>
                        <a href="index.php?action=order_detail&id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-outline-secondary">🔍 Detail</a>
                    </td>

                    <?php if (in_array('admin', $_SESSION['roles'], true)): ?>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td>
                            <?php if ($order['status'] === 'pending'): ?>
                                <a href="index.php?action=confirm_admin_order&id=<?= (int)$order['id'] ?>" class="btn btn-sm btn-success">✅ Potvrdit</a>
                                <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=canceled" class="btn btn-sm btn-danger">❌ Zrušit</a>
                            <?php elseif ($order['status'] === 'confirmed'): ?>
                                <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=shipped" class="btn btn-sm btn-primary">📦 Odeslat</a>
                            <?php elseif ($order['status'] === 'shipped'): ?>
                                <a href="index.php?action=update_order&id=<?= (int)$order['id'] ?>&status=delivered" class="btn btn-sm btn-info">📬 Doručeno</a>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Žádné objednávky k zobrazení.</p>
<?php endif; ?>

<p class="mt-3">
    <a href="index.php" class="btn btn-secondary">← Zpět na hlavní stránku</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

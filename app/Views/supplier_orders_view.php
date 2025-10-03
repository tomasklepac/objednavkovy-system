<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Objednávky mých produktů</h1>

<?php if (!empty($orders)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Zákazník</th>
                <th>Stav</th>
                <th>Datum</th>
                <th>Detail</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= (int)$order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
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
    <p>Nemáš žádné objednávky obsahující tvoje produkty.</p>
<?php endif; ?>

<p class="mt-3">
    <a href="index.php" class="btn btn-secondary">← Zpět na hlavní stránku</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

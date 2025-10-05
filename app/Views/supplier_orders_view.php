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

                    <td>
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
                                echo '<span class="badge bg-danger">✗ Zrušeno</span>';
                                break;
                            case 'delivered':
                                echo '<span class="badge bg-success">✓ Ukončeno</span>';
                                break;
                            default:
                                echo htmlspecialchars($order['status']);
                                break;
                        }
                        ?>
                    </td>

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

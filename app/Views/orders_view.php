<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container mt-4">
    <h2>Objednávky</h2>

    <?php
    // Convert roles to lowercase for reliable comparison
    $roles = array_map('strtolower', $_SESSION['roles'] ?? []);
    $isCustomer = in_array('customer', $roles);
    ?>

    <div class="table-responsive">
        <table class="table table-striped align-middle mt-3">
            <thead>
            <tr>
            <th>ID</th>
            <th>Stav</th>
            <th>Celkem</th>
            <th>Datum</th>
            <th>Detail</th>
            <?php if (!$isCustomer): ?>
                <th>Zákazník</th>
                <th>Akce</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>

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
                            case 'cancelled':
                                echo '<span class="badge bg-danger">Zrušeno</span>';
                                break;
                            case 'delivered':
                            case 'completed':
                            case 'finished':
                                echo '<span class="badge bg-success">Ukončeno</span>';
                                break;
                            default:
                                echo htmlspecialchars($order['status']);
                                break;
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        $totalCents = isset($order['total_cents']) ? (int)$order['total_cents'] : 0;
                        echo number_format($totalCents / 100, 2, ',', ' ') . ' Kč';
                        ?>
                    </td>

                    <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>

                    <td>
                        <a href="index.php?action=order_detail&id=<?= urlencode($order['id']) ?>"
                           class="btn btn-outline-secondary btn-sm">
                            🔍 Detail
                        </a>
                    </td>

                    <?php if (!$isCustomer): ?>
                        <td>
                            <?php
                            if (!empty($order['customer_name'])) {
                                echo htmlspecialchars($order['customer_name']);
                            } else {
                                echo '–';
                            }
                            ?>
                        </td>

                        <td>
                            <?php
                            // Only admin or supplier roles can see action buttons
                            if (in_array('admin', $roles) || in_array('supplier', $roles)) {
                                if ($order['status'] === 'pending') {
                                    echo '<a href="index.php?action=confirm_admin_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-success" 
                                           style="border:none;">
                                           ✓ Potvrdit</a> ';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-danger" 
                                           style="border:none;"
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');">
                                           ✕ Zrušit</a>';
                                } elseif ($order['status'] === 'confirmed') {
                                    echo '<a href="index.php?action=mark_shipped&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-primary" 
                                           style="background-color:#7e57c2;border:none;">
                                           📦 Odeslat</a> ';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-danger" 
                                           style="border:none;"
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');">
                                           ✕ Zrušit</a>';
                                } elseif ($order['status'] === 'shipped') {
                                    echo '<a href="index.php?action=mark_completed&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-info text-white" 
                                           style="background-color:#26c6da;border:none;">
                                           📬 Doručeno</a> ';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-danger" 
                                           style="border:none;"
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');">
                                           ✕ Zrušit</a>';
                                } else {
                                    echo '<span class="text-muted">–</span>';
                                }
                            } else {
                                echo '<span class="text-muted">–</span>';
                            }
                            ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="<?= $isCustomer ? 5 : 7 ?>" class="text-center text-muted">
                    Žádné objednávky k zobrazení.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
            </table>
    </div>

    <a href="index.php" class="btn btn-secondary mt-3">← Zpět na hlavní stránku</a>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

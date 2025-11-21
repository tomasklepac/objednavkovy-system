<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-4"><i class="fas fa-receipt"></i> Objednávky</h1>

<?php
// Convert roles to lowercase for reliable comparison
$roles = array_map('strtolower', $_SESSION['roles'] ?? []);
$isCustomer = in_array('customer', $roles);
?>

<?php if (!empty($orders)): ?>
    <!-- Summary Stats -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon"><i class="fas fa-list"></i></div>
                <div class="summary-content">
                    <h6>Celkem objednávek</h6>
                    <h3><?= count($orders) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card highlight">
                <div class="summary-icon"><i class="fas fa-clock"></i></div>
                <div class="summary-content">
                    <h6>Čekají na potvrzení</h6>
                    <h3><?= count(array_filter($orders, fn($o) => $o['status'] === 'pending')) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon"><i class="fas fa-check-circle"></i></div>
                <div class="summary-content">
                    <h6>Dokončeno</h6>
                    <h3><?= count(array_filter($orders, fn($o) => in_array($o['status'], ['delivered', 'completed', 'finished']))) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <thead>
            <tr>
                <th><i class="fas fa-hashtag"></i> ID</th>
                <th class="text-center"><i class="fas fa-circle"></i> Stav</th>
                <th class="text-center"><i class="fas fa-money-bill"></i> Celkem</th>
                <th class="text-center"><i class="fas fa-calendar"></i> Datum</th>
                <th class="text-center"><i class="fas fa-eye"></i> Detail</th>
                <?php if (!$isCustomer): ?>
                    <th class="text-center"><i class="fas fa-user"></i> Zákazník</th>
                    <th class="text-center"><i class="fas fa-tools"></i> Akce</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr class="cart-item-row">
                    <td class="text-center fw-600">#<?= htmlspecialchars($order['id']) ?></td>

                    <td class="text-center">
                        <?php
                        $statusConfig = [
                            'pending' => ['badge bg-secondary', 'Čeká na potvrzení'],
                            'confirmed' => ['badge bg-warning text-dark', 'Potvrzeno'],
                            'shipped' => ['badge bg-info text-white', 'Odesláno'],
                            'canceled' => ['badge bg-danger', 'Zrušeno'],
                            'cancelled' => ['badge bg-danger', 'Zrušeno'],
                            'delivered' => ['badge bg-success', 'Doručeno'],
                            'completed' => ['badge bg-success', 'Dokončeno'],
                            'finished' => ['badge bg-success', 'Hotovo'],
                        ];
                        
                        $status = $order['status'];
                        $config = $statusConfig[$status] ?? ['badge bg-secondary', $status];
                        echo '<span class="' . $config[0] . '">' . $config[1] . '</span>';
                        ?>
                    </td>

                    <td class="text-center">
                        <span class="price-badge-small">
                            <?= number_format((int)($order['total_cents'] ?? 0) / 100, 2, ',', ' ') ?> Kč
                        </span>
                    </td>

                    <td class="text-center"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>

                    <td class="text-center">
                        <a href="index.php?action=order_detail&id=<?= urlencode($order['id']) ?>"
                           class="btn btn-sm btn-quantity" title="Zobrazit detail">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>

                    <?php if (!$isCustomer): ?>
                        <td class="text-center">
                            <?php
                            if (!empty($order['customer_name'])) {
                                echo '<small>' . htmlspecialchars($order['customer_name']) . '</small>';
                            } else {
                                echo '<span class="text-muted">–</span>';
                            }
                            ?>
                        </td>

                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                            <?php
                            // Only admin or supplier roles can see action buttons
                            if (in_array('admin', $roles) || in_array('supplier', $roles)) {
                                if ($order['status'] === 'pending') {
                                    echo '<a href="index.php?action=confirm_admin_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-quantity" title="Potvrdit objednávku" style="background:#2dce89; color:white;">
                                           <i class="fas fa-check"></i>
                                           </a>';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-remove" 
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');" title="Zrušit objednávku">
                                           <i class="fas fa-times"></i>
                                           </a>';
                                } elseif ($order['status'] === 'confirmed') {
                                    echo '<a href="index.php?action=mark_shipped&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-quantity" title="Označit jako odesláno" style="background:#667eea; color:white;">
                                           <i class="fas fa-shipping-fast"></i>
                                           </a>';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-remove" 
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');" title="Zrušit objednávku">
                                           <i class="fas fa-times"></i>
                                           </a>';
                                } elseif ($order['status'] === 'shipped') {
                                    echo '<a href="index.php?action=mark_completed&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-sm btn-quantity" title="Označit jako doručeno" style="background:#11cdef; color:white;">
                                           <i class="fas fa-check-double"></i>
                                           </a>';
                                    echo '<a href="index.php?action=cancel_order&id=' . urlencode($order['id']) . '" 
                                           class="btn btn-remove" 
                                           onclick="return confirm(\'Opravdu zrušit tuto objednávku?\');" title="Zrušit objednávku">
                                           <i class="fas fa-times"></i>
                                           </a>';
                                } else {
                                    echo '<span class="text-muted">–</span>';
                                }
                            } else {
                                echo '<span class="text-muted">–</span>';
                            }
                            ?>
                            </div>
                        </td>
                    <?php endif; ?>
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
    <div class="alert alert-info text-center" style="padding: 3rem;">
        <i class="fas fa-inbox" style="font-size: 2rem; color: #667eea;"></i>
        <p class="mt-3 mb-0">Žádné objednávky k zobrazení.</p>
        <p class="text-muted">Zatím jste si neobjednali žádný produkt.</p>
        <a href="index.php?action=products" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-bag"></i> Prohlédnout produkty
        </a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

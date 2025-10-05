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

                            <!-- Snížit množství -->
                            <form method="post" action="index.php?action=decrease_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">➖</button>
                            </form>

                            <span><?= (int)$item['quantity'] ?></span>

                            <!-- Zvýšit množství -->
                            <form method="post" action="index.php?action=increase_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">➕</button>
                            </form>

                        </div>
                    </td>
                    <td><?= number_format($subtotal, 2, ',', ' ') ?> Kč</td>
                    <td>
                        <!-- Odebrat produkt -->
                        <form method="post" action="index.php?action=remove_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Opravdu smazat tento produkt z košíku?');">
                                🗑 Smazat
                            </button>
                        </form>
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

    <!-- Potvrzení objednávky -->
    <form method="post" action="index.php?action=confirm_order">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <button type="submit" class="btn btn-success">✅ Pokračovat k potvrzení objednávky</button>
    </form>

<?php else: ?>
    <p>Tvůj košík je prázdný.</p>
<?php endif; ?>

<p><a href="index.php?action=products" class="btn btn-secondary">← Zpět k produktům</a></p>

<?php require __DIR__ . '/partials/footer.php'; ?>

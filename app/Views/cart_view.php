<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Shopping cart heading -->
<h1 class="h3 mb-3">Tvůj košík</h1>

<!-- Display shopping cart if it's not empty -->
<?php if (!empty($_SESSION['cart'])): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <!-- Table header with column names -->
            <thead class="table-light">
            <tr>
                <th>Název</th>
                <th>Cena (Kč)</th>
                <th>Množství</th>
                <th>Celkem</th>
                <th>Akce</th>
            </tr>
            </thead>
            <!-- Table body with cart items -->
            <tbody>
            <?php
            // Calculate total for the cart
            $total = 0;
            foreach ($_SESSION['cart'] as $id => $item):
                // Calculate subtotal for each item
                $subtotal = ($item['price_cents'] / 100) * $item['quantity'];
                $total += $subtotal;
                ?>
                <!-- Display each item in cart as a row -->
                <tr>
                    <!-- Product name -->
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <!-- Unit price formatted as currency -->
                    <td><?= number_format($item['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Quantity adjustment buttons -->
                    <td>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <!-- Decrease quantity button -->
                            <form method="post" action="index.php?action=decrease_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">➖</button>
                            </form>

                            <!-- Display current quantity -->
                            <span><?= (int)$item['quantity'] ?></span>

                            <!-- Increase quantity button -->
                            <form method="post" action="index.php?action=increase_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-secondary">➕</button>
                            </form>
                        </div>
                    </td>
                    <!-- Subtotal for this item (price × quantity) -->
                    <td><?= number_format($subtotal, 2, ',', ' ') ?> Kč</td>
                    <!-- Remove item from cart button -->
                    <td>
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
            <!-- Total row displaying cart sum -->
            <tr>
                <td colspan="3"><strong>Celkem</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2, ',', ' ') ?> Kč</strong></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Form to proceed to order confirmation -->
    <form method="post" action="index.php?action=confirm_order">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <button type="submit" class="btn btn-success">✅ Pokračovat k potvrzení objednávky</button>
    </form>

<?php else: ?>
    <!-- Message displayed when cart is empty -->
    <p>Tvůj košík je prázdný.</p>
<?php endif; ?>

<!-- Link back to products page -->
<p><a href="index.php?action=products" class="btn btn-secondary">← Zpět k produktům</a></p>

<?php require __DIR__ . '/partials/footer.php'; ?>

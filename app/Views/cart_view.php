<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Shopping cart heading -->
<h1 class="h3 mb-4"><i class="fas fa-shopping-cart"></i> Tvůj košík</h1>

<!-- Display shopping cart if it's not empty -->
<?php if (!empty($_SESSION['cart'])): ?>
    <?php
    // Calculate total for the cart
    $total = 0;
    $itemCount = 0;
    foreach ($_SESSION['cart'] as $id => $item) {
        $subtotal = ($item['price_cents'] / 100) * $item['quantity'];
        $total += $subtotal;
        $itemCount += $item['quantity'];
    }
    ?>
    
    <!-- Cart Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-6">
            <div class="summary-card">
                <div class="summary-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="summary-content">
                    <h6>Počet položek</h6>
                    <h3><?= (int)$itemCount ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="summary-card highlight">
                <div class="summary-icon">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="summary-content">
                    <h6>Celková cena</h6>
                    <h3><?= number_format($total, 2, ',', ' ') ?> Kč</h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cart Items Table -->
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <!-- Table header with column names -->
            <thead>
            <tr>
                <th><i class="fas fa-box"></i> Název</th>
                <th class="text-center"><i class="fas fa-tag"></i> Cena</th>
                <th class="text-center"><i class="fas fa-hashtag"></i> Množství</th>
                <th class="text-center"><i class="fas fa-calculator"></i> Celkem</th>
                <th class="text-center"><i class="fas fa-tools"></i> Akce</th>
            </tr>
            </thead>
            <!-- Table body with cart items -->
            <tbody>
            <?php foreach ($_SESSION['cart'] as $id => $item):
                // Calculate subtotal for each item
                $subtotal = ($item['price_cents'] / 100) * $item['quantity'];
                ?>
                <!-- Display each item in cart as a row -->
                <tr class="cart-item-row">
                    <!-- Product name -->
                    <td class="fw-600"><?= htmlspecialchars($item['name']) ?></td>
                    <!-- Unit price formatted as currency -->
                    <td class="text-center">
                        <span class="price-badge-small"><?= number_format($item['price_cents'] / 100, 2, ',', ' ') ?> Kč</span>
                    </td>
                    <!-- Quantity adjustment buttons -->
                    <td>
                        <div class="quantity-control">
                            <!-- Decrease quantity button -->
                            <form method="post" action="index.php?action=decrease_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-quantity" title="Snížit množství">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </form>

                            <!-- Display current quantity -->
                            <input type="number" value="<?= (int)$item['quantity'] ?>" disabled class="quantity-display">

                            <!-- Increase quantity button -->
                            <form method="post" action="index.php?action=increase_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" class="btn btn-sm btn-quantity" title="Zvýšit množství">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <!-- Subtotal for this item (price × quantity) -->
                    <td class="text-center">
                        <strong><?= number_format($subtotal, 2, ',', ' ') ?> Kč</strong>
                    </td>
                    <!-- Remove item from cart button -->
                    <td class="text-center">
                        <form method="post" action="index.php?action=remove_from_cart&id=<?= (int)$id ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="btn btn-sm btn-remove"
                                    onclick="return confirm('Opravdu smazat tento produkt z košíku?');"
                                    title="Odstranit z košíku">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Action Buttons -->
    <div class="cart-actions d-flex gap-3 flex-wrap">
        <a href="index.php?action=products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Pokračovat v nákupu
        </a>
        <form method="post" action="index.php?action=confirm_order" style="display:contents;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check-circle"></i> Potvrdit objednávku
            </button>
        </form>
    </div>

<?php else: ?>
    <!-- Message displayed when cart is empty -->
    <div class="alert alert-info text-center" style="padding: 3rem;">
        <i class="fas fa-inbox" style="font-size: 2rem; color: #667eea;"></i>
        <p class="mt-3 mb-0">Tvůj košík je prázdný.</p>
        <p class="text-muted">Přidej si některý z našich produktů!</p>
        <a href="index.php?action=products" class="btn btn-primary mt-3">
            <i class="fas fa-shopping-bag"></i> Prohlédnout produkty
        </a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

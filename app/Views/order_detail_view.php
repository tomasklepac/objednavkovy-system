<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Order detail heading with order ID -->
<h1 class="h3 mb-3">Detail objednávky #<?= htmlspecialchars($_GET['id'] ?? '') ?></h1>

<!-- Order header with customer info -->
<?php if (!empty($order)): ?>
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Zákazník</h5>
                    <p>
                        <strong>Jméno:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
                        <strong>E-mail:</strong> <?= htmlspecialchars($order['customer_email']) ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Doručovací adresa</h5>
                    <p>
                        <strong>Ulice:</strong> <?= htmlspecialchars($order['street']) ?><br>
                        <strong>Město:</strong> <?= htmlspecialchars($order['city']) ?><br>
                        <strong>PSČ:</strong> <?= htmlspecialchars($order['zip']) ?>
                    </p>
                </div>
            </div>
            <?php if (!empty($order['note'])): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <strong>Poznámka:</strong><br>
                        <p class="text-muted"><?= htmlspecialchars($order['note']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <hr>
            <div class="row">
                <div class="col-12">
                    <strong>Stav objednávky:</strong>
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
                            echo '<span class="badge bg-danger">Zrušeno</span>';
                            break;
                        case 'delivered':
                            echo '<span class="badge bg-success">Ukončeno</span>';
                            break;
                        default:
                            echo htmlspecialchars($order['status']);
                            break;
                    }
                    ?>
                    <br><br>
                    <strong>Datum vytvoření:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?><br>
                    <strong>Celková cena:</strong> <?= number_format($order['total_cents'] / 100, 2, ',', ' ') ?> Kč
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Display order items table if there are any items -->
<?php if (!empty($items)): ?>
    <h4 class="mb-3">Objednané položky</h4>
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <!-- Table headers -->
            <thead>
            <tr>
                <th class="text-center">Produkt</th>
                <th class="text-center">Množství</th>
                <th class="text-center">Cena/ks</th>
                <th class="text-center">Celkem</th>
            </tr>
            </thead>
            <!-- Loop through and display each order item -->
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <!-- Product name -->
                    <td class="text-center"><?= htmlspecialchars($item['name']) ?></td>
                    <!-- Ordered quantity -->
                    <td class="text-center"><?= (int)$item['quantity'] ?></td>
                    <!-- Unit price in CZK -->
                    <td class="text-center"><?= number_format($item['unit_price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Total for this line (price × quantity) -->
                    <td class="text-center"><?= number_format(($item['unit_price_cents'] * $item['quantity']) / 100, 2, ',', ' ') ?> Kč</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when order has no items -->
    <p>Tato objednávka neobsahuje žádné položky.</p>
<?php endif; ?>

<!-- Link back to orders list -->
<p class="mt-3">
    <a href="index.php?action=orders" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Zpět na seznam objednávek</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

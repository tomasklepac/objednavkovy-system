<?php require __DIR__ . '/partials/header.php'; ?>

<!-- My products heading -->
<h1 class="h3 mb-3">Moje produkty</h1>

<!-- Button to add new product -->
<p>
    <a href="index.php?action=add_product" class="btn btn-success">Přidat nový produkt</a>
</p>

<!-- Display products table if there are any products -->
<?php if (!empty($products)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <!-- Table headers -->
            <thead class="table-light">
            <tr>
                <th>Obrázek</th>
                <th>Název</th>
                <th>Popis</th>
                <th>Cena</th>
                <th>Skladem</th>
                <th>Akce</th>
            </tr>
            </thead>
            <!-- Loop through and display each product -->
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <!-- Display product image or placeholder text -->
                    <td>
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Obrázek produktu"
                                 style="max-height:80px;" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">Bez obrázku</span>
                        <?php endif; ?>
                    </td>
                    <!-- Product name -->
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <!-- Product description -->
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <!-- Product price in CZK -->
                    <td><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Stock quantity or sold out message -->
                    <td>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <?= (int)$product['stock'] ?> ks
                        <?php else: ?>
                            <span class="text-danger fw-bold">Vyprodáno</span>
                        <?php endif; ?>
                    </td>
                    <!-- Action buttons: edit and delete -->
                    <td class="d-flex gap-1">
                        <!-- Edit button -->
                        <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">Upravit</a>

                        <!-- Delete button with confirmation -->
                        <form method="post" action="index.php?action=delete_product&id=<?= (int)$product['id'] ?>" onsubmit="return confirm('Opravdu smazat tento produkt?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Smazat</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when no products exist -->
    <p>Nemáš zatím žádné produkty.</p>
<?php endif; ?>

<!-- Link back to dashboard -->
<p>
    <a href="index.php" class="btn btn-secondary">🏠 Zpět na dashboard</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

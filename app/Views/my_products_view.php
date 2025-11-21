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
                <th>Status</th>
                <th>Akce</th>
            </tr>
            </thead>
            <!-- Loop through and display each product -->
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <!-- Display product image or placeholder text -->
                    <td class="text-center">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Obrázek produktu"
                                 style="max-height:80px;" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">Bez obrázku</span>
                        <?php endif; ?>
                    </td>
                    <!-- Product name -->
                    <td class="text-center"><?= htmlspecialchars($product['name']) ?></td>
                    <!-- Product description -->
                    <td class="text-center"><?= htmlspecialchars($product['description']) ?></td>
                    <!-- Product price in CZK -->
                    <td class="text-center"><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <!-- Stock quantity or sold out message -->
                    <td class="text-center">
                        <?php if ((int)$product['stock'] > 0): ?>
                            <?= (int)$product['stock'] ?> ks
                        <?php else: ?>
                            <span class="text-danger fw-bold">Vyprodáno</span>
                        <?php endif; ?>
                    </td>
                    <!-- Product status: active or archived -->
                    <td class="text-center">
                        <?php if ($product['is_active']): ?>
                            <span class="badge bg-success">Aktivní</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Archivován</span>
                        <?php endif; ?>
                    </td>
                    <!-- Action buttons: edit, archive, reactivate -->
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <!-- Edit button -->
                            <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">Upravit</a>

                            <?php if ($product['is_active']): ?>
                                <!-- Archive button (only for active products) -->
                                <form method="post" action="index.php?action=delete_product" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Archivovat tento produkt?');">Archivovat</button>
                                </form>
                            <?php else: ?>
                                <!-- Reactivate button (only for archived products) -->
                                <a href="index.php?action=reactivate_product&id=<?= (int)$product['id'] ?>" 
                                   class="btn btn-sm btn-outline-success"
                                   onclick="return confirm('Reaktivovat tento produkt? Stock bude nastaven na 0.');">Reaktivovat</a>
                            <?php endif; ?>
                        </div>
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

<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Moje produkty</h1>

<p>
    <a href="index.php?action=add_product" class="btn btn-success">➕ Přidat nový produkt</a>
</p>

<?php if (!empty($products)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
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
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td>
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Obrázek produktu"
                                 style="max-height:80px;" class="img-thumbnail">
                        <?php else: ?>
                            <span class="text-muted">Bez obrázku</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</td>
                    <td>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <?= (int)$product['stock'] ?> ks
                        <?php else: ?>
                            <span class="text-danger fw-bold">Vyprodáno</span>
                        <?php endif; ?>
                    </td>
                    <td class="d-flex gap-1">
                        <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">✏ Upravit</a>

                        <form method="post" action="index.php?action=delete_product&id=<?= (int)$product['id'] ?>" onsubmit="return confirm('Opravdu smazat tento produkt?');">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">🗑 Smazat</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Nemáš zatím žádné produkty.</p>
<?php endif; ?>

<p>
    <a href="index.php" class="btn btn-secondary">🏠 Zpět na dashboard</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

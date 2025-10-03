<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Odkaz na přidání produktu (jen pro dodavatele nebo admina) -->
<?php if (!empty($_SESSION['roles']) && (in_array('supplier', $_SESSION['roles']) || in_array('admin', $_SESSION['roles']))): ?>
    <p class="mb-3">
        <a href="index.php?action=add_product" class="btn btn-success">➕ Přidat produkt</a>
    </p>
<?php endif; ?>

<h1 class="h3 mb-3">Seznam produktů</h1>

<?php if (!empty($products)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
            <tr>
                <th>Obrázek</th>
                <th>Název</th>
                <th>Popis</th>
                <th>Cena</th>
                <th>Skladem</th>
                <th>Dodavatel (ID)</th>
                <th>Akce</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <?php
                $isOwner = isset($_SESSION['user_id']) && ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
                $isAdmin = !empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true);
                $isCustomer = !empty($_SESSION['roles']) && in_array('customer', $_SESSION['roles'], true);
                ?>
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
                    <td><?= htmlspecialchars($product['supplier_id']) ?></td>
                    <td>
                        <?php if ($isOwner || $isAdmin): ?>
                            <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">✏ Upravit</a>
                            <a href="index.php?action=delete_product&id=<?= (int)$product['id'] ?>"
                               onclick="return confirm('Opravdu smazat tento produkt?');"
                               class="btn btn-sm btn-outline-danger">🗑 Smazat</a>
                        <?php endif; ?>

                        <?php if ($isCustomer): ?>
                            <?php if ((int)$product['stock'] > 0): ?>
                                <a href="index.php?action=add_to_cart&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-success">🛒 Přidat do košíku</a>
                            <?php else: ?>
                                <span class="text-muted">Nelze přidat</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Žádné produkty nejsou dostupné.</p>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

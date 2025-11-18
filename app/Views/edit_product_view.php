<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Edit product form heading -->
<h1 class="h3 mb-3">Upravit produkt</h1>

<!-- Form for editing an existing product -->
<form method="post" action="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" enctype="multipart/form-data" class="card p-3">
    <!-- CSRF token for security -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Product name input field with current value -->
    <div class="mb-3">
        <label for="name" class="form-label">Název:</label>
        <input type="text" id="name" name="name"
               value="<?= htmlspecialchars($product['name']) ?>"
               class="form-control" required>
    </div>

    <!-- Product description text area with current value -->
    <div class="mb-3">
        <label for="description" class="form-label">Popis:</label>
        <textarea id="description" name="description" rows="4" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <!-- Product price input with current value in CZK -->
    <div class="mb-3">
        <label for="price" class="form-label">Cena (Kč):</label>
        <input type="number" id="price" name="price" step="0.01"
               value="<?= number_format($product['price_cents'] / 100, 2, '.', '') ?>"
               class="form-control" required>
    </div>

    <!-- Stock quantity input with current value -->
    <div class="mb-3">
        <label for="stock" class="form-label">Skladem (ks):</label>
        <input type="number" id="stock" name="stock" min="0"
               value="<?= (int)$product['stock'] ?>"
               class="form-control" required>
    </div>

    <!-- Product image upload (optional, displays current image if exists) -->
    <div class="mb-3">
        <label for="image" class="form-label">Obrázek produktu:</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">

        <!-- Display current product image if it exists -->
        <?php if (!empty($product['image_path'])): ?>
            <div class="mt-2">
                <p>Aktuální obrázek:</p>
                <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Produkt" style="max-width:150px; height:auto;">
            </div>
        <?php endif; ?>
    </div>

    <!-- Save and cancel buttons -->
    <button type="submit" class="btn btn-primary">💾 Uložit změny</button>
    <a href="index.php?action=products" class="btn btn-secondary">← Zpět na produkty</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>

<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Add new product form heading -->
<h1 class="h3 mb-3">Přidat produkt</h1>

<!-- Form for adding a new product with image upload -->
<form method="post" action="index.php?action=add_product" enctype="multipart/form-data" class="card p-3">
    <!-- CSRF token for security -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Product name input field -->
    <div class="mb-3">
        <label for="name" class="form-label">Název:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <!-- Product description text area -->
    <div class="mb-3">
        <label for="description" class="form-label">Popis:</label>
        <textarea id="description" name="description" rows="4" class="form-control"></textarea>
    </div>

    <!-- Product price in CZK with decimal input -->
    <div class="mb-3">
        <label for="price" class="form-label">Cena (Kč):</label>
        <input type="number" id="price" name="price" step="0.01" class="form-control" required>
    </div>

    <!-- Stock quantity input -->
    <div class="mb-3">
        <label for="stock" class="form-label">Skladem (ks):</label>
        <input type="number" id="stock" name="stock" min="0" class="form-control" required>
    </div>

    <!-- Product image upload (accepts common image formats, max 2MB) -->
    <div class="mb-3">
        <label for="image" class="form-label">Obrázek produktu (jpg/png/webp, max 2 MB):</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>

    <!-- Submit and cancel buttons -->
    <button type="submit" class="btn btn-primary">Uložit produkt</button>
    <a href="index.php?action=products" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Zpět na produkty</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
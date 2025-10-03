<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Přidat produkt</h1>

<form method="post" action="index.php?action=add_product" enctype="multipart/form-data" class="card p-3">
    <!-- Název produktu -->
    <div class="mb-3">
        <label for="name" class="form-label">Název:</label>
        <input type="text" id="name" name="name" class="form-control" required>
    </div>

    <!-- Popis produktu -->
    <div class="mb-3">
        <label for="description" class="form-label">Popis:</label>
        <textarea id="description" name="description" rows="4" class="form-control"></textarea>
    </div>

    <!-- Cena produktu v Kč -->
    <div class="mb-3">
        <label for="price" class="form-label">Cena (Kč):</label>
        <input type="number" id="price" name="price" step="0.01" class="form-control" required>
    </div>

    <!-- Počet kusů skladem -->
    <div class="mb-3">
        <label for="stock" class="form-label">Skladem (ks):</label>
        <input type="number" id="stock" name="stock" min="0" class="form-control" required>
    </div>

    <!-- Obrázek produktu -->
    <div class="mb-3">
        <label for="image" class="form-label">Obrázek produktu (jpg/png/webp, max 2 MB):</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">Uložit produkt</button>
    <a href="index.php?action=products" class="btn btn-secondary">← Zpět na produkty</a>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>
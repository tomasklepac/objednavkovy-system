<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Order confirmation heading -->
<h1 class="h3 mb-3">Potvrzení objednávky</h1>

<!-- Form for entering delivery address and additional order information -->
<form method="post" class="card p-4 mx-auto" style="max-width: 500px;">
    <!-- CSRF token for security -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Street address and house number input -->
    <div class="mb-3">
        <label for="street" class="form-label">Ulice a č.p.:</label>
        <input type="text" id="street" name="street" class="form-control" required>
    </div>

    <!-- City/town input -->
    <div class="mb-3">
        <label for="city" class="form-label">Město:</label>
        <input type="text" id="city" name="city" class="form-control" required>
    </div>

    <!-- Postal code (5 digits) input -->
    <div class="mb-3">
        <label for="zip" class="form-label">PSČ:</label>
        <input type="text" id="zip" name="zip" pattern="\d{5}" title="Zadej 5 číslic" class="form-control" required>
    </div>

    <!-- Optional delivery note -->
    <div class="mb-3">
        <label for="note" class="form-label">Poznámka (nepovinné):</label>
        <textarea id="note" name="note" rows="2" class="form-control"></textarea>
    </div>

    <!-- Action buttons: back to cart or confirm order -->
    <div class="d-flex justify-content-between">
        <a href="index.php?action=view_cart" class="btn btn-outline-danger">❌ Zpět do košíku</a>
        <button type="submit" class="btn btn-success">✅ Potvrdit objednávku</button>
    </div>
</form>

<?php require __DIR__ . '/partials/footer.php'; ?>

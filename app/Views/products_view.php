<!-- Link to add product (only for suppliers or admins) -->
<?php if (!empty($_SESSION['roles']) && (in_array('supplier', $_SESSION['roles']) || in_array('admin', $_SESSION['roles']))): ?>
    <div class="mb-4">
        <a href="index.php?action=add_product" class="btn btn-success">
            <i class="fas fa-plus"></i> Přidat produkt
        </a>
    </div>
<?php endif; ?>

<h1 class="h3 mb-4"><i class="fas fa-boxes"></i> Produkty</h1>

<?php if (!empty($products)): ?>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <?php
            // Check user role permissions
            $isOwner = isset($_SESSION['user_id']) && ((int)$product['supplier_id'] === (int)$_SESSION['user_id']);
            $isAdmin = !empty($_SESSION['roles']) && in_array('admin', $_SESSION['roles'], true);
            $isCustomer = !empty($_SESSION['roles']) && in_array('customer', $_SESSION['roles'], true);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card product-card h-100">
                    <!-- Product Image -->
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img-top product-image">
                        <?php else: ?>
                            <div class="card-img-top product-image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($product['description']) ?></p>
                        
                        <!-- Price and Stock -->
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="price-badge">
                                    <strong><?= number_format($product['price_cents'] / 100, 2, ',', ' ') ?> Kč</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <?php if ((int)$product['stock'] > 0): ?>
                                    <div class="stock-badge stock-available">
                                        <i class="fas fa-check-circle"></i> <?= (int)$product['stock'] ?> ks
                                    </div>
                                <?php else: ?>
                                    <div class="stock-badge stock-unavailable">
                                        <i class="fas fa-times-circle"></i> Vyprodáno
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if ($isOwner || $isAdmin): ?>
                                <a href="index.php?action=edit_product&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-edit"></i> Upravit
                                </a>

                                <form method="post" action="index.php?action=delete_product" style="display:contents;">
                                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger flex-grow-1"
                                            onclick="return confirm('Opravdu archivovat tento produkt?');">
                                        <i class="fas fa-trash"></i> Archivovat
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($isCustomer): ?>
                                <?php if ((int)$product['stock'] > 0): ?>
                                    <form method="post" action="index.php?action=add_to_cart" style="display:contents;">
                                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success flex-grow-1">
                                            <i class="fas fa-cart-plus"></i> Přidat do košíku
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="btn btn-sm btn-secondary disabled w-100">
                                        <i class="fas fa-ban"></i> Vyprodáno
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Žádné produkty nejsou dostupné.
    </div>
<?php endif; ?>

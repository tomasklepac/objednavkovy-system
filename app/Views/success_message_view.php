<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Success Message Container -->
<div class="success-container">
    <div class="success-card">
        <!-- Icon -->
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <!-- Title -->
        <h1 class="success-title"><?= htmlspecialchars($title ?? 'Operace byla úspěšná!') ?></h1>
        
        <!-- Message -->
        <p class="success-message"><?= htmlspecialchars($message ?? '') ?></p>
        
        <!-- Details (if provided) -->
        <?php if (!empty($details)): ?>
            <div class="success-details">
                <?php foreach ($details as $label => $value): ?>
                    <div class="detail-row">
                        <span class="detail-label"><?= htmlspecialchars($label) ?>:</span>
                        <span class="detail-value"><?= htmlspecialchars($value) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="success-actions">
            <?php if (!empty($actions)): ?>
                <?php foreach ($actions as $label => $url): ?>
                    <a href="<?= htmlspecialchars($url) ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-right"></i> <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

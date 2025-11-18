<?php require __DIR__ . '/partials/header.php'; ?>

<!-- User management heading -->
<h1 class="h3 mb-3">Správa uživatelů</h1>

<!-- Display users table if there are any users -->
<?php if (!empty($users)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <!-- Table headers -->
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Stav</th>
                <th>Akce</th>
            </tr>
            </thead>
            <!-- Loop through and display each user -->
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <!-- User ID -->
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <!-- User email address -->
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <!-- User roles -->
                    <td><?= htmlspecialchars($user['roles']) ?></td>
                    <!-- User account status -->
                    <td>
                        <!-- Display active or blocked/pending status -->
                        <?php if ($user['is_active']): ?>
                            <span class="text-success fw-bold">Aktivní</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Blokován / Čeká</span>
                        <?php endif; ?>
                    </td>
                    <!-- Action buttons: block/approve user -->
                    <td>
                        <!-- Prevent admin users from being blocked -->
                        <?php if (strpos($user['roles'], 'admin') !== false): ?>
                            <span class="text-muted">Nelze blokovat admina</span>
                        <?php else: ?>
                            <!-- Block active user button -->
                            <?php if ($user['is_active']): ?>
                                <form method="post" action="index.php?action=block_user&id=<?= (int)$user['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">🚫 Blokovat</button>
                                </form>
                            <!-- Approve blocked/pending user button -->
                            <?php else: ?>
                                <form method="post" action="index.php?action=approve_user&id=<?= (int)$user['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">✅ Schválit</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <!-- Message when no users found -->
    <p>Žádní uživatelé k zobrazení.</p>
<?php endif; ?>

<!-- Link back to home page -->
<p class="mt-3">
    <a href="index.php" class="btn btn-secondary">← Zpět na hlavní stránku</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

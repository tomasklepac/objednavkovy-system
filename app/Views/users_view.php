<?php require __DIR__ . '/partials/header.php'; ?>

<h1 class="h3 mb-3">Správa uživatelů</h1>

<?php if (!empty($users)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Role</th>
                <th>Stav</th>
                <th>Akce</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['roles']) ?></td>
                    <td>
                        <?php if ($user['is_active']): ?>
                            <span class="text-success fw-bold">Aktivní</span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">Blokován / Čeká</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (strpos($user['roles'], 'admin') !== false): ?>
                            <span class="text-muted">Nelze blokovat admina</span>
                        <?php else: ?>
                            <?php if ($user['is_active']): ?>
                                <a href="index.php?action=block_user&id=<?= (int)$user['id'] ?>"
                                   class="btn btn-sm btn-outline-danger">🚫 Blokovat</a>
                            <?php else: ?>
                                <a href="index.php?action=approve_user&id=<?= (int)$user['id'] ?>"
                                   class="btn btn-sm btn-success">✅ Schválit</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>Žádní uživatelé k zobrazení.</p>
<?php endif; ?>

<p class="mt-3">
    <a href="index.php" class="btn btn-secondary">← Zpět na hlavní stránku</a>
</p>

<?php require __DIR__ . '/partials/footer.php'; ?>

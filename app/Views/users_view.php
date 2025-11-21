<?php require __DIR__ . '/partials/header.php'; ?>

<!-- User management heading -->
<h1 class="h3 mb-4"><i class="fas fa-users"></i> Správa uživatelů</h1>

<!-- Display users table if there are any users -->
<?php if (!empty($users)): ?>
    <!-- Summary Stats -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon"><i class="fas fa-user"></i></div>
                <div class="summary-content">
                    <h6>Celkem uživatelů</h6>
                    <h3><?= count($users) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card highlight">
                <div class="summary-icon"><i class="fas fa-check-circle"></i></div>
                <div class="summary-content">
                    <h6>Aktivní</h6>
                    <h3><?= count(array_filter($users, fn($u) => $u['is_active'])) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card">
                <div class="summary-icon"><i class="fas fa-ban"></i></div>
                <div class="summary-content">
                    <h6>Blokováno/Čeká</h6>
                    <h3><?= count(array_filter($users, fn($u) => !$u['is_active'])) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-responsive cart-table-wrapper mb-4">
        <table class="table cart-table align-middle">
            <!-- Table headers -->
            <thead>
            <tr>
                <th><i class="fas fa-hashtag"></i> ID</th>
                <th class="text-center"><i class="fas fa-envelope"></i> Email</th>
                <th class="text-center"><i class="fas fa-shield-alt"></i> Role</th>
                <th class="text-center"><i class="fas fa-circle"></i> Stav</th>
                <th class="text-center"><i class="fas fa-tools"></i> Akce</th>
            </tr>
            </thead>
            <!-- Loop through and display each user -->
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr class="cart-item-row">
                    <!-- User ID -->
                    <td class="fw-600">#<?= htmlspecialchars($user['id']) ?></td>
                    <!-- User email address -->
                    <td class="text-center">
                        <small><?= htmlspecialchars($user['email']) ?></small>
                    </td>
                    <!-- User roles -->
                    <td class="text-center">
                        <?php
                        $roles = explode(',', $user['roles']);
                        foreach ($roles as $role) {
                            $role = trim($role);
                            $roleConfig = [
                                'admin' => ['badge bg-danger', 'Admin'],
                                'supplier' => ['badge bg-primary', 'Dodavatel'],
                                'dodavatel' => ['badge bg-primary', 'Dodavatel'],
                                'customer' => ['badge bg-success', 'Zákazník'],
                                'zakaznik' => ['badge bg-success', 'Zákazník'],
                            ];
                            $config = $roleConfig[strtolower($role)] ?? ['badge bg-secondary', $role];
                            echo '<span class="' . $config[0] . '">' . $config[1] . '</span> ';
                        }
                        ?>
                    </td>
                    <!-- User account status -->
                    <td class="text-center">
                        <!-- Display active or blocked/pending status -->
                        <?php if ($user['is_active']): ?>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle"></i> Aktivní
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock"></i> Čeká/Blokován
                            </span>
                        <?php endif; ?>
                    </td>
                    <!-- Action buttons: block/approve user -->
                    <td class="text-center">
                        <!-- Prevent admin users from being blocked -->
                        <?php if (strpos($user['roles'], 'admin') !== false): ?>
                            <span class="text-muted" title="Admina nelze blokovat">–</span>
                        <?php else: ?>
                            <!-- Block active user button -->
                            <?php if ($user['is_active']): ?>
                                <form method="post" action="index.php?action=block_user&id=<?= (int)$user['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-remove" title="Blokovat uživatele">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            <!-- Approve blocked/pending user button -->
                            <?php else: ?>
                                <form method="post" action="index.php?action=approve_user&id=<?= (int)$user['id'] ?>" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <button type="submit" class="btn btn-sm btn-quantity" style="background:#2dce89; color:white;" title="Schválit uživatele">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mb-3">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Zpět na dashboard
        </a>
    </div>
<?php else: ?>
    <!-- Message when no users found -->
    <div class="alert alert-info text-center" style="padding: 3rem;">
        <i class="fas fa-inbox" style="font-size: 2rem; color: #667eea;"></i>
        <p class="mt-3 mb-0">Žádní uživatelé k zobrazení.</p>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>

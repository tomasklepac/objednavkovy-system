<?php require __DIR__ . '/partials/header.php'; ?>

<div class="container mt-4">
    <h1 class="display-5 mb-4">
        <i class="fas fa-shield-alt"></i> SuperAdmin Panel
    </h1>

    <!-- Pending Admin Requests Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fas fa-hourglass-half"></i> Čekající žádosti o admin práva (<?= count($pendingRequests) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($pendingRequests)): ?>
                <p class="text-muted mb-0">Žádné čekající žádosti</p>
            <?php else: ?>
                <div class="table-responsive cart-table-wrapper">
                    <table class="table table-hover cart-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Email</th>
                                <th class="text-center">Jméno</th>
                                <th class="text-center">Datum žádosti</th>
                                <th class="text-center">Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $request): ?>
                                <tr class="cart-item-row">
                                    <td class="text-center">
                                        <small><?= htmlspecialchars($request['email']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <small><?= htmlspecialchars($request['name']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <small><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- Approve Button -->
                                            <form method="POST" action="index.php?action=approve_admin" style="display:inline;">
                                                <input type="hidden" name="admin_id" value="<?= $request['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Reject Button -->
                                            <form method="POST" action="index.php?action=reject_admin" style="display:inline;" 
                                                  onsubmit="return confirm('Opravdu chceš odmítnout tuto žádost?');">
                                                <input type="hidden" name="admin_id" value="<?= $request['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Approved Admins Section -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white fw-bold">
            <i class="fas fa-users-cog"></i> Schválení admini (<?= count($adminUsers) ?>)
        </div>
        <div class="card-body">
            <?php if (empty($adminUsers)): ?>
                <p class="text-muted mb-0">Žádní admini zatím nejsou schváleni</p>
            <?php else: ?>
                <div class="table-responsive cart-table-wrapper">
                    <table class="table table-hover cart-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">Email</th>
                                <th class="text-center">Jméno</th>
                                <th class="text-center">Stav</th>
                                <th class="text-center">Schválení</th>
                                <th class="text-center">Datum vytvoření</th>
                                <th class="text-center">Akce</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($adminUsers as $admin): ?>
                                <tr class="cart-item-row">
                                    <td class="text-center">
                                        <small><?= htmlspecialchars($admin['email']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <small><?= htmlspecialchars($admin['name']) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ((int)$admin['is_active'] === 1): ?>
                                            <span class="badge bg-success">Aktivní</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Blokovaný</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ((int)$admin['is_approved'] === 1): ?>
                                            <span class="badge bg-success">Schváleno</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Čeká na schválení</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <small><?= date('d.m.Y H:i', strtotime($admin['created_at'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                                            <?php if ((int)$admin['is_active'] === 1): ?>
                                                <!-- Block Admin Button -->
                                                <form method="POST" action="index.php?action=block_admin" style="display:inline;" 
                                                      onsubmit="return confirm('Opravdu chceš blokovat tohoto admina?');">
                                                    <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning" title="Block">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <!-- Unblock Admin Button -->
                                                <form method="POST" action="index.php?action=unblock_admin" style="display:inline;">
                                                    <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-info" title="Unblock">
                                                        <i class="fas fa-unlock"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back to Dashboard Link -->
    <div class="mt-4 text-center">
        <a href="index.php?action=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Zpět na dashboard
        </a>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>

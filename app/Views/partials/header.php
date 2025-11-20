<!-- HTML document structure with Bootstrap framework -->
<!doctype html>
<html lang="cs">
<head>
    <!-- Character encoding for proper text rendering -->
    <meta charset="utf-8">
    <!-- Viewport settings for responsive design on mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Page title that appears in browser tab -->
    <title><?= htmlspecialchars($title ?? 'Objednávkový systém') ?></title>

    <!-- Bootstrap CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom application styles (minimal overrides) -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-1">
    <div class="container-fluid">
        <!-- Logo/Brand -->
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fas fa-shopping-cart"></i> Objednávkový Systém
        </a>
        <!-- Navbar toggle for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navigation items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=logout">
                            <i class="fas fa-sign-out-alt"></i> Odhlásit se
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=login">
                            <i class="fas fa-sign-in-alt"></i> Přihlášení
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main container for page content -->
<main class="container py-4">

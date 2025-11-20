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
    <!-- Custom application styles (minimal overrides) -->
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
<!-- Main container for page content -->
<main class="container py-4">

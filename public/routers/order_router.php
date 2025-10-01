<?php
$action = $_GET['action'] ?? null;

require_once __DIR__ . '/../../app/Controllers/order_controller.php';

$pdo = Database::getInstance();

$orderController = new order_controller($pdo);

// Zákazník potvrzuje objednávku
if ($action === 'confirm_order') {
    if (empty($_SESSION['user_id']) || empty($_SESSION['roles']) || !in_array('customer', $_SESSION['roles'], true)) {
        echo "<p style='color:red'>Jen zákazníci mohou potvrdit objednávku.</p>";
        exit;
    }

    if (empty($_SESSION['cart'])) {
        echo "<p style='color:red'>Košík je prázdný, nemůžeš vytvořit objednávku.</p>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $address = trim($_POST['address'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if ($address === '') {
            echo "<p style='color:red'>Musíš zadat adresu!</p>";
            require __DIR__ . '/../../app/Views/confirm_order_view.php';
            exit;
        }

        // Výpočet ceny a vytvoření objednávky
        $totalCents = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalCents += $item['price_cents'] * $item['quantity'];
        }

        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, status, delivery_address, note, total_cents, created_at) VALUES (?, 'pending', ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $address,
            $note,
            $totalCents
        ]);

        $orderId = $pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO order_item (order_id, product_id, quantity, unit_price_cents) VALUES (?, ?, ?, ?)");

        foreach ($_SESSION['cart'] as $productId => $item) {
            $itemStmt->execute([
                $orderId,
                $productId,
                $item['quantity'],
                $item['price_cents']
            ]);
        }

        unset($_SESSION['cart']);

        echo "<p style='color:green'>Objednávka byla úspěšně vytvořena!</p>";
        echo "<p><a href='index.php'>Zpět na produkty</a></p>";
        exit;
    } else {
        require __DIR__ . '/../../app/Views/confirm_order_view.php';
        exit;
    }
}

// Zákazník nebo admin si prohlíží objednávky
if ($action === 'orders') {
    if (in_array('admin', $_SESSION['roles'], true)) {
        $orders = $orderController->getAllOrders();
    } else {
        $orders = $orderController->getOrdersByCustomer($_SESSION['user_id']);
    }

    require __DIR__ . '/../../app/Views/orders_view.php';
    exit;
}

// Admin mění stav objednávky
if ($action === 'update_order' && isset($_GET['id'], $_GET['status'])) {
    if (!in_array('admin', $_SESSION['roles'], true)) {
        echo "<p style='color:red'>Nemáš oprávnění měnit stav objednávky.</p>";
        exit;
    }

    $orderController->updateStatus((int)$_GET['id'], $_GET['status']);
    header("Location: index.php?action=orders");
    exit;
}

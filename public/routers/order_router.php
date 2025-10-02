<?php
// public/routers/order_router.php
// ----------------------------------------------
// Router pro správu objednávek
// Zákazník: vytváří objednávky, vidí své
// Admin: spravuje všechny, mění stavy
// Dodavatel: vidí objednávky obsahující jeho produkty
// ----------------------------------------------

require_once __DIR__ . '/../../app/Controllers/order_controller.php';
require_once __DIR__ . '/../../config/db.php';

$pdo = Database::getInstance();
$orderController = new order_controller($pdo);

// Získaná akce
$action = $_GET['action'] ?? null;

switch ($action) {

    // ------------------------------------------------
    // 1. Vytvoření objednávky z košíku (jen zákazník)
    // ------------------------------------------------
    case 'confirm_order':
        if (empty($_SESSION['user_id']) || !in_array('customer', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Jen zákazníci mohou vytvořit objednávku.</p>";
            exit;
        }

        if (empty($_SESSION['cart'])) {
            echo "<p style='color:red'>Košík je prázdný, nemůžeš vytvořit objednávku.</p>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $street = trim($_POST['street'] ?? '');
            $city   = trim($_POST['city'] ?? '');
            $zip    = trim($_POST['zip'] ?? '');
            $note   = trim($_POST['note'] ?? '');

            // validace adresy
            if ($street === '' || $city === '' || $zip === '') {
                echo "<p style='color:red'>Musíš vyplnit všechny údaje adresy!</p>";
                require __DIR__ . '/../../app/Views/confirm_order_view.php';
                exit;
            }

            // celková cena
            $totalCents = 0;
            foreach ($_SESSION['cart'] as $item) {
                $totalCents += $item['price_cents'] * $item['quantity'];
            }

            // vytvoření objednávky
            $stmt = $pdo->prepare("
                INSERT INTO orders (customer_id, status, street, city, zip, note, total_cents, created_at)
                VALUES (?, 'pending', ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $street,
                $city,
                $zip,
                $note,
                $totalCents
            ]);

            $orderId = $pdo->lastInsertId();

            // vložení položek objednávky
            $itemStmt = $pdo->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, unit_price_cents)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($_SESSION['cart'] as $productId => $item) {
                $itemStmt->execute([$orderId, $productId, $item['quantity'], $item['price_cents']]);
            }

            // vyčištění košíku
            unset($_SESSION['cart']);

            echo "<p style='color:green'>Objednávka byla úspěšně vytvořena!</p>";
            echo "<p><a href='index.php'>🏠 Zpět na hlavní stránku</a></p>";
            echo "<p><a href='index.php?action=orders'>📦 Zobrazit moje objednávky</a></p>";
            exit;
        }

        // GET → zobrazit formulář
        require __DIR__ . '/../../app/Views/confirm_order_view.php';
        exit;

    // ------------------------------------------------
    // 2. Výpis objednávek
    // ------------------------------------------------
    case 'orders':
        if (in_array('admin', $_SESSION['roles'] ?? [], true)) {
            $orders = $orderController->getAllOrders(); // admin → všechny
        } else {
            $orders = $orderController->getOrdersByCustomer($_SESSION['user_id']); // zákazník → jen své
        }
        require __DIR__ . '/../../app/Views/orders_view.php';
        exit;

    // ------------------------------------------------
    // 3. Detail objednávky (všechny položky)
    // ------------------------------------------------
    case 'order_detail':
        $orderId = (int)($_GET['id'] ?? 0);
        $items   = $orderController->getOrderItems($orderId);

        if (!$items) {
            echo "<p style='color:red'>Objednávka #$orderId nemá žádné položky nebo neexistuje.</p>";
            echo "<p><a href='index.php?action=orders'>Zpět na objednávky</a></p>";
            exit;
        }

        require __DIR__ . '/../../app/Views/order_detail_view.php';
        exit;

    // ------------------------------------------------
    // 4. Admin potvrzuje objednávku (odečte sklad)
    // ------------------------------------------------
    case 'confirm_admin_order':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Nemáš oprávnění potvrdit objednávku.</p>";
            exit;
        }

        $orderId = (int)($_GET['id'] ?? 0);
        try {
            $orderController->confirmOrder($orderId);
            echo "<p style='color:green'>Objednávka #$orderId byla potvrzena a sklad aktualizován.</p>";
        } catch (Exception $e) {
            echo "<p style='color:red'>Chyba: {$e->getMessage()}</p>";
        }
        echo "<p><a href='index.php?action=orders'>Zpět na objednávky</a></p>";
        exit;

    // ------------------------------------------------
    // 5. Admin mění stav objednávky
    // ------------------------------------------------
    case 'update_order':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Nemáš oprávnění měnit stav objednávky.</p>";
            exit;
        }

        if (isset($_GET['id'], $_GET['status'])) {
            $orderController->updateStatus((int)$_GET['id'], $_GET['status']);
        }
        header("Location: index.php?action=orders");
        exit;

    // ------------------------------------------------
    // 6. Dodavatel – seznam objednávek jeho produktů
    // ------------------------------------------------
    case 'supplier_orders':
        if (!in_array('supplier', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Nemáš oprávnění zobrazit objednávky dodavatele.</p>";
            exit;
        }
        $orders = $orderController->getOrdersBySupplier((int)$_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/supplier_orders_view.php';
        exit;

    // ------------------------------------------------
    // 7. Dodavatel – detail objednávky (jen jeho položky)
    // ------------------------------------------------
    case 'supplier_order_detail':
        if (!in_array('supplier', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Nemáš oprávnění zobrazit tento detail.</p>";
            exit;
        }

        $orderId  = (int)($_GET['id'] ?? 0);
        $items    = $orderController->getSupplierOrderItems($orderId, (int)$_SESSION['user_id']);
        $customer = $orderController->getOrderCustomer($orderId);

        if (!$items) {
            echo "<p>Tahle objednávka neobsahuje žádné tvoje produkty.</p>";
            echo "<p><a href='index.php?action=supplier_orders'>Zpět na objednávky mých produktů</a></p>";
            exit;
        }

        require __DIR__ . '/../../app/Views/supplier_order_detail_view.php';
        exit;

    // ------------------------------------------------
    // Neznámá akce
    // ------------------------------------------------
    default:
        echo "<p>Neplatná akce.</p>";
        exit;
}

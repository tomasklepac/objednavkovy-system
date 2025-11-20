<?php
// public/routers/order_router.php
// ----------------------------------------------
// Router for order management
// Customer: creates orders, sees their own
// Admin: manages all, changes statuses
// Supplier: sees orders containing their products
// ----------------------------------------------

require_once __DIR__ . '/../../app/Controllers/order_controller.php';

$orderController = new order_controller();

// Get action
$action = $_GET['action'] ?? null;

switch ($action) {

    // ------------------------------------------------
    // 1. Create order from cart (customer only)
    // ------------------------------------------------
    case 'confirm_order':
        if (empty($_SESSION['user_id']) || !in_array('customer', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>Only customers can create an order.</p>";
            exit;
        }

        if (empty($_SESSION['cart'])) {
            echo "<p style='color:red'>Cart is empty, you cannot create an order.</p>";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $street = trim($_POST['street'] ?? '');
            $city   = trim($_POST['city'] ?? '');
            $zip    = trim($_POST['zip'] ?? '');
            $note   = trim($_POST['note'] ?? '');

            // address validation
            if ($street === '' || $city === '' || $zip === '') {
                echo "<p style='color:red'>You must fill in all address fields!</p>";
                require __DIR__ . '/../../app/Views/confirm_order_view.php';
                exit;
            }

            // total price
            $totalCents = 0;
            foreach ($_SESSION['cart'] as $item) {
                $totalCents += $item['price_cents'] * $item['quantity'];
            }

            // create order
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

            // insert order items
            $itemStmt = $pdo->prepare("
                INSERT INTO order_item (order_id, product_id, quantity, unit_price_cents)
                VALUES (?, ?, ?, ?)
            ");
            foreach ($_SESSION['cart'] as $productId => $item) {
                $itemStmt->execute([$orderId, $productId, $item['quantity'], $item['price_cents']]);
            }

            // Deduct stock immediately when order is placed
            try {
                $orderController->confirmOrder($orderId);
            } catch (Exception $e) {
                // If stock deduction fails, we still have the order record
                // Admin will see the error when trying to process it
                echo "<p style='color:orange'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
            }

            // clear cart
            unset($_SESSION['cart']);

            echo "<p style='color:green'>Order was successfully created!</p>";
            echo "<p><a href='index.php'>Back to main page</a></p>";
            echo "<p><a href='index.php?action=orders'>Show my orders</a></p>";
            exit;
        }

        // GET → show form
        require __DIR__ . '/../../app/Views/confirm_order_view.php';
        exit;

    // ------------------------------------------------
    // 2. List orders
    // ------------------------------------------------
    case 'orders':
        if (in_array('admin', $_SESSION['roles'] ?? [], true)) {
            $orders = $orderController->getAllOrders(); // admin → all
        } else {
            $orders = $orderController->getOrdersByCustomer($_SESSION['user_id']); // customer → only their own
        }
        require __DIR__ . '/../../app/Views/orders_view.php';
        exit;

    // ------------------------------------------------
    // 3. Order detail (all items)
    // ------------------------------------------------
    case 'order_detail':
        $orderId = (int)($_GET['id'] ?? 0);
        $order = $orderController->getOrderWithCustomer($orderId);
        $items = $orderController->getOrderItems($orderId);

        if (!$order || !$items) {
            echo "<p style='color:red'>Order #$orderId not found or has no items.</p>";
            echo "<p><a href='index.php?action=orders'>Back to orders</a></p>";
            exit;
        }

        require __DIR__ . '/../../app/Views/order_detail_view.php';
        exit;

    // ------------------------------------------------
    // 4. Admin confirms order (deducts stock)
    // ------------------------------------------------
    case 'confirm_admin_order':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to confirm order.</p>";
            exit;
        }

        $orderId = (int)($_GET['id'] ?? 0);
        try {
            $orderController->confirmPendingOrder($orderId);
            echo "<p style='color:green'>Order #$orderId was confirmed.</p>";
        } catch (Exception $e) {
            echo "<p style='color:red'>Error: {$e->getMessage()}</p>";
        }
        echo "<p><a href='index.php?action=orders'>Back to orders</a></p>";
        exit;

    // ------------------------------------------------
    // 5. Admin changes order status
    // ------------------------------------------------
    case 'update_order':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to change order status.</p>";
            exit;
        }

        if (isset($_GET['id'], $_GET['status'])) {
            $orderController->updateStatus((int)$_GET['id'], $_GET['status']);
        }
        header("Location: index.php?action=orders");
        exit;

    // ------------------------------------------------
    // 5b. Admin marks order as shipped
    // ------------------------------------------------
    case 'mark_shipped':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to change order status.</p>";
            exit;
        }

        $orderId = (int)($_GET['id'] ?? 0);
        $orderController->markShipped($orderId);
        header("Location: index.php?action=orders");
        exit;

    // ------------------------------------------------
    // 5c. Admin marks order as completed/delivered
    // ------------------------------------------------
    case 'mark_completed':
        if (!in_array('admin', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to change order status.</p>";
            exit;
        }

        $orderId = (int)($_GET['id'] ?? 0);
        $orderController->markDelivered($orderId);
        header("Location: index.php?action=orders");
        exit;

    // ------------------------------------------------
    // 6. Supplier – list of orders for their products
    // ------------------------------------------------
    case 'supplier_orders':
        if (!in_array('supplier', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to view supplier orders.</p>";
            exit;
        }
        $orders = $orderController->getOrdersBySupplier((int)$_SESSION['user_id']);
        require __DIR__ . '/../../app/Views/supplier_orders_view.php';
        exit;

    // ------------------------------------------------
    // 7. Supplier – order detail (only their items)
    // ------------------------------------------------
    case 'supplier_order_detail':
        if (!in_array('supplier', $_SESSION['roles'] ?? [], true)) {
            echo "<p style='color:red'>You don't have permission to view this detail.</p>";
            exit;
        }

        $orderId  = (int)($_GET['id'] ?? 0);
        $items    = $orderController->getSupplierOrderItems($orderId, (int)$_SESSION['user_id']);
        $customer = $orderController->getOrderCustomer($orderId);

        if (!$items) {
            echo "<p>This order doesn't contain any of your products.</p>";
            echo "<p><a href='index.php?action=supplier_orders'>Back to my product orders</a></p>";
            exit;
        }

        require __DIR__ . '/../../app/Views/supplier_order_detail_view.php';
        exit;

    // ------------------------------------------------
    // Unknown action
    // ------------------------------------------------
    default:
        echo "<p>Invalid action.</p>";
        exit;
}

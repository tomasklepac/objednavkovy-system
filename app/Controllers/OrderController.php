<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Config\Database;

/**
 * Controller for working with orders.
 * Handles order management from perspectives of admin, customer, and supplier.
 * Works with OrderModel for database operations.
 */
class OrderController {

    // ================================================================
    // ADMIN
    // ================================================================

    /**
     * Returns all orders for admin view.
     *
     * @return array[] List of all orders with customer information
     */
    public function getAllOrders(): array {
        return OrderModel::getAllOrders();
    }

    // ================================================================
    // CUSTOMER
    // ================================================================

    /**
     * Returns all orders for a specific customer.
     *
     * @param int $customerId Customer ID
     * @return array[] List of customer's orders
     */
    public function getOrdersByCustomer(int $customerId): array {
        return OrderModel::getOrdersByCustomer($customerId);
    }

    /**
     * Returns all line items in a specific order.
     *
     * @param int $orderId Order ID
     * @return array[] List of order items with product names
     */
    public function getOrderItems(int $orderId): array {
        return OrderModel::getOrderItems($orderId);
    }

    /**
     * Returns complete order information with customer details.
     *
     * @param int $orderId Order ID
     * @return array|null Order data with customer info
     */
    public function getOrderWithCustomer(int $orderId): ?array {
        return OrderModel::getOrderWithCustomer($orderId);
    }

    // ================================================================
    // ORDER STATUS CHANGES
    // ================================================================

    /**
     * Updates the status of an order.
     *
     * @param int $orderId Order ID
     * @param string $status New status
     * @return void
     */
    public function updateStatus(int $orderId, string $status): void {
        OrderModel::updateStatus($orderId, $status);
    }

    /**
     * Deducts stock for a new order.
     * Checks if sufficient stock exists before committing.
     *
     * @param int $orderId Order ID
     * @return void
     * @throws Exception If stock is insufficient
     */
    public function deductStock(int $orderId): void {
        OrderModel::deductStock($orderId);
    }

    /**
     * Admin confirms a pending order (stock already deducted).
     * Simply changes status to confirmed.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public function confirmPendingOrder(int $orderId): void {
        OrderModel::confirmPendingOrder($orderId);
    }

    /**
     * Marks an order as shipped.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public function markShipped(int $orderId): void {
        OrderModel::markShipped($orderId);
    }

    /**
     * Marks an order as delivered.
     *
     * @param int $orderId Order ID
     * @return void
     */
    public function markDelivered(int $orderId): void {
        OrderModel::markDelivered($orderId);
    }

    /**
     * Cancels an order and refunds stock.
     *
     * @param int $orderId Order ID
     * @return void
     * @throws Exception If cancellation fails
     */
    public function cancelOrder(int $orderId): void {
        OrderModel::cancelOrder($orderId);
    }

    // ================================================================
    // SUPPLIER
    // ================================================================

    /**
     * Returns all orders containing products from a specific supplier.
     *
     * @param int $supplierId Supplier ID
     * @return array[] List of orders with supplier's products
     */
    public function getOrdersBySupplier(int $supplierId): array {
        return OrderModel::getOrdersBySupplier($supplierId);
    }

    /**
     * Returns items in an order that belong to a specific supplier.
     *
     * @param int $orderId Order ID
     * @param int $supplierId Supplier ID
     * @return array[] List of order items from the supplier
     */
    public function getSupplierOrderItems(int $orderId, int $supplierId): array {
        return OrderModel::getSupplierOrderItems($orderId, $supplierId);
    }

    /**
     * Returns customer information for an order.
     *
     * @param int $orderId Order ID
     * @return array|null Customer data or null if not found
     */
    public function getOrderCustomer(int $orderId): ?array {
        return OrderModel::getOrderCustomer($orderId);
    }
}

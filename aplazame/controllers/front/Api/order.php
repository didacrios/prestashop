<?php
/**
 * This file is part of the official Aplazame module for PrestaShop.
 *
 * @author    Aplazame <soporte@aplazame.com>
 * @copyright 2015-2016 Aplazame
 * @license   see file: LICENSE
 */

final class AplazameApiOrder
{
    /**
     * @var Db
     */
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;
    }

    public function history(array $params)
    {
        if (!isset($params['order_id'])) {
            return AplazameApiModuleFrontController::notFound();
        }

        $orderId = Order::getOrderByCartId($params['order_id']);
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            return AplazameApiModuleFrontController::notFound();
        }

        $orders = $this->db->executeS(
            'SELECT id_order FROM ' . _DB_PREFIX_ . 'orders'
            . ' WHERE id_customer = ' . (int) $order->id_customer
        );

        $historyOrders = array();

        foreach ($orders as $orderData) {
            $historyOrders[] = Aplazame_Aplazame_Api_BusinessModel_HistoricalOrder::createFromOrder(new Order($orderData['id_order']));
        }

        return AplazameApiModuleFrontController::success(Aplazame_Sdk_Serializer_JsonSerializer::serializeValue($historyOrders));
    }
}

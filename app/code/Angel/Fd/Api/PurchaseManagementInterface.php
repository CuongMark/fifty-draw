<?php


namespace Angel\Fd\Api;

interface PurchaseManagementInterface
{

    /**
     * POST for purchase api
     * @param int $product
     * @param int $qty
     * @param int $customerId
     * @return \Angel\Fd\Api\Data\TicketInterface
     */
    public function postPurchase($product, $qty, $customerId);
}

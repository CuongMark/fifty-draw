<?php


namespace Angel\Fd\Api;

interface GetTicketManagementInterface
{

    /**
     * @param int $ticketId
     * @param int $customerId
     * @return \Angel\Fd\Api\Data\ReceiptInterface
     */
    public function getGetTicket($ticketId, $customerId);
}

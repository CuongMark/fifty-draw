<?php


namespace Angel\Fd\Api;

interface GetTicketManagementInterface
{

    /**
     * @param int $ticket_id
     * @param int $customerId
     * @return \Angel\Fd\Api\Data\ReceiptInterface
     */
    public function getGetTicket($ticket_id, $customerId);
}

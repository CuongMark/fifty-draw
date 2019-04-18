<?php


namespace Angel\Fd\Api\Data;

interface TicketSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Ticket list.
     * @return \Angel\Fd\Api\Data\TicketInterface[]
     */
    public function getItems();

    /**
     * Set product_id list.
     * @param \Angel\Fd\Api\Data\TicketInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

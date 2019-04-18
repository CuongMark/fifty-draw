<?php


namespace Angel\Fd\Model\ResourceModel\Ticket;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Angel\Fd\Model\Ticket::class,
            \Angel\Fd\Model\ResourceModel\Ticket::class
        );
    }
}

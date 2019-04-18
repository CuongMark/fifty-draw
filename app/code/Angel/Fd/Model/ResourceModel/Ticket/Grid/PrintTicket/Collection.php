<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Angel\Fd\Model\ResourceModel\Ticket\Grid\PrintTicket;

use Angel\Fd\Model\Ticket\Status;
use Angel\Fd\Model\FdManagement;
use Angel\Fd\Model\TicketManagement;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Order grid collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @var RequestInterface
     */
    private $request;
    private $ticketManagement;
    private $fdManagement;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param TicketManagement $ticketManagement
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        TicketManagement $ticketManagement,
        FdManagement $fdManagement,
        $mainTable = 'angel_fd_ticket',
        $resourceModel = \Angel\Fd\Model\ResourceModel\Ticket::class
    ) {
        $this->request = $request;
        $this->ticketManagement = $ticketManagement;
        $this->fdManagement = $fdManagement;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_addFilters();
        $this->_joinFields();
        return $this;
    }

    protected function _joinFields()
    {
        $this->fdManagement->joinProductName($this);
        $this->fdManagement->joinCustomerEmail($this);
    }

    protected function _addFilters()
    {
        if ($this->request->getParam('current_product_id'))
            $this->addFieldToFilter('main_table.product_id', $this->request->getParam('current_product_id'));
        $this->addFieldToFilter('status', ['in' => [Status::STATUS_PAID, Status::STATUS_WAITING]]);
    }

}

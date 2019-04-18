<?php
/**
 * Angel Fifty Raffles
 * Copyright (C) 2018 Mark Wolf
 *
 * This file included in Angel/Fifty is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Angel\Fd\Block\Tickets;

use Angel\Fd\Model\FdManagement;
use Angel\Fd\Model\ResourceModel\Ticket\Collection;
use Angel\Fd\Model\Ticket\Status;
use Angel\Fd\Model\TicketManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $ticketManagement;
    /**
     * @var Collection
     */
    protected $ticketCollection;
    private $fdManagement;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Session $customerSession
     * @param TicketManagement $ticketManagement
     * @param PriceCurrencyInterface $priceCurrency
     * @param FdManagement $fdManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Session $customerSession,
        TicketManagement $ticketManagement,
        PriceCurrencyInterface $priceCurrency,
        FdManagement $fdManagement,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->ticketManagement = $ticketManagement;
        $this->priceCurrency = $priceCurrency;
        $this->fdManagement = $fdManagement;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getTickets()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.tickets.pager'
            )->setCollection(
                $this->getTickets()
            );
            $this->setChild('pager', $pager);
            $this->getTickets()->load();
        }
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTickets()
    {
        if (!$this->ticketCollection) {
            $this->ticketCollection = $this->ticketManagement->getCollectionByCustomer($this->_customerSession->getCustomerId());
            $this->fdManagement->joinProductName($this->ticketCollection);
        }
        return $this->ticketCollection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }


    /**
     * Retrieve formated price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value, $isHtml = true)
    {
        return $this->priceCurrency->format(
            $value,
            $isHtml,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            1 //Todo getStore
        );
    }

    public function getStatusLabel($ticket){
        $options = Status::getOptionArray();
        return $options[$ticket->getStatus()];
    }
}

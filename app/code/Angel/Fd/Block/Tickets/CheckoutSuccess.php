<?php


namespace Angel\Fd\Block\Tickets;

use Angel\Fd\Model\FdManagement;
use Angel\Fd\Model\Ticket\Status;
use Angel\Fd\Model\TicketManagement;
use \Magento\Checkout\Model\Session as CheckoutSession;
use Angel\Fd\Model\ResourceModel\Ticket\Collection;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class CheckoutSuccess extends \Magento\Framework\View\Element\Template
{
    private $checkoutSession;
    private $ticketManagement;
    /**
     * @var Collection
     */
    protected $ticketCollection;
    private $fdManagement;
    private $priceCurrency;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CheckoutSession $checkoutSession,
        TicketManagement $ticketManagement,
        FdManagement $fdManagement,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->ticketManagement = $ticketManagement;
        $this->fdManagement = $fdManagement;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $data);
    }


    /**
     * @return Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderedTickets(){
        if (!$this->ticketCollection) {
            $this->ticketCollection = $this->ticketManagement->getCollectionByOrderId($this->checkoutSession->getLastRealOrder()->getId());
            $this->fdManagement->joinProductName($this->ticketCollection);
        }
        return $this->ticketCollection;
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

<?php


namespace Angel\Fd\Observer\Backend\Catalog;

use Angel\Fd\Model\FdManagement;

class ProductEditAction implements \Magento\Framework\Event\ObserverInterface
{

    private $fdManagement;

    public function __construct(
        FdManagement $fdManagement
    ){
        $this->fdManagement = $fdManagement;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getProduct();
        $this->fdManagement->updateStatus($product);
    }
}

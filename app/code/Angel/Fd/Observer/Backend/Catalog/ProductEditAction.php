<?php


namespace Angel\Fd\Observer\Backend\Catalog;

use Angel\Fd\Model\FdManagement;
use \Magento\Framework\Message\ManagerInterface;

class ProductEditAction implements \Magento\Framework\Event\ObserverInterface
{

    private $fdManagement;
    private $messageManager;

    public function __construct(
        FdManagement $fdManagement,
        ManagerInterface $messageManager
    ){
        $this->fdManagement = $fdManagement;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $observer->getEvent()->getProduct();
            $this->fdManagement->updateStatus($product);
            $this->fdManagement->checkFinished($product);
        } catch (\Exception $e){
            $this->messageManager->addExceptionMessage($e);
        }
    }
}

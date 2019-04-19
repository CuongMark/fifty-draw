<?php


namespace Angel\Fd\Controller\Index;

use Angel\Fd\Model\FdManagement;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    private $fdManagement;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param FdManagement $fdManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        FdManagement $fdManagement
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->fdManagement = $fdManagement;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->fdManagement->massUpdateStatus();
        $page = $this->resultPageFactory->create();
        $page->getConfig()->addBodyClass('page-products');
        $page->getConfig()->getTitle()->prepend(__('50/50 Raffle Tickets'));
        return $page;
    }
}

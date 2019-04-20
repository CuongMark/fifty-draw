<?php


namespace Angel\Fd\Model;


use Angel\Fd\Model\Product\Attribute\Source\Status;
use Angel\Fd\Model\Product\Type\Fd;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class FdManagement
{
    private $ticketManagement;
    private $productRepository;
    private $productCollectionFactory;

    public function __construct(
        TicketManagement $ticketManagement,
        ProductRepository $productRepository,
        CollectionFactory $productCollectionFactory
    ){
        $this->ticketManagement = $ticketManagement;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function getJackPot($product){
        return $product->getData('fd_start_pot') + $this->ticketManagement->getTotalSale($product->getId());
    }

    /**
     * @param Collection $collection
     * @return Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addJackPotToProductCollection($collection){
        $collection = $this->ticketManagement->joinTotalSales($collection);
        $collection->joinAttribute('fd_start_pot', 'catalog_product/fd_start_pot', 'entity_id', null, 'inner');
        $collection->getSelect()->columns(['jack_pot' => '(at_fd_start_pot.value + IF(ticket_total_price.total_price, ticket_total_price.total_price, 0))']);
        return $collection;
    }

    /**
     * @param Product $product
     */
    public function updateStatus($product){
        try {
            if ($product->getTypeId() == Fd::TYPE_ID) {
                $product->getResource()->beginTransaction();

                $now = new \DateTime();
                $start_at = new \DateTime($product->getFdStartAt());
                $finish_at = new \DateTime($product->getFdFinishAt());
                if ($product->getFdStatus() == Status::NOT_START) {
                    if ($now > $start_at) {
                        $this->productRepository->save($product->setFdStatus(Status::PROCESSING));
                    } elseif ($now > $finish_at){
                        $this->productRepository->save($product->setFdStatus(Status::CANCELED));
                    }
                } elseif ($product->getFdStatus() == Status::PROCESSING){
                    if ($now < $start_at){
                        $this->productRepository->save($product->setFdStatus(Status::NOT_START));
                    } elseif ($now > $finish_at){
                        $this->ticketManagement->waittingTickets($product);
                        $this->productRepository->save($product->setFdStatus(Status::WAITING));
                    }
                }
                $product->getResource()->commit();
            }
        } catch (\Exception $e){
            $product->getResource()->rollBack();
        }
    }

    /**
     * @param $product
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function checkFinished($product){
        if ($product->getFdWinningNumber()){
            if ($product->getFdStatus() != Status::FINISHED){
                $this->productRepository->save($product->setFdWinningNumber(null));
                throw new \Exception(__('You can not set Winning Number before set the raffle is finished'));
            } else {
                $isWinning = $this->ticketManagement->winningTickets($product);
                if (!$isWinning) {
                    $product->setFdWinningNumber(null);
                    $this->setCorrectStatus($product);
                }
            }
        } elseif (!$product->getFdWinningNumber() && $product->getFdStatus() == Status::FINISHED){
            $this->setCorrectStatus($product);
        }
    }

    public function massUpdateStatus(){
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('type_id', Fd::TYPE_ID)
            ->addAttributeToFilter('fd_status', ['in' => [Status::NOT_START, Status::PROCESSING]])
            ->addAttributeToSelect(['fd_status', 'fd_start_at', 'fd_finish_at', 'fd_start_pot', 'fd_winning_number']);
        foreach ($productCollection as $product){
            $this->updateStatus($product);
        }
    }

    /**
     * @param \Angel\Fd\Model\ResourceModel\Ticket\Collection $collection
     * @return \Angel\Fd\Model\ResourceModel\Ticket\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function joinProductName($collection){
        $productCollection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name']);
        $productCollection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');
        $collection->getSelect()->joinLeft(['product' => new \Zend_Db_Expr('('.$productCollection->getSelect()->__toString().')')],
            "product.entity_id = main_table.product_id",
            ['product_name' => 'product.name']
        );
        return $collection;
    }

    /**
     * @param \Angel\Fd\Model\ResourceModel\Ticket\Collection $collection
     * @return \Angel\Fd\Model\ResourceModel\Ticket\Collection
     */
    public function joinCustomerEmail($collection){
        $collection->getSelect()->joinLeft(['customer' => $collection->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['customer_email' => 'customer.email']
        );
        return $collection;
    }

    /**
     * @param $product
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function setCorrectStatus($product)
    {
        $now = new \DateTime();
        $start_at = new \DateTime($product->getFdStartAt());
        $finish_at = new \DateTime($product->getFdFinishAt());
        if ($now < $start_at) {
            $this->productRepository->save($product->setFdStatus(Status::NOT_START));
        } elseif ($now >= $start_at && $now < $finish_at) {
            $this->productRepository->save($product->setFdStatus(Status::PROCESSING));
        } elseif ($now >= $finish_at) {
            $this->productRepository->save($product->setFdStatus(Status::WAITING));
        }
    }

}

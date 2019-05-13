<?php


namespace Angel\Fd\Model;

use Angel\Fd\Model\Product\Type\Fd;
use Angel\Fd\Model\Ticket\Status;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\ManagerInterface;
use Angel\Fd\Model\Product\Attribute\Source\Status as FdStatus;
use Magento\Framework\DataObject;

class PurchaseManagement implements \Angel\Fd\Api\PurchaseManagementInterface
{

    private $messageManager;
    private $customerSession;
    private $productRepository;
    private $ticketDataModel;
    private $ticketRepository;
    private $ticketManagement;
    private $eventManager;
    private $ticket;
    private $customerManagement;

    public function __construct(
        ManagerInterface $message,
        Session $customerSession,
        ProductRepository $productRepository,
        \Angel\Fd\Model\Data\Ticket $ticketDataModel,
        TicketRepository $ticketRepository,
        TicketManagement $ticketManagement,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Ticket $ticket,
        CustomerManagement $customerManagement
    ){
        $this->messageManager = $message;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->ticketDataModel = $ticketDataModel;
        $this->ticketRepository = $ticketRepository;
        $this->ticketManagement = $ticketManagement;
        $this->eventManager = $eventManager;
        $this->ticket = $ticket;
        $this->customerManagement = $customerManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function postPurchase($product_id, $qty, $customerId)
    {
        try {
            $this->ticket->getResource()->beginTransaction();
            
            if ($qty<=0){
                throw new \Exception('The Qty is not available');
            }
            
            $product = $this->productRepository->getById($product_id);
            if ($product->getFdStatus() != FdStatus::PROCESSING){
                throw new \Exception('The Raffle is not processing');
            }
            /** @var Ticket $lastTicket */
            $lastTicket = $this->ticketManagement->getLastTicket($product_id);
            $lastTicketNumber = $lastTicket->getEnd();

            $freeTickets = new DataObject(['free_ticket' => 0]);
            $this->eventManager->dispatch('angel_get_free_ticket', ['product' => $product, 'qty' => $qty, 'free' => $freeTickets]);
            $freeTickets = $freeTickets->getData('free_ticket');

            $fee = new DataObject(['extra_fee' => 0]);
            $this->eventManager->dispatch('angel_get_extra_fee', ['product' => $product, 'qty' => $qty, 'fee' => $fee]);
            $fee = $fee->getData('extra_fee');

            $this->ticketDataModel->setStart($lastTicketNumber + 1)
                ->setEnd($lastTicketNumber + $qty + $freeTickets)
                ->setPrice($product->getPrice() * $qty + $fee)
                ->setCustomerId($customerId)
                ->setProductId($product_id)
                ->setStatus(Status::STATUS_PENDING)
                ->setSerial($this->generateSerial());


            $this->eventManager->dispatch('angel_fd_create_new_ticket', ['ticket' => $this->ticketDataModel, 'product' => $product]);
            $ticketData = $this->ticketRepository->save($this->ticketDataModel);

            $this->ticket->getResource()->commit();
            if (!$freeTickets){
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 ticket(s)', $qty));
            } else {
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 tickets and get %2 free ticket(s)', $qty, $freeTickets));
            }
            return $ticketData;
        } catch (\Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->ticket->getResource()->rollBack();
        }
    }

    /**
     * @param $product_id
     * @param $qty
     * @param $customerId
     * @param $status
     * @param null $customPrice
     * @return \Angel\Fd\Api\Data\TicketInterface|Data\Ticket|boolean
     */
    public function postPurchaseAdmin($product_id, $qty, $customerId, $status, $customPrice = null)
    {
        try {
            $this->ticket->getResource()->beginTransaction();
            if ($qty<=0){
                throw new \Exception('The Qty is not available');
            }
            $product = $this->productRepository->getById($product_id);
            if (! in_array($product->getFdStatus(), [FdStatus::PROCESSING, FdStatus::WAITING])){
                throw new \Exception('The Raffle is not saleable');
            }
            /** @var Ticket $lastTicket */
            $lastTicket = $this->ticketManagement->getLastTicket($product_id);
            $lastTicketNumber = $lastTicket->getEnd();

            $price = $customPrice?$customPrice:$product->getPrice() * $qty;

            $freeTickets = new DataObject(['free_ticket' => 0]);
            $this->eventManager->dispatch('angel_get_free_ticket', ['product' => $product, 'qty' => $qty, 'free' => $freeTickets]);
            $freeTickets = $freeTickets->getData('free_ticket');

            $fee = new DataObject(['extra_fee' => 0]);
            $this->eventManager->dispatch('angel_get_extra_fee', ['product' => $product, 'qty' => $qty, 'fee' => $fee]);
            $fee = $fee->getData('extra_fee');

            $this->ticketDataModel->setStart($lastTicketNumber + 1)
                ->setEnd($lastTicketNumber + $qty + $freeTickets)
                ->setPrice($price + $fee)
                ->setCustomerId($customerId)
                ->setProductId($product_id)
                ->setStatus($status)
                ->setSerial($this->generateSerial());
            $ticketData = $this->ticketRepository->save($this->ticketDataModel);

            $this->ticket->getResource()->commit();
            if (!$freeTickets){
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 ticket(s)', $qty));
            } else {
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 tickets and get %2 free ticket(s)', $qty, $freeTickets));
            }
            return $ticketData;
        } catch (\Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->ticket->getResource()->rollBack();
        }
        return false;
    }

    /**
     * @param $product
     * @param $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importTicket($product, $data){
        unset($data[0]);
        if (! in_array($product->getFdStatus(), [FdStatus::PROCESSING, FdStatus::WAITING])){
            throw new \Exception('The Queen of Hearts Raffle is not able to import new ticket. The status is not processing or waiting');
        }
        /** @var Ticket $lastTicket */
        $lastTicket = $this->ticketManagement->getLastTicket($product->getId());
        $lastTicketNumber = $lastTicket->getEnd();

        /**
         * 0 - customer_emai
         * 1 - qty
         * 2 - customer_price
         * 3 - status
         */
        foreach ($data as $ticket){
            if ($ticket[1] <= 0){
                throw new \Exception(__('The Qty "%1" is not available', $ticket[1]));
            }

            if (!in_array($ticket[3],[Status::STATUS_PENDING, Status::STATUS_PAID, Status::STATUS_CANCELED, Status::STATUS_WAITING])){
                throw new \Exception(__('The Status "%1" is not available', $ticket[3]));
            }

            $price = $ticket[2]?$ticket[2]:$product->getPrice() * $ticket[1];
            $customer = $this->customerManagement->getOrCreateCustomerByEmail($ticket[0]);
            $this->ticketDataModel->setStart($lastTicketNumber + 1)
                ->setEnd($lastTicketNumber + $ticket[1])
                ->setPrice($price)
                ->setCustomerId($customer->getId())
                ->setProductId($product->getId())
                ->setStatus($ticket[3])
                ->setSerial($this->generateSerial());
            $this->ticketRepository->save($this->ticketDataModel);
            $lastTicketNumber += $ticket[1];
        }
    }

    /**
     * @param $invoiceItem
     * @return \Angel\Fd\Api\Data\TicketInterface|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createTicketByInvoiceItem($invoiceItem){
        $product = $this->productRepository->getById($invoiceItem->getProductId());
        if ($product->getTypeId()!=Fd::TYPE_ID){
            return true;
        }
        try {
            $this->ticket->getResource()->beginTransaction();
            $qty = $invoiceItem->getQty();

//            $product = $this->productRepository->getById($invoiceItem->getProductId());
            if ($product->getFdStatus() != FdStatus::PROCESSING){
                throw new \Exception('The Raffle is not processing');
            }
            /** @var Ticket $lastTicket */
            $lastTicket = $this->ticketManagement->getLastTicket($invoiceItem->getProductId());
            $lastTicketNumber = $lastTicket->getEnd();

            $customerId = $invoiceItem->getOrderItem()->getOrder()->getCustomerId();

            $freeTickets = new DataObject(['free_ticket' => 0]);
            $this->eventManager->dispatch('angel_get_free_ticket', ['product' => $product, 'qty' => $qty, 'free' => $freeTickets]);
            $freeTickets = $freeTickets->getData('free_ticket');

            $fee = new DataObject(['extra_fee' => 0]);
            $this->eventManager->dispatch('angel_get_extra_fee', ['product' => $product, 'qty' => $qty, 'fee' => $fee]);
            $fee = $fee->getData('extra_fee');

            $this->ticketDataModel->setStart($lastTicketNumber + 1)
                ->setEnd($lastTicketNumber + $qty + $freeTickets)
                ->setPrice($product->getPrice() * $qty + $fee)
                ->setCustomerId($customerId)
                ->setProductId($invoiceItem->getProductId())
                ->setStatus(Status::STATUS_PAID)
                ->setSerial($this->generateSerial())
                ->setInvoiceItemId($invoiceItem->getId());

            $ticketData = $this->ticketRepository->save($this->ticketDataModel);

            $this->ticket->getResource()->commit();
            if (!$freeTickets){
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 ticket(s)', $qty));
            } else {
                $this->messageManager->addSuccessMessage(__('You purchased successfully %1 tickets and get %2 free ticket(s)', $qty, $freeTickets));
            }
            return $ticketData;
        } catch (\Exception $e){
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->ticket->getResource()->rollBack();
        }
    }

    private function generateSerial()
    {
        $characters = '0123456789';
        $randstring = '';
        for ($i = 0; $i < 13; $i++) {
            $randstring .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 1/26/2019
 * Time: 10:31 PM
 */
namespace Angel\Fd\Block\Index;

use Angel\Fd\Model\Product\Attribute\Source\Status;
use Angel\Fd\Model\FdManagement;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Url\Helper\Data;

Class Index extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $customerSession;
    private $fdManagement;

    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        Session $customerSession,
        FdManagement $fdManagement,
        array $data = []
    ){
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
        $this->productCollectionFactory = $productCollectionFactory;
        $this->priceCurrency = $priceCurrency;
        $this->customerSession = $customerSession;
        $this->fdManagement = $fdManagement;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getProductCollection()
    {
        if (!$this->_productCollection) {
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
            $collection->addFieldToFilter('type_id', \Angel\Fd\Model\Product\Type\Fd::TYPE_ID);
            $collection->addAttributeToFilter('fd_status', ['in' => [Status::PROCESSING, Status::WAITING]]);
            $collection->addStoreFilter($this->_storeManager->getStore()->getId());
            $collection = $this->fdManagement->addJackPotToProductCollection($collection);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @return PriceCurrencyInterface
     */
    public function getPriceCurrency(){
        return $this->priceCurrency;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(){
        return $this->customerSession->isLoggedIn();
    }
}
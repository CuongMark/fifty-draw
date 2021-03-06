<?php

namespace Angel\Fd\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;

/**
 * Class ReviewDataProvider
 *
 * @api
 *
 * @method \Magento\Catalog\Model\ResourceModel\Product\Collection getCollection
 * @since 100.1.0
 */
class FdDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->getCollection()->addAttributeToFilter('type_id', ['in' => [\Angel\Fd\Model\Product\Type\Fd::TYPE_ID]]);
        $this->getCollection()->addAttributeToSelect(['fd_start_at', 'fd_finish_at', 'fd_status', 'fd_start_pot']);
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items),
        ];
    }


    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed|void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        switch ($filter->getField()) {
            case 'winning_number':
                $this->getCollection()->getSelect()->where('prize.winning_number like(\''.$filter->getValue().'\')');
                break;
            case 'customer_email':
                $this->getCollection()->getSelect()->where('customer.email like(\''.$filter->getValue().'\')');
                break;
            case 'winning_prize':
                if ($filter->getConditionType() == 'gteq') {
                    $this->getCollection()->getSelect()->where('prize.winning_prize >= ' . $filter->getValue());
                } else if ($filter->getConditionType() == 'lteq'){
                    $this->getCollection()->getSelect()->where('prize.winning_prize <= ' . $filter->getValue());
                }
                break;
            case 'total_price':
                if ($filter->getConditionType() == 'gteq') {
                    $this->getCollection()->getSelect()->where('total_ticket.total_price >= ' . $filter->getValue());
                } else if ($filter->getConditionType() == 'lteq'){
                    $this->getCollection()->getSelect()->where('total_ticket.total_price <= ' . $filter->getValue());
                }
                break;
            case 'total_ticket':
                $this->getCollection()->getSelect()->where('total_ticket.total_ticket like(\''.$filter->getValue().'\')');
                break;
            default:
                parent::addFilter($filter);
        }
    }
}

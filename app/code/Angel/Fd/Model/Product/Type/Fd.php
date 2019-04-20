<?php


namespace Angel\Fd\Model\Product\Type;

use Angel\Fd\Model\Product\Attribute\Source\Status;

class Fd extends \Magento\Catalog\Model\Product\Type\Virtual
{

    const TYPE_ID = 'fd';

    /**
     * {@inheritdoc}
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // method intentionally empty
    }

    public function isSalable($product)
    {
        if ($product->getFdStatus() != Status::PROCESSING){
            return false;
        }
        return parent::isSalable($product);
    }
}

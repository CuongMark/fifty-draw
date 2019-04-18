<?php


namespace Angel\Fd\Model\Product\Type;

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
}

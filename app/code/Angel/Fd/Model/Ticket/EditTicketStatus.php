<?php
/**
 * Angel Queen of Hearts
 * Copyright (C) 2018 Mark Wolf
 *
 * This file included in Angel/Fd is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Angel\Fd\Model\Ticket;

class EditTicketStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_WAITING = 2;
    const STATUS_PRINTED = 3;
    const STATUS_WINNING = 4;
    const STATUS_LOSE = 5;
    const STATUS_CANCELED = 6;

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['value' => Status::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Status::STATUS_PAID, 'label' => __('Paid')],
            ['value' => Status::STATUS_WAITING, 'label' => __('Waiting')],
            ['value' => Status::STATUS_PRINTED, 'label' => __('Printed')],
            ['value' => Status::STATUS_CANCELED, 'label' => __('Canceled')],
        ];
        return $this->_options;
    }

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            Status::STATUS_PENDING => __('Pending'),
            Status::STATUS_PAID => __('Paid'),
            Status::STATUS_WAITING => __('Waiting'),
            Status::STATUS_PRINTED => __('Printed'),
            Status::STATUS_CANCELED => __('Canceled'),
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptions()
    {
        $options = array();
        foreach (Status::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    public function toOptionArray()
    {
        return Status::getOptions();
    }
}

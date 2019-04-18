<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Angel\Fd\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayManager;

class General extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    private $priceCurrency;

    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        PriceCurrencyInterface $priceCurrency
    ){
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     * @since 101.0.0
     */
    public function modifyMeta(array $meta)
    {
        /** @var Product $product */
        $product = $this->locator->getProduct();
        if ($product->getTypeId() != \Angel\Fd\Model\Product\Type\Fd::TYPE_ID){
            return $meta;
        }
        $meta = $this->enableTime($meta);
        return $meta;
    }

    protected function disableStatusField(array $meta){
        $meta = array_replace_recursive(
            $meta,
            [
                'product-details' => [
                    'children' => [
                        'container_fifty_status' => [
                            'children' => [
                                'fifty_status' =>[
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => true,
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        return $meta;
    }

    protected function disableStartPotField(array $meta){
        $meta = array_replace_recursive(
            $meta,
            [
                'product-details' => [
                    'children' => [
                        'container_fd_start_pot' => [
                            'children' => [
                                'fd_start_pot' =>[
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => true,
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        return $meta;
    }


    protected function disableFinishAtField(array $meta){
        $meta = array_replace_recursive(
            $meta,
            [
                'product-details' => [
                    'children' => [
                        'container_fd_finish_at' => [
                            'children' => [
                                'fd_finish_at' =>[
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'disabled' => true,
                                                'notice' => ''
                                            ],
                                        ],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        return $meta;
    }

    /**
     * Customise Custom Attribute field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function enableTime(array $meta)
    {
        $fieldCode = 'fd_start_at';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
        if ($elementPath) {
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'default' => '',
                                        'options' => [
                                            'dateFormat' => 'Y-m-d',
                                            'timeFormat' => 'HH:mm:ss',
                                            'showsTime' => true
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }

        $fieldCode = 'fd_finish_at';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
        if ($elementPath) {
            $meta = $this->arrayManager->merge(
                $containerPath,
                $meta,
                [
                    'children' => [
                        $fieldCode => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'default' => '',
                                        'options' => [
                                            'dateFormat' => 'Y-m-d',
                                            'timeFormat' => 'HH:mm:ss',
                                            'showsTime' => true
                                        ]
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            );
        }
        return $meta;
    }
}

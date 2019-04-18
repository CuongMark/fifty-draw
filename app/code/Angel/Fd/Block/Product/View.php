<?php


namespace Angel\Fd\Block\Product;

use Angel\Fd\Model\FdManagement;
use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends \Magento\Catalog\Block\Product\View
{
    private $fdManagement;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        FdManagement $fdManagement,
        array $data = []
    ){
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
        $this->fdManagement = $fdManagement;
    }

    public function getJackPot(){
        return $this->fdManagement->getJackPot($this->getProduct());
    }

    /**
     * @return array
     */
    public function getPrizes(){
        $fields = ['prize_id', 'card', 'card_number', 'prize', 'winning_number'];
        $prizes = $this->fdManagement->getPrizes($this->getProduct());
        $prizeData = [];
        foreach ($prizes as $prize){
            foreach ($fields as $field){
                $data[$field] = $prize->getData($field);
            }
            $prizeData[] = $data;
        }
        return $prizeData;
    }
}

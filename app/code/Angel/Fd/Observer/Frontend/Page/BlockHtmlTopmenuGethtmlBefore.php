<?php


namespace Angel\Fd\Observer\Frontend\Page;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\UrlInterface;

class BlockHtmlTopmenuGethtmlBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ){
        $this->urlBuilder = $urlBuilder;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $data = [
            'name'      => __('50/50 Raffle'),
            'id'        => 'fd_item',
            'url'       => $this->urlBuilder->getUrl('fifty_percent'),
            'is_active' => false
        ];
        $node = new Node($data, 'id', $tree, $menu);
        $menu->addChild($node);

        $data = [
            'name'      => __('Current Raffle'),
            'id'        => 'fd_current_item',
            'url'       => $this->urlBuilder->getUrl('fifty_percent'),
            'is_active' => false
        ];
        $processing = new Node($data, 'id', $tree, $node);
        $node->addChild($processing);

        $data = [
            'name'      => __('Finished'),
            'url'       => $this->urlBuilder->getUrl('fifty_percent/finished'),
            'is_active' => false
        ];
        $finished = new Node($data, 'id', $tree, $node);
        $node->addChild($finished);
    }
}

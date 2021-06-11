<?php
/**
 * Copyright © 2017 x-mage2(Crealevant). All rights reserved.
 * See README.md for details.
 */
namespace Crealevant\Relevant\Block\Widget\Sections;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Section extends Template implements BlockInterface {

    protected $_template = "widget/sections.phtml";

    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        parent::__construct($context, $data);
    }

    /**
     * @param string $description
     * @return string
     */
    public function getFilteredContent(string $filter)
    {
        return $this->filterProvider
            ->getBlockFilter()
            ->filter($filter);
    }

}
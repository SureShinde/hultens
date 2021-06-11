<?php
namespace Crealevant\Relevant\Block;
class Categories extends \Magento\Framework\View\Element\Template
{
    protected $_categoryHelper;
    protected $categoryFlatConfig;
    protected $topMenu;
    protected $_categoryFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Theme\Block\Html\Topmenu $topMenu,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_categoryHelper = $categoryHelper;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->topMenu = $topMenu;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }
    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }
    /**
     * Return top menu html
     * getHtml($outermostClass = '', $childrenWrapClass = '', $limit = 0)
     * example getHtml('level-top', 'submenu', 0)
     */
    public function getHtml()
    {
        return $this->topMenu->getHtml();
    }
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }
    /**
     * Retrieve child store categories
     *
     */
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }

    public function getImageUrl($category){
        $category = $this->_categoryFactory->create()->load($category->getId());
        return $category->getImageUrl();
    }

    public function getThumbnail($category){
        $category = $this->_categoryFactory->create()->load($category->getId());
        return $category->getImageUrl('thumbnail');
    }

}
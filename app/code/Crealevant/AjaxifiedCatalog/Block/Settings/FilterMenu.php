<?php
namespace Crealevant\AjaxifiedCatalog\Block\Settings;

class FilterMenu extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $_categoryFactory;
    protected $_category;
    protected $_categoryHelper;
    protected $_categoryRepository;
    protected $categoryFlatConfig;


    // Config Ajaxified Catalog Settings
    const XML_PATH_AJAXFIED_CATALOG_GENERAL_CATEGORY_DROPDOWN_LIST = 'ajaxfied_catalog/general/category_dropdown_list';
    const XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION = 'ajaxfied_catalog/filter_settings/popular_items_position';
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\Category $categoryView,
        array $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryRepository = $categoryRepository;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->categoryView = $categoryView;
        parent::__construct($context, $data);
    }

    public function getCategoryDropdownValue() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_GENERAL_CATEGORY_DROPDOWN_LIST, $storeScope);
    }
    public function getPopularFilterPosition() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return $this->scopeConfig->getValue(self::XML_PATH_AJAXFIED_CATALOG_FILTER_SETTINGS_POPULAR_ITEMS_POSITION, $storeScope);
    }
    /**
     * Get category object
     * Using $_categoryFactory
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory($categoryId)
    {
        $this->_category = $this->_categoryFactory->create();
        $this->_category->load($categoryId);
        return $this->_category;
    }

    /**
     * Get category object
     * Using $_categoryRepository
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategoryById($categoryId)
    {
        return $this->_categoryRepository->get($categoryId);
    }

    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection or
     * \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true) {
        return $this->_categoryHelper->getStoreCategories();
    }

    public function getChildCategories($categoryId) {

        $_category = $this->_categoryFactory->create();

        $category = $_category->load($categoryId);

        //Get category collection
        $collection = $category->getCollection()
            ->addIsActiveFilter()
            ->addOrderField('name')
            ->addIdFilter($category->getChildren());
        return $collection;
    }
    public function getCategoryView() {
        return $this->categoryView;
    }
}

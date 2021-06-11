<?php
namespace Crealevant\Relevant\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ModelProduct;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\Store;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;

class Data extends \Magento\Framework\Url\Helper\Data
{

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;
    protected $listProductBlock;
    protected $_priceCurrency;
    private $stockRegistry;

    public function __construct(
        Context $context,
        TimezoneInterface $localeDate,
        StockRegistry $stockRegistry,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Block\Product\ListProduct $listProductBlock,
        \Magento\CatalogRule\Model\Rule $ruleRepository,
        \Magento\Framework\App\ResourceConnection $_resource,
        array $data = []
    ) {
        $this->localeDate = $localeDate;
        $this->stockRegistry = $stockRegistry;
        $this->listProductBlock = $listProductBlock;
        $this->_priceCurrency = $priceCurrency;
        $this->ruleRepository = $ruleRepository;
        $this->_resource = $_resource;
        parent::__construct($context);
    }

    public function getAddToCartPostParams(Product $product)
    {
        return $this->listProductBlock->getAddToCartPostParams($product);
    }

    public function isNew(ModelProduct $product)
    {

        $newsFromDate = $product->getNewsFromDate();
        $newsToDate = $product->getNewsToDate();

        if (!$newsFromDate && !$newsToDate) {
            return false;
        }

        return $this->localeDate->isScopeDateInInterval(
            $product->getStore(),
            $newsFromDate,
            $newsToDate
        );
    }

    public function getConfig()
    {
        return $this->_scopeConfig;
    }

    public function getStockQty(Product $product)
    {
        $qty = 0;
        if ($product->getTypeId() === 'configurable') {
            foreach ($product->getTypeInstance()->getUsedProducts($product) as $simpleProduct) {
                $thisQty = $this->stockRegistry->getStockItem($simpleProduct->getId(),$simpleProduct->getStore()->getWebsiteId())->getQty();
                if ($thisQty > $qty) {
                    $qty = $thisQty;
                }
            }
        } else {
            $qty = $this->stockRegistry->getStockItem($product->getId(),$product->getStore()->getWebsiteId())->getQty();
        }

        return $qty;
    }

    public function getFlags(Product $product, $related, $view = 'list')
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('catalogrule_product');
        $sql = 'SELECT * FROM ' . $tableName .' WHERE product_id='.$product->getID().' group by product_id';
        $result = $connection->fetchAll($sql);
        $campaignbutton = "";
        $desc = "";
        foreach($result as $r)
        {
            $rule_id = $r['rule_id'];

            $sql = 'SELECT * FROM catalogrule WHERE rule_id='.$rule_id;
            $rule = $connection->fetchAll($sql);

            foreach($rule as $rr)
            {
                $desc = $rr['description'];
            }
//            $cats = $product->getCategoryIds();
//            $campaignbutton = $rule->getDescription();
//            var_dump($rule_id);
        }

//        if(!isset($desc) || $desc == '') {
//            echo "sale?";
//        } else {
//            echo $desc;
//        }
        $SpecialTxt = __('Offer');
        $OfferTxt = __('Offer');
        $OutofStockTxt = __('Out of stock');
        $NewTxt = __('New Product');
        $LowStockTxt = __('Low Stock');


        if($view == 'list') {
            $FlagSpecial = '<div class="flag-special"><div class="flag-txt">' . $SpecialTxt . '</div></div>';
            $FlagOffer = '<div class="flag-offer"><div class="flag-txt">' . $OfferTxt . '</div></div>';
            $FlagOutStock = '<div class="flag-outstock"><div class="flag-txt">' . $OutofStockTxt . '</div></div>';
            $FlagNew = '<div class="flag-new"><div class="flag-txt">' . $NewTxt . '</div></div>';
            $FlagLowStock = '<div class="flag-lowstock"><div class="flag-txt">' . $LowStockTxt . '</div></div>';
            $specialFlag = '<div class="flag-outstock"><div class="flag-txt">' . $desc . '</div></div>';
        } else {
            $FlagSpecial = '<div class="flag-special-prod"><div class="flag-txt-prod">' . $SpecialTxt . '</div></div>';
            $FlagOffer = '<div class="flag-offer-prod"><div class="flag-txt-prod">' . $OfferTxt . '</div></div>';
            $FlagOutStock = '<div class="flag-outstock-prod"><div class="flag-txt-prod">' . $OutofStockTxt . '</div></div>';
            $FlagNew = '<div class="flag-new-prod"><div class="flag-txt-prod">' . $NewTxt . '</div></div>';
            $FlagLowStock = '<div class="flag-lowstock-prod"><div class="flag-txt-prod">' . $LowStockTxt . '</div></div>';
            $specialFlag = '<div class="flag-outstock-prod"><div class="flag-txt-prod">' . $desc . '</div></div>';
        }


        $product_flags = "";
        if($related == 'yes'){
            if ($this->isNew($product)) {
                if(!isset($desc) || $desc == '') {
                    if($this->hasSpecialPricebyDate($product)) {
                        $product_flags .= $FlagSpecial;
                    } else {
                        $product_flags .= $FlagNew;
                    }
                } else {
                    $product_flags = $specialFlag;
                }
            }
            elseif($this->hasSpecialPricebyDate($product)) {
                $product_flags .= $FlagSpecial;
            }
        } else {
            if(!isset($desc) || $desc == '') {
                if ($this->isNew($product)) {
                    $product_flags .= $FlagNew;
                }
                if($this->hasSpecialPricebyDate($product)) {
                    $product_flags .= $FlagSpecial;
                }
            } else {
                $product_flags = $specialFlag;
            }
        }

        return $product_flags;
    }

    public function hasSpecialPricebyDate(Product $product) {
        $regularPrice = $product->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE)->getValue();
        $finalPrice = $product->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE)->getValue();

        $_configurableInstance = $product->getTypeInstance();
	if (method_exists($_configurableInstance, 'getUsedProducts'))
	{
		$_children = $_configurableInstance->getUsedProducts($product);
		foreach ($_children as $simpleProduct) {
		    $SpecialFromDate = $simpleProduct->getSpecialFromDate();
		    if(empty($simpleProduct->getSpecialToDate())) {
			$SpecialToDate = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 1 day"));
		    } else {
			$SpecialToDate = $simpleProduct->getSpecialToDate();
		    }

		    $paymentDate = date('Y-m-d H:i:s');
		    $paymentDate = date('Y-m-d H:i:s', strtotime($paymentDate));

		    $contractDateBegin = date('Y-m-d H:i:s', strtotime($SpecialFromDate));
		    $contractDateEnd = date('Y-m-d H:i:s', strtotime($SpecialToDate));
		    if ($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd && $finalPrice < $regularPrice) {
			return true;
		    }
		}
	}
	else {
	    $SpecialFromDate = $product->getSpecialFromDate();
            if(empty($product->getSpecialToDate())) {
                $SpecialToDate = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 1 day"));
            } else {
                $SpecialToDate = $product->getSpecialToDate();
            }

            $paymentDate = date('Y-m-d H:i:s');
            $paymentDate = date('Y-m-d H:i:s', strtotime($paymentDate));

            $contractDateBegin = date('Y-m-d H:i:s', strtotime($SpecialFromDate));
            $contractDateEnd = date('Y-m-d H:i:s', strtotime($SpecialToDate));
            if ($paymentDate >= $contractDateBegin && $paymentDate <= $contractDateEnd && $finalPrice < $regularPrice) {
                return true;
            }
	}
        return false;
    }

    public function showOldPrice(Product $product) {
        $precision = 2;
        $regularPrice = $product->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE)->getValue();
        $formattedPrice = $this->_priceCurrency->format(
            $regularPrice,
            $includeContainer = true,
            $precision,
            $scope = null,
            $currency = "SEK"
        );
        if($this->hasSpecialPricebyDate($product)) {
            return __('Old price') . " " . $formattedPrice;
        }
    }
    public function ColorBtnByStatus(Product $product)
    {
        $qty = $this->getStockQty($product);

        if ($this->hasSpecialPricebyDate($product)) {
            $class = " btn-danger";
        } elseif ($this->isNew($product)) {
            $class = " btn-info";
        } elseif (($qty <= 5) && ($product->getIsSalable())) {
            $class = " btn-primary";
        } else {
            $class = " btn-primary";
        }

        return $class;
    }
}

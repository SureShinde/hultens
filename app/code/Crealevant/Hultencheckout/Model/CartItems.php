<?php
namespace Crealevant\Hultencheckout\Model;

use Crealevant\Hultencheckout\Api\CartItemsInterface;
use Crealevant\Hultencheckout\Api\Data\ItemUpdateInterface;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\PaymentDetailsFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Magento\Quote\Api\Data\TotalsItemInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\CatalogInventory\Model\StockStateProvider;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Quote\Model\Quote\Item\Repository as QuoteItemRepository;

/**
 *
 * Class CartItems
 * @package Crealevant\Hultencheckout\Model
 */
class CartItems implements CartItemsInterface
{

    protected $quoteIdMaskFactory;

    protected $quoteRepository;

    protected $paymentDetailsFactory;

    protected $cartTotalRepository;

    protected $paymentMethodManagement;

    protected $eventManager;

    protected $cart;

    protected $customerSession;

    protected $totalsItemExtensionInterfaceFactory;

    protected $stockStateProvider;

    protected $stockItemRepository;

    protected $stockState;

    protected $checkoutSession;

    protected $quoteItemRepository;

    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $quoteRepository,
        PaymentDetailsFactory $paymentDetailsFactory,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalRepositoryInterface,
        ManagerInterface $eventManager,
        Cart $cart,
        Session $session,
        TotalsItemExtensionInterfaceFactory $extensionInterfaceFactory,
        StockStateProvider $stockStateProvider,
        StockItemRepositoryInterface $stockItemRepository,
        StockStateInterface $stockState,
        CheckoutSession $checkoutSession,
        QuoteItemRepository $quoteItemRepository
    ) {
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepositoryInterface;
        $this->eventManager = $eventManager;
        $this->cart = $cart;
        $this->customerSession = $session;
        $this->totalsItemExtensionInterfaceFactory = $extensionInterfaceFactory;
        $this->stockStateProvider = $stockStateProvider;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockState = $stockState;
        $this->checkoutSession = $checkoutSession;
        $this->quoteItemRepository = $quoteItemRepository;
    }

    /**
     *
     *
     * @param string $cartId
     * @param ItemUpdateInterface $itemUpdateInterface
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function updateItemQty($cartId, ItemUpdateInterface $itemUpdateInterface)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $quote = $this->quoteRepository->get($quoteIdMask->getQuoteId());
        $itemId = $itemUpdateInterface->getItemId();
        $qty = $itemUpdateInterface->getQty();
        $quoteItem = $quote->getItemById($itemId);

        if ($qty < 1) {
            $this->quoteItemRepository->deleteById($quote->getId(), $itemId);
        }
        else {
            $quoteItem->setQty($qty);
            $quoteItem->save();
            $this->quoteRepository->save($quote);
        }

        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($quote->getId()));
        $this->checkoutSession->getQuote()->setTotalsCollectedFlag(false);
        $this->checkoutSession->getQuote()->collectTotals();
        $totals = $this->cartTotalRepository->get($quote->getId());

        $paymentDetails->setTotals($totals);

        return $paymentDetails;
    }
}
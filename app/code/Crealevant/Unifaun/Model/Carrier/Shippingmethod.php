<?php

namespace Crealevant\Unifaun\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;

class Shippingmethod extends \Mediastrategi\Unifaun\Model\Carrier\Shippingmethod
{
    private function _valueIsInSpan($value, $span)
    {
        if (empty($span)
            || $span == '*'
        ) {
            return true;
        }
        if (strpos($span, '-') === false) {
            if ($value == $span) {
                return true;
            }
        } else {
            $parts = explode('-', $span);
            $min = trim($parts[0]);
            $max = trim($parts[1]);
            if ((empty($min)
                    || $value >= $min)
                && (empty($max)
                    || $value < $max)
            ) {
                return true;
            }
        }
        return false;
    }

    public function collectRates(RateRequest $request)
    {
        $this->log(__METHOD__ . ' ' . print_r($request->debug(), true));

        // Make sure that Shipping method is enabled
        if (!$this->isActive()) {
            return false;
        }

        $this->addMissingRequestPackageFields($request);
        $currentStoreId = $this->_storeManager->getStore()->getId();

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);
        $useFreeShipping = ($request->getFreeShipping() === true);
        $packageQuantity = $request->getPackageQty();

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $matchesCount = 0;
        $shippingMethod = $this->_shippingMethodFactory->create();
        $collection = $shippingMethod->getCollection();
        foreach ($collection as $item) {
            if ($item->getData('active')
                && (empty($item->getData('store'))
                    || $item->getData('store') == $currentStoreId)
            ) {
                $options = (array)json_decode($item->getData('options'), true);

                $lowestPrice = null;
                $lowestPriceTitle = null;
                $foundMatching = false;

                if (is_array($options)
                    && !empty($options)
                ) {
                    foreach ($options as $option) {
                        $countryMatching = false;
                        $zipMatching = false;
                        $weightMatching = false;
                        $heightMatching = false;
                        $widthMatching = false;
                        $depthMatching = false;
                        $volumeMatching = false;
                        $cartSubtotalMatching = false;

                        if (empty($option['country'])
                            || $option['country'] == '*'
                            || $option['country'] == $request->getDestCountryId()
                        ) {
                            $countryMatching = true;
                        } elseif (strpos($option['country'], ',') !== false) {
                            $countries = array_map('trim', explode(',', $option['country']));
                            if (in_array($request->getDestCountryId(), $countries)) {
                                $countryMatching = true;
                            }
                        }

                        if ($this->_valueIsInSpan(
                            str_replace(' ', '', $request->getDestPostcode()),
                            (isset($option['zip']) ? $option['zip'] : '')
                        )
                        ) {
                            $zipMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageWeight(),
                            (isset($option['weight']) ? $option['weight'] : '')
                        )
                        ) {
                            $weightMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageHeight(),
                            (isset($option['height']) ? $option['height'] : '')
                        )
                        ) {
                            $heightMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageWidth(),
                            (isset($option['width']) ? $option['width'] : '')
                        )
                        ) {
                            $widthMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getPackageDepth(),
                            (isset($option['depth']) ? $option['depth'] : '')
                        )
                        ) {
                            $depthMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            ($request->getPackageHeight() * $request->getPackageWidth() * $request->getPackageDepth()),
                            (isset($option['volume']) ? $option['volume'] : '')
                        )
                        ) {
                            $volumeMatching = true;
                        }

                        if ($this->_valueIsInSpan(
                            $request->getBaseSubtotalInclTax(),
                            (isset($option['cart_subtotal']) ? $option['cart_subtotal'] : '')
                        )
                        ) {
                            $cartSubtotalMatching = true;
                        }

                        if ($countryMatching
                            && $zipMatching
                            && $weightMatching
                            && $heightMatching
                            && $widthMatching
                            && $depthMatching
                            && $volumeMatching
                            && $cartSubtotalMatching
                        ) {
                            if ($useFreeShipping
                                || $freeBoxes == $packageQuantity
                            ) {
                                $price = 0.00;
                            } else {
                                $price = (!empty($option['price']) ? (float)$option['price'] : 0.0);
                            }

                            if (!isset($lowestPrice)
                                || $price < $lowestPrice
                            ) {
                                $lowestPrice = $price;
                                $lowestPriceTitle = $option['title'];
                            }
                            $foundMatching = true;
                        }
                    }

                    if ($foundMatching) {
                        $method = $this->_rateMethodFactory->create();
                        $method->setCarrier($this->getCarrierCode());
                        $method->setCarrierTitle($item->getData('title'));
                        $method->setMethod($this->getCarrierCode() . '_' . $item->getData('id'));
                        $method->setMethodTitle($lowestPriceTitle);
                        $method->setPrice($lowestPrice);
                        $method->setCost($lowestPrice);
                        $pickup = $item->getData('pickup');

                        if (!empty($pickup)) {
                            if ($agents = $this->_helper->getPickUpLocations(
                                $request->getDestCountryId(),
                                $pickup,
                                str_replace(' ', '', $request->getDestPostcode()))
                            ) {
                                $method->setData(
                                    'msunifaun_agents',
                                    json_encode($agents)
                                );
                            }
                        }
                        $method->setData(
                            'msunifaun_carrier',
                            $item->getData('method')
                        );

                        if ($item->getData('description')) {
                            $method->setData('msunifaun_description', $item->getData('description'));
                        }
                        if ($item->getData('image')) {
                            $method->setData('msunifaun_image', $item->getData('image'));
                        }

                        $result->append($method);
                        $matchesCount++;
                    }
                }
            }
        }

        $this->log(__(
            'Found %1 matching rates',
            $matchesCount
        ));
        return $result;
    }
}
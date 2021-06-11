<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Crealevant\Relevant\Block;

class Current extends \Magento\Framework\View\Element\Html\Link\Current
{
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if($this->getPath() == 'sales/order/history') {
            $icon = "<div class='icon hu-cart-icon'></div>";
        }

        if($this->getPath() == 'customer/account') {
            $icon = "<div class='fa fa-tachometer'></div>";
        }

        if($this->getPath() == 'newsletter/manage') {
            $icon = "<div class='fa fa-newspaper-o'></div>";
        }

        if($this->getPath() == 'review/customer') {
            $icon = "<div class='fa fa-pencil'></div>";
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            $html .= '<strong>'. $icon .''
                . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>'. $icon .'';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel()));

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }

        return $html;
    }

}

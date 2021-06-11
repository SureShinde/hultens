<?php

namespace Crealevant\FeedManager\Model\Config\Source;

class FileType implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [['value' => 0, 'label' => __('TXT')], ['value' => 1, 'label' => __('CSV')], ['value' => 2, 'label' => __('XML')]];
	}
}

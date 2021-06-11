<?php

namespace Crealevant\AjaxifiedCatalog\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
/**
 * Class AdditionalEmail
 */
class FilterColumns extends AbstractFieldArray
{
    protected $_categoryGroupRenderer;
    protected $_attributeGroupRenderer;

    private function getAttributeGroupRenderer(){
        if (!$this->_attributeGroupRenderer) {
            $this->_attributeGroupRenderer = $this->getLayout()->createBlock(
                \Crealevant\AjaxifiedCatalog\Block\Adminhtml\Form\Field\AttributeGroup::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_attributeGroupRenderer->setClass('required-entry');
        }
        return $this->_attributeGroupRenderer;
    }
    private function getCategoryGroupRenderer(){
        if (!$this->_categoryGroupRenderer) {
            $this->_categoryGroupRenderer = $this->getLayout()->createBlock(
                \Crealevant\AjaxifiedCatalog\Block\Adminhtml\Form\Field\CategoryGroup::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_categoryGroupRenderer->setClass('required-entry');
        }
        return $this->_categoryGroupRenderer;
    }
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $requiredLabelNote = "<strong style='color: #b70f0f;padding:0px 0px 0px 5px;'>*</strong>";
        $this->addColumn(
            'attribute_group', [
                'label' => __('Attributes') .$requiredLabelNote ,
                'class' => 'required-entry',
                'extra_params' => 'multiple="multiple"',
                'renderer' => $this->getAttributeGroupRenderer(),
            ]
        );
        $this->addColumn(
            'category_group', [
                'label' => __('Categories') . $requiredLabelNote,
                'class' => 'required-entry',
                'extra_params' => 'multiple="multiple"',
                'renderer' => $this->getCategoryGroupRenderer(),
            ]
        );
        $this->addColumn('menu_position', ['label' => __('Menu Position') . $requiredLabelNote, 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Groups');
    }
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row) {
        $options = [];

        $row->setData('option_extra_attrs', $options);
    }
}
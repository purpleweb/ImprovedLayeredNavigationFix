<?php

class MOC_ShopbyFix_Helper_Layer_View_Strategy_Attribute extends Amasty_Shopby_Helper_Layer_View_Strategy_Attribute
{

    protected function setTemplate()
    {
        $template = 'mocshopbyfix/attribute.phtml';

        $isSwatchesDisplayType = is_object($this->model) && $this->model->getDisplayType() == Amasty_Shopby_Model_Source_Attribute::DT_MAGENTO_SWATCHES;
        if ($isSwatchesDisplayType) {
            if ($this->isSwatchesAvailable()) {
                $template = 'configurableswatches/catalog/layer/filter/swatches.phtml';
            } else {
                $this->model->setDisplayType(Amasty_Shopby_Model_Source_Attribute::DT_LABELS_ONLY);
            }
        }

        return $template;
    }

}

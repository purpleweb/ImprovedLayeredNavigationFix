<?php

class MOC_ShopbyFix_Block_Catalog_Category_View extends Mage_Catalog_Block_Category_View
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $helper = Mage::helper('mocshopbyfix');
        if( $helper->isRequestedFilterAttributes() )
        {
	        if ($headBlock = $this->getLayout()->getBlock('head')) {

	            $category = $this->getCurrentCategory();

	            if ($title = $category->getMetaTitleTemplate()) {
	                $headBlock->setTitle($title);
	            }
	            if ($description = $category->getMetaDescriptionTemplate()) {
	                $headBlock->setDescription($description);
	            }
	        }
        }

        return $this;
    }

}

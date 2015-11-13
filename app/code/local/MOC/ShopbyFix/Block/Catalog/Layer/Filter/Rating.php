<?php

class MOC_ShopbyFix_Block_Catalog_Layer_Filter_Rating extends Amasty_Shopby_Block_Catalog_Layer_Filter_Rating
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mocshopbyfix/attribute.phtml');
    }

}
<?php

class MOC_ShopbyFix_Helper_Layer_View_Strategy_Decimal extends Amasty_Shopby_Helper_Layer_View_Strategy_Decimal
{
    protected function setTemplate()
    {
        return 'mocshopbyfix/price.phtml';
    }
}
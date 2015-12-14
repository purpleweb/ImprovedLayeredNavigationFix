<?php

class MOC_ShopbyFix_Block_Top extends Amasty_Shopby_Block_Top
{
    /* remove canonical handled by Amasty */

    protected function _handleCanonical($page = null)
    {
    }

    protected function _replaceCanonical($url)
    {
    }

}



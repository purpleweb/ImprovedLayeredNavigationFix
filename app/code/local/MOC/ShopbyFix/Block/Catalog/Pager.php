<?php

class MOC_ShopbyFix_Block_Catalog_Pager extends Amasty_Shopby_Block_Catalog_Pager
{

    /**
     * [_construct]
     * setTemplate doesnot work
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mocshopbyfix/pager.phtml');
    }

    public function getFirstPageUrl()
    {
        return $this->getPageUrl(array());
    }

    public function getPageUrl($page)
    {
        if( $page == 1 ){
            return $this->getFirstPageUrl();
        } else {
            return $this->getPagerUrl(array($this->getPageVarName()=>$page));            
        }
    }

    public function getPreviousPageUrl()
    {
        $previousPage = $this->getCollection()->getCurPage(-1);
        if( $previousPage == 1 ){
            return $this->getFirstPageUrl();
        } else {
            return $this->getPageUrl($this->getCollection()->getCurPage(-1));
        }
    }

}


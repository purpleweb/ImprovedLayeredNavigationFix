<?php


class MOC_ShopbyFix_Model_Url_Builder extends Amasty_Shopby_Model_Url_Builder
{

    public function getUrl()
    {
        $this->updateEffectiveQuery();

        $paramPart = $this->getParamPart();
        $basePart = $this->getBasePart($paramPart);

        $url = $basePart . $paramPart;

        //Mage::log('url   : '.$url, null, 'url-builder.log', true);
        //Mage::log('base  : '.$basePart, null, 'url-builder.log', true);
        Mage::log('param : '.$paramPart, null, 'url-builder.log', true);

        if( !empty($paramPart) ){
            // if ? then no trailling slash
            if (strpos($paramPart,'?') !== false) {
                // nothing
            } else {
                // else add a slash
                $url .= '/';
            }
        }

        $url = preg_replace('|(^:)/{2,}|', '$1/', $url);

        return $url;
    }

}


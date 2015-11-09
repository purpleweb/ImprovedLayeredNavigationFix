<?php


class MOC_ShopbyFix_Model_Url_Builder extends Amasty_Shopby_Model_Url_Builder
{

    public function getUrl()
    {
        $this->updateEffectiveQuery();

        $paramPart = $this->getParamPart();
        $basePart = $this->getBasePart($paramPart);

        $url = $basePart . $paramPart;

        if( !empty($paramPart) ){
            $url .= '/';
        }

        $url = preg_replace('|(^:)/{2,}|', '$1/', $url);

        return $url;
    }

}


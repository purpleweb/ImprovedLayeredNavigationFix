<?php

class MOC_ShopbyFix_Helper extends Mage_Core_Helper_Abstract
{

    public function isRequestedFilterAttributes()
    {
        $_helper_shopby_attributes = Mage::helper('amshopby/attributes');
        $filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();

        return !empty( $filtered_attributes );
    }

    public function getRequestedFilterAttributesCount()
    {
    	$_helper_shopby_attributes = Mage::helper('amshopby/attributes');
        $filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();

        $count = 0;
        foreach ($filtered_attributes as $key => $value) {
        	$values_tab = explode(',', $value);
        	foreach ($values_tab as $value) {
        		$count++;
        	}
        }

        return $count;
    }

    public function getRobots()
    {

        // on prend la configuration de base de Magneto
        if (empty($this->_data['robots'])) {
            $this->_data['robots'] = Mage::getStoreConfig('design/head/default_robots');
        }

        // si des attributs sont selectionner on prend la config générale sur le nombre d'attribut
        $count = $this->getRequestedFilterAttributesCount();
        switch ($count) {
        	case 0:
        		$robots = $this->_data['robots'];
        		break;

        	case 1:
        		$robots = Mage::getStoreConfig('mageoncloudshopby/general/robots1');
        		break;
        	
        	case 2:
                $robots = Mage::getStoreConfig('mageoncloudshopby/general/robots2');
        		break;
        	
        	default:
                $robots = Mage::getStoreConfig('mageoncloudshopby/general/robots3');
        		break;
        }

        // si 0 ou 1 résultat, on n'index pas
        /*
        if( $this->getTotalProducts() <= 1 )
        {
            return 'noindex,nofollow';
        }*/

        // si un attribut est selectionné plusieurs fois, on n'index pas
        if( $this->getSingleAttributeMultipleSelection() )
        {
            return 'noindex,nofollow';
        }

    	return $robots;
    }

    public function getRobotsIndex(){
        $robots = $this->getRobots();
        list( $index , $follow ) = explode( ',' , $robots );
        return strtolower( $index );
    }

    public function getRobotsFollow(){
        $robots = $this->getRobots();
        list( $index , $follow ) = explode( ',' , $robots );
        return strtolower( $follow );
    }

    public function getTotalProducts()
    {
        $_category = Mage::registry('current_category');
        if( $_category ){
            $products_count = Mage::getModel('catalog/category')->load($_category->getId())->getProductCount();
            return($products_count);            
        } else {
            return 0;
        }
    }

    public function getSingleAttributeMultipleSelection()
    {
        $_helper_shopby_attributes = Mage::helper('amshopby/attributes');
        $filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();
        $return = false;

        foreach ($filtered_attributes as $key => $value)
        {
            if (strpos($value, ',') !== False) {
                $return = true;
                continue;
            }
        }

        return $return;
    }

    public function encrypt_links( $item, $force = false )
    {
        $debug = $this->getCSStoreConfigs('system/mocshopbyfix/debug');

        // Ajouter un slash a l'url pour compatibilite avec l ancienne version de Shopby
        // mais ne pas en ajouter si l'url est l'url ne contient pas de filtre
        if (strpos( $item['url'] ,'?') !== false) {
            $item['url'] = rtrim( $item['url'] );
        } else {
            if(strpos( $item['url'] ,'/l/') !== false){
                $item['url'] = rtrim( $item['url'] , '/') . '/';            
            } else {
                $item['url'] = rtrim( $item['url'] , '/');       
            }
        }


        $shopby_nofollow = false;
        // ancienne version
        if( isset( $item['css'] ) ){
            if (strpos( $item['css'] , 'rel="nofollow' ) !== false) {
                $shopby_nofollow = true;
            }            
        }
        //nouvelle version
        if( isset( $item['rel'] ) ){
            if ( $item['rel'] == ' rel="nofollow" ') {
                $shopby_nofollow = true;
            }            
        }

        $quantite_insuffisante = false;
        if( isset( $item['count'] ) ){
            $count = trim( $item['count'] );
            sscanf( $count , "(%d)" , $count );
            if( $count <= 1 ) {
                $quantite_insuffisante = true;
            }
        }

        if( $debug )
        {
            if( !function_exists('echo_bool') ){
                function echo_bool( $bool ){
                    if( $bool )
                        echo '<b style="color:green">TRUE</b>';
                    else
                        echo '<b style="color:red">FALSE</b>';
                }
            }            
            ?>
            <div style="font-size:10px; color: blue; background-color:#fefacd; padding: 5px;">
                <p>config_nb_attribute: <?php echo_bool($this->is_encrypt_enable()) ?></p>
                <p>force: <?php echo_bool($force) ?></p>
                <p>shopby_nofollow: <?php echo_bool($shopby_nofollow) ?> </p>
                <p>quantite_insuffisante: <?php echo_bool($quantite_insuffisante) ?></p>
            </div>
            <?php
        }


        if( ($this->is_encrypt_enable() || $force || $shopby_nofollow) || $quantite_insuffisante )
        {
            $item['url64'] = base64_encode($item['url']);
            $item['url'] = '#';
            //$item['label'] = 'X '.$item['label'];
            $item['label'] = $item['label'];

            $css = explode('"', $item['css']);
            $item['css'] = $css[0].' crypted ';
        } else {
            //$item['label'] = '- '.$item['label'];            
            $item['label'] = $item['label'];            
        }

        return $item;

    }

    function force_encrypt_link( $url )
    {
        return base64_encode( $url );
    }


    function is_encrypt_enable()
    {
        $count = $this->getRequestedFilterAttributesCount();

        switch ($count) {
            case 0:
                return Mage::getStoreConfig('mageoncloudshopby/general/jslinks0') == 1;
                break;

            case 1:
                return Mage::getStoreConfig('mageoncloudshopby/general/jslinks1') == 1;
                break;
            
            case 2:
                return Mage::getStoreConfig('mageoncloudshopby/general/jslinks2') == 1;
                break;
            
            default:
                return Mage::getStoreConfig('mageoncloudshopby/general/jslinks3') == 1;
                break;
        }

    }


    public function _isPageHandled()
    {
        /** @var Amasty_Shopby_Helper_Page $pageHelper */
        $pageHelper = Mage::helper('amshopby/page');
        $page = $pageHelper->getCurrentMatchedPage();

        if (is_null($page)) {
            return false;
        } else {
            return true;
        }

    }


    public function is_category_list()
    {
        if(Mage::registry('current_category')){
            if(Mage::registry('current_product')) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function is_cms()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms');
    }

}


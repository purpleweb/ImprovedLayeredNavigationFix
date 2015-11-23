<?php


class MOC_ShopbyFix_Helper_Data extends Mage_Core_Helper_Abstract
{

    public static $canonicalIgnore = array(
        'catalogsearch_result_index',
        'catalogsearch_advanced_index',
        'catalogsearch_advanced_result',
        'checkout_cart_index',
        'checkout_onepage_index',
    );

    public static $canonicalNoindexFollow = array(
        '^checkout_.+',
        '^contacts_.+',
        '^customer_.+',
        '^catalog_product_compare_.+',
        '^rss_.+',
        '^catalogsearch_.+',
        '.*?_product_send$',
        '^tag_.+',
        '^wishlist_.+',
        /*
        'mentions-legales',
        'awislider/*',
        'p=*',
        '?limit=',
        '?nosto=',
        '?',
        */
    );

    /*
    public static $canonicalNoindexNofollow = array(
        'filtre*',
        '?dir=',
    );
    */

    public function getCurrentUrl()
    {
        //return Mage::helper('core/url')->getCurrentUrl();
        $urlString = Mage::helper('core/url')->getCurrentUrl();
        $url = Mage::getSingleton('core/url')->parseUrl($urlString);
        $path = $url->getPath();
        return $path;
    }

    public function getPage()
    {
        $controller = Mage::app()->getRequest()->getControllerName();
        $action = Mage::app()->getRequest()->getActionName();
        $route = Mage::app()->getRequest()->getRouteName();
        $page = $route.'_'.$controller.'_'.$action;
        return $page;
    }

    /*===========================
      ========== UTILS ==========
      ===========================*/

    /**
     * test if page is cms
     * @return boolean
     */
    public function is_cms()
    {
        return (Mage::app()->getFrontController()->getRequest()->getRouteName() == 'cms');
    }

    /**
     * test if page is product
     * @return boolean
     */
    public function is_product()
    {
        $res = false;
        if(Mage::registry('current_product')) {
            $res = true;
        }
        return $res;
    }

    /**
     * is_category_list
     * @return boolean
     */
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

    /* Pop le dernier caractere si celui ci est un slash */
    protected function popSlash($string)
    {
        return trim( $string , '/' );
    }


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


    public function debug()
    {
        return Mage::getStoreConfig('system/mocshopbyfix/debug');
    }


    /************************
    ******* FUNCTIONS *******
    *************************/


    /**
     * [getRobots description]
     * @return text
     */
    public function getRobots()
    {

        /* ignored pages */
        $ignorePages = self::$canonicalIgnore;
        if (in_array( $this->getPage(), $ignorePages)) {
            //Mage::getSingleton('core/session')->addNotice('ignored page');
            return;
        }

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
        }
        */

        // si un attribut est selectionné plusieurs fois, on n'index pas
        if( $this->getSingleAttributeMultipleSelection() )
        {
            return 'noindex,nofollow';
        }

        /* si order=xxx noindex,nofollow */
        /* a mettre avant noindex,follow */
        $url = Mage::helper('core/url')->getCurrentUrl();
        $parsedUrl = parse_url($url, PHP_URL_QUERY);
        $output = array();
        parse_str( $parsedUrl, $output );
        //print_r($output);
        if( array_key_exists( 'order' , $output ) ){
            return 'NOINDEX, NOFOLLOW';
        }        

        /* si page de type ?p=2 noindex,follow */
        $url = Mage::helper('core/url')->getCurrentUrl();
        $parsedUrl = parse_url($url, PHP_URL_QUERY);
        $output = array();
        parse_str( $parsedUrl, $output );
        //print_r($output);
        if( array_key_exists( 'p' , $output ) ){
            return 'NOINDEX, FOLLOW';
        }

        /* Noindex Follow */
        $noindexFollow = self::$canonicalNoindexFollow;
        foreach ($noindexFollow as $entry) {
            if (preg_match('/' . $entry . '/', $this->getPage() )) {
                $robots = 'NOINDEX, FOLLOW';
                break;
            }
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
        $debug = Mage::getStoreConfig('system/mocshopbyfix/debug');

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
            //print_r($item['count']);
            $count = trim( $item['count'] );
            $count = strip_tags($count);
            $count = preg_replace("/&#?[a-z0-9]+;/i","",$count);
            list($count) = sscanf( $count , "(%d)" );
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
            $item['label'] = $item['label'];
            $css = explode('"', $item['css']);
            $item['css'] = $css[0].' crypted ';
            if($debug){
                $item['style'] = ' style="background-color: #ffcece;" ';
            }
        } else {
            $item['label'] = $item['label'];
            if($debug){
                $item['style'] = ' style="background-color: #b9f5a7;" ';
            }
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
        $_helper_shopby_attributes = Mage::helper('amshopby/attributes');
        $filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();
        if( empty($filtered_attributes) ){
            return true;
        } else {
            return false;
        }
    }


    /**
     * Check if there's filtered attributes selected in current page
     * @return boolean [description]
     */
    public function _isFilteredAttributes()
    {
        $_helper_shopby_attributes = Mage::helper('amshopby/attributes');
        $filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();
        if( empty($filtered_attributes) ){
            return false;
        } else {
            return true;
        }
    }



    /**
     * [getProductCategory description]
     * @return [type] [description]
     */
    public function getProductCategory() {
        /* @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('current_product');
        if ($product->getId()) {
            $categoryIds = $product->getCategoryIds();
            print_r($categoryIds);
            if (is_array($categoryIds) and count($categoryIds) > 1) {
                $cat = Mage::getModel('catalog/category')->load($categoryIds[0]);
                return Mage::getModel('catalog/category')->load($categoryIds[0]);
            };
        }
        return false;
    }


    /**
     * getCanonical return canonical tag
     * @return text
     */
    public function getCanonical()
    {

        $helper = Mage::helper('mocshopbyfix');
        $_DEBUG = Mage::getStoreConfig('design/head/default_robots');


        /* pour toutes les pages de CMS sauf la home page */
        if( $helper->is_cms() )
        {

            if(!Mage::getBlockSingleton('page/html_header')->getIsHomePage())
            {
                $url = $this->popSlash( Mage::helper('core/url')->getCurrentUrl() );
                $canonical = '<link rel="canonical" href="'.$url.'" />';
                if($_DEBUG) {
                    $canonical .= '<!-- Shopby canonical -cms -->';                
                }
                return $canonical;
            } else {
                $url = $this->popSlash( Mage::helper('core/url')->getCurrentUrl() );
                $canonical = '<link rel="canonical" href="'.$url.'" />';
                if($_DEBUG) {
                    $canonical .= '<!-- Shopby canonical -homepage -->';                
                }
                return $canonical;
            }
        }

        /* pour toutes les fiches produits */
        if( $this->is_product() )
        {
            $product = Mage::registry('current_product');
            if ($product->getId()) {
                $categoryIds = $product->getCategoryIds();
                $canonical = '';
                foreach ($categoryIds as $categoryId)
                {
                    $cat = Mage::getModel('catalog/category')->load($categoryId);
                    //$pUrl = $cat->getUrl().'/'.$product->getProductUrl();
                    $pUrl = $product->getProductUrl();
                    if( strlen($pUrl) > strlen($canonical) ){
                        $canonical = $pUrl;
                    }
                }
                $canonicalTag = '<link rel="canonical" href="'.$canonical.'" /><!-- Shopby canonical -product -->';
                return $canonicalTag;
            }
        }


        /* pour toutes les categories sans tri ou pager */
        if( $helper->is_category_list() )
        {
            $arrParams = Mage::app()->getRequest()->getParams();
            if(
                   array_key_exists( 'limit' , $arrParams ) 
                || array_key_exists( 'dir'   , $arrParams )
                || array_key_exists( 'order' , $arrParams )
                //|| array_key_exists( 'p'     , $arrParams )
            ) {
                              
                $current_category = Mage::registry('current_category');
                if( $current_category )
                {
                    $canonical = '<link rel="canonical" href="'.$current_category->getUrl().'" /><!-- Shopby canonical -category with attributes -->';
                } else {
                    $canonical = '';
                }
            }
            else
            {
                if( array_key_exists('p', $arrParams) )
                {
                    $canonical = '';
                }
                else
                {
                    $url = Mage::helper('core/url')->getCurrentUrl();
                    /* CATEGORIE BRUTE */
                    if(strpos( $url ,'/l/') === false){
                        $url = rtrim($url, '/');
                        $url = strtok($url, '?');
                        $url = rtrim($url, '/');
                    } else {
                        /* CATEGORIE AVEC ATTRIBUTS */
                        $url = strtok($url, '?');
                        $url = rtrim($url, '/');
                        $url = $url.'/';
                    }
                    $canonical = '<link rel="canonical" href="'.$url.'" />';
                    if($_DEBUG) {
                        $canonical .= '<!-- Shopby canonical -category -->';                
                        /*Mage::getSingleton('core/session')->addNotice(htmlspecialchars($canonical));*/
                    }
                    
                }

            }
            return $canonical;
        }


    }

}


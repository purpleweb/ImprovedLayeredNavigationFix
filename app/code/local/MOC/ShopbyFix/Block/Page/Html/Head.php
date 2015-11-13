<?php
class MOC_ShopbyFix_Block_Page_Html_Head extends Mage_Page_Block_Html_Head
{

    /**
     * Template processor instance
     *
     * @var Varien_Filter_Template
     */
    protected $_templateProcessor = null;

 


    protected function _getTemplateProcessor()
    {
        if (null === $this->_templateProcessor) {
            $this->_templateProcessor = Mage::helper('catalog')->getPageTemplateProcessor();
        }

        return $this->_templateProcessor;
    }

    /*
    public function getRobots()
    {
        $_helper = Mage::helper('mageoncloud_shopby');
        return $_helper->getRobots();
    }
    */


    public function setDescription( $str )
    {
    	$this->_data['description'] = $this->_getTemplateProcessor()->filter($str);
    }


    public function setTitle($str)
    {
    	$this->_data['title'] = $this->_getTemplateProcessor()->filter($str);
    }

    public function getTitle()
    {
        $helper = Mage::helper('mocshopbyfix');

        //print_r( $this->_layout );

        if( $helper->isRequestedFilterAttributes() )
        {
            //print_r($this->_data);
            if( isset($this->_data['meta_title_template']) ){
                $title_template = $this->_data['meta_title_template'];
            }else{
                $title_template = NULL;
            }

            if( ! empty( $title_template ) ){
                return $title_template;
            }
        }

        return  $this->_data['title'];
    }

    /* Pop le dernier caractere si celui ci est un slash */
    protected function popSlash($string)
    {
        return trim( $string , '/' );
    }

    public function getCanonical()
    {
        $helper = Mage::helper('mocshopbyfix');
        $_DEBUG = true;

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
        /* pour toutes les categories sans tri ou pager */
        elseif( $helper->is_category_list() )
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
                    }
                    
                }

            }
            return $canonical;
        }

    }

}


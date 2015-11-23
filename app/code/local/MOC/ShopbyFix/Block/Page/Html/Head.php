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

    public function getRobots()
    {
        $_helper = Mage::helper('mocshopbyfix');
        $this->_data['robots'] = $_helper->getRobots();
        return $this->_data['robots'];
    }   

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

    public function getCanonical()
    {
        $helper = Mage::helper('mocshopbyfix');
        $canonical = $helper->getCanonical();
        return $canonical;

    }

}


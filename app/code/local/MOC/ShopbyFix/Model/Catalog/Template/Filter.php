<?php

class MOC_ShopbyFix_Model_Catalog_Template_Filter extends Mage_Catalog_Model_Template_Filter
{

    const CONSTRUCTION_IFATTR_PATTERN   = '/{{ifattr\s*(.*?)}}(.*?){{\\/ifattr\s*}}/si';
    const CONSTRUCTION_IFATTRS_PATTERN  = '/{{ifanyattr\s*(.*?)}}(.*?){{\\/ifanyattr\s*}}/si';
    const CONSTRUCTION_IFNOATTR_PATTERN = '/{{ifnoattr\s*(.*?)}}(.*?){{\\/ifnoattr\s*}}/si';
    
    public function filter($value)
    {
        // "depend" and "if" operands should be first
        foreach (array(
            self::CONSTRUCTION_IFATTR_PATTERN   => 'ifattrDirective',
            self::CONSTRUCTION_IFATTRS_PATTERN  => 'ifattrsDirective',
            self::CONSTRUCTION_IFNOATTR_PATTERN => 'ifnoattrDirective',
            self::CONSTRUCTION_DEPEND_PATTERN   => 'dependDirective',
            self::CONSTRUCTION_IF_PATTERN       => 'ifDirective',
            ) as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach($constructions as $index => $construction) {
                    $replacedValue = '';
                    $callback = array($this, $directive);
                    if(!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if(preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach($constructions as $index=>$construction) {
                $replacedValue = '';
                $callback = array($this, $construction[1].'Directive');
                if(!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }
        return $value;
    }


    public function attrDirective($construction)
    {
        $params = $this->_getIncludeParameters($construction[2]);

        if (!isset($params['code']))
        {
            return '';
        }
        else
        {
            if( isset( $params['code'] ) ){
                $code   = $params['code'];
            }else {
                $code   = NULL;
            } 
            
            if( isset( $params['prefix'] ) ){
                $prefix   = $params['prefix'];
            }else {
                $prefix   = NULL;
            }
            
            if( isset( $params['suffix'] ) ){
                $suffix   = $params['suffix'];
            }else {
                $suffix   = NULL;
            }

			$_helper_shopby_attributes = Mage::helper('amshopby/attributes');
			$filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();
            //print_r( $filtered_attributes );
			if(isset($filtered_attributes[$code]) ){
                $return = '';                
				$attr_ids = explode(',',$filtered_attributes[$code]);
                #print_r($attr_ids);
				$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
				if ($attribute->usesSource()) {
				    $options = $attribute->getSource()->getAllOptions(false);
				}
				foreach ($options as $option) {
                    //if($option['value']==$attr_id){
                    if(  in_array($option['value'], $attr_ids)){
                        if( !empty($return) )
                            $return .= ', ';
						$return .= strtolower( $option['label'] );
                        continue;
					}
				}
                return $prefix.$return.$suffix;
			}
            return '';
        }  	
    }

    public function ifattrDirective($construction)
    {
    	$_helper_shopby = Mage::helper('mocshopbyfix');

		$params = explode(' ', $construction[1] );

		if( $_helper_shopby->isRequestedFilterAttributes() )
		{
			if( empty( $params ) )
			{
				return $construction[2];
			}
			else
			{
				$_helper_shopby_attributes = Mage::helper('amshopby/attributes');
				$filtered_attributes = $_helper_shopby_attributes->getRequestedFilterCodes();

				$requirements = true;

				foreach ($params as $attr_code )
				{
					if(!isset($filtered_attributes[$attr_code]) ){
						$requirements = false;
						break;
					}
				}

				if( $requirements ){
					return $construction[2];
				}

			}
		}

		return '';

    }



    public function ifnoattrDirective($construction)
    {
        //print_r( $construction );

        $_helper_shopby = Mage::helper('mocshopbyfix');

        //$params = explode(' ', $construction[1] );

        if( ! $_helper_shopby->isRequestedFilterAttributes() )
        {
            return $construction[2];
        }

        return '';

    }  
    public function ifanyattrDirective($construction)
    {

        #print_r( $construction );

        $_helper_shopby = Mage::helper('mocshopbyfix');

        $params = explode(' ', $construction[1] );

        if( $_helper_shopby->isRequestedFilterAttributes() )
        {
            return $construction[2];
        }

        return '';

    }    

}


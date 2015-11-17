<?php

class MOC_ShopbyFix_Model_Observer
{

	public function check(Varien_Event_Observer $observer)
	{

		$debug = Mage::getStoreConfig('system/mocshopbyfix/debug');

		if( $debug ) {

			$helper = Mage::helper('mocshopbyfix');
			$canonical = $helper->getCanonical();
			if(empty($canonical)){
				Mage::getSingleton('core/session')->addNotice('No canonical defined');
			} else {
				Mage::getSingleton('core/session')->addNotice(htmlspecialchars($canonical));
			}

			$robots = $helper->getRobots();
			Mage::getSingleton('core/session')->addNotice(htmlspecialchars($robots));

		}

	}

}

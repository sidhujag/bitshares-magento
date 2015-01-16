<?php
class Bitshares_Bitshares_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'Bitshares';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	/**
	 * Can show this payment method as an option on checkout payment page?
	 */
	protected $_canUseCheckout          = true;
	/**
	 * Is this payment method a gateway (online auth/charge) ?
	 */
	protected $_isGateway               = true;
 
	/**
	 * Can authorize online?
	 */
	protected $_canAuthorize            = true;
  	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('Bitshares/payment/redirect', array('_secure' => true));
	}
}
?>
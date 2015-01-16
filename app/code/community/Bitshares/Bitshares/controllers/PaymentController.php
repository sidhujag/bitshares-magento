<?php
/*
Bitshares Payment Controller
By: Jag Sidhu
*/

class Bitshares_Bitshares_PaymentController extends Mage_Core_Controller_Front_Action {
	// The redirect action is triggered when someone places an order
	public function redirectAction() {
		$this->loadLayout();
        $block = $this->getLayout()->createBlock('Mage_Core_Block_Template','Bitshares',array('template' => 'Bitshares/redirect.phtml'));
		$this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
	}
	
	public function responseAction() {
		
	}
	
}
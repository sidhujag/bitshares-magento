<?php
$path = getcwd();
chdir(ROOT.'..');
require 'app/Mage.php';
umask(0);
Mage::app('default');
chdir($path);
require 'config.php';

function getOrderCartHelper($id)
{
 
  // get all open orders
  if($id == NULL)
  {

    $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('state',Mage_Sales_Model_Order::STATE_NEW);
    return $orders;
  }
  // get single 
  else
  {

    $order = Mage::getModel('sales/order')->loadByIncrementId($id);
    if ($order->getId()) {
        
        return $order;
    }

  }
  return FALSE;
  
}
// response_code Q=pending, P=complete
function getOrderWithStatusFromCartHelper($id, $response_code)
{
	$response = getOrderCartHelper($id);
  $orders = array();
	if ($response !== FALSE){
    if($id === NULL)
    {
      
      // loop over all orders
        foreach($response->getItems() as $order)
        {
           $orderModel = Mage::getModel('sales/order');
           $orderModel->load($order['entity_id']); 
           $valid = FALSE;
           if($response_code === 'P')
           {
            if($orderModel->getState() === Mage_Sales_Model_Order::STATE_COMPLETE)
            {
              $valid = TRUE;
            }
            
           }
           else if($response_code === 'Q')
           {
            if($orderModel->getState() === Mage_Sales_Model_Order::STATE_NEW)
            {
              $valid = TRUE;
            }       
           }         
          if($valid)
           {
           
              $currency = $orderModel->getBaseCurrency();
              if (is_object($currency)) {
                  $currencyCode = $currency->getCurrencyCode();
              }
              $ret = array (
                "order_id" => $orderModel->getRealOrderId(),
	              "currency" =>	$currencyCode,
	              "order_total" =>	$orderModel->getTotalDue()
	            );
              array_push($orders, $ret);
            }
      }
      
      
    }
    else 
    {
      if($response->getRealOrderId() == $id)
      {

       $valid = FALSE;
       if($response_code === 'P')
       {
        if($response->getState() === Mage_Sales_Model_Order::STATE_COMPLETE)
        {
          $valid = TRUE;
        }
        
       }
       else if($response_code === 'Q')
       {
        if($response->getState() === Mage_Sales_Model_Order::STATE_NEW)
        {
          $valid = TRUE;
        }       
       }
       if($valid)
       {
       
          $currency = $response->getBaseCurrency();
          if (is_object($currency)) {
              $currencyCode = $currency->getCurrencyCode();
          }
          $ret = array (
            "order_id" => $id,
	          "currency" =>	$currencyCode,
	          "order_total" =>	$response->getTotalDue()
	        );
          array_push($orders, $ret);
        }
      }
    }  
  }
	return $orders;
}

function sendToCart($order_id, $statusCode)
{
  $response = array();
  $order = Mage::getModel('sales/order');
	$order->loadByIncrementId($order_id);
  if($order->getId()) 
  {
    if($statusCode === 'P')
    {
	      $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has authorized the payment.');
	  }
    else if($statusCode === 'C')
    {
       
       $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has cancelled the payment.');
       
    }
	  $order->sendNewOrderEmail();
	  $order->setEmailSent(true);
  	
	  $order->save();
    
  }
  else
  {
    $response['error'] = 'Could not find this order in the system, please review the Order ID and Memo';
  }
	
  
	return $response;
}
function getOpenOrdersUser()
{

	$openOrderList = array();
  // find open orders status id (not paid)
	$result = getOrderWithStatusFromCartHelper(NULL, 'Q');
  foreach ($result as $responseOrder) {
		$newOrder = array();
		$total = $responseOrder['order_total'];
		$total = number_format((float)$total,2);		
		$newOrder['total'] = $total;
		$newOrder['currency_code'] = $responseOrder['currency'];
		$newOrder['order_id'] = $responseOrder['order_id'];
		$newOrder['date_added'] = 0;
		array_push($openOrderList,$newOrder);    
	}
  echo json_encode($openOrderList);
	return $openOrderList;
}
function isOrderCompleteUser($memo, $order_id)
{
  // find orders with id order_id and status id (completed)
	$result = getOrderWithStatusFromCartHelper($order_id, 'P');
	foreach ($result as $responseOrder) {
			$total = $responseOrder['order_total'];
			$total = number_format((float)$total,2);
			$asset = btsCurrencyToAsset($responseOrder['currency']);
			$hash =  btsCreateEHASH(accountName,$order_id, $total, $asset, hashSalt);
			$memoSanity = btsCreateMemo($hash);		
			if($memoSanity === $memo)
			{	
				return TRUE;
			}
	}
	return FALSE;	
}
function doesOrderExistUser($memo, $order_id)
{
  // find orders with id order_id and status id (not paid)
	$result = getOrderWithStatusFromCartHelper($order_id, 'Q');
	foreach ($result as $responseOrder) {
			$total = $responseOrder['order_total'];
			$total = number_format((float)$total,2);
			$asset = btsCurrencyToAsset($responseOrder['currency']);
      $hash =  btsCreateEHASH(accountName,$order_id, $total, $asset, hashSalt);
      $memoSanity = btsCreateMemo($hash);
			if($memoSanity === $memo)
			{	
				$order = array();
				$order['order_id'] = $order_id;
				$order['total'] = $total;
				$order['asset'] = $asset;
				$order['memo'] = $memo;	
				return $order;
			}
	}
	return FALSE;
}

function completeOrderUser($order)
{
  $response = sendToCart($order['order_id'], 'P');  
	if(!array_key_exists('error', $response))
	{	
		$response['url'] = baseURL.'checkout/onepage/success';
	} 
	return $response;
}
function cancelOrderUser($order)
{
  $response = sendToCart($order['order_id'], 'C');  
	if(!array_key_exists('error', $response))
	{	
		$response['url'] = baseURL.'checkout/onepage/failure';
	}   
  
	return $response;
}
function cronJobUser()
{
  cancelOldPendingOrders();
	return 'Success!';
}
function createOrderUser()
{

	$order_id    = $_REQUEST['order_id'];
	$asset = btsCurrencyToAsset($_REQUEST['code']);
	$total = number_format((float)$_REQUEST['total'],2);
	$hash =  btsCreateEHASH(accountName,$order_id, $total, $asset, hashSalt);
	$memo = btsCreateMemo($hash);
	$ret = array(
		'accountName'     => accountName,
		'order_id'     => $order_id,
		'memo'     => $memo
	);
	
	return $ret;	
}
function cancelOldPendingOrders()
{
    $orderCollection = Mage::getResourceModel('sales/order_collection');

    $orderCollection
            ->addFieldToFilter('state', array(Mage_Sales_Model_Order::STATE_NEW, Mage_Sales_Model_Order::STATE_CANCELED))
            ->addFieldToFilter('created_at', array(
      'lt' =>  new Zend_Db_Expr("DATE_ADD('".now()."', INTERVAL 1 MONTH)")))
            ->getSelect()
            ->order('e.entity_id')
            ->limit(10000);


    foreach($orderCollection->getItems() as $order)
    {
      $orderModel = Mage::getModel('sales/order');
      $orderModel->load($order['entity_id']);

      $orderModel->cancel();
      $orderModel->setState(Mage_Sales_Model_Order::STATE_CANCELED);
      $orderModel->save();

    }
}
?>
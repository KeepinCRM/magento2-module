<?php

namespace KeepinCRM\Core\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderExtensionInterface;

use \Magento\Framework\Event\Observer as Observer;
use \KeepinCRM\Core\Helper\Data as Helper;

use Psr\Log\LoggerInterface;

class PlaceOrderAfter implements ObserverInterface {
  protected $logger;
  protected $_helper;

  public function __construct(
    Helper $helper,
    LoggerInterface $logger
  ) {
    $this->_helper = $helper;
    $this->_logger = $logger;
  }

  public function execute(Observer $observer) {
    $this->api_key = $this->_helper->getApiKey();
    $this->source_id = $this->_helper->getSourceId();

    $this->payment_method_field_id = $this->_helper->getPaymentMethodFieldId();
    $this->delivery_method_field_id = $this->_helper->getDeliveryMethdFieldId();
    $this->address_field_id = $this->_helper->getAddressFieldId();

    if (!isset($this->api_key)) {
      return;
    }

    try {
      $order = $observer->getEvent()->getOrder();
      $shippingAddress = $order->getShippingAddress();

      $i = 0;
      $products_list = array();

      foreach ($order->getAllItems() as $item) {
        $products_list[$i] = array (
          'amount'              => $item->getQtyOrdered(),
          'title'               => $item->getName(),
          'product_attributes'  => array (
            'sku'               => $item->getSku(),
            'title'             => $item->getName(),
            'price'             => $item->getPrice()
          )
        );

        $i++;
      }

      $i = 0;
      $custom_fields = array();

      if ($this->payment_method_field_id) {
        $custom_fields[$i] = array (
          'name'         => 'field_' . $this->payment_method_field_id,
          'value'        => $order->getPayment()->getAdditionalInformation('method_title')
        );
        $i++;
      }
      if ($this->delivery_method_field_id) {
        $custom_fields[$i] = array (
          'name'         => 'field_' . $this->delivery_method_field_id,
          'value'        => $order->getShippingDescription()
        );
        $i++;
      }
      if ($this->address_field_id) {
        $custom_fields[$i] = array (
          'name'         => 'field_' . $this->address_field_id,
          'value'        => $shippingAddress->getData('city') . ' ' . $shippingAddress->getData('street')
        );
        $i++;
      }

      $order_details = array (
        'title'             => $order->getIncrementId(),
        'comment'           => $order['status_histories'] ? $order['status_histories'][0]['comment'] : '',
        'source_id'         => $this->source_id,
        'client_attributes' => array (
          'person'          => $shippingAddress->getFirstname() . ' ' . $shippingAddress->getMiddlename() . ' ' . $shippingAddress->getLastname(),
          'email'           => $shippingAddress->getEmail(),
          'lead'            => false,
          'source_id'       => $this->source_id,
          'phones'          => array (
            0 => $shippingAddress->getTelephone()
          )
        ),
        'jobs_attributes'   => $products_list,
        'custom_fields'     => $custom_fields
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://api.keepincrm.com/v1/agreements');
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Auth-Token: ' . $this->api_key, 'Content-Type: application/json'));
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($order_details));
      curl_exec($curl);
      curl_close($curl);
    } catch (\Exception $e) {
      $this->_logger->info($e->getMessage());
    }
  }
}

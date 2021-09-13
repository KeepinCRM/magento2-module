<?php

namespace KeepinCRM\Core\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class PlaceOrderAfter implements ObserverInterface {
  protected $logger;

  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  public function execute(\Magento\Framework\Event\Observer $observer) {
    try {
      $order = $observer->getEvent()->getOrder();

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, 'https://api.keepincrm.com/v1/agreements');
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Auth-Token: Jz2TV6sWuhXibwJkd1v25rvr', 'Content-Type: application/json'));
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_POST, 1);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($order));
      curl_exec($curl);
      curl_close($curl);

    } catch (\Exception $e) {
      $this->logger->info($e->getMessage());
    }
  }
}

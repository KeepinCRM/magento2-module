<?php

namespace KeepinCRM\Core\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Escaper;

use \Magento\Framework\Event\Observer as Observer;
use \KeepinCRM\Core\Helper\Data as Helper;

use Psr\Log\LoggerInterface;

class MainObserver implements ObserverInterface {
  protected $logger;
  protected $_helper;
  protected $_request;
  protected $_escaper;

  public function __construct(
    Helper $helper,
    LoggerInterface $logger,
    Http $request,
    Escaper $escaper
  ) {
    $this->_helper = $helper;
    $this->_logger = $logger;
    $this->_request = $request;
    $this->_escaper = $escaper;
  }

  public function execute(Observer $observer) {
    $this->api_key = $this->_helper->getApiKey();
    $this->source_id = $this->_helper->getSourceId();

    if (!isset($this->api_key)) {
      return;
    }

    try {
      if ($this->_request->getFullActionName() == 'contact_index_post') {
        $request = $this->_request->getParams();

        if ($request) {
          $task_details = array (
            'title'               => "Зворотній зв'язок",
            'comment'             => $this->_escaper->escapeHtml($request['comment']),
            'source_id'           => $this->source_id,
            'client_attributes'   => array (
              'person'            => $this->_escaper->escapeHtml($request['name']),
              'email'             => $this->_escaper->escapeHtml($request['email']),
              'lead'              => true,
              'source_id'         => $this->source_id,
              'phones'            => array (
                0 => $this->_escaper->escapeHtml($request['telephone'])
              )
            )
          );

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_URL, 'https://api.keepincrm.com/v1/tasks');
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'X-Auth-Token: ' . $this->api_key, 'Content-Type: application/json'));
          curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($curl, CURLOPT_POST, 1);
          curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($task_details));
          curl_exec($curl);
          curl_close($curl);
        }
      }
    } catch (\Exception $e) {
      $this->_logger->info($e->getMessage());
    }
  }
}

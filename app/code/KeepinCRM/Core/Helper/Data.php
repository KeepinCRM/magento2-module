<?php

namespace KeepinCRM\Core\Helper;

use \Magento\Store\Model\ScopeInterface as ScopeInterface;
use \Magento\Framework\App\Helper\AbstractHelper as AbstractHelper;

class Data extends AbstractHelper {
  public function getConfig($configPath) {
    return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
  }

  public function getApiKey() {
    return $this->getConfig('keepincrm_config/general_configuration/api_key');
  }

  public function getSourceId() {
    return $this->getConfig('keepincrm_config/general_configuration/source_id');
  }

  public function getPaymentMethodFieldId() {
    return $this->getConfig('keepincrm_config/general_configuration/payment_method_field_id');
  }

  public function getDeliveryMethdFieldId() {
    return $this->getConfig('keepincrm_config/general_configuration/delivery_method_field_id');
  }

  public function getAddressFieldId() {
    return $this->getConfig('keepincrm_config/general_configuration/address_field_id');
  }
}

<?php
namespace Foxsea\Paghiper\Observer;

use Magento\Payment\Observer\AbstractDataAssignObserver;

class DataAssignObserver extends AbstractDataAssignObserver {

    protected $_session;

    public function __construct(\Magento\Checkout\Model\Session $session) {
        $this->_session = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $paymentInfo = $method->getInfoInstance();

        $quoteTaxvat = strval($this->_session->getQuote()->getBillingAddress()->getVatId());

        // $this->log("Inside DataAssignObserver class!");
        $this->log("Quote taxvat: '" . $quoteTaxvat . "'");

        if ($data->getDataByKey('additional_data') !== null){
            $additional = $data->getDataByKey('additional_data');
            if (isset($additional['paghiper_taxvat']) && '' !== $additional['paghiper_taxvat']) {
                $paymentInfo->setAdditionalInformation(
                    'paghiper_taxvat',
                    $additional['paghiper_taxvat']
                );
            } else if ('' !== $quoteTaxvat) {
                $this->log("Vat ID is set: '" . $quoteTaxvat . "'");
                $paymentInfo->setAdditionalInformation(
                    'paghiper_taxvat',
                    $quoteTaxvat
                );
            }
        }
    }

    /* public function execute(\Magento\Framework\Event\Observer $observer) {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $paymentInfo = $method->getInfoInstance();

        if($data->getDataByKey('additional_data') !== null){
            $additional = $data->getDataByKey('additional_data');
            if(isset($additional['paghiper_taxvat'])){
                $paymentInfo->setAdditionalInformation(
                    'paghiper_taxvat',
                    $additional['paghiper_taxvat']
                );
            }
        }
    } */

    protected function log($msg){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paghiper.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($msg);
    }
}

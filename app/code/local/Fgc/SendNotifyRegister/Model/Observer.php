<?php

class Fgc_SendNotifyRegister_Model_Observer {

    public function customerRegisterSuccess(Varien_Event_Observer $observer) {
        $templateId = 2;
        $customer = $observer->getCustomer()->getData();
        
        $email = $customer['email'];
        $fullname = $customer['firstname'] . ' ' . $customer['lastname'];
        $phone = $_REQUEST['telephone'];

        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');

        $senderName = 'Main site';
        $sender = array('name' => $senderName,
            'email' => $adminEmail);
        $recepientName = "Admin";
        /*
         *  Get Store ID		
         */
        $storeId = Mage::app()->getStore()->getId();

        /*
         * Set variables that can be used in email template
         * Khoi tao cac bien de su hien thi trong transactional mail
         * De su dung ta chi can goi {{username}}
         */
        $vars = array(
            'username' => $fullname,
            'useremail' => $email,
            'userphone' => $phone,
        );

        $translate = Mage::getSingleton('core/translate');

        /*
         *  Send Transactional Email
         */
        Mage::getModel('core/email_template')
                ->sendTransactional($templateId, $sender, $adminEmail, $recepientName, $vars, $storeId);

        $translate->setTranslateInline(true);
        
        $_custom_address = array(
/*            'firstname' => 'Branko',
            'lastname' => 'Ajzele',
            'street' => array(
                '0' => 'Sample address part1',
                '1' => 'Sample address part2',
            ),
            'city' => 'Osijek',
            'region_id' => '',
            'region' => '',
            'postcode' => '31000',
                                 */
            'telephone' => $phone,
        );
        $customAddress = Mage::getModel('customer/address');
//$customAddress = new Mage_Customer_Model_Address();
        $customAddress->setData($_custom_address)
                ->setCustomerId($customer['entity_id'])
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
        try {
            $customAddress->save();
        } catch (Exception $ex) {
            //Zend_Debug::dump($ex->getMessage());
        }
    }

}

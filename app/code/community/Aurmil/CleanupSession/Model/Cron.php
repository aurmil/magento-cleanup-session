<?php
/**
 * @author Aurélien Millet
 * @link https://github.com/aurmil/magento-cleanup-session
 * @license https://github.com/aurmil/magento-cleanup-session/blob/master/LICENSE.md
 */

class Aurmil_CleanupSession_Model_Cron
extends Mage_Core_Model_Abstract
{
    const XML_PATH_EMAIL_SESSION_CLEAN_TEMPLATE     = 'system/session_clean/error_email_template';
    const XML_PATH_EMAIL_SESSION_CLEAN_IDENTITY     = 'system/session_clean/error_email_identity';
    const XML_PATH_EMAIL_SESSION_CLEAN_RECIPIENT    = 'system/session_clean/error_email';
    const XML_PATH_SESSION_CLEAN_ENABLED            = 'system/session_clean/enabled';

    public function sessionClean()
    {
        if (Mage::getStoreConfigFlag(self::XML_PATH_SESSION_CLEAN_ENABLED)) {
            $errorMessage = null;

            switch (Mage::getSingleton('core/session')->getSessionSaveMethod()) {
                case 'files':
                    $sessions = glob(Mage::getSingleton('core/session')->getSessionSavePath()
                                    . DS
                                    . 'sess_*');

                    if (is_array($sessions) && !empty($sessions)) {
                        $timeLimit = time() - (int) Mage::getResourceSingleton('core/session')->getLifeTime();

                        foreach ($sessions as $session) {
                            if (file_exists($session) && is_file($session)
                                && filemtime($session) < $timeLimit
                            ) {
                                if (!@unlink($session) && !$errorMessage) {
                                    $errorMessage = Mage::helper('aurmil_cleanupsession')->__(
                                        'Some sessions files could not be deleted.'
                                    );
                                }
                            }
                        }
                    }

                    break;

                case 'db':
                    try {
                        // Mage::getSingleton('core/resource')->gc() is too random
                        $resource = Mage::getSingleton('core/resource');
                        $resource->getConnection('core_write')
                            ->delete(
                                $resource->getTableName('core/session'),
                                array('session_expires < ?' => time(),
                            ));
                    } catch (Exception $e) {
                        $errorMessage = Mage::helper('aurmil_cleanupsession')->__(
                            'An error occurred while cleaning database session table: %s',
                            "\n\n" . $e->getMessage() . "\n\n" . $e->getTraceAsString()
                        );
                    }

                    break;

                default:
                    $errorMessage = Mage::helper('aurmil_cleanupsession')->__(
                        'Unsupported session save method: %s',
                        (string) Mage::getSingleton('core/session')->getSessionSaveMethod()
                    );

                    break;
            }

            if ($errorMessage
                && Mage::getStoreConfig(self::XML_PATH_EMAIL_SESSION_CLEAN_RECIPIENT)
            ) {
                $emailTemplate = Mage::getModel('core/email_template');
                /* @var $emailTemplate Mage_Core_Model_Email_Template */
                $emailTemplate->setDesignConfig(array('area' => 'backend'))
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SESSION_CLEAN_TEMPLATE),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SESSION_CLEAN_IDENTITY),
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_SESSION_CLEAN_RECIPIENT),
                        null,
                        array('warnings' => $errorMessage)
                    );
            }
        }
    }
}

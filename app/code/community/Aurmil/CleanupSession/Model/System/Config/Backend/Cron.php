<?php
/**
 * @author Aurélien Millet
 * @link https://github.com/aurmil/magento-cleanup-session
 * @license https://github.com/aurmil/magento-cleanup-session/blob/master/LICENSE.md
 */

class Aurmil_CleanupSession_Model_System_Config_Backend_Cron
extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH  = 'crontab/jobs/aurmil_session_clean/schedule/cron_expr';

    protected function _afterSave()
    {
        $enabled   = $this->getData('groups/session_clean/fields/enabled/value');
        $time      = $this->getData('groups/session_clean/fields/time/value');
        $frequency = $this->getData('groups/session_clean/fields/frequency/value');

        $frequencyDaily   = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY;
        $frequencyWeekly  = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;

        $cronExprString = '';
        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                              # Minute
                intval($time[0]),                              # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*', # Day of the Month
                '*',                                           # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',  # Day of the Week
            );
            $cronExprString = implode(' ', $cronExprArray);
        }

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
        }
    }
}

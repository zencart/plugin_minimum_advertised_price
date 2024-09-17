<?php
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected function executeInstall()
    {
        global $sniffer;

        // check for and ensure that the database contains the required fields for MAP support:
        if ($sniffer->field_exists(TABLE_PRODUCTS, 'map_enabled', true) !== true) {
            $sql = "ALTER TABLE " . TABLE_PRODUCTS . "
                    ADD map_enabled TINYINT NOT NULL DEFAULT 0 AFTER products_priced_by_attribute,
                    ADD map_price DECIMAL(15, 4) NOT NULL DEFAULT '0.00' AFTER map_enabled";
            $this->executeInstallerSql($sql);
        }

        return true;
    }
}

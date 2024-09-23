<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Last updated by DrByte 2024-09-17   $
 *
 * Original concept MAP Pricing contributed by SlickRicky Design : http://www.slickricky.com
 */

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

    // Uninstall not implemented, because we don't want to drop these columns if the user is accidentally "uninstalling" to troubleshoot something
    // However, the README gives the SQL to run in the SQL Patch tool if they want to clean it up themselves.
}

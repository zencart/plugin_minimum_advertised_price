<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Last updated by DrByte 2024-09-17   $
 *
 * Original concept MAP Pricing contributed by SlickRicky Design : http://www.slickricky.com
 */
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$autoLoadConfig[200][] = [
    'autoType' => 'class',
    'loadFile' => 'observers/MapMinimumAdvertisedPriceAdmin.php',
    'classPath' => DIR_WS_CLASSES,
];
$autoLoadConfig[200][] = [
    'autoType' => 'classInstantiate',
    'className' => 'MapMinimumAdvertisedPriceAdmin',
    'objectName' => 'minAdvPriceAdminObserver',
];

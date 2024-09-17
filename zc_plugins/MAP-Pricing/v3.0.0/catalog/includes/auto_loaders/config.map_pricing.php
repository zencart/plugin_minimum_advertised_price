<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$autoLoadConfig[200][] = [
    'autoType' => 'class',
    'loadFile' => 'observers/MapMinimumAdvertisedPriceCatalog.php',
    'classPath' => DIR_WS_CLASSES,
];
$autoLoadConfig[200][] = [
    'autoType' => 'classInstantiate',
    'className' => 'MapMinimumAdvertisedPriceCatalog',
    'objectName' => 'minAdvPriceCatalogObserver',
];

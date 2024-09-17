<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2024-09-17   $
 *
 * Original concept MAP Pricing contributed by SlickRicky Design : http://www.slickricky.com
 *
 * Designed for v2.1.0+ (depends on plugin directory lookup capabilities added in v2.1.0 )
 */

use App\Models\PluginControl;
use App\Models\PluginControlVersion;
use Zencart\PluginManager\PluginManager;

class MapMinimumAdvertisedPriceCatalog extends base
{
    protected bool $enabled = true;
    protected bool $show_map_prices_in_admin_listing = false;

    protected ?int $product_id = null;
    protected string $default_MAP_message;
    protected string $zcPluginDir;
    protected string $plugin_name = 'MAP-Pricing';


    public function __construct()
    {
        if (!$this->enabled) {
            return;
        }

        // @TODO - this feature is not implemented yet (needs reworking output for admin table columns, instead of customer-facing messaging)
        if (IS_ADMIN_FLAG && !$this->show_map_prices_in_admin_listing) {
            return;
        }

        defined('MAP_PRICING_ENABLED') || define('MAP_PRICING_ENABLED', 'true');

        $this->attach($this, [
            // Template hook to allow adding CSS
            'NOTIFY_HTML_HEAD_END',

            // Pricing functions
            'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_SALE',
            'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_SPECIAL',
            'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_NORMAL',
            'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_FREE_OR_CALL',
        ]);

        defined('MAP_PRICE_STORE_FRONT_TEXT') || define('MAP_PRICE_STORE_FRONT_TEXT', "Priced so low, we're not able to advertise it. Add to cart for price.");
        defined('MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED') || define('MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED', "For an even lower price, add to cart.");

        $this->default_MAP_message = '<span class="map_pricing">' . MAP_PRICE_STORE_FRONT_TEXT . '</span>';

        /**
         * Determine this zc_plugin's installed directory; used to attach CSS hrefs
         */
        $plugin_manager = new PluginManager(new PluginControl(), new PluginControlVersion());
        $this->zcPluginDir = str_replace(
            DIR_FS_CATALOG,
            '',
            $plugin_manager->getPluginVersionDirectory($this->plugin_name, $plugin_manager->getInstalledPlugins()) . 'catalog/'
        );

    }

    /* Note: This update() method fires for all the DISPLAY_PRICE notifiers, because there is no
     *       function named specifically for those, in this observer.
     *       This is intentional because all 4 of those hooks use shared parameters/logic patterns.
     */
    public function update(&$class, $eventID, $param1, &$isHandled, &$param3, &$param4, &$param5): void
    {
        /** @var queryFactory $db */
        global $db;

        $skipAddingMessage = false;
        $is_map_enabled = false;

        if (!empty($param1['products_id'])) {
            $this->product_id = (int)$param1['products_id'];
        }

        if (!empty($this->product_id)) {
            $query = "SELECT map_enabled, map_price FROM " . TABLE_PRODUCTS . " WHERE products_id = " . (int)$this->product_id;
            $result = $db->Execute($query, 1);
            $is_map_enabled = ($result->fields['map_enabled'] ?? 0) > 0;
            $map_price = convertToFloat($result->fields['map_price'] ?? 0);
        }

        if (!$is_map_enabled) {
            return;
        }

        if ($eventID === 'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_SALE') {
            $isHandled = true; // $pricing_handled
            $param3 = ''; // $show_sale_discount
        }
        if ($eventID === 'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_SPECIAL') {
            $isHandled = true; // $pricing_handled
            $param3 = ''; // $show_normal_price
            $param4 = ''; // $show_special_price
            $param5 = ''; // $show_sale_price
        }
        if ($eventID === 'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_NORMAL') {
            $isHandled = true; // $pricing_handled
            $param3 = ''; // $show_normal_price
            $param4 = ''; // $show_special_price
            $param5 = ''; // $show_sale_price
        }
        if ($eventID === 'NOTIFY_ZEN_GET_PRODUCTS_DISPLAY_PRICE_FREE_OR_CALL') {
            $isHandled = true; // $tags_handled
            $param3 = ''; // $free_tag
            $param4 = ''; // $call_tag
            $skipAddingMessage = true; // don't set MAP message in this case, else it may be duplicated since it is already set for DISPLAY_PRICE_NORMAL
        }

        if (!$skipAddingMessage) {
            $param3 = $this->default_MAP_message;
            if (!empty($map_price)) {
                $param3 = '<span class="map_pricing">$' . round($map_price, 2) . ' ' . MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED . '</span>';
            }
        }
    }

    /**
     * Catalog: Runs at the end of the active template's html_header.php (just before the </head> tag)
     * Enables the plugin's CSS file to be inserted.
     */
    protected function notify_html_head_end(&$class, string $current_page_base): void
    {
        global $template;

        $stylesheet = 'map_pricing.css';
        echo '<link rel="stylesheet" href="' . $this->getZcPluginDir() . DIR_WS_TEMPLATES . "default/css/$stylesheet" . '">' . "\n";

//        // legacy support for old v2 css filename
//        $stylesheet = 'stylesheet_map_addition.css';
//        $stylesheet_dir = $template->get_template_dir($stylesheet, DIR_WS_TEMPLATE, $current_page_base, 'css');
//        if (strpos($stylesheet_dir, $this->getZcPluginDir()) === false && file_exists($stylesheet_dir . $stylesheet)) {
//            echo '<link rel="stylesheet" href="' . $stylesheet_dir . $stylesheet . '">' . "\n";
//        }
//        $stylesheet = 'map_pricing.css';

        $stylesheet_dir = $template->get_template_dir($stylesheet, DIR_WS_TEMPLATE, $current_page_base, 'css');
        if (!str_contains($stylesheet_dir, $this->getZcPluginDir()) && file_exists($stylesheet_dir . $stylesheet)) {
            echo '<link rel="stylesheet" href="' . $stylesheet_dir . $stylesheet . '">' . "\n";
        }
    }

    /**
     * Return the plugin's currently-installed zc_plugin directory for the catalog.
     */
    public function getZcPluginDir(): string
    {
        return $this->zcPluginDir;
    }
}

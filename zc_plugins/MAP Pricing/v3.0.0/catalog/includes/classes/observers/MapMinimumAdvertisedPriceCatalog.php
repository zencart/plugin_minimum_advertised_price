<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Last updated by DrByte 2024-09-17   $
 *
 * Original concept MAP Pricing contributed by SlickRicky Design : http://www.slickricky.com
 *
 * Designed for v2.1.0+ (depends on InteractsWithPlugins trait added in v2.1.0 )
 */

use Zencart\Traits\InteractsWithPlugins;
use Zencart\Traits\NotifierManager;
use Zencart\Traits\ObserverManager;

class MapMinimumAdvertisedPriceCatalog
{
    use InteractsWithPlugins;
    use NotifierManager;
    use ObserverManager;

    protected bool $enabled = true;

    protected ?int $product_id = null;
    protected string $default_MAP_message;


    public function __construct()
    {
        if (!$this->enabled) {
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
        defined('MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED') || define('MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED', "%s For an even lower price, add to cart.");

        $this->default_MAP_message = '<span class="map_pricing">' . MAP_PRICE_STORE_FRONT_TEXT . '</span>';

        /**
         * Determine this zc_plugin's paths: $this->zcPluginCatalogPath is used to attach CSS hrefs
         */
        $this->detectZcPluginDetails(__DIR__);
    }

    /* Note: This update() method fires for all the DISPLAY_PRICE notifiers, because there is no
     *       function named specifically for those, in this observer.
     *       This is intentional because all 4 of those hooks use shared parameters/logic patterns.
     */
    public function update(&$class, $eventID, $param1, &$isHandled, &$param3, &$param4, &$param5): void
    {
        /** @var currencies $currencies */
        /** @var queryFactory $db */
        global $db, $currencies;

        $skipAddingMessage = false;
        $is_map_enabled = false;

        if (!empty($param1['products_id'])) {
            $this->product_id = (int)$param1['products_id'];
        }

        $products_tax_rate = zen_get_tax_rate($param1['products_tax_class_id'] ?? 0);

        if (!empty($this->product_id)) {
            $query = "SELECT map_enabled, map_price FROM " . TABLE_PRODUCTS . " WHERE products_id = " . (int)$this->product_id;
            $result = $db->Execute($query, 1);
            $is_map_enabled = ($result->fields['map_enabled'] ?? 0) > 0;
            $map_price = convertToFloat($result->fields['map_price'] ?? 0);
            $map_price_formatted = $currencies->display_price($map_price, $products_tax_rate);
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
                $param3 = '<span class="map_pricing">' . sprintf(MAP_PRICE_STORE_FRONT_TEXT_WITH_MAP_PRICE_DISPLAYED, '<span class="map_price">' . $map_price_formatted . '</span>') . '</span>';
            }
        }
    }

    /**
     * Catalog: Runs at the end of the active template's html_header.php (just before the </head> tag)
     * Enables the plugin's CSS file to be inserted.
     */
    public function notify_html_head_end(&$class, $eventID, string $current_page_base): void
    {
        $this->linkCatalogStylesheet('map_pricing.css', $current_page_base);
    }
}

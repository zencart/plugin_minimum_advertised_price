<?php
/**
 * @copyright Copyright 2003-2024 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: DrByte 2024-09-17   $
 *
 * Original concept MAP Pricing contributed by SlickRicky Design : http://www.slickricky.com
 *
 * Designed for v2.1.0+ (Depends on notifier hooks in admin collect_info.php, added in v2.1.0)
 */

class MapMinimumAdvertisedPriceAdmin extends base
{
    protected bool $enabled = true;
    protected ?int $product_id = null;

    public function __construct()
    {
        if (!$this->enabled) {
            return;
        }

        $this->attach($this, [
            // template for Admin collect_info page:
            'NOTIFY_ADMIN_PRODUCT_PRICE_EDIT_SECTION_TOP',
            'NOTIFY_ADMIN_PRODUCT_PRICE_EDIT_ABOVE',

            'NOTIFY_MODULES_UPDATE_PRODUCT_END',
        ]);
    }

    protected function notify_admin_product_price_edit_section_top(&$class, $eventID, $pInfo, array &$additional_fields): void
    {
        $additional_fields[] = [
            'label' => MAP_PRICING_LABEL_ENABLE,
            'fieldname' => '', // blank because using radio buttons
            'input' =>
'<label class="radio-inline">' . zen_draw_radio_field('map_enabled', '1', (($pInfo->map_enabled ?? 0) == 1)) . TEXT_PRODUCT_IS_PRICED_BY_ATTRIBUTE . '</label>' . "\n" .
'<label class="radio-inline">' . zen_draw_radio_field('map_enabled', '0', (($pInfo->map_enabled ?? 0) == 0)) . TEXT_PRODUCT_NOT_PRICED_BY_ATTRIBUTE . '</label>' . "\n",
        ];
    }
    protected function notify_admin_product_price_edit_above(&$class, $eventID, $pInfo, array &$additional_fields): void
    {
        $additional_fields[] = [
            'label' => MAP_PRICING_LABEL_LOWEST_PRICE,
            'fieldname' => 'map_price',
            'input' => zen_draw_input_field('map_price', $pInfo->map_price ?? 0, 'onkeyup="updateTaxIncl()" class="form-control"') . "\n" .
                       '<span class="errorText">' . MAP_PRICING_HELPER_TEXT . '</span>',
        ];
    }

    protected function notify_modules_update_product_end(&$class, $eventID, array $data): void
    {
        // NOTE: $data = ['action' => $action, 'products_id' => $products_id]

        $sql_data_array = [
            'map_enabled' => zen_db_prepare_input((int)$_POST['map_enabled']),
            'map_price' => convertToFloat($_POST['map_price']),
        ];
        zen_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = ' . (int)$data['products_id']);
    }
}

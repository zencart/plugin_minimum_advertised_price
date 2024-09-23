# MAP Pricing v3

MAP Pricing = Minimum/Manufacturer's Advertised Price. 

_This is a revamped version of MAP Pricing v0.1 by Richard Kersey (slickricky.com)._

MAP Pricing is used when you want to sell something for a price that is lower than what you are allowed to advertise on your website. The actual price will only display after adding the product to the shopping basket.

This module adds a choice in the admin section when you're adding or editing a product, to specify if this product should have MAP pricing enabled. 
If MAP pricing is enabled for that product, it replaces the price throughout your website with (editable) text that says: "Priced so low, we're not able to advertise it. Add to cart for price."

You may optionally also specify a "minimum displayed price" in the product pricing area, which (when the price is not 0) will cause the storefront to say "$xxx.xx but for a lower price, add to cart".

## Prerequisites
This encapsulated plugin requires a minimum Zen Cart version 2.1.0

If you want to use this plugin on a Zen Cart version prior to ZC v2.1.0 use the older 2.0.2 version of this plugin instead. https://www.zen-cart.com/downloads.php?do=file&id=66


## Installation

1. If you are upgrading from v2 of this plugin, go remove all of the plugin's v2 files from your server, since they will no longer be needed, and will clash with this version (causing extra database queries and double MAP pricing messages on-screen, and possible PHP errors in the logs).

2. Using FTP, upload the `zc_plugins/MAP Pricing` directory to your Zen Cart server's `zc_plugins/MAP Pricing` directory. (If you already have an older version inside that directory, upload just the newest version subdirectory. Example: if you already have `v3.0.0` in there, and the latest has `v3.0.1`, just upload the `v3.0.1` directory.)

3. Login to your Admin area, and go to `Modules -> Plugin Manager`, and click on `MAP Minimum Advertised Price`. Then click `Install` to enable the module and make the required database changes.


## Customization Options:

### Language
If you need to customize the default displayed messages provided by this plugin, copy `/zc_plugins/MAP-Pricing/vx.x.x/catalog/includes/languages/english/extra_definitions/lang.map_pricing.php` to your `/includes/languages/english/extra_definitions` directory and make the changes in that file.

To translate the text for this plugin to another language, copy the admin and catalog language files to your language's `extra_definitions` directory and make your translations there.

### CSS

This plugin adds `.map_pricing` (for the text message) and `.map_price` (for the price itself) styles via CSS, with some default styling.

If you wish to customize that styling, simply add a new `map_pricing.css` file in your template's `css` directory, and that will be loaded after the one provided by this plugin, allowing you to override some or all of the content of the plugin's core CSS file.

## Troubleshooting
NOTE: This plugin depends on the changes added (included in) Zen Cart v2.1.0 found in https://github.com/zencart/zencart/commit/d4aeaadd405294c74cc89a9b1b1e2aad359fe262 ... If your Admin product-edit page doesn't show the MAP fields, then you're probably using an outdated Zen Cart version of collect_info.php

NOTE: This plugin depends on the InteractsWithPlugins trait included in Zen Cart v2.1.0 in the /includes/classes/traits/ directory. A fatal PHP error will occur with a blank screen if that trait file is missing.

## Removal

a) Go to `Admin-> Modules-> Plugin Manager`. Click on `MAP Pricing`. Click `Uninstall`. This will deconfigure the module. **It will NOT delete the pricing data from the database.**

b) If you want to also delete the pricing data added/maintained by this plugin, you will need to run a small SQL patch:

```sql
  ALTER TABLE products DROP map_enabled;
  ALTER TABLE products DROP map_price;
```
(If you run this via phpMyAdmin, you may need to add the appropriate table-name prefix before the word `products`, if your site is using a prefix for database table names.)


## REVISION HISTORY:
- v0.1 - 2006 released by Richard Kersey slickricky.com
- v1.0 - 2006 updated by bgroup99 thebricktongroup@gmail.com
- v1.1 - 2006 updated by DrByte to simplify the code and make integration easier for storeowners
- v1.2 - 2007 (was initially called "2.0" but was later changed to 1.2) updated by slickricky.com to add the ability to state what the MAP-price "is" instead of just "so low we can't say".
- v1.5 - 2012 updated by DrByte -- to make it work with Zen Cart v1.5.0 specifically.  This version will NOT work with prior versions of Zen Cart before v1.5.0.
- v1.5.1 - Sept 2012 - updated by DrByte for ZC v1.5.1 specifically. NOTE: requires manual adaptation if needed for other than product-general product type.
- v1.5.3 - 2014 updated by DrByte to make it work with ZC v1.5.0-thru-v1.5.3
- v1.5.4 - 6/13/2017 updates by jeking to be compatible with ZC 1.5.5e
- v1.5.5 - 1/4/2019 updates by jeking to be compatible with ZC 1.5.6
- v1.5.7 - 10/27/2020 updates by jeking to be compatible with ZC 1.5.7
- v2.0.0 - 05/25/2024 updates by DrByte to be compatible with ZC 2.0.0
- v2.0.1 - 05/30/2024 updates by DrByte -- fix bug in plugin v2.0.0 that included code intended for ZC v2.1.0
- v2.0.2 - 09/12/2024 updates by DrByte -- fix bug preventing prices from displaying on non-map-enabled products
- v3.0.0 - 09/17/2024 updates by DrByte to make it into an Encapsulated Plugin for easy installation

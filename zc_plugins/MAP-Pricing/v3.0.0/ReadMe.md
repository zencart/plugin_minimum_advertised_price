# MAP Pricing v3

MAP Pricing = Manufacturer's Advertised Price. 

This is a revamped version of MAP Pricing v1.0 by Richard Kersey (www.slickricky.com).   

It is used when you want to sell something for a price that is lower than what you are able to advertise on your website.  The actual price will only display after adding the product to the cart.

This module adds a choice in the admin section when you're adding or editing a product, to specify if this product should have MAP pricing enabled. 
If MAP pricing is enabled for that product, it replaces the price throughout your website with editable text that says: "Priced so low, we're not able to advertise it. Add to cart for price."

You may optionally also specify a "minimum displayed price" in the product pricing area, which will cause the storefront to say "$xxx.xx but for a lower price, add to cart".


## Installation

1. If you are upgrading from v2 of this plugin, go remove all of the plugin's v2 files from your server, since they will no longer be needed, and will clash with this version (causing extra database queries and double MAP pricing messages on-screen, and possible PHP errors in the logs).

2. Using FTP, upload the `MAP-Pricing` directory to your Zen Cart `zc_plugins` directory.

3. Login to your Admin area, and go to `Modules -> Plugin Manager`, and click on `MAP Minimum Advertised Price`. Then click `Install` to enable the module and make the required database changes.


## Customization Options:

### Language
If you need to customize the default displayed messages provided by this plugin, copy `/zc_plugins/MAP-Pricing/vx.x.x/catalog/includes/languages/english/extra_definitions/lang.map_pricing.php` to your `/includes/languages/english/extra_definitions` directory and make the changes in that file.

### CSS

This plugin adds a `.map_pricing` style via CSS, with some default styling.

If you wish to customize that, simply add a new `.map_pricing` style to your template's stylesheet, and that will override the one provided by this plugin.


## Removal

a) Go to `Admin-> Modules-> Plugin Manager`. Click on `MAP Pricing`. Click `Uninstall`. This will disable the module. **It will NOT delete the pricing data from the database.**

b) If you want to delete the pricing data added/maintained by this plugin, you will need to run a small SQL patch:

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
- v3.0.0 - 09/17/2024 updates by DrByte to make it into an Encapsulated Plugin for easy installation

<?php

/**
 * Plugin Name:       Modularity OnePage
 * Plugin URI:        https://github.com/helsingborg-stad/modularity-onepage/
 * Description:       Adds the ability to create one-page-ish layouts to Modularity
 * Version:           1.0.0
 * Author:            Sebastian Thulin, Kristoffer Svanmark
 * Author URI:        https://github.com/helsingborg-stad/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       modularity-onepage
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('MODULARITY_ONEPAGE_PATH', plugin_dir_path(__FILE__));
define('MODULARITY_ONEPAGE_URL', plugins_url('', __FILE__));
define('MODULARITY_ONEPAGE_TEMPLATE_PATH', MODULARITY_ONEPAGE_PATH . 'templates/');

load_plugin_textdomain('modularity-onepage', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once MODULARITY_ONEPAGE_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once MODULARITY_ONEPAGE_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new ModularityOnePage\Vendor\Psr4ClassLoader();
$loader->addPrefix('ModularityOnePage', MODULARITY_ONEPAGE_PATH);
$loader->addPrefix('ModularityOnePage', MODULARITY_ONEPAGE_PATH . 'source/php/');
$loader->register();

// Start application
new ModularityOnePage\App();

//Purge varnish when needed
new ModularityOnePage\Helper\Varnish();

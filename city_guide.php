<?php

/**
 * City Guide
 *
 * @package           CityGuide
 * @author            Paddy
 * @copyright         2022 MPS Infotech
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       City Guide
 * Description:       Adding Places and Listings.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Paddy
 * Author URI:        https://mpsinfotech.in
 * Text Domain:       city-guide-mps
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

define('PLUGIN_CITY_GUIDE__FILE__', __FILE__);
define('PLUGIN_CITY_GUIDE__DIR__', __DIR__);
define('PLUGIN_CITY_GUIDE__URL__', plugin_dir_url(__FILE__));
include('models/Category.php');
include('models/Place.php');
include('controller/CategoryController.php');
include('controller/PlaceController.php');
include('controller/MainController.php');
include('admin/CategoryListTable.php');
include('admin/AdminCategoryPage.php');
include('admin/PlaceListTable.php');
include('admin/AdminPlacePage.php');
include('admin/AdminPlacePageAdd.php');
include('CityGuide.php');

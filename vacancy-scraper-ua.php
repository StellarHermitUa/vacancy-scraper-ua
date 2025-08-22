<?php
/**
 * Plugin Name: Vacancy Scraper UA
 * Description: Receives job vacancies from work.ua and robota.ua for display on the website
 * Version: 1.1
 * Requires at least: 6.8
 * Tested up to: 6.8
 * Author: StellarHermitUa
 * Author URI: https://github.com/StellarHermitUa
 * Text Domain: vacancy-scraper-ua
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; 
}

require_once plugin_dir_path(__FILE__) . 'includes/class-settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-fetcher-workua.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-fetcher-rabota.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-cities.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend-functions.php';

register_activation_hook(__FILE__, ['VacancyScraperUA_Activator', 'activate']);

add_action('admin_menu', ['VacancyScraperUA_Settings_Page', 'register']);

VacancyScraperUA_Admin_Handler::init();

add_action('init', ['VacancyScraperUA_Frontend', 'init']);
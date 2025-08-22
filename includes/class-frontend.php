<?php
/**
 * Class for frontend vacancies data.
 */
if (!defined( 'ABSPATH' )) exit; 

class VacancyScraperUA_Frontend {

    /**
     * Initialize shortcodes.
     */
    public static function init() {
        add_shortcode('vacancies_workua', [self::class, 'getall_workua']);
        add_shortcode('vacancies_robotaua', [self::class, 'getall_robotaua']);

        add_action('wp_enqueue_scripts', [self::class,'vacancy_scraper_enqueue_frontend_styles']);
    }

    /**
     * Shortcode: Work.ua vacancies.
     */
    public static function getall_workua() {
        return self::load_template('workua');
    }

    /**
     * Shortcode: Rabota.ua vacancies.
     */
    public static function getall_robotaua() {
        return self::load_template('rabotaua');
    }


    /**
     * Get vacancies data (cached).
     *
     * @param string|array $source Source(s).
     * @param int          $limit  Number of vacancies.
     * @return array
     */
    public static function get_vacancies($source, $limit = 0) {
        global $wpdb;

        $source = sanitize_text_field($source);
        $cache_key = 'vacancy_scraper_' . $source . '_' . intval($limit);

        $vacancies = wp_cache_get($cache_key, 'vacancy_scraper_ua');

        if ($vacancies === false) {
            if ($limit > 0) {
                $sql = $wpdb->prepare( // phpcs:ignore
                    "SELECT * FROM {$wpdb->prefix}vacancy_scraper_ua_jobs WHERE source = %s ORDER BY id DESC LIMIT %d",
                    $source,
                    $limit
                );
            } else {
                $sql = $wpdb->prepare( // phpcs:ignore
                    "SELECT * FROM {$wpdb->prefix}vacancy_scraper_ua_jobs WHERE source = %s ORDER BY id DESC",
                    $source
                );
            }

            $vacancies = $wpdb->get_results($sql, ARRAY_A); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
 
            wp_cache_set($cache_key, $vacancies, 'vacancy_scraper_ua', HOUR_IN_SECONDS);
        }
        return $vacancies;
    }

    /**
     * Get company options.
     *
     * @param string|array $source Source(s).
     * @return array
     */
    public static function get_options($source) {
        global $wpdb;

        $source = sanitize_text_field($source);
        $cache_key = 'vacancy_scraper_options_' . $source ;

        $options = wp_cache_get($cache_key, 'vacancy_scraper_ua');

        if ($options === false) {

            $options = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                $wpdb->prepare(
                    "SELECT options FROM {$wpdb->prefix}vacancy_scraper_ua_companies WHERE source = %s",
                    $source
                )
            ); 

            wp_cache_set($cache_key, $options, 'vacancy_scraper_ua', HOUR_IN_SECONDS);
        }

        return $options;
    }

    /**
     * Load a frontend template.
     *
     * @param string $source
     * @param array  $vacancies
     * @return string
     */
    private static function load_template($source) {
        $vacancies = self::get_vacancies($source);
        $options = json_decode(self::get_options($source),true);
        ob_start();
        $template_file = plugin_dir_path(__FILE__) . '../templates/frontend/' . $source . '-vacancies.php';

        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<p>' . esc_html__('Template not found.', 'vacancy-scraper-ua') . '</p>';
        }

        return ob_get_clean();
    }


    public static function vacancy_scraper_enqueue_frontend_styles() {
            wp_enqueue_style(
                'vacancy-scraper-work-dynamic',
                plugin_dir_url(__FILE__) . '../assets/css/vacancy-work-colors.css',
                ['vacancy-scraper-frontend'],
                filemtime(plugin_dir_path(__FILE__) . '../assets/css/vacancy-work-colors.css') 
            );  

            wp_enqueue_style(
                'vacancy-scraper-robota-dynamic',
                plugin_dir_url(__FILE__) . '../assets/css/vacancy-robota-colors.css',
                ['vacancy-scraper-frontend'],
                filemtime(plugin_dir_path(__FILE__) . '../assets/css/vacancy-robota-colors.css') 
            );  

            wp_enqueue_style(
                'vacancy-scraper-frontend',
                plugin_dir_url(__FILE__) . '../assets/css/vacancy-front.css',
                [],
                '1.0.0'
            );
          
            wp_enqueue_script(
                'vacancy-scraper-ua-admin',
                plugin_dir_url(__FILE__) . '../assets/js/vacancy-front.js',
                ['jquery'],
                '1.0.0',
                true
            );
    }  

    /**
     * Work.ua raw vacancies.
     */
    public static function getall_raw_workua() {
        $vacancies = self::get_vacancies('workua');
        $options = json_decode(self::get_options('workua'),true);
        return [ 'options' => $options, 'jobs' => $vacancies];
    }

    /**
     * Rabota.ua raw vacancies.
     */
    public static function getall_raw_robotaua() {
        $vacancies = self::get_vacancies('rabotaua');
        $options = json_decode(self::get_options('rabotaua'),true);
        return [ 'options' => $options, 'jobs' => $vacancies];
    }



}

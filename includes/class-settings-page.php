<?php
if (!defined( 'ABSPATH' )) exit; 

class VacancyScraperUA_Settings_Page {

    public static function register() {
        add_menu_page(
            esc_html__('Vacancy Scraper UA', 'vacancy-scraper-ua'),
            esc_html__('Vacancy Scraper', 'vacancy-scraper-ua'),
            'manage_options',
            'vacancy-scraper-ua',
            [self::class, 'render_page'],
            'dashicons-businessperson'
        );

        add_submenu_page(
            'vacancy-scraper-ua',
            esc_html__('Work.ua', 'vacancy-scraper-ua'),
            esc_html__('Work.ua', 'vacancy-scraper-ua'),
            'manage_options',
            'vacancy-scraper-ua',
            [self::class, 'render_page']
        );

        add_submenu_page(
            'vacancy-scraper-ua',
            esc_html__('Robota.ua', 'vacancy-scraper-ua'),
            esc_html__('Robota.ua', 'vacancy-scraper-ua'),
            'manage_options',
            'vacancy-scraper-ua-rabotaua',
            function() {
                $_GET['tab'] = 'rabotaua';
                self::render_page();
            }
        );
        add_submenu_page(
            'vacancy-scraper-ua',
            esc_html__('Help', 'vacancy-scraper-ua'),
            esc_html__('Help', 'vacancy-scraper-ua'),
            'manage_options',
            'vacancy-scraper-ua-help',
            [self::class, 'render_help_page']
        );


        add_action('admin_enqueue_scripts', [self::class, 'enqueue_scripts']);

    }

    public static function enqueue_scripts($hook) {

        if (strpos($hook, 'vacancy-scraper-ua') === false) {
            return;
        }

        wp_enqueue_style(
            'vacancy-scraper-ua-admin',
            plugin_dir_url(__FILE__) . '../assets/css/vacancy-admin.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'vacancy-scraper-ua-admin',
            plugin_dir_url(__FILE__) . '../assets/js/vacancy-admin.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('vacancy-scraper-ua-admin', 'VacancyScraperUA', [
            'confirmDelete' => esc_html__('Are you sure you want to delete the company? All data will be deleted.', 'vacancy-scraper-ua'),
            'confirmFetch'  => esc_html__('Do you want to fetch vacancies now? Existing vacancies will be rewritten.', 'vacancy-scraper-ua'),
        ]);
    }


    public static function render_page() {
        global $wpdb;
        $table = $wpdb->prefix . 'vacancy_scraper_ua_companies';

        $companies = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->prepare( "SELECT source, company_id, options FROM {$table} WHERE source IN (%s, %s)", 'workua', 'rabotaua' ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                OBJECT_K
            ); 

        $workua_id   = isset($companies['workua']) ? $companies['workua']->company_id : null;
        $rabotaua_id = isset($companies['rabotaua']) ? $companies['rabotaua']->company_id : null;
        $workua_company_options = !empty($companies['workua']->options) ? json_decode($companies['workua']->options, true) : [];
        $rabotaua_company_options =  !empty($companies['rabotaua']->options) ? json_decode($companies['rabotaua']->options, true) : [];

        $active_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'workua';// phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $jobs = [];
        $per_page = 25;
        $current_page = isset($_GET['paged']) ? max(1, intval(sanitize_key(wp_unslash($_GET['paged'])))) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        $total_jobs = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}vacancy_scraper_ua_jobs WHERE source = %s AND company_id = %d",
                $active_tab,
                $active_tab === 'workua' ? $workua_id : $rabotaua_id
            )
        ); 

        $offset = ($current_page - 1) * $per_page;
        
        $jobs = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $wpdb->prepare(
                "SELECT * 
                FROM {$wpdb->prefix}vacancy_scraper_ua_jobs
                WHERE source = %s AND company_id = %d
                ORDER BY created_at DESC
                LIMIT %d OFFSET %d",
                $active_tab,
                $active_tab === 'workua' ? $workua_id : $rabotaua_id,
                $per_page,
                $offset
            )
        );


        include plugin_dir_path(__FILE__) . '../templates/admin-page.php';
    }

    public static function render_help_page() {
        $active_tab = 'help';
        include plugin_dir_path(__FILE__) . '../templates/admin-page.php';
    }

    public static function sanitize_workua_company_id($input) {
        $input = trim($input);

        if ($input === '') {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'empty_workua_id',
                esc_html__('The Work.ua company ID or URL cannot be empty.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        if (filter_var($input, FILTER_VALIDATE_URL)) {
            if (strpos($input, 'work.ua/jobs/by-company/') !== false) {
                if (preg_match('/by-company\/([0-9]+)/', $input, $matches)) {
                    $input = $matches[1];
                } else {
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'invalid_workua_url',
                        esc_html__('Could not extract company ID from the Work.ua URL.', 'vacancy-scraper-ua'),
                        'error'
                    );
                    return null;
                }
            } else {
                add_settings_error(
                    'vacancy_scraper_ua_messages',
                    'invalid_workua_url',
                    esc_html__('The URL must be from work.ua.', 'vacancy-scraper-ua'),
                    'error'
                );
                return null;
            }
        }

        if (!preg_match('/^[0-9]+$/', $input)) {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'invalid_workua_id',
                esc_html__('The Work.ua company ID must be numeric.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        $data = VacancyScraperUA_Fetcher_WorkUA::get_remote_data($input, 1);
        if (empty($data['jobs'])) {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'workua_company_not_found',
                esc_html__('No vacancies found or invalid Work.ua company ID.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        return $input;
    }


    public static function sanitize_rabotaua_company_id($input) {
        $input = trim($input);

        if ($input === '') {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'empty_rabotaua_id',
                esc_html__('The Rabota.ua company ID or URL cannot be empty.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        if (filter_var($input, FILTER_VALIDATE_URL)) {
            if (strpos($input, 'robota.ua/company') !== false) {
                if (preg_match('/company([0-9]+)/', $input, $matches)) {
                    $input = $matches[1];
                } else {
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'invalid_rabotaua_url',
                        esc_html__('Could not extract company ID from the Rabota.ua URL.', 'vacancy-scraper-ua'),
                        'error'
                    );
                    return null;
                }
            } else {
                add_settings_error(
                    'vacancy_scraper_ua_messages',
                    'invalid_rabotaua_url',
                    esc_html__('The URL must be from robota.ua.', 'vacancy-scraper-ua'),
                    'error'
                );
                return null;
            }
        }

        if (!preg_match('/^[0-9]+$/', $input)) {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'invalid_rabotaua_id',
                esc_html__('The Rabota.ua company ID must be numeric.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        $data = VacancyScraperUA_Fetcher_RabotaUA::get_remote_data($input, 1);
        if (empty($data['filteredVacancies']) || !is_array($data['filteredVacancies'])) {
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'workua_company_not_found',
                esc_html__('No vacancies found or invalid Robota.ua company ID.', 'vacancy-scraper-ua'),
                'error'
            );
            return null;
        }

        return $input;
    }

}

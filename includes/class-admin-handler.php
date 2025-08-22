<?php
/**
 * Class VacancyScraperUA_Admin_Handler
 * Handles saving, deleting, and fetching vacancies for Work.ua and Rabota.ua.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access.
}

class VacancyScraperUA_Admin_Handler {

    /**
     * Initialize hooks.
     */
    public static function init() {
        add_action('admin_post_vacancy_scraper_ua_save', [self::class, 'handle_save']);
    }

    /**
     * Handle form actions (save/delete/fetch).
     */
    public static function handle_save() {

        if (
            !isset($_POST['vacancy_scraper_ua_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['vacancy_scraper_ua_nonce'])),
                'vacancy_scraper_ua_save'
            )
        ) {
            wp_die(esc_html__('Security check failed.', 'vacancy-scraper-ua'));
        }


        $tab = isset($_POST['tab']) ? sanitize_key(wp_unslash($_POST['tab'])) : 'workua';
        $action = isset($_POST['vacancy_scraper_ua_action'])
            ? sanitize_key(wp_unslash($_POST['vacancy_scraper_ua_action']))
            : 'save';

        global $wpdb;
        $table = $wpdb->prefix . 'vacancy_scraper_ua_companies';

        if ($action === 'delete_workua' || $action === 'delete_rabotaua') {
            $source = ($action === 'delete_workua') ? 'workua' : 'rabotaua';

            $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $table,
                [
                    'company_id' => null,
                    'name'       => null,
                    'created_at' => current_time('mysql'),
                ],
                ['source' => $source],
                ['%s', '%s', '%s'],
                ['%s']
            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

            add_settings_error(
                'vacancy_scraper_ua_messages',
                'deleted_' . $source,
                esc_html__('Company data removed.', 'vacancy-scraper-ua'),
                'updated'
            );
        }

        if ($action === 'save') {
            $company_field = 'vacancy_scraper_ua_' . $tab . '_company_id';
            $company_id = isset($_POST[$company_field])
                ? trim(sanitize_text_field(wp_unslash($_POST[$company_field])))
                : '';

            if ($tab === 'workua') {
                $company_id = VacancyScraperUA_Settings_Page::sanitize_workua_company_id($company_id);
            } elseif ($tab === 'rabotaua') {
                $company_id = VacancyScraperUA_Settings_Page::sanitize_rabotaua_company_id($company_id);
                
                if (VacancyScraperUA_Cities::is_rabotaua_cities_empty()) {
                    VacancyScraperUA_Cities::update_rabotaua_cities();
                }
                           
            }

            if ($company_id) {
                $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $table,
                    [
                        'company_id' => $company_id,
                        'created_at' => current_time('mysql'),
                    ],
                    ['source' => $tab],
                    ['%s', '%s'],
                    ['%s']
                ); // phpcs:ignore

                add_settings_error(
                    'vacancy_scraper_ua_messages',
                    'updated_' . $tab . '_id',
                    esc_html__('Company ID saved successfully.', 'vacancy-scraper-ua'),
                    'updated'
                );
            }
        }

        if ($action === 'fetch_workua' || $action === 'fetch_rabotaua') {
            $source = ($action === 'fetch_workua') ? 'workua' : 'rabotaua';


                $company_id = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $wpdb->prepare(
                        "SELECT company_id FROM {$wpdb->prefix}vacancy_scraper_ua_companies WHERE source = %s",
                        $source
                    )
                );

      

            if($source == 'workua'){
                if(VacancyScraperUA_Fetcher_WorkUA::fetch_jobs($company_id)){
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'fetch_' . $source,
                        esc_html__('Vacancies copied', 'vacancy-scraper-ua'),
                        'info'
                    );
                }else{
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'fetch_' . $source,
                        esc_html__('Vacancies fetching will be implemented soon.', 'vacancy-scraper-ua'),
                        'error'
                    );
                }

            }else{
                if(VacancyScraperUA_Fetcher_RabotaUA::fetch_jobs($company_id)){
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'fetch_' . $source,
                        esc_html__('Vacancies copied', 'vacancy-scraper-ua'),
                        'info'
                    );
                }else{
                    add_settings_error(
                        'vacancy_scraper_ua_messages',
                        'fetch_' . $source,
                        esc_html__('Vacancies fetching will be implemented soon.', 'vacancy-scraper-ua'),
                        'error'
                    );
                }
            }

        }

        if ($action === 'save_workua_options') {

            $filter = isset($_POST['vacancy_scraper_ua_workua_filter'])
                ?  sanitize_text_field(wp_unslash($_POST['vacancy_scraper_ua_workua_filter']))
                : '';

            $colors_raw = isset($_POST['vacancy_scraper_ua_workua_colors'])
                ?  array_map('sanitize_text_field', wp_unslash($_POST['vacancy_scraper_ua_workua_colors']))
                : [];

            $colors = [];
            foreach (['jv-background', 'jv-title', 'jv-city', 'jv-button','jv-salary','jv-button-hover'] as $key) {
                $colors[$key] = isset($colors_raw[$key]) ? sanitize_hex_color($colors_raw[$key]) : '';
            }
            //'language' => $languages,
            $options = ['colors' => $colors,'filter' => $filter];
            
            $wpdb->update(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                "{$wpdb->prefix}vacancy_scraper_ua_companies",
                ['options' => wp_json_encode($options)],
                ['source' => 'workua'],
                ['%s'],
                ['%s']
            );
            self::update_vacancy_css_file('workua', $colors);
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'save_workua_options',
                esc_html__('Options saved.', 'vacancy-scraper-ua'),
                'updated'
            );
        }

        if ($action === 'save_rabotaua_options') {

            $filter = isset($_POST['vacancy_scraper_ua_rabotaua_filter'])
                ?   sanitize_text_field(wp_unslash($_POST['vacancy_scraper_ua_rabotaua_filter']))
                : '';

            $colors_raw = isset($_POST['vacancy_scraper_ua_rabotaua_colors'])
                ? array_map('sanitize_text_field',wp_unslash($_POST['vacancy_scraper_ua_rabotaua_colors']))
                : [];

            $colors = [];
            foreach (['jv-background', 'jv-title', 'jv-city', 'jv-button','jv-salary','jv-button-hover'] as $key) {
                $colors[$key] = isset($colors_raw[$key]) ? sanitize_hex_color($colors_raw[$key]) : '';
            }
            //'language' => $languages,
           $options = ['colors' => $colors,'filter' => $filter];
            
            $wpdb->update(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                "{$wpdb->prefix}vacancy_scraper_ua_companies",
                ['options' => wp_json_encode($options)],
                ['source' => 'rabotaua'],
                ['%s'],
                ['%s']
            );
            self::update_vacancy_css_file('rabotaua', $colors);
            add_settings_error(
                'vacancy_scraper_ua_messages',
                'save_rabotaua_options',
                esc_html__('Options saved.', 'vacancy-scraper-ua'),
                'updated'
            );
        }

        $errors = get_settings_errors();
        if (!empty($errors)) {
            set_transient('vacancy_scraper_ua_admin_notices', $errors, 30);
        }

        $redirect_url = add_query_arg(
            [
                'page'              => 'vacancy-scraper-ua',
                'tab'               => $tab,
                'settings-updated'  => 'true',
            ],
            admin_url('admin.php')
        );

        wp_safe_redirect($redirect_url);
        exit;
    }

    public static function update_vacancy_css_file($source, $colors) {
        global $wp_filesystem;
        
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        $creds = request_filesystem_credentials(site_url());
        if (!WP_Filesystem($creds)) {
            return false;
        }

        $prefix = ($source === 'workua') ? 'w' : 'r';
        $file_prefix = ($source === 'workua') ? '-work' : '-robota';
        
        $css_vars = [
            'jv-background'    => "--{$prefix}jv-background",
            'jv-title'         => "--{$prefix}jv-title",
            'jv-city'          => "--{$prefix}jv-city",
            'jv-button'        => "--{$prefix}jv-button",
            'jv-salary'        => "--{$prefix}jv-salary",
            'jv-button-hover'  => "--{$prefix}jv-button-hover"
        ];
        
        $css = ".{$source}_box {\n";
        foreach ($css_vars as $key => $var) {
            if (!empty($colors[$key])) {
                $css .= "\t{$var}: {$colors[$key]};\n";
            }
        }
        $css .= "}\n";

        $css_file = plugin_dir_path(__FILE__) . '../assets/css/vacancy' . $file_prefix . '-colors.css';
        
        $dir = dirname($css_file);
        if (!$wp_filesystem->exists($dir)) {
            $wp_filesystem->mkdir($dir);
        }
        
        return $wp_filesystem->put_contents($css_file, $css, FS_CHMOD_FILE);
    }

}

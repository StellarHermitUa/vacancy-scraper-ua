<?php
/**
 * Class VacancyScraperUA_Fetcher_WorkUA
 * Fetches jobs from Work.ua and stores them in the database.
 */

if (!defined('ABSPATH')) {
    exit; 
}

class VacancyScraperUA_Fetcher_WorkUA {

    /**
     * Fetch and store jobs for the given company ID.
     *
     * @param int $company_id The ID of the company on Work.ua.
     * @return int The number of jobs successfully inserted.
     */
    public static function fetch_jobs($company_id) {
        global $wpdb;

        $company_id = absint($company_id);
        if (!$company_id) {
            return 0;
        }

      
        $data = self::get_remote_data($company_id);
        if (empty($data['jobs']) || !is_array($data['jobs'])) {
            return 0;
        }
        
        wp_cache_delete('vacancy_scraper_workua', 'vacancy_scraper_ua');
        wp_cache_delete('vacancy_scraper_rabotaua', 'vacancy_scraper_ua');
        wp_cache_delete('vacancy_scraper_workua_rabotaua', 'vacancy_scraper_ua');

        self::clear_jobs();

        $table = $wpdb->prefix . 'vacancy_scraper_ua_jobs';
        $count = 0;


        foreach ($data['jobs'] as $job) {
            $name = sanitize_text_field($job['name']);
            $salary = sanitize_text_field(html_entity_decode($job['salary'], ENT_QUOTES, 'UTF-8'));
            $currency = !empty($job['salary']) ? sanitize_text_field($job['grn']) : 'грн';
            $city = sanitize_text_field($job['region']);
            $city_comment = !empty($job['regionf']) ? sanitize_text_field($job['regionf']) : '';
            $link = esc_url_raw($job['link']);

            $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $table,
                [
                    'company_id'    => $company_id,
                    'source'        => 'workua',
                    'vacancy_id'    => md5($link), 
                    'name'          => $name,
                    'salary'        => $salary,
                    'currency'      => $currency,
                    'city'          => $city,
                    'city_comment'  => $city_comment,
                    'link'          => $link,
                    'date_posted'   => null,
                    'created_at'    => current_time('mysql'),
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

            if ($wpdb->insert_id) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Fetch remote data and decode JSON.
     *
     * @param string $url
     * @return array|null
     */
    public static function get_remote_data($company_id, $limit = 1000) {
        $company_id = absint($company_id);
        if (!$company_id) {
            return null;
        }

        $lang = 'uk';

        $url = esc_url_raw(
            'https://www.work.ua/export/company/company_jobs.php?id=' . $company_id .
            '&callback=workInformerJobsList&limit=' . absint($limit) .
            '&lang=' . $lang
        );

        $response = wp_remote_get($url);
        if (is_wp_error($response)) {
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return null;
        }

        $body = preg_replace('/^workInformerJobsList\(|\)$/', '', $body);
        $data = json_decode($body, true);

        return is_array($data) ? $data : null;
    }
    

    /**
     * Delete old jobs for the given company ID from Work.ua source.
     *
     * @param int $company_id
     * @return void
     */
    public static function clear_jobs() {
        global $wpdb;


            $table = $wpdb->prefix . 'vacancy_scraper_ua_jobs'; 
            $wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $table,
                ['source' => 'workua'],
                [ '%s']
            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
   
    }
}

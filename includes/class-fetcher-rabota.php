<?php
/**
 * Class for fetching and saving vacancies from Rabota.ua.
 */
if (!defined( 'ABSPATH' )) exit; 

class VacancyScraperUA_Fetcher_RabotaUA {

    /**
     * Fetches vacancies for a given company and saves them in the database.
     *
     * @param int $company_id Company ID.
     * @return bool True on success, false on failure.
     */
    public static function fetch_jobs($company_id) {
        $data = self::get_remote_data($company_id);
        if (empty($data['filteredVacancies']) || !is_array($data['filteredVacancies'])) {
            return false;
        }
        self::clear_jobs();
        global $wpdb;
        $table = $wpdb->prefix . 'vacancy_scraper_ua_jobs';

        foreach ($data['filteredVacancies'] as $vacancy) {
            $vacancy_id = isset($vacancy['id']) ? sanitize_text_field($vacancy['id']) : '';
            $name       = isset($vacancy['name']) ? sanitize_text_field($vacancy['name']) : '';
            $link       = $vacancy_id ? 'https://robota.ua/company'.intval($company_id).'/vacancy'.sanitize_text_field($vacancy['id']) : '';
            $city_id    = isset($vacancy['cityId']) ? intval($vacancy['cityId']) : 0;
            $city       = ($city_id > 0) ? self::get_city_name_by_id($city_id) : '';
            $salary     = isset($vacancy['salary']) ? sanitize_text_field($vacancy['salary']) : '';
            $currency   = 'грн';
            $date_posted = isset($vacancy['date']) ? gmdate('Y-m-d H:i:s', strtotime($vacancy['date'])) : null;

            // Вставляем или обновляем вакансию
            $wpdb->replace( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $table,
                [
                    'company_id'  => $company_id,
                    'source'      => 'rabotaua',
                    'vacancy_id'  => $vacancy_id,
                    'name'        => $name,
                    'salary'      => $salary,
                    'currency'    => $currency,
                    'city'        => $city,
                    'link'        => $link,
                    'date_posted' => $date_posted,
                ],
                [
                    '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
                ]
            );
        }

        return true;
    }

    /**
     * Gets data from Rabota.ua API.
     *
     * @param int $company_id Company ID.
     * @return array Decoded API response.
     */
    public static function get_remote_data($company_id) {
        $url = esc_url_raw("https://api.robota.ua/companies/{$company_id}/published-vacancies");
        $response = wp_remote_get($url, ['timeout' => 15]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Gets the city name (uk) by city_id from the cities table.
     *
     * @param int $city_id City ID.
     * @return string City name in Ukrainian or empty string.
     */
    private static function get_city_name_by_id($city_id) {
        global $wpdb;

        $city_json = $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $wpdb->prepare(
                "SELECT names FROM {$wpdb->prefix}vacancy_scraper_ua_cities WHERE source = %s AND city_id = %d LIMIT 1",
                'rabotaua',
                $city_id
            )
        ); 

        if (!$city_json) {
            return '';
        }

        $names = json_decode($city_json, true);
        return $names['ua'] ?? '';
    }

    /**
     * Delete old jobs for the given company ID from rabota.ua source.
     *
     * @param int $company_id
     * @return void
     */
    public static function clear_jobs() {
           global $wpdb;

            $table = $wpdb->prefix . 'vacancy_scraper_ua_jobs'; 
            $wpdb->delete( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $table,
                [ 'source' => 'rabotaua'],
                [ '%s']
            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
 
    }


}

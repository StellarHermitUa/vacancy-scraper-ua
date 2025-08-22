<?php
if (!defined( 'ABSPATH' )) exit; 

class VacancyScraperUA_Cities {

    /**
     * Updates the list of cities from Rabota.ua.
     *
     * @return bool True on success, false on failure.
     */

    public static function update_rabotaua_cities() {
        $url = 'https://api.robota.ua/dictionary/city'; 
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $cities = json_decode($body, true);

        if (empty($cities) || !is_array($cities)) {
            return false;
        }

        global $wpdb;
        foreach ($cities as $city) {
            if (empty($city['id']) || empty($city['ua']) || empty($city['ru'])) {
                continue;
            }

            $wpdb->replace(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                "{$wpdb->prefix}vacancy_scraper_ua_cities",
                [
                    'source'      => 'rabotaua',
                    'city_id'     => (int) $city['id'],
                    'region_name' => wp_json_encode($city['regionName'] ?? []),
                    'names'       => wp_json_encode([
                        'ua' => $city['ua'],
                        'ru' => $city['ru'],
                        'en' => $city['en'] ?? ''
                    ])
                ],
                ['%s', '%d', '%s', '%s']
            );
        }

        return true;
    }
    /**
     * Checks if the Rabota.ua cities table is empty.
     *
     * @return bool True if empty, false otherwise.
     */
    public static function is_rabotaua_cities_empty() {
        global $wpdb;
        $count = (int) $wpdb->get_var(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            "SELECT COUNT(*) FROM {$wpdb->prefix}vacancy_scraper_ua_cities WHERE source = 'rabotaua'"
        );

        return $count === 0;
    }    
}

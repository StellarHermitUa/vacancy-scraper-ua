<?php

if (!defined( 'ABSPATH' )) exit; 

class VacancyScraperUA_Activator {

    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $table_jobs = $wpdb->prefix . 'vacancy_scraper_ua_jobs';
        $sql_jobs = "CREATE TABLE $table_jobs (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            company_id BIGINT UNSIGNED NOT NULL,
            source VARCHAR(50) NOT NULL,     
            vacancy_id VARCHAR(100) NOT NULL,  
            name VARCHAR(255) NOT NULL,        
            salary VARCHAR(100) DEFAULT NULL,   
            currency VARCHAR(20) DEFAULT NULL,  
            city VARCHAR(100) NOT NULL,        
            city_comment VARCHAR(255) DEFAULT NULL, 
            link TEXT NOT NULL,                 
            date_posted DATETIME DEFAULT NULL,  
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        $table_companies = $wpdb->prefix . 'vacancy_scraper_ua_companies';
        $sql_companies = "CREATE TABLE $table_companies (
            id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
            source VARCHAR(50) NOT NULL,
            company_id VARCHAR(100) DEFAULT NULL,
            name VARCHAR(255) DEFAULT NULL,
            options LONGTEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_source (source)
        ) $charset_collate;";

        $cities = $wpdb->prefix . 'vacancy_scraper_ua_cities';
        $sql_cities = "CREATE TABLE {$wpdb->prefix}vacancy_scraper_ua_cities (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            source VARCHAR(50) NOT NULL,   
            city_id BIGINT UNSIGNED NOT NULL,
            region_name JSON DEFAULT NULL,       
            names JSON NOT NULL,                   
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_source_city (source, city_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql_jobs);
        dbDelta($sql_companies);
        dbDelta($sql_cities);



        $default_companies = [
            ['source' => 'workua', 'company_id' => null, 'name' => null, 'options' => '{"colors":{"jv-background":"#ffffff","jv-title":"#000000","jv-city":"#cccccc","jv-button":"#c6cbcd","jv-salary":"#98a1a4","jv-button-hover":"#000000"},"filter":"1"}'],
            ['source' => 'rabotaua', 'company_id' => null, 'name' => null, 'options' => '{"colors":{"jv-background":"#ffffff","jv-title":"#000000","jv-city":"#cccccc","jv-button":"#c6cbcd","jv-salary":"#98a1a4","jv-button-hover":"#000000"},"filter":"1"}'],
        ];

        foreach ($default_companies as $company) {
            $exists = $wpdb->get_var( $wpdb->prepare("SELECT id FROM {$table_companies} WHERE source = %s LIMIT 1",$company['source']) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            if (!$exists) {
                $wpdb->insert($table_companies, $company); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            }
        }


    }
}

<?php 
if (!defined( 'ABSPATH' )) exit; 

if ( ! function_exists( 'vacancy_scraper_render_workua' ) ) {
    /**
     * Render Work.ua vacancies.
     *
     * @return string
     */
    function vacancy_scraper_render_workua() {
        return VacancyScraperUA_Frontend::getall_workua();
    }
}

if ( ! function_exists( 'vacancy_scraper_render_robotaua' ) ) {
    /**
     * Render Robota.ua vacancies.
     *
     * @return string
     */
    function vacancy_scraper_render_robotaua() {
        return VacancyScraperUA_Frontend::getall_robotaua();
    }
}


if ( ! function_exists( 'vacancy_scraper_getraw_workua' ) ) {
    /**
     * Get raw data on job vacancies at Work.ua
     *
     * @return string
     */
    function vacancy_scraper_getraw_workua() {
        return VacancyScraperUA_Frontend::getall_raw_workua();
    }
}


if ( ! function_exists( 'vacancy_scraper_getraw_robotaua' ) ) {
    /**
     * Get raw data on job vacancies at Rabota.ua
     *
     * @return string
     */
    function vacancy_scraper_getraw_robotaua() {
        return VacancyScraperUA_Frontend::getall_raw_robotaua();
    }
}


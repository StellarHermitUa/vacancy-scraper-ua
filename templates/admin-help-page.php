<?php if (!defined( 'ABSPATH' )) exit; ?>
<div class="help_content">
    
    <h2 class="jv_additional_title"><?php esc_html_e('Data output for work.ua', 'vacancy-scraper-ua'); ?></h2>

    <h3><?php esc_html_e('Shortcode usage', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('To display vacancies from Work.ua, use the following shortcode on any page or post:', 'vacancy-scraper-ua'); ?></p>
    <code>[vacancies_workua]</code>
    <p><?php esc_html_e('The output will be based on the options selected in the plugin settings page (Work.ua tab).', 'vacancy-scraper-ua'); ?></p>

    <h3><?php esc_html_e('Using in template code', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('To embed the list of vacancies from Work.ua directly into your theme template files, use the following function:', 'vacancy-scraper-ua'); ?></p>
    <pre><code>&lt;?php echo vacancy_scraper_render_workua(); ?&gt;</code></pre>

    <h3><?php esc_html_e('Getting raw data', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('If you want to get raw vacancy data for custom processing, use the following function:', 'vacancy-scraper-ua'); ?></p>
    <pre><code>&lt;?php $data = vacancy_scraper_getraw_workua(); ?&gt;</code></pre>
    <p><?php esc_html_e('The returned array includes two main sections: "options" (settings used for output) and "jobs" (list of vacancies).', 'vacancy-scraper-ua'); ?></p>

    <br><br>
    <h2 class="jv_additional_title"><?php esc_html_e('Data output for robota.ua', 'vacancy-scraper-ua'); ?></h2>

    <h3><?php esc_html_e('Shortcode usage', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('To display vacancies from Robota.ua, use the following shortcode:', 'vacancy-scraper-ua'); ?></p>
    <code>[vacancies_robotaua]</code>
    <p><?php esc_html_e('The output will be based on the options selected in the plugin settings page (Robota.ua tab)', 'vacancy-scraper-ua'); ?></p>

    <h3><?php esc_html_e('Using in template code', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('To embed the list of vacancies from Robota.ua directly into your theme template files, use the following function:', 'vacancy-scraper-ua'); ?></p>
    <pre><code>&lt;?php echo vacancy_scraper_render_robotaua(); ?&gt;</code></pre>

    <h3><?php esc_html_e('Getting raw data', 'vacancy-scraper-ua'); ?></h3>
    <p><?php esc_html_e('For full access to the raw data from Robota.ua, use:', 'vacancy-scraper-ua'); ?></p>
    <pre><code>&lt;?php $data = vacancy_scraper_getraw_robotaua(); ?&gt;</code></pre>
    <p><?php esc_html_e('The returned array includes two main sections: "options" (settings used for output) and "jobs" (list of vacancies).', 'vacancy-scraper-ua'); ?></p>


</div>
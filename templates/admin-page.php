<?php if (!defined( 'ABSPATH' )) exit; ?>
<div class="wrap">
    <h1><?php esc_html_e('Vacancy Scraper UA – Settings', 'vacancy-scraper-ua'); ?></h1>

    <?php
    // Вывод уведомлений
    $errors = get_transient('vacancy_scraper_ua_admin_notices');
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $class = ($error['type'] === 'error') ? 'notice notice-error' : 'notice notice-success';
            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($error['message']));
        }
        delete_transient('vacancy_scraper_ua_admin_notices');
    }
    ?>

    <h2 class="nav-tab-wrapper jv-nav-tab-wrapper">
        <a href="?page=vacancy-scraper-ua&tab=workua"
           class="nav-tab <?php echo ($active_tab === 'workua') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Work.ua', 'vacancy-scraper-ua'); ?>
        </a>
        <a href="?page=vacancy-scraper-ua&tab=rabotaua"
           class="nav-tab <?php echo ($active_tab === 'rabotaua') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Robota.ua', 'vacancy-scraper-ua'); ?>
        </a>
        <a href="?page=vacancy-scraper-ua-help"
           class="nav-tab <?php echo ($active_tab === 'help') ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Help', 'vacancy-scraper-ua'); ?>
        </a>
    </h2>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('vacancy_scraper_ua_save', 'vacancy_scraper_ua_nonce'); ?>
        <input type="hidden" name="action" value="vacancy_scraper_ua_save">
        <input type="hidden" name="tab" value="<?php echo esc_attr($active_tab); ?>">

        <?php
        if ($active_tab === 'workua') {
            $company_id = $workua_id;
            include plugin_dir_path(__FILE__) . 'forms/workua-form.php';
        } else if ($active_tab === 'rabotaua')  {
            $company_id = $rabotaua_id;
            include plugin_dir_path(__FILE__) . 'forms/rabotaua-form.php';
        } else if ($active_tab === 'help'){
            include plugin_dir_path(__FILE__) . 'admin-help-page.php';
        }else{
            $company_id = $workua_id;
            include plugin_dir_path(__FILE__) . 'forms/workua-form.php';
        }
        ?>
    </form>
</div>

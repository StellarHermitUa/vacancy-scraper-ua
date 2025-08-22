<?php
/**
 * Template for Work.ua company form.
 *
 * @var string $company_id
 * @var array  $company_options
 */
if (!defined( 'ABSPATH' )) exit; 

if (empty($company_id)) : ?>
    <input type="text" name="vacancy_scraper_ua_workua_company_id" class="company_id_field">
    <p><em><?php esc_html_e('Enter Work.ua company ID(123456) or URL(https://www.work.ua/jobs/by-company/123456/) and click Save.', 'vacancy-scraper-ua'); ?></em></p>
    <button type="submit" name="vacancy_scraper_ua_action" value="save" class="button button-primary">
        <?php esc_html_e('Save', 'vacancy-scraper-ua'); ?>
    </button>
<?php else : ?>


    <p><strong><?php esc_html_e('Company added:', 'vacancy-scraper-ua'); ?></strong> <?php echo esc_html($company_id); ?></p>
    <button type="submit" name="vacancy_scraper_ua_action" value="delete_workua" class="button button-secondary">
        <?php esc_html_e('Delete', 'vacancy-scraper-ua'); ?>
    </button>
    <button type="submit" name="vacancy_scraper_ua_action" value="fetch_workua" class="button button-primary">
        <?php esc_html_e('Fetch Vacancies', 'vacancy-scraper-ua'); ?>
    </button>
<div class="jv_additional">
    <h2 class="jv_additional_title"><?php esc_html_e('Additional Options', 'vacancy-scraper-ua'); ?></h2>

    <h3><?php esc_html_e('Filter by city', 'vacancy-scraper-ua'); ?></h3>

    <div class="jv-theme">
        <label>
            <?php esc_html_e('On/Off', 'vacancy-scraper-ua'); ?>:
            <input type="checkbox" name="vacancy_scraper_ua_workua_filter" value="1" <?php echo $workua_company_options['filter']? 'checked':'';?>>
        </label>
              
    </div>

    <h3><?php esc_html_e('Job Vacancies Colors', 'vacancy-scraper-ua'); ?></h3>

    <div class="jv-theme">
        <label>
            <?php esc_html_e('Background Color', 'vacancy-scraper-ua'); ?>:
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-background]" value="<?php echo esc_html($workua_company_options['colors']['jv-background'] ?? '#ffffff');?>">
        </label>
        <label>
            <?php esc_html_e('Title Color', 'vacancy-scraper-ua'); ?>:
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-title]" value="<?php echo esc_html($workua_company_options['colors']['jv-title'] ?? '#000000');?>">
        </label>
        <label>
            <?php esc_html_e('City Color', 'vacancy-scraper-ua'); ?>:
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-city]" value="<?php echo esc_html($workua_company_options['colors']['jv-city'] ?? '#cccccc');?>">
        </label>

        <label>
            <?php esc_html_e('Salary Color', 'vacancy-scraper-ua'); ?>:
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-salary]" value="<?php echo esc_html($workua_company_options['colors']['jv-salary'] ?? '#98a1a4');?>">
        </label>

        <label>
            <?php esc_html_e('Button Color', 'vacancy-scraper-ua'); ?>:
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-button]" value="<?php echo esc_html($workua_company_options['colors']['jv-button'] ?? '#c6cbcd');?>">
        </label>
         
        <label>
            <?php esc_html_e('Button Color', 'vacancy-scraper-ua'); ?> (hover):
            <input type="color" name="vacancy_scraper_ua_workua_colors[jv-button-hover]" value="<?php echo esc_html($workua_company_options['colors']['jv-button-hover'] ?? '#000000');?>">
        </label>
                
    </div>


    <button type="submit" name="vacancy_scraper_ua_action" value="save_workua_options" class="button button-primary">
        <?php esc_html_e('Save Options', 'vacancy-scraper-ua'); ?>
    </button>
 </div>

<?php if (!empty($jobs)) : ?>
    <h2><?php esc_html_e('Vacancies List', 'vacancy-scraper-ua'); ?></h2>
    <table class="widefat fixed striped">
        <thead>
            <tr>
                <th><?php esc_html_e('Name', 'vacancy-scraper-ua'); ?></th>
                <th><?php esc_html_e('City', 'vacancy-scraper-ua'); ?></th>
                <th><?php esc_html_e('Salary', 'vacancy-scraper-ua'); ?></th>
                <th><?php esc_html_e('Link', 'vacancy-scraper-ua'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job) : ?>
                <tr>
                    <td><?php echo esc_html($job->name); ?></td>
                    <td><?php echo esc_html($job->city); echo !empty($job->city_comment) ? esc_html($job->city_comment):''; ?></td>
                    <td>
                        <?php echo esc_html($job->salary); ?>
                        <?php if (!empty($job->currency)) echo ' ' . esc_html($job->currency); ?>
                    </td>
                    <td>
                        <a href="<?php echo esc_url($job->link); ?>" target="_blank">
                            <?php esc_html_e('View Vacancy', 'vacancy-scraper-ua'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

        <?php
        $total_pages = ceil($total_jobs / $per_page);
        if ($total_pages > 1) : ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    $page_links = paginate_links([
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'prev_text' => __('« Previous', 'vacancy-scraper-ua'),
                        'next_text' => __('Next »', 'vacancy-scraper-ua'),
                        'total'     => $total_pages,
                        'current'   => $current_page
                    ]);

                    echo $page_links ? wp_kses_post($page_links) : '';
                    ?>
                </div>
            </div>
        <?php endif; ?>


<?php else : ?>
    <p><?php esc_html_e('No vacancies found.', 'vacancy-scraper-ua'); ?></p>
<?php endif; ?>

    
<?php endif; ?>

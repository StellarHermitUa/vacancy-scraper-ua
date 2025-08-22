 <?php
/**
 * Template for Robota.ua vacancies list.
 *
 * @var array $vacancies
 */
if (!defined( 'ABSPATH' )) exit; 

if (empty($vacancies)) {
    echo '<p>' . esc_html__('No vacancies found.', 'vacancy-scraper-ua') . '</p>';
    return;
}

?>


<div class="vacancy_block rabotaua_box">

    <?php 
    
        if($options['filter']){
            $filter = array_unique(array_column($vacancies,'city')); 
                echo '<div class="job_filters_box">';   
            
            echo '<div class="dropdown_wrapper">';
                echo '<div class="dropdown_header btn btn_grey df-cb">';
                echo ' <span class="selected_text">'.esc_html__('All cities', 'vacancy-scraper-ua').'</span>';
                    echo '<i class="dropdown_arrow"></i>';
            echo ' </div>';
                echo '<div class="dropdown_list">';
                echo '<div class="dropdown_item" data-value="">'.esc_html__('All cities', 'vacancy-scraper-ua').'</div>';
                foreach($filter as $filt): 
                ?>
                    <div class="dropdown_item" data-value="<?php echo esc_html($filt);?>"><?php echo esc_html($filt);?></div>
                <?php
                endforeach;
            echo '</div></div></div>';
        }
        
        ?>

        <div class="job_box">
            <?php foreach ($vacancies as $vacancy): ?>
                <a href="<?php echo esc_url($vacancy['link']); ?>" class="job_item"  target="_blank" rel="noopener" data-city="<?php echo esc_html($vacancy['city']); ?>">
                    <div class="job_item_body">
                    <div class="job_item_name"><?php echo esc_html($vacancy['name']); ?></div>
                    <div class="job_item_data">
                        <div class="job_item_price"><?php echo $vacancy['salary'] ? esc_html($vacancy['salary'] . ' ' . $vacancy['currency']) : ''; ?></div>
                        <div class="job_item_misto"><?php echo esc_html($vacancy['city'] . ' ' . $vacancy['city_comment']); ?></div>
                    </div>
                    </div>
                    <div class="job_item_btn">
                    <svg width="14" height="24" viewBox="0 0 14 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.0607 13.0607C13.6464 12.4749 13.6464 11.5251 13.0607 10.9393L3.51472 1.3934C2.92893 0.807611 1.97919 0.807611 1.3934 1.3934C0.807611 1.97919 0.807611 2.92893 1.3934 3.51472L9.87868 12L1.3934 20.4853C0.807611 21.0711 0.807611 22.0208 1.3934 22.6066C1.97919 23.1924 2.92893 23.1924 3.51472 22.6066L13.0607 13.0607ZM11 13.5H12V10.5H11V13.5Z" fill="white"></path>
                    </svg>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
</div>

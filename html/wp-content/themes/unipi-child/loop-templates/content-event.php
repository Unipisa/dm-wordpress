<?php
/**
 * Single post partial template.
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$size = get_theme_mod( 'unipi_single_thumb' );
if(empty($size) || trim($size) == '') {
    $size = 'large';
}
$fields = get_fields(get_the_ID());
?>

<?php
$custom = get_post_custom(get_the_ID());
$sd = $custom["unipievents_startdate"][0];
$ed = $custom["unipievents_enddate"][0];

$date_format = get_option('date_format');

$stimestd = date_i18n('Y-m-d', $sd);
$etimestd = date_i18n('Y-m-d', $ed);

$dt = date_i18n($date_format, $sd);
if($stimestd != $etimestd) {
    $dt .= ' - ' . date_i18n($date_format, $ed);
}

// - local time format -
$time_format = get_option('time_format');
$stime = date_i18n($time_format, $sd);
$etime = date_i18n($time_format, $ed);
$clock = $stime;
if($stime != $etime) {
    $clock .= ' - ' . $etime;
}

$location = $custom["unipievents_place"][0];
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">

        <?php the_title('<h1 class="entry-title bgpantone bgtitle py-1">', '</h1>'); ?>
        
        <div class="entry-meta small">

            <?php if($ed < strtotime('today')): ?>
                <span class="badge badge-primary archivio mr-2">Archivio</span>
            <?php endif; ?>
            <span class="publish-date mr-2"><span class="far fa-calendar"></span> <?php echo $dt ?></span>
            <?php if($clock != '00:00'): ?>
                <span class="hours mr-2"><span class="far fa-clock"></span> <?php echo $clock; ?></span>
            <?php endif ?>
            <?php if($location): ?>
                <span class="location mr-2"><span class="fas fa-map-marker-alt"></span> <?php echo $location ?></span>
            <?php endif; ?>
            <?php if(has_term('', 'unipievents_taxonomy')): ?>
                <span class="tag mr-2"><i class="fa fa-tags"></i> <?php the_terms(get_the_ID(), 'unipievents_taxonomy', '', ', ', ''); ?></span>
            <?php endif ?>

        </div><!-- .entry-meta -->

    </header><!-- .entry-header -->

    <div class="entry-content box mb-0 pt-0 bbottom clearfix">
        
        <?php if( has_post_thumbnail() && $size !== 'none' ): ?>
            <?php the_post_thumbnail($size); ?>
        <?php endif; ?>

        <?php
        if(isset($fields['speaker']) && strlen(trim($fields['speaker'])) > 0 && isset($fields['affiliation']) && strlen(trim($fields['affiliation'])) > 0) {
            if($location) {
                echo '<h4 class="mt-0">Venue</h4>' . "\n";
                echo '<p>' . $location . '</p>';
            }
            echo '<h4>Abstract</h4>';
            the_content();
        } else if(isset($fields['link']) && strlen(trim($fields['link'])) > 0) {
            the_content();
            echo '<p>Further information is available on the <a href="'.esc_url($fields['link']).'">event page</a>.</p>';
        } else {
            the_content();
        }
        ?>

        <?php
        wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . __('Pages:', 'unipi'),
                    'after' => '</div>',
                )
        );
        ?>

    </div><!-- .entry-content -->

</article><!-- #post-## -->

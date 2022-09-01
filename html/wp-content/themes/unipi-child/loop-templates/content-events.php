<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
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
		<?php
		the_title(
			sprintf( '<h2 class="h3 title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
			'</a></h2>'
		);
		?>

		<div class="entry-meta small mb-2">

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
            	<?php
            	$tlist = get_the_terms(get_the_ID(), 'unipievents_taxonomy');
            	$links = [];
            	if(is_array($tlist)) {
            		foreach ($tlist as $t) {
        				$link = get_term_link( $t, 'unipievents_taxonomy' );
				        if ( is_wp_error( $link ) ) {
				            continue;
				        }
				        $links[$t->term_id] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $t->name . '</a>';
            		}
            	}
            	?>
                <span class="tag mr-2"><i class="fa fa-tags"></i> <?php echo implode(', ', $links); ?></span>
            <?php endif ?>

        </div><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<?php
	$size = get_theme_mod( 'unipi_loop_thumb' );
    if(empty($size) || trim($size) == '') {
        $size = 'large';
    }
	?>
	<?php if( has_post_thumbnail() && $size !== 'none' ): ?>
		<div class="post-thumbnail">
			<?php echo get_the_post_thumbnail( $post->ID, $size ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content clearfix">

		<?php the_excerpt(); ?>

		<?php
		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'unipi' ),
				'after'  => '</div>',
			)
		);
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer small clearfix">

		<?php unipi_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->

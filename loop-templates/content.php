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

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php if ( 'post' == get_post_type() ) : ?>

			<?php
	        // Bandi e categorie figlie
	        if(get_current_blog_id() == 1 && apply_filters( 'wp_has_category', 170 )) {
	            $date_format = get_option('date_format');
	            $time_format = get_option('time_format');
	            $date1 = get_field('date1');
	            $date2 = get_field('date2');
	            $dtime = strtotime($date1);
	            $dt = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
	            $dt = $dt->format('Y-m-d H:i:s');
	            $lbl = __('Open', 'unipi');
	            if($date1 < $dt && $dt < $date2) {
	                $lbl = __('In progress', 'unipi');
	            } else if($date2 < $dt) {
	                $lbl = __('Closed', 'unipi');
	            }
	            echo '<div class="entry-meta">';
	            echo '<span class="badge badge-primary archivio mr-2">'.$lbl.'</span>';
	            echo '<small><strong>' . __('Deadline', 'unipi') . ':</strong> ' . date_i18n($date_format, $dtime) . ' - ' . date_i18n($time_format, $dtime) . '</small>';
	            echo '</div><!-- .entry-meta -->';
	        } else {
	            echo '<div class="entry-meta small">';
	            echo unipi_posted_on();
	            echo '</div><!-- .entry-meta -->';
	        }
	        ?>

		<?php endif; ?>

		<?php
		the_title(
			sprintf( '<h2 class="h3 title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
			'</a></h2>'
		);
		?>

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

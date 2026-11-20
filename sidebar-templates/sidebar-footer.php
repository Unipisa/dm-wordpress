<?php
/**
 * Sidebar setup for footer full.
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php if ( is_active_sidebar( 'footer' ) ) : ?>

        <?php
        $bg = get_theme_mod( 'unipi_footer_background' );
        if(!empty($bg) && $bg != '') {
            $bg = ' style="background-image: url('.$bg.')"';
        }
        ?>

	<div class="wrapper bgcover bgdip py-5" id="wrapper-footer-full"<?= $bg ?>>

		<div class="container" id="footer-full-content">

				<?php dynamic_sidebar( 'footer' ); ?>

		</div>

	</div><!-- #wrapper-footer-full -->

<?php endif; ?>

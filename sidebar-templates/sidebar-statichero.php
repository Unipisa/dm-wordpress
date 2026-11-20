<?php
/**
 * Static hero sidebar setup.
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php if ( is_active_sidebar( 'statichero' ) ) : ?>

	<div class="wrapper bgpantone" id="wrapper-static-hero">

			<div class="container" id="wrapper-static-content">

				<div>

					<?php dynamic_sidebar( 'statichero' ); ?>

				</div>

			</div>

	</div><!-- #wrapper-static-hero -->

<?php endif; ?>

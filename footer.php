<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

            <?php get_template_part( 'sidebar-templates/sidebar', 'bottomfull' ); ?>

            <footer class="footer">
                <?php get_template_part('sidebar-templates/sidebar', 'footer'); ?>
                <div class="bgpantone small py-3">
                    <div class="container clearfix site-footer" id="colophon">
                        <div class="float-left site-info"><?php unipi_site_info(); ?></div>
                        <div class="float-right"><?php get_template_part('sidebar-templates/sidebar', 'privacy'); ?></div>
                    </div>
                </div>
            </footer>

        </div>

        <a id="totop" href="#page"><span class="fas fa-angle-up"></span><span class="sr-only"><?= __('Back to top', 'unipi') ?></span></a>

    <?php wp_footer(); ?>

    <script type='text/javascript' src='/cookiechoices.js' ></script>
    <script>
      document.addEventListener('DOMContentLoaded', function(event) {
        cookieChoices.showCookieConsentBar('Questo sito utilizza cookie, anche di terze parti, al fine di garantire una migliore navigazione. Se si continua a navigare, si accetta il loro utilizzo.',
          'OK', 'Cookie policy', 'https://www.unipi.it/index.php/documenti-ateneo/item/12721-privacy-policy/');
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

</body>

</html>


<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();
?>

<div class="wrapper" id="page-wrapper">

    <div class="container py-4" id="content">

        <div class="row">

            <div class="col-md content-area" id="primary">

                <?php unipi_yoast_breadcrumb_bootstrap() ?>

                <main class="site-main" id="main">

                    <?php while (have_posts()) : the_post(); ?>

                        <?php get_template_part('loop-templates/content', 'people'); ?>

                    <?php endwhile; // end of the loop.  ?>

                </main><!-- #main -->
            </div>

            <?php get_template_part('sidebar-templates/sidebar', 'right'); ?>

        </div><!-- .row -->

    </div><!-- #content -->

</div><!-- #page-wrapper -->

<?php get_footer(); ?>

<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package unipi
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

// bandi + traduzione
if(get_current_blog_id() == 1 && is_category(170) || is_category(171)) {
    if ( isset( $_GET['filter'] ) && in_array( $_GET['filter'], ['open', 'in-progress', 'closed'] ) ) {
        $filter = $_GET['filter'];
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $dt = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
        $dt = $dt->format('Y-m-d H:i:s');
        switch ($filter) {
            case 'in-progress':
                $args = array(
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'=>'date1',
                            'compare' => '<',
                            'value' => $dt
                        ),
                        array(
                            'key'=>'date2',
                            'compare' => '>',
                            'value' => $dt
                        ),
                    ),
                    'meta_key'=>'date1',
                    'paged' => $paged,
                    'orderby'=>'meta_value',
                    'order'=>'ASC'
                );
                break;

            case 'closed':
                $args = array(
                    'meta_query' => array(
                        array(
                            'key'=>'date2',
                            'compare' => '<',
                            'value' => $dt
                        ),
                    ),
                    'meta_key'=>'date1',
                    'paged' => $paged,
                    'orderby'=>'meta_value',
                    'order'=>'ASC'
                );
                break;
            
            default:
                $args = array(
                    'meta_query' => array(
                        array(
                            'key'=>'date1',
                            'compare' => '>',
                            'value' => $dt
                        ),
                    ),
                    'meta_key'=>'date1',
                    'paged' => $paged,
                    'orderby'=>'meta_value',
                    'order'=>'ASC'
                );
                break;
        }

        
    } else {
        $args = array(
            'meta_key'=>'date1',
            'paged' => $paged,
            'orderby'=>'meta_value',
            'order'=>'ASC'
        );
    } 

    $args = array_merge( $args, $wp_query->query );
    query_posts( $args );  
}
?>

<div class="wrapper" id="archive-wrapper">

	<div class="container py-4" id="content">

		<div class="row">

			<div class="col-md content-area" id="primary">

                                <main class="site-main" id="main">

                                        <?php if ( have_posts() ) : ?>

                                                <header class="page-header">
                                                    <h1 class="page-title bgpantone bgtitle py-1">
                                                        <?php single_cat_title(); ?>
                                                    </h1>
                                                    <?php
                                                    the_archive_description( '<div class="taxonomy-description box mb-0">', '</div>' );
                                                    ?>
                                                </header><!-- .page-header -->

                                                <div class="box">
                                                    
                                                    <?php while ( have_posts() ) : the_post(); ?>

                                                            <?php

                                                            /*
                                                             * Include the Post-Format-specific template for the content.
                                                             * If you want to override this in a child theme, then include a file
                                                             * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                                                             */
                                                            get_template_part( 'loop-templates/content', get_post_format() );
                                                            ?>

                                                    <?php endwhile; ?>
                                                
                                                </div>
                                                
                                        <?php else : ?>

                                                <?php get_template_part( 'loop-templates/content', 'none' ); ?>

                                        <?php endif; ?>

                                </main><!-- #main -->
                    
                                <!-- The pagination component -->
                                <?php unipi_pagination(); ?>
                        </div>

			<?php get_template_part( 'sidebar-templates/sidebar', 'right' ); ?>

		</div> <!-- .row -->

	</div><!-- #content -->

	</div><!-- #archive-wrapper -->

<?php get_footer(); ?>

<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
global $blog_id;
$ancestor = 0;
$sublvl = 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>

        <div class="site" id="page">

            <a class="skip-link sr-only sr-only-focusable" href="#content"><?php esc_html_e('Skip to content', 'unipi'); ?></a>

            <header id="wrapper-navbar" class="header" itemscope itemtype="http://schema.org/WebSite">

                <div class="preheader bgpantone">
                    <div class="container">
                        <div class="row pt-2">
                            <div class="col-7 col-sm-8 d-flex align-items-center">
                                <div class="brand site-title">
                                    <!-- Your site title as branding in the menu -->
                                    <?php if (is_main_site()) { ?>
                                        <?php $blogname = __(get_bloginfo('name', 'display'), 'unipi-child'); ?>
                                        <?php
                                            if (has_custom_logo() && $show !== 'title') {
                                                the_custom_logo();
                                            } else {
                                                echo '<a class="site-title h2" rel="home" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($blogname); ?>" itemprop="url">' . $blogname . '</a>';
                                            }
                                            ?>

                                        <?php
                                    } else { ?>
                                        <?php
                                        $subsite = (array) explode('-', get_bloginfo('name'));
                                        $subsite = trim(current($subsite));
                                        $subsite = __($subsite, 'unipi');
                                        $blogname = __('Dipartimento di Matematica', 'unipi');
                                        $sublvl++;
                                        ?>
                                        <a class="site-title h2" href="<?php echo esc_url(network_site_url()); ?>" title="<?php echo esc_attr($blogname); ?>" itemprop="url"><?php
                                            if (has_custom_logo() && $show !== 'title') {
                                                the_custom_logo();
                                            } else {
                                                echo $blogname;
                                            }
                                            ?></a><br />
                                        <a class="site-sub-title d-inline-block mt-2" rel="home" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url"><?= $subsite; ?></a>
                                    <?php }
                                    ?><!-- end custom logo -->
                                </div>
                            </div>
                            <div class="col-5 col-sm-4 d-flex align-items-center justify-content-end">
                                <a href="https://www.unipi.it"><img src="<?= get_template_directory_uri() ?>/images/cherubino-white.svg" alt="cherubino" class="img-fluid logocherubino" /></a>
                            </div>
                        </div>
                        <div class="row small pt-1 pb-2">
                            <div class="col-12 d-md-flex justify-content-end subhead">
                                <?php
                                //$current_blog_id = $blog_id;
                                //switch_to_blog(1);

                                wp_nav_menu(
                                    array(
                                        'theme_location' => 'header',
                                        'container_class' => 'topbtns',
                                        'container_id' => 'header-menu-container',
                                        'menu_class' => 'list-unstyled list-inline mb-0',
                                        'fallback_cb' => '',
                                        'menu_id' => 'header-menu',
                                        'depth' => 1,
                                    )
                                );

                                //switch_to_blog($current_blog_id); 
                                ?>
                                <div class="cerca form-inline">
                                    <?php get_template_part('searchformheader'); ?>
                                </div>
                                <?php
                                wp_nav_menu(
                                    array(
                                        'theme_location' => 'language',
                                        'container_class' => 'topbtns languagemenu',
                                        'container_id' => 'lang-menu-container',
                                        'menu_class' => 'list-unstyled list-inline mb-0',
                                        'fallback_cb' => '',
                                        'menu_id' => 'language-menu',
                                        'depth' => 2,
                                        'walker' => new unipi_WP_Bootstrap_Navwalker(),
                                    )
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <nav class="navbar navbar-expand-lg navbar-light navbar-main">

                    <div class="container">


                        <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="<?php esc_attr_e('Toggle navigation', 'unipi'); ?>">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <?php
                        $depth = 2;
                        if(get_theme_mod( 'unipi_multilevel_menu' )) {
                            $depth = 6;
                        }
                        $menuname = 'primary';

                        echo '<div id="navbarNavDropdown" class="collapse navbar-collapse">';
                        wp_nav_menu(
                                array(
                                    'theme_location' => $menuname,
                                    'container'=> false,
                                    //'container_class' => 'collapse navbar-collapse',
                                    //'container_id' => 'navbarNavDropdown',
                                    'menu_class' => 'navbar-nav mr-auto sublvl'.$sublvl,
                                    'fallback_cb' => '',
                                    'menu_id' => 'main-menu',
                                    'depth' => $depth,
                                    'walker' => new unipi_WP_Bootstrap_Navwalker(),
                                )
                        );

                        wp_nav_menu(
                                array(
                                    'theme_location' => 'navbar-right',
                                    'container'=> false,
                                    //'container_class' => 'collapse navbar-collapse',
                                    //'container_id' => 'navbarNavDropdown',
                                    'menu_class' => 'navbar-nav',
                                    'fallback_cb' => '',
                                    'menu_id' => 'main-menu-right',
                                    'depth' => $depth,
                                    'walker' => new unipi_WP_Bootstrap_Navwalker(),
                                )
                        );
                        echo '</div>';
                        ?>

                    </div><!-- .container -->

                </nav><!-- .site-navigation -->

            </header><!-- #wrapper-navbar end -->

            <?php get_template_part( 'global-templates/hero' ); ?>

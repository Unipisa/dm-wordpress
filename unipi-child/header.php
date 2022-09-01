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
$lid = 0;
$ancestor = 0;
/*if($blog_id == 2) {
    $ancestor = get_post_ancestors($post);
    $ancestor = end($ancestor);
    if(ICL_LANGUAGE_CODE === 'en') {
        if(is_page(12371) || $ancestor === 12371) { // L-INF
            $lid = 1;
        } else if(is_page(12336) || $ancestor === 12336) { // LM-WIF
            $lid = 2;
        } else if(is_page(12304) || $ancestor === 12304) { // LM-WEA
            $lid = 3;
        } else if(is_page(15632) || $ancestor === 15632) { // LM-WIN
            $lid = 4;
        }
    } else {
        if(is_page(12103) || $ancestor === 12103) { // L-INF
            $lid = 1;
        } else if(is_page(12104) || $ancestor === 12104) { // LM-WIF
            $lid = 2;
        } else if(is_page(12130) || $ancestor === 12130) { // LM-WEA
            $lid = 3;
        } else if(is_page(12131) || $ancestor === 12131) { // LM-WIN
            $lid = 4;
        }
    }
}*/
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
                        <div class="row pt-2 pb-3">
                            <div class="col-7 col-sm-8 d-flex align-items-end">
                                <div class="brand site-title">
                                    <!-- Your site title as branding in the menu -->
                                    <?php if (is_main_site()) { ?>
                                        <?php $blogname = __(get_bloginfo('name', 'display'), 'unipi-child'); ?>
                                        <a class="site-title h2" rel="home" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($blogname); ?>" itemprop="url"><?= $blogname; ?></a>

                                        <?php
                                    } else { ?>
                                        <?php
                                        $subsite = (array) explode('-', get_bloginfo('name'));
                                        $subsite = trim(current($subsite));
                                        $subsite = __($subsite, 'unipi-child');
                                        $blogname = __(get_blog_option( 1, 'blogname' ), 'unipi-child');
                                        $sublvl++;
                                        ?>
                                        <a class="site-title h2" href="<?php echo esc_url(network_site_url()); ?>" title="<?php echo esc_attr($blogname); ?>" itemprop="url"><?= $blogname; ?></a><br />
                                        <a class="site-sub-title" rel="home" href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url"><?= $subsite; ?></a>
                                        <?php
                                        /*if($lid > 0) {
                                            switch ($lid) {
                                                case 1:
                                                case 2:
                                                case 3:
                                                case 4:
                                                    $title = get_the_title($ancestor);
                                                    echo ' <span class="fsub"><i class="fas fa-angle-right fa-fw"></i></span> <a class="site-sub-sub-title" href="' . esc_url(get_the_permalink($ancestor)) . '" title="'.esc_attr($title).'" itemprop="url">'.$title.'</a>';
                                                    break;
                                                
                                                default:
                                                    # code...
                                                    break;
                                            }
                                            $sublvl++;
                                        }*/
                                        ?>
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
                        if($lid > 0) {
                            switch ($lid) {
                                case 1:
                                    $menuname = 'l-inf';
                                    break;
                                case 2:
                                    $menuname = 'lm-wif';
                                    break;
                                case 3:
                                    $menuname = 'lm-wds';
                                    break;
                                case 4:
                                    $menuname = 'lm-win';
                                    break;
                                
                                default:
                                    break;
                            }
                        }

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

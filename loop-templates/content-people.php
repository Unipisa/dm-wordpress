<?php
/**
 * Partial template for content in page.php
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$fields = get_fields();
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">

        <?php the_title('<h1 class="entry-title bgpantone bgtitle py-1">', '</h1>'); ?>

    </header><!-- .entry-header -->

    <div class="entry-content box clearfix">

        <div class="d-flex flex-wrap align-middle">
            <?php if( has_post_thumbnail() && $size !== 'none' ): ?>
                <div class="mr-4 mb-4">
                    <?php the_post_thumbnail('thumbnail', ['class' => 'rounded img-fluid']); ?>
                </div>
            <?php else: ?>
                <div class="mr-4 mb-4">
                    <img src="https://i0.wp.com/www.dm.unipi.it/wp-content/uploads/2022/07/No-Image-Placeholder.svg_.png?resize=280%2C280&amp;ssl=1" class="rounded img-fluid wp-post-image jetpack-lazy-image jetpack-lazy-image--handled" alt="" data-lazy-loaded="1" loading="eager" width="280" height="280">
                </div>
            <?php endif; ?>

            <div class="ml-4">
                <?php
                $genere = get_field('Genere') == 'Donna' ? 'f' : 'm';
                ?>
                <div class="h4">
                    <?= isset($fields['qualifica']) ? get_people_role_label($fields['qualifica'], ICL_LANGUAGE_CODE, $genere) : '' ?>
                    <?= isset($fields['ulteriore_qualifica']) && $fields['ulteriore_qualifica'] != '' ? ' - ' . get_people_role_label($fields['ulteriore_qualifica'], ICL_LANGUAGE_CODE, $genere) : '' ?>
                </div>


                <?php if(isset($fields['ssd']) && $fields['ssd'] != ''): ?>
                    <p>
                        <strong><?= __('Research Area', 'unipi') ?>:</strong> <?= get_ssd($fields['ssd'], ICL_LANGUAGE_CODE) ?>
                    </p>
                <?php endif; ?>
                </p>
                <?php if($fields['stanza']): ?>
                <div class="d-flex justify-left">
                    <div>
                        <i class="fas fa-address-card mr-2"></i>
                    </div>
                    <div>
                        <?= __('Building', 'unipi') ?> <?= isset($fields['edificio']) ? $fields['edificio'] : '' ?>,
                        <?php
                        $floor = ['', ''];
                        if(isset($fields['piano'])) {
                            switch ($fields['piano']) {
                                case 0:
                                    $floor = ['Ground Floor', 'Piano Terra'];
                                    break;
                                case 1:
                                    $floor = ['First Floor', 'Primo Piano'];
                                    break;
                                case 2:
                                    $floor = ['Second Floor', 'Secondo Piano'];
                                    break;
                                default:
                                    break;
                            }
                        }
                        if(ICL_LANGUAGE_CODE == 'en') {
                            $floor = $floor[0];
                        } else  {
                            $floor = $floor[1];
                        }
                        ?>

                        <?= isset($fields['piano']) ? $floor : '' ?>, <?= __('Room', 'unipi') ?> <?= isset($fields['stanza']) ? $fields['stanza'] : '' ?>,<br>
                        <?php if ($fields['edificio'] == 'Ex Albergo'): ?>
                          Via Buonarroti, 1/c, 56127 Pisa (PI), Italy.
                        <?php else: ?>
                          L.go B. Pontecorvo, 5, 56127 Pisa (PI), Italy.
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <p></p>
                <?php if($fields['email']): ?>
                <p>
                    <i class="fas fa-at mr-2"></i><a href="mailto:<?= isset($fields['email']) ? $fields['email'] : '' ?>"><?= isset($fields['email']) ? $fields['email'] : '' ?></a>
                </p>
                <?php endif; ?>
                <?php if($fields['telefono']): ?>
                <p>
                    <i class="fas fa-phone mr-2"></i><a href="tel:<?= isset($fields['telefono']) ? $fields['telefono'] : '' ?>"><?= isset($fields['telefono']) ? $fields['telefono'] : '' ?></a>
                </p>
		<?php endif; ?>
                <?php if(isset($fields['pagina_personale']) && $fields['pagina_personale'] != ''): ?>
                    <p>
                        <i class="fas fa-link mr-2"></i><a href="<?= $fields['pagina_personale'] ?>"><?= $fields['pagina_personale'] ?></a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        $username = get_field('username');
        $username = ltrim($username, 'a0');
        if($username) {
            $username = get_field('username');
            $username = ltrim($username, 'a');
            $ret = do_shortcode('[arpi id="'.$username.'"]');
            $aux = [];
            $arpilink = do_shortcode('[arpilink id="'.$username.'"]');
            if($arpilink != '') {
                $aux[] = $arpilink;
            }
            if($fields['orcid']) {
                $aux[] = '<a href="https://orcid.org/'.$fields['orcid'].'" target="_blank">Orcid</a>';
            }
            if($fields['arxiv_orcid'] == 1) {
                $aux[] = '<a href="https://arxiv.org/a/'.$fields['orcid'].'" target="_blank">arXiv</a>';
            }
            if($fields['google_scholar']) {
                $aux[] = '<a href="https://scholar.google.com/citations?user='.$fields['google_scholar'].'" target="_blank">Google Scholar</a>';
            }
            if($fields['mathscinet']) {
                $aux[] = '<a href="https://mathscinet.ams.org/mathscinet/MRAuthorID/'.$fields['mathscinet'].'" target="_blank">Mathscinet</a>';
            }
            if(strlen($ret) > 0 || count($aux) > 0) {
                echo '<!-- wp:pb/accordion-item {"titleTag":"h4","uuid":'.$id.'} -->
                        <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js" data-initially-open="false" data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0"><h4 id="at-1001" class="c-accordion__title js-accordion-controller" role="button">' . __('Recent publications', 'unipi') . '</h4><div id="ac-1001" class="c-accordion__content"><!-- wp:freeform -->';
                if(strlen($ret) > 0) {
                    echo $ret;
                }
                if(count($aux) > 0) {
                    echo __('See all the publications on', 'unipi') . ': ';
                    echo implode(', ', $aux);
                }
                echo '<!-- /wp:freeform -->';
                echo '</div></div>
                    <!-- /wp:pb/accordion-item -->';
            }

            echo do_shortcode('[persona id="'.$username.'"]');
        }
        ?>

    </div><!-- .entry-content -->

</article><!-- #post-## -->

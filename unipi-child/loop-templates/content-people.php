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
            <?php endif; ?>
            <div class="my-auto ml-4">
                <div class="h4"><?= isset($fields['qualifica']) ? $fields['qualifica'] : '' ?></div>
                <p>
                </p><div class="d-flex justify-left">
                    <div> 
                        <i class="fas fa-address-card mr-2"></i>
                    </div>
                    <div>
                        Edificio <?= isset($fields['edificio']) ? $fields['edificio'] : '' ?>, Piano <?= isset($fields['piano']) ? $fields['piano'] : '' ?>, Stanza <?= isset($fields['stanza']) ? $fields['stanza'] : '' ?>,<br>
                        L.go B. Pontecorvo, 5, 56127 Pisa (PI), Italy.
                    </div>
                </div>
                <p></p>
                <p>
                    <i class="fas fa-at mr-2"></i><a href="mailto:<?= isset($fields['email']) ? $fields['email'] : '' ?>"><?= isset($fields['email']) ? $fields['email'] : '' ?></a>
                </p>
                <p>
                    <i class="fas fa-phone mr-2"></i><a href="tel:<?= isset($fields['telefono']) ? $fields['telefono'] : '' ?>"><?= isset($fields['telefono']) ? $fields['telefono'] : '' ?></a>
                </p>
                <p>
                    <i class="fas fa-link mr-2"></i><a href="<?= isset($fields['pagina_personale']) ? $fields['pagina_personale'] : '' ?>"><?= isset($fields['pagina_personale']) ? $fields['pagina_personale'] : '' ?></a>
                </p>
            </div>
        </div>

    </div><!-- .entry-content -->

</article><!-- #post-## -->

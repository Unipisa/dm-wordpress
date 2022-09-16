<?php
/**
 * Single post partial template.
 *
 * @package unipi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
$size = get_theme_mod( 'unipi_single_thumb' );
if(empty($size) || trim($size) == '') {
    $size = 'large';
}
$fields = get_fields();
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

    <header class="entry-header">

        <?php the_title('<h1 class="entry-title bgpantone bgtitle py-1">', '</h1>'); ?>

        <?php
        // Bandi e categorie figlie
        if(get_current_blog_id() == 1 && apply_filters( 'wp_has_category', 170 )) {
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');
            $date1 = isset($fields['date1']) ? $fields['date1'] : null;;
            $date2 = isset($fields['date2']) ? $fields['date2'] : null;;
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
            echo unipi_posted_on(false);
            echo '</div><!-- .entry-meta -->';
        }
        ?>

    </header><!-- .entry-header -->

    <div class="entry-content box mb-0 pt-0 bbottom clearfix">
        
        <?php if( has_post_thumbnail() && $size !== 'none' ): ?>
            <?php the_post_thumbnail($size); ?>
        <?php endif; ?>

        <?php the_content(); ?>

        <?php
        $catlist = [];
        $cats = get_the_category();
        foreach ($cats as $c) {
            $catlist[] = $c->slug;
        }

        // Bandi e categorie figlie
        if(in_array('bandi', $catlist) || in_array('openings', $catlist)) {

            // Assegni di ricerca | post doc
            if(in_array('assegni-di-ricerca', $catlist) || in_array('postdoc-positions', $catlist)) {
                $numero_posizioni = isset($fields['numero_posizioni']) ? $fields['numero_posizioni'] : null;
                $progetto = isset($fields['progetto']) ? $fields['progetto'] : null;
                $numero_mesi = isset($fields['numero_mesi']) ? $fields['numero_mesi'] : null;
                $bando = isset($fields['link_bando']) ? $fields['link_bando'] : null;
                $informazioni = isset($fields['informazioni']) ? $fields['informazioni'] : null;
                $approvazione_atti = isset($fields['approvazione_atti']) ? $fields['approvazione_atti'] : null;

                if($numero_posizioni && $progetto) {
                    if (ICL_LANGUAGE_CODE == 'en') {
                        //echo '<p><strong>Postdoctoral position:</strong> ' . get_the_title() . '</p>';
                        if($numero_posizioni == 1) {
                            echo '<p>A position as Postdoctoral Research Fellow is available at the Department of Mathematics. The appointment is a full-time position and is for a period of ' . $numero_mesi . ' months. The supporting grant for this position is part of the ' . $progetto . '.</p>';
                        } else {
                            echo '<p>No. ' . $numero_posizioni . ' positions as Postdoctoral Research Fellows are available at the Department of Mathematics. Each appointment is a full-time position and is for a period of ' . $numero_mesi . ' months. The supporting grant for this position is part of the ' . $progetto . '.</p>';
                        }
                        if($informazioni) {
                            echo '<p>Further information can be found on the following page: <a href="' . $informazioni . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                        if($bando) {
                            echo '<p>The call is published on the following page: <a href="' . $bando . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                        if($approvazione_atti) {
                            echo '<p>Attention: the committee has concluded the hiring procedure. The results can be consulted on the following page: <a href="' . $approvazione_atti . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                    } else {
                        //echo '<p><strong>Assegno di Ricerca:</strong> ' . get_the_title() . '</p>';
                        if($numero_posizioni == 1) {
                            echo '<p>Una posizione di assegnista di ricerca è disponibile presso il Dipartimento di Matematica. La posizione è della durata di ' . $numero_mesi . ' mesi. Essa è finanziata dal ' . $progetto . '.</p>';
                        } else {
                            echo '<p>No. ' . $numero_posizioni . ' posizioni di assegnisti di ricerca sono disponibili presso il Dipartimento di Matematica. Ogni posizione è della durata di ' . $numero_mesi . ' mesi. Ognuna è finanziata dal ' . $progetto . '.</p>';
                        }
                        if($bando) {
                            echo '<p>Il bando è consultabile alla seguente pagina: <a href="' . $bando . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                        if($informazioni) {
                            echo '<p>Maggiori informazioni sono disponibili alla seguente pagina: <a href="' . $informazioni . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                        if($approvazione_atti) {
                            echo '<p>Attenzione: la commissione ha terminato i suoi lavori. I risultati sono consultabili al seguente link: <a href="' . $approvazione_atti . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                        }
                    }

                }
            // incarichi di lavoro autonomo
            } else if(in_array('incarichi-di-lavoro-autonomo', $catlist)) {
                $mesi_incarico = isset($fields['mesi_incarico']) ? $fields['mesi_incarico'] : null;
                $bando_incarico = isset($fields['bando_incarico']) ? $fields['bando_incarico'] : null;
                $esito_selezione_incarico = isset($fields['esito_selezione_incarico']) ? $fields['esito_selezione_incarico'] : null;
                if($bando_incarico && $mesi_incarico) {
                    echo '<p>';
                    if($mesi_incarico) {
                        echo 'Un incarico di lavoro autonomo è disponibile presso il Dipartimento di Matematica, della durata di ' . $mesi_incarico . ' mesi.';
                    }
                    if($bando_incarico) {
                        echo ' È possibile consultare il bando alla pagina relativa: <a href="' . $bando_incarico . '" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                    }
                    echo '</p>';
                    if($esito_selezione_incarico) {
                        echo '<p>Attenzione: la procedura di assegnazione dell’incarico è terminata. L’esito della selezione è consultabile alla seguente pagina: <a href="' . $esito_selezione_incarico . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                    }
                }
            // tutorato
            } else if(in_array('collaborazioni-studentesche', $catlist)) {
                $titolo_incarico = isset($fields['titolo_incarico']) ? $fields['titolo_incarico'] : '';
                $numero_di_incarichi = isset($fields['numero_di_incarichi']) ? $fields['numero_di_incarichi'] : 1;
                $periodo_incarico = isset($fields['periodo_incarico']) ? $fields['periodo_incarico'] : '';
                $bando = isset($fields['bando']) ? $fields['bando'] : null;
                $esito = isset($fields['informazioni']) ? $fields['informazioni'] : null;

                if($titolo_incarico != '' && $periodo_incarico != '') {
                    if($numero_di_incarichi > 1) {
                        echo '<p>È stata attivata una procedura comparativa per il conferimento di no. ' . $numero_di_incarichi . ' ' . $titolo_incarico . ' ' . $periodo_incarico . '.</p>';
                    } else {
                        echo '<p>È stata attivata una procedura comparativa per il conferimento di un ' . $titolo_incarico . ' ' . $periodo_incarico . '.</p>';
                    }
                
                    if($bando) {
                        echo '<p>Il bando è consultabile alla seguente pagina: <a href="' . $bando . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                    }
                    if($esito) {
                        echo '<p>L’esito della selezione &egrave; consultabile alla seguente pagina: <a href="' . $esito . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';
                    }
                }
            }
        } else if(in_array('elezioni', $catlist)) {
            $numero = isset($fields['numero']) ? $fields['numero'] : 1;
            $carica = isset($fields['carica']) ? $fields['carica'] : null;
            $nomina_rinnovo = isset($fields['nomina_rinnovo']) ? $fields['nomina_rinnovo'] : null;
            $mesi = isset($fields['mesi']) ? $fields['mesi'] : null;
            $sito_provvedimento_di_indizione = isset($fields['sito_provvedimento_di_indizione']) ? $fields['sito_provvedimento_di_indizione'] : null;
            $esito_elezioni = isset($fields['esito_elezioni']) ? $fields['esito_elezioni'] : null;

            if($carica && $mesi && nomina_rinnovo) {
                if($numero > 1) {
                    echo '<p>';
                    if($nomina_rinnovo && $carica) {
                        echo 'Sono indette le elezioni per la ' . $nomina_rinnovo . ' delle no. ' . $numero . ' cariche di ' . $carica . '.';                    
                    }
                    if($mesi) {
                        echo ' Ognuna delle cariche avrà durata pari a ' . $mesi . ' mesi.';                    
                    }
                    echo '</p>';
                } else {
                    echo '<p>';
                    if($nomina_rinnovo && $carica) {
                        echo 'Sono indette le elezioni per la ' . $nomina_rinnovo . ' della carica di ' . $carica . '.';                    
                    }
                    if($mesi) {
                        echo ' Essa avrà durata pari a ' . $mesi . ' mesi.';                    
                    }
                    echo '</p>';
                }
                if($sito_provvedimento_di_indizione) {
                    echo '<p>Il provvedimento di indizione delle elezioni è consultabile alla seguente pagina: <a href="' . $sito_provvedimento_di_indizione . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';                
                }
                if($esito_elezioni) {
                    echo '<p>Le elezioni si sono concluse. I risultati sono consultabili alla seguente pagina: <a href="' . $esito_elezioni . '" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>';                
                }
            }
           
        } else if(in_array('dottorato', $catlist) || in_array('ph-d', $catlist)) {
            $title_course = isset($fields['title_course']) ? $fields['title_course'] : '';
            $name = isset($fields['name']) ? $fields['name'] : '';
            $date = isset($fields['date']) ? $fields['date'] : '';
            $time = isset($fields['time']) ? $fields['time'] : '';
            $room = isset($fields['room']) ? $fields['room'] : '';
            $course_link = isset($fields['course_link']) ? $fields['course_link'] : '';

            if($title_course != '') {
                if (ICL_LANGUAGE_CODE == 'en') {
                    echo '<p>We are pleased to announce the Course “' . $title_course . '”, given by ' . $name . '.</p>';
                    if ($date) {
                        echo '<p>The first lesson will take place on ' . $date;
                        if ($time) {
                             echo ' at ' . $time;
                        }
                        if ($room) {
                            echo ' in “' . $room . '”';
                        }
                        echo ".</p>";
                    }
                    if ($course_link) {
                        echo '<p>All the information is available on the <a href="' . $course_link . '">page of the course</a> and on the <a href="https://www.dm.unipi.it/phd/current-ph-d-courses/">page of the current Ph.D. courses</a>.</p>';
                    }
                    else {
                        echo '<p>All the information is available on the <a href="https://www.dm.unipi.it/phd/current-ph-d-courses/">page of the current Ph.D. courses</a>.</p>';
                    }
                } else {
                    echo "<p>Si comunica l'attivazione del corso“" . $title_course . '”, tenuto da ' . $name . '.</p>';
                    if ($date) {
                        echo '<p>La prima lezione si terrà il giorno ' . $date;
                        if ($time) {
                             echo ' alle ore ' . $time;
                        }
                        if ($room) {
                            echo ' in “' . $room . '”';
                        }
                        echo ".</p>";
                    }
                    if ($course_link) {
                        echo '<p>Tutte le informazioni sono disponibili alla <a href="' . $course_link . '">pagina del corso</a> e alla <a href="https://www.dm.unipi.it/phd/current-ph-d-courses/">pagina dei corsi di dottorato</a>.</p>';
                    }
                    else {
                        echo '<p>Tutte le informazioni sono disponibili alla <a href="https://www.dm.unipi.it/phd/current-ph-d-courses/">pagina dei corsi di dottorato</a>.</p>';
                    }
                }
            }
        }
        ?>

        <?php
        wp_link_pages(
                array(
                    'before' => '<div class="page-links">' . __('Pages:', 'unipi'),
                    'after' => '</div>',
                )
        );
        ?>

        <footer class="entry-footer small clearfix">

            <?php unipi_entry_footer(); ?>

        </footer><!-- .entry-footer -->

    </div><!-- .entry-content -->

</article><!-- #post-## -->

<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include 'secrets.php';

define('API_URL', 'https://api.unipi.it:443/');
define('TOKEN', 'to be defined in secrets.php');
define('TOKENARPI', 'to be defined in secrets.php');
define('TOKENARPILINK', 'to be defined in secrets.php');

class Unimap
{
    // private $anno;
    private $corso;
    
    function __construct()
    {
    }

    public function getTeachers($anno, $corso) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_URL . 'ricevimento/1.0.0/corso/' . $corso . '?anno=' . $anno);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . TOKEN;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
            $resp = json_decode($result, true);
            if(isset($resp['Results']['docente'])) {
                $aux = $resp['Results']['docente'];
                usort($aux, function($a, $b) {
                    return $a['cognome'] <=> $b['cognome'];
                });

                $ret = [];
                $ret[] = '<div class="personlist">';
                foreach ($aux as $p) {
                    $ore = trim($p['orario']);
                    $luogo = trim($p['luogo']);
                    $note = trim($p['note']);
                    $chiave = 'ri=' . $p['chiaveunimap'];
                    if($chiave == 'ri=') {
                        $chiave = 'mat=' . intval($p['codice']);
                    }

                    $ret[] = '<div class="single-person">';
                    $ret[] = '<h4>'.$p['nome'] . ' ' . $p['cognome'] .' <small><a target="_blank" href="https://unimap.unipi.it/cercapersone/dettaglio.php?'.$chiave.'">Vedi su Unimap</a></small></h4>';

                    if(strlen($ore)>0) {
                        $ret[] = '<p><strong>Orario:</strong> ' . $ore .'</p>';
                    }
                    if(strlen($luogo)>0) {
                        $ret[] = '<p><strong>Luogo:</strong> ' . $luogo .'</p>';
                    }
                    if(strlen($note)>0) {
                        $ret[] = '<p><strong>Note:</strong><br />' . nl2br($note) .'</p>';
                    }
                    $ret[] = '</div>';
                }
                $ret[] = '</div>';
                return implode("\n", $ret) . "\n";
            }
        }
        return '';
    }

    public function getPersona($id) {

        $ret = [];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_URL . 'unimapserv/3.0/getPersona?matr=' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer' . TOKEN;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
            //print_r($result);
            $resp = json_decode($result, true);
            if(isset($resp['Entries']['Didattica'])) {
                $ret[] = '<!-- wp:pb/accordion-item {"titleTag":"h4","uuid":'.$id.'} -->
                        <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js" data-initially-open="false" data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0"><h4 id="at-1002" class="c-accordion__title js-accordion-controller" role="button">' . __('Courses', 'unipi') . '</h4><div id="ac-1002" class="c-accordion__content">';

                $ret[] = '<!-- wp:freeform --><ul>';
                foreach ($resp['Entries']['Didattica'] as $kd => $vd) {
                    if($kd == 'Corso') {
                        $arr = isset($vd['Condivisione_mutuazione']['COD_AD']) ? [$vd] : $vd;
                        foreach ($arr as $a) {
                            $aux = [];
                            $extra = '';
                            if(isset($a['Condivisione_mutuazione']['COD_AD']) && !is_array($a['Condivisione_mutuazione']['COD_AD'])) {
                                $aux[] = $a['Condivisione_mutuazione']['COD_AD'];
                                $extra = ' per ' . $a['Condivisione_mutuazione']['TIPOCORSO_CDS'] . ' in ' . $a['Condivisione_mutuazione']['DES_CDS'] . '<br /> &nbsp;&nbsp; <strong>Insegnamenti condivisi/mutuati:</strong> ' . $a['Condivisione_mutuazione']['DES_AD'];
                            }
                            $aux[] = isset($a['NOME_REGISTRO']) ? $a['NOME_REGISTRO'] : '';
                            $ret[] = '<li>' . implode(' - ', $aux) . $extra . '</li>';
                        }
                        
                    }
                    
                }
                $ret[] = '</ul><!-- /wp:freeform -->';
                $ret[] = '</div></div>
                    <!-- /wp:pb/accordion-item -->';
            }
        }
        return implode("\n", $ret);
    }

    function getRegistri($id, $anno = 2021) {

        $ret = [];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, API_URL . 'registri/1.0/elenco/'.$id.'?anno=' . $anno);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $en = get_locale() !== "it_IT";
        $teaching_desc = $en ? "Teaching" : "Didattica";
        $courses_desc = $en ? "Courses for the current academic year:" : "Corsi insegnati nel corrente anno accademico:";

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . TOKEN;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
            //print_r($result);
            $resp = json_decode($result, true);
            //echo '<pre>';
            //print_r($resp);
            //echo '</pre>';
            if(isset($resp['results']['registro'])) {
                $ret[] = '<!-- wp:pb/accordion-item {"titleTag":"h4","uuid":'.$id.'} -->
                        <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js" data-initially-open="false" data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0"><h4 id="at-1002" class="c-accordion__title js-accordion-controller" role="button">' . $teaching_desc . '</h4><div id="ac-1002" class="c-accordion__content">';

                $ret[] = '<p>' . $courses_desc . '</p>';
                $ret[] = '<!-- wp:freeform --><ul>';
                foreach ($resp['results']['registro'] as $kd => $vd) {
                    $ret[] = '<li>';
                    if($vd['modulo'] !== 'NESSUNO') {
                        //  GEOMETRIA (Modulo dell'insegnamento GEOMETRIA E ALGEBRA LINEARE - Cod. 177AA) CdS ICE-L INGEGNERIA CIVILE AMBIENTALE E EDILE
                        $ret[] = '<strong>' . $vd['modulo'] . '</strong>';
                        $ret[] = '(Modulo dell\'insegnamento '.$vd['descrizione'].' - Cod. ' . $vd['codiceInsegnamento'] . ')';
                        $ret[] = 'CdS ' . $vd['codiceCorso'] . ' ' . $vd['denominazione'];
                    } else {
                        // GEOMETRIA E TOPOLOGIA DIFFERENZIALE (Cod. 055AA) CdS MAT-L MATEMATICA
                        $ret[] = '<strong>' . $vd['descrizione'] . '</strong>';
                        $ret[] = '(Cod. ' . $vd['codiceInsegnamento'] . ')';
                        $ret[] = 'CdS ' . $vd['codiceCorso'] . ' ' . $vd['denominazione'];
                    }
                    $ret[] = ' (<a href="https://unimap.unipi.it/registri/dettregistriNEW.php?re='.$vd['id'].'::::&ri='.$vd['matricola'].'" target="_blank">Registro</a>)';

                    /*
                    [partizionamento] => Corso Nessun partizionamento
                    [numeroModuli] => 2
                    [progressivoModulo] => 1
                    */
                    $ret[] = '</li>';
                }
                $ret[] = '</ul><!-- /wp:freeform -->';
                $ret[] = '</div></div>
                    <!-- /wp:pb/accordion-item -->';
            }
        }
        return implode("\n", $ret);
    }

    function getArpi($id, $anno = 2021) {

        $ret = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . 'arpicineca/1.0/getElencoPeriodo/'.$id.'/' . $anno);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . TOKENARPI;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
            $resp = json_decode($result, true);
            if(isset($resp['entries']['entry'])) {
                $ret[] = '<ul>';
                $count = 0;
                foreach ($resp['entries']['entry'] as $kd => $vd) {
                    if($count == 5) break;
                    $ret[] = '<li>';
                    // $vd['collectionDes']
                    $ret[] = $vd['title']. ' ['.$vd['anno'].']' .' <a href="'.$vd['link'].'" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                    $ret[] = '</li>';
                    $count++;
                }
                $ret[] = '</ul>';
            }
        }
        return implode("\n", $ret);
    }

    function getArpiLink($id) {

        $ret = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . 'uniarpi/1.0/linkRicerca/'.$id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Authorization: Bearer ' . TOKENARPILINK;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpcode === 200) {
            $resp = json_decode($result, true);
            if(isset($resp['linkToArpi'])) {
                $ret = $resp['linkToArpi'];
            }
        }
        return $ret;
    }
}



/* Shortcode */
function get_ricevimento( $atts ) {
    extract( shortcode_atts( array(
        'anno' => date('Y'),
        'corso' => 'INF-L',
    ), $atts ) );

    $ret = '';

    if($anno && $corso) {
        $md5 = md5('ricev' . $anno . '-' . $corso);
        if ( false === ( $ret = get_transient( $md5 ) ) ) {
            $r = new Unimap();
            if($r->authenticate()) {
                $ret = $r->getTeachers(intval($anno), $corso);
                $ret .= '<p class="small my-4">[sorgente dati: UNIMAP. Ogni modifica apparirà entro le 24h successive]</p>';
                set_transient( $md5, $ret, 1 * HOUR_IN_SECONDS );
            }
        }
    }

    return $ret;
}
add_shortcode('ricevimento', 'get_ricevimento');


function get_persona( $atts ) {
    extract( shortcode_atts( array(
        'id' => '',
    ), $atts ) );

    $ret = '';

    $r = new Unimap();
    //$ret = $r->getPersona($id);
    // $anno = get_field('anno', 'option');
    $anno = 0;

    // if (! $anno) {
    $anno = intval(date('Y'));
    if (intval(date('m')) < 10) {
        $anno = $anno - 1;
    }
    $anno = strval($anno);
    // }
    $ret .= $r->getRegistri($id, $anno);

    return "<!-- anno: $anno  -->\n" . $ret;
}
add_shortcode('persona', 'get_persona');

function get_arpi( $atts ) {
    extract( shortcode_atts( array(
        'id' => '',
    ), $atts ) );

    $ret = '';

    $r = new Unimap();
    $anno = date('Y');
    $ret .= $r->getArpi($id, $anno);

    return $ret;
}
add_shortcode('arpi', 'get_arpi');

function get_arpi_link( $atts ) {
    extract( shortcode_atts( array(
        'id' => '',
    ), $atts ) );

    $r = new Unimap();
    $lnk = $r->getArpiLink($id, $anno);
    if($lnk && $lnk != 'https://arpi.unipi.it') {
        return '<a giorgio="giorgio1" href="'.$lnk.'" target="_blank">Arpi</a>';
    }
    return '';
}
add_shortcode('arpilink', 'get_arpi_link');

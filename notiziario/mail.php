<?php
require_once __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$cont = @file_get_contents('https://www.dm.unipi.it/notiziario/');

$d = new DOMDocument;
$mock = new DOMDocument;
libxml_use_internal_errors(true);
$d->loadHTML($cont);
libxml_clear_errors();
$body = $d->getElementsByTagName('body')->item(0);
foreach ($body->childNodes as $child) {
  if(isset($child->tagName) && ($child->tagName == 'script' || $child->tagName == 'p')) continue;
    $mock->appendChild($mock->importNode($child, true));
}

$ret = $mock->saveHTML();

/*$ret = str_replace('<span class="far fa-calendar"></span>' , 'Data: ', $ret);
$ret = str_replace('<span class="far fa-clock"></span>' , ' | ore: ', $ret);
$ret = str_replace('<i class="fa fa-tags"></i>' , ' | Tags: ', $ret);
$ret = str_replace('<span class="fas fa-map-marker-alt"></span>' , ' | presso: ', $ret);
$ret = str_replace('<article>' , '<hr><div>', $ret);
$ret = str_replace('</article>' , '</div>', $ret);
$ret = str_replace('<span class="sr-only">Pubblicato il</span> <time' , '<span', $ret);
$ret = str_replace('</time></span>' , '</span>', $ret);*/

$transport = (new Swift_SmtpTransport('mixer.unipi.it', 25));

$mailer = new Swift_Mailer($transport);

$text = \Soundasleep\Html2Text::convert($ret);

$message = (new Swift_Message('Ultime dal Dipartimento di Matematica'))
  ->setFrom(['noreply@dm.unipi.it' => 'Dipartimento di Matematica - Unipi'])
  ->setTo(['deirossi@inwind.it', 'notiziario@dm.unipi.it'])
  ->setBody($text)
  ->addPart($ret, 'text/html');

// Send the message
$result = $mailer->send($message);

print_r($result);
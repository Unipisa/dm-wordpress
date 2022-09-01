<?php
$minyear = 1988;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://www.genealogy.math.ndsu.nodak.edu/query-prep.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "chrono=0&given_name=&other_names=&family_name=&school=Universit%C3%A0+di+Pisa&year=&thesis=&country=&msc=&submit=Invia+richiesta");

$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:101.0) Gecko/20100101 Firefox/101.0';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
$headers[] = 'Accept-Language: it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Origin: https://www.genealogy.math.ndsu.nodak.edu';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Referer: https://www.genealogy.math.ndsu.nodak.edu/search.php';
$headers[] = 'Cookie: PHPSESSID=km3po6cb6mce5n9uhv4djef575; EU_COOKIE_LAW_CONSENT=true; _ga=GA1.2.2080285315.1656541134; _gid=GA1.2.2123580154.1656712519; _gat_gtag_UA_16329138_2=1';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'Sec-Fetch-Dest: document';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Pragma: no-cache';
$headers[] = 'Cache-Control: no-cache';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://www.genealogy.math.ndsu.nodak.edu/results.php?');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


$headers = array();
$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:101.0) Gecko/20100101 Firefox/101.0';
$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
$headers[] = 'Accept-Language: it-IT,it;q=0.8,en-US;q=0.5,en;q=0.3';
$headers[] = 'Accept-Encoding: gzip, deflate, br';
$headers[] = 'Referer: https://www.genealogy.math.ndsu.nodak.edu/search.php';
$headers[] = 'Connection: keep-alive';
$headers[] = 'Cookie: PHPSESSID=km3po6cb6mce5n9uhv4djef575; EU_COOKIE_LAW_CONSENT=true; _ga=GA1.2.2080285315.1656541134; _gid=GA1.2.2123580154.1656712519; _gat_gtag_UA_16329138_2=1';
$headers[] = 'Upgrade-Insecure-Requests: 1';
$headers[] = 'Sec-Fetch-Dest: document';
$headers[] = 'Sec-Fetch-Mode: navigate';
$headers[] = 'Sec-Fetch-Site: same-origin';
$headers[] = 'Sec-Fetch-User: ?1';
$headers[] = 'Pragma: no-cache';
$headers[] = 'Cache-Control: no-cache';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

libxml_use_internal_errors(true);
$xml = new DOMDocument();
$xml->validateOnParse = true;
$xml->loadHTML($result);

$xpath = new DOMXPath($xml);
$table =$xpath->query("//table")->item(0);

$rows = $table->getElementsByTagName("tr");

$ret = [];

foreach ($rows as $row) {
  $cells = $row->getElementsByTagName('td');
  $count = 0;
  $aux = [];
  foreach ($cells as $cell) {
    if($count != 1) {
        $aux[$count] = $cell->nodeValue; // print cells' content as 124578
        $a = $cell->getElementsByTagName('a');
        if($a->item(0)) {
            $aux['link'] = 'https://www.genealogy.math.ndsu.nodak.edu/' . $a->item(0)->getAttribute('href');
        }
    }
    $count++;
  }

  if($aux[2] >= $minyear) {
    if(!isset($ret[$aux[2]])) {
        $ret[$aux[2]] = [];
      }
      $ret[$aux[2]][] = $aux;
  }
}

krsort($ret);

$id = 4000;
$tot = count($ret);
$locale = 'en_US';
$nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
foreach ($ret as $k => $r) {

    echo '<!-- wp:pb/accordion-item {"titleTag":"h4","uuid":'.$id.'} -->
            <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js" data-initially-open="false" data-click-to-close="true" data-auto-close="true" data-scroll="false" data-scroll-offset="0"><h4 id="at-'.$id.'" class="c-accordion__title js-accordion-controller" role="button">' . $k . '</h4><div id="ac-'.$id.'" class="c-accordion__content">';

    echo '<!-- wp:freeform --><div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Surname</th><th>Name</th><th>The Mathematics Genealogy Project Card</th></tr></thead><tbody>';

    foreach ($r as $e) {
        list($s, $n) = explode(',', $e[0]);
        echo '<tr><td>'.trim($s).'</td><td>'.trim($n).'</td><td><a target="_blank" href="'.$e['link'].'">[icon class="fas fa-id-card fa-fw"]</a></td></tr>';
    }
    echo '</tbody></table></div><!-- /wp:freeform -->';

    echo '</div></div>
                <!-- /wp:pb/accordion-item -->';
    $id++;
    $tot--;
}

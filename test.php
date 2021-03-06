<?php 
require_once('simple_html_dom.php');
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
$time = time();
$start = isset($argv[1]) ?  $argv[1] : 1;
$end = isset($argv[2]) ?  $argv[2] : 1 ;
$fp = fopen($start.'-'.$end.'.csv', 'w');
for($i = $start; $i <= $end ; $i++ ) {
    echo "Page ".$i.PHP_EOL;
    $endPoint =  'https://batdongsan.com.vn/ct-thi-cong-xay-dung/p'.$i;
    //var_dump($endPoint);
    $html = _CURL($endPoint);
    $dom = str_get_html($html);
    $content = $dom->find('.infoitem');
    //var_dump($content);die;
    if($content){
        foreach ($content as $key => $value) {        
            $data = [
                'name' => $value->find('h3 a', 0)->plaintext,
                'link' => $value->find('h3 a',0)->href,
                'address' => $value->find('.broker-address', 0)->plaintext,
                'phone' =>  $value->find('.broke-phone', 0)->plaintext
            ];
            // var_dump($data);die;
            echo ' + '.$data['name'].PHP_EOL;
            if($value->find('a', 2) != null && $value->find('a', 2)) {
                $b = file_get_contents($data['link']);
                $a = str_get_html($b);
                $email = $a->find('.ttmg .mb5 ', 2) != null ? $a->find('.ttmg .mb5 1', 1)->plaintext : '';
                $data['email'] = $email;
                
            }else {
                $data['email'] = '';
            }
            
            unset($data['link']);
            fputcsv($fp, $data);
        }
    }
}
echo time() - $time;
echo "\n";
echo "Done";
fclose($fp);
function validateDate($string)
{
    $string = preg_replace('/\(.+\)/', '', $string);
    $string = str_replace('Ngày thành lập:', '', $string);
    return trim($string);
}
function validateCate($string)
{
    // $string = preg_replace('/\(.+\)/', '', $string);
    $string = str_replace('Ngành nghề chính:', '', $string);
    return trim($string);
}
function _CURL($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url,
        // CURLOPT_PROXY =>  '115.84.178.44:3128',
        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html'
    ));
    $res = curl_exec($curl);
    //var_dump($res);die;
    curl_close($curl);
    return $res;
}
<?php

require '../vendor/autoload.php';

$app = new \Wave\Framework\Application\Core('Wave Skeleton Application');

$app->controller('/', 'GET', function(){
    echo "Hello World!";
    echo "See your horoscope, chose your sign:<br />";
    echo '<ul>';
    echo '
    <li><a href="/sign/aquarius">Aquarius</a></li>
    <li><a href="/sign/pieces">Pieces</a></li>
    <li><a href="/sign/aries">Aries</a></li>
    <li><a href="/sign/taurus">Taurus</a></li>
    <li><a href="/sign/gemini">Gemini</a></li>
    <li><a href="/sign/cancer">Cancer</a></li>
    <li><a href="/sign/leo">Leo</a></li>
    <li><a href="/sign/virgo">Virgo</a></li>
    <li><a href="/sign/libra">Libra</a></li>
    <li><a href="/sign/Scorpio">Scorpio</a></li>
    <li><a href="/sign/sagittarius">Sagittarius</a></li>
    <li><a href="/sign/capricorn">capricorn</a></li>
    ';
    echo '</ul>';
});

$app->controller('/sign/:sign', 'GET', function($arguments) {
    if (extension_loaded('curl')) {
        $request = new \Wave\Framework\Http\Curl\Request();
        $r = $request->setUrl(sprintf('http://widgets.fabulously40.com/horoscope.json?sign=%s', $arguments->sign))
            ->setMethod('GET')
            ->setUA('WaveFramework/2.0 Http\Curl\Request Client')
            ->send();

        if (!is_null($r)) {
            $data = json_decode($r->getData(), true);
            echo sprintf("Sign: %s <br />", ucfirst($data['horoscope']['sign']));
            echo sprintf("Horoscope: %s", htmlentities($data['horoscope']['horoscope']));
        }
    } else {
        echo "Sorry, you don't have curl installed. Please install it and try again!";
    }

    $response = new \Wave\Framework\Http\Response(1.1);
    $response->cache(true, 3600);
    $response->send();
}, array(
    'sign' => 'aquarius|pisces|aries|taurus|gemini|cancer|leo|virgo|libra|scorpio|sagittarius|capricorn'
));

$app->run(new \Wave\Framework\Http\Request($_SERVER));

<?php

require '../vendor/autoload.php';

$app = new \Wave\Framework\Application\Core('Wave Skeleton Application');
$ioc = new \Wave\Framework\Application\IoC();

/**
 * Register Twig
 */
$ioc->register('view-twig', function() {
    $loader = new Twig_Loader_Filesystem('../application/templates');
    return new Twig_Environment($loader, array(
        'cache' => '../application/templates/cache'
    ));
});


/**
 * Register Smarty
 */
$ioc->register('view-smarty', function() {
    $smarty = new Smarty();
    $smarty->setTemplateDir('../application/templates');
    $smarty->setCompileDir('../application/templates/cache');

    return $smarty;
});

/**
 * Register Plates
 */
$ioc->register('view-plates', function() {
    $plates = new \League\Plates\Engine('../application/templates');
    $plates->setFileExtension('phtml');
    return new \League\Plates\Template($plates);
});

$app->controller('/', 'GET', function () use ($ioc) {
    $view = $view = $ioc->get('view-twig');

    echo $view->render(
        'index.twig',
        explode('|', 'aquarius|pisces|aries|taurus|gemini|cancer|leo|virgo|libra|scorpio|sagittarius|capricorn')
    );
});

/**
 * This controller makes requests to the horoscope API and renders
 * the templates with the results.
 *
 * The switch in this controller is only for preview, in normal circumstances
 * you do not need more than one templating engine at a time.
 */
$app->controller('/:engine/sign/:sign', 'GET', function($arguments) use ($ioc) {
    if (extension_loaded('curl')) {
        $view = $ioc->get(sprintf('view-%s', $arguments->engine));


        $model = new \Model\HoroscopeAPIModel($arguments->sign);

        if (($data = $model->fetch()) !== null) {
            switch($arguments->engine) {
                case 'twig':
                    //$view->loadTemplate('horoscope.twig');
                    echo $view->render('horoscope.twig', $data['horoscope']);
                    break;
                case 'smarty':
                    $view->assign('sign', $data['horoscope']['sign']);
                    $view->assign('horoscope', $data['horoscope']['horoscope']);
                    $view->display('horoscope.tpl');
                    break;
                case 'plates':
                    $view->sign = $data['horoscope']['sign'];
                    $view->horoscope = $data['horoscope']['horoscope'];

                    echo $view->render('horoscope');
                    break;
            }
        }
    } else {
        echo "Sorry, you don't have curl installed. Please install it and try again!";
    }

    $response = new \Wave\Framework\Http\Response(1.1);
    $response->cache(true, 3600);
    $response->send();
}, array(
    'sign' => 'aquarius|pisces|aries|taurus|gemini|cancer|leo|virgo|libra|scorpio|sagittarius|capricorn',
    'engine' => 'twig|smarty|plates'
));

$app->run(new \Wave\Framework\Http\Request($_SERVER));

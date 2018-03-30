<?php
/**
 * @author Donii Sergii <s.doniy@infomir.com>.
 */
require_once __DIR__.'/../../vendor/autoload.php';

use Silex\Application;

$app = new Application();
$app->get('/', function () use ($app) {
    return $app->json(['status' => 'OK']);
});

$app->post('/', function (Application $app) {
    return $app->json(['status' => 'OK_POST']);
});

$app->boot();

return $app;

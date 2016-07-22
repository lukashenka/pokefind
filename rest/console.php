<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = \Rest\Silex\SilexApp::getApp();

$app['dispatcher']->addListener(\Knp\Console\ConsoleEvents::INIT, function(\Knp\Console\ConsoleEvent $event) {
	$app = $event->getApplication();
	$app->add(new \Rest\Command\ApplicationService());
});

$app->run();
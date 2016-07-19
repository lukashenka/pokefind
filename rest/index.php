<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = \Rest\Silex\SilexApp::getApp();
$app['debug'] = true;


$app->get('/hello/{name}', function ($name) use ($app) {
	return 'Hello '.$app->escape($name);
});

$app->finish(function (Request $request, Response $response) use ($app) {
//	$logger = $app["apiDBLogger"];
//	$logger->write(
//		$request->getUri(),
//		json_encode($request->headers->all()),
//		$request->getContent(),
//		json_encode($response->headers->all()),
//		$response->getContent(),
//		$response->getStatusCode()
//	);
});

$app->run();
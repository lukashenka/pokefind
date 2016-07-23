<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = \Rest\Silex\SilexApp::getApp();


$app->before(function (Request $request) use ($app) {

	$app['userProvider']->init($request);

});

$app->get('/hello', function () use ($app) {

	return new Response("Hi ;)", 200);
});

$app->get('/pokemon/list/{lat}/{lng}', function ($lat, $lng) use ($app) {

	$app['userProvider']->track($lat, $lng);

	$service = $app['pokemonLocation'];
	$pokemons = $service->getNear($lat, $lng);

	return new Response(\GuzzleHttp\json_encode($pokemons), 200);
});


$app->get('/user/loadProgress', function () use ($app) {

	$service = $app['userProgress'];
	$progress = $service->getProgress();
	return new Response(\GuzzleHttp\json_encode((array) $progress), 200);
});

$app->get('/user/getNear/{lat}/{lng}', function ($lat, $lng) use ($app) {

	$service = $app['userService'];
	$users = $service->getUserNear($lat, $lng);

	return new Response(\GuzzleHttp\json_encode((array) $users), 200);
});


$app->get('/pokemon/list/all', function () use ($app) {

	$service = $app['pokemonLocation'];
	$pokemons = $service->getAll();
	return new Response(\GuzzleHttp\json_encode($pokemons), 200);
});


$app->get('/users/updated', function () use ($app) {

	$service = $app['userService'];
	$users = $service->getLast();
	return new Response(\GuzzleHttp\json_encode($users), 200);
});

$app->get('/users/positionQueue', function () use ($app) {

	$service = $app['userProgress'];
	$position = $service->getQueuePosition();
	return new Response(\GuzzleHttp\json_encode($position), 200);
});

$app->get('clearExpired' ,function() use ($app) {
	$sevice = $app['clearService'];
	$sevice->expired();
	return "OK";
});
$app->finish(function (Request $request, Response $response) use ($app) {
	$app['logger']->addInfo("Request: " . $request);
	$app['logger']->addInfo("Response: " . $response);

});

$app->run();

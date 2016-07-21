<?php
// web/index.php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = \Rest\Silex\SilexApp::getApp();
$app['debug'] = true;


$app->get('/pokemon/list/{lat}/{lng}', function ($lat, $lng) use ($app) {

	$service = $app['pokemonLocation'];
	$pokemons = $service->getNear($lat, $lng, 1);
	return new Response(\GuzzleHttp\json_encode($pokemons), 200);
});

$app->get('/pokemon/list/all', function ($lat, $lng) use ($app) {

	$service = $app['pokemonLocation'];
	$pokemons = $service->getAll($lat, $lng);
	return new Response(\GuzzleHttp\json_encode($pokemons), 200);
});


$app->get('/users/updated', function () use ($app) {

	$service = $app['user'];
	$users = $service->getLast();
	return new Response(\GuzzleHttp\json_encode($users), 200);
});

$app->get('clearExpired' ,function() use ($app) {
	$sevice = $app['clearService'];
	$sevice->expired();
	return "OK";
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

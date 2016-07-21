<?php

namespace Rest\Silex;

use Monolog\Handler\StreamHandler;
use Rest\Model\UserResponse;
use Rest\Service\ClearService;
use Rest\Service\GeneratorService;
use Rest\Service\PokemonLocation;
use Rest\Service\UserLocation;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Rest\Service\APIDBLogger;
use Sorien\Provider\PimpleDumpProvider;
use Symfony\Component\Debug\ErrorHandler;
use Silex\Provider\DoctrineServiceProvider;

class SilexApp
{
	/**
	 * @var Application
	 */
	private static $app;

	/**
	 * @return Application
	 */
	public static function getApp()
	{
		if (!isset(self::$app) && !self::$app instanceof Application) {
			self::$app = self::initApplication();

			return self::$app;
		}

		return self::$app;
	}

	/**
	 * @return Application
	 */
	public static function initApplication()
	{
		$app = new Application();

		$app['debug'] = true;
		$app['project_root'] = realpath(__DIR__ . "/../../");
		$app['log_folder'] = $app['project_root'] . "/log/";

		$app->register(new PimpleDumpProvider());

		$app->register(new MonologServiceProvider(), array(
			'monolog.logfile' => $app['log_folder'] . '/silex/silex-' . date("Ymd") . '.log',
		));

		$app->register(new DoctrineServiceProvider(), array(
			'db.options' => array(
				'driver' => 'pdo_mysql',
				'host' => 'localhost',
				'dbname' => 'pokemon',
				'user' => 'pok',
				'password' => 'pikapikachu',
				'charset' => 'utf8',
			),
		));

		$app['monolog.silex_api'] = function ($app) {
			$date = date("Ymd");
			$log = new $app['monolog.logger.class']('silex_api');
			$handler = new StreamHandler($app['log_folder'] . "/rest-{$date}.log", \Monolog\Logger::DEBUG);
			$log->pushHandler($handler);
			return $log;
		};


		$app['apiDBLogger'] = function () {
			return new APIDBLogger();
		};
		$app['pokemonLocation'] = function () {
			return new PokemonLocation();
		};
		$app['clearService'] = function () {
			return new ClearService();
		};
		$app['generator'] = function () {
			return new GeneratorService();
		};
		$app['user'] = function () {
			return new UserLocation();
		};
		ErrorHandler::register();
		return $app;
	}
} 

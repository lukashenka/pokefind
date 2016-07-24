<?php

namespace Rest\Silex;

use Knp\Provider\ConsoleServiceProvider;
use Monolog\Handler\StreamHandler;
use Rest\Model\UserResponse;
use Rest\Service\ClearService;
use Rest\Service\GeneratorService;
use Rest\Service\PokemonLocation;
use Rest\Service\UserLocation;
use Rest\Service\UserProgress;
use Rest\Service\UserProvider;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Rest\Service\APIDBLogger;
use Silex\Provider\SecurityServiceProvider;
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

		$app['debug'] = false;

		$app['project_root'] = realpath(__DIR__ . "/../../");
		$app["app.config"] = \GuzzleHttp\json_decode(file_get_contents($app['project_root']."/config/config.json"), true);


		$app['log_folder'] = $app['project_root'] . "/log/";

		$app->register(new PimpleDumpProvider());


		$app->register(new MonologServiceProvider(), array(
			'monolog.logfile' => $app['log_folder'] . '/silex/silex-' . date("Ymd") . '.log',
		));

		$app->register(new DoctrineServiceProvider(), array(
			'db.options' => array(
				'driver' => 'pdo_mysql',
				'host' => $app['app.config']['db']['host'],
				'dbname' => $app['app.config']['db']['dbname'],
				'user' => $app['app.config']['db']['dbuser'],
				'password' =>  $app['app.config']['db']['dbpass'],
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

		$app->register(new ConsoleServiceProvider(), array(
			'console.name'              => 'MyApplication',
			'console.version'           => '1.0.0',
			'console.project_directory' => __DIR__.'/..'
		));



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
		$app['userService'] = function () {
			return new UserLocation();
		};
		$app['userProvider'] = function () use ($app) {
			return new UserProvider($app);
		};
		$app['userProgress'] = function () use ($app) {
			return new UserProgress();
		};
		ErrorHandler::register();
		return $app;
	}
} 

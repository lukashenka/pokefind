<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/20/16
 * Time: 3:23 AM
 */

namespace Rest\Service;


use Rest\Silex\SilexApp;

class GeneratorService
{

	const MIN_POKEMONS_FOR_NEW_GENERATE = 7;

	public function addGeneratorTask($lat, $lng)
	{
		$sql = "INSERT INTO location_for_update(lat,lng,created) VALUES ({$lat}, {$lng}, NOW())";
		$app = SilexApp::getApp();
		$db = $app['db'];
		$db->exec($sql);
	}
}
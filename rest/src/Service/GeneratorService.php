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
	const MIN_POKEMONS_FOR_NEW_GENERATE = 30;

	public function addGeneratorTask($lat, $lng)
	{
		$app = SilexApp::getApp();
		$db = $app['db'];
		$user = $app['userProvider']->getUserSession();

		$nearJobs = "SELECT (6371 * acos(cos(radians(:lat)) * cos(radians(lat)) * cos(radians(lng) - radians(:lng) ) + sin( radians(:lat)) * sin(radians(lat)))) AS distance
                             FROM location_for_update 
                            WHERE created >= NOW() - INTERVAL :expired_sec SECOND
				 HAVING distance < :distance";
		$nearJobs = $db->fetchAll($nearJobs, [
			'lat' => $lat,
			'lng' => $lng,
			'distance' => $app['app.config']['generator']['min_distance_for_prevent_new_generate'],
			'expired_sec' => $app['app.config']['generator']['expired_sec_for_prevent_generate']
		]);
		if (count($nearJobs) == 0) {

			$sql = "
				INSERT INTO location_for_update(user_session_id, lat,lng,created)
				VALUES (:user, :lat, :lng, NOW())
			";

			$stmt = $db->prepare($sql);
			$stmt->execute([
				'user' => $app['userProvider']->getUserSession()->id,
				'lat' => $lat,
				'lng' => $lng
			]);
		}
	}
}

<?php

namespace Rest\Service;


use Rest\Model\UserResponse;
use Rest\Silex\SilexApp;

class UserLocation
{

	public function getLast()
	{
		$usersSql = "SELECT lat,lng, created
                             FROM location_for_update
                            WHERE created >= NOW() - INTERVAL 1 HOUR
		";

		$app = SilexApp::getApp();

		$db = $app['db'];
		$users = $db->fetchAll($usersSql);
		$usersResponse = [];

		foreach($users as $user) {
			$userResponse = new UserResponse();
			$userResponse->lng = (float) $user["lng"];
			$userResponse->lat = (float) $user["lat"];
			$userResponse->created = $user["created"];
			array_push($usersResponse, $userResponse);
		}
		return $usersResponse;
	}

	public function getUserNear($lat, $lng) {
		$app = SilexApp::getApp();

		$db = $app['db'];
		$distance = $app['app.config']['user']['min_distance_for_show_near_user'];
		$minute = $app['app.config']['user']['min_minutes_for_show_near_user'];

		$sql = "
		SELECT t.updated, t.lat, t.lng, u.guid,
		(6371 * acos(cos(radians(:lat)) * cos(radians(lat)) * cos(radians(lng) - radians(:lng)) + sin( radians(:lat)) * sin(radians(lat)))) AS distance
		FROM user_session_track AS t
		LEFT JOIN user_sessions as u ON u.id = t.user_session_id

		WHERE t.updated > NOW() - INTERVAL :minute MINUTE

		GROUP BY t.user_session_id
		HAVING distance < :kilometers
		ORDER BY distance
		";

		$users = $db->fetchAll($sql, ['lat' => $lat,
											'lng' => $lng,
											'kilometers' => $distance,
											'minute' => $minute
		]);


		$userList = [];
		foreach ($users as $user) {
			$userResponse = new UserResponse();
			$userResponse->guid = $user["guid"];
			$userResponse->lng = (float)$user["lng"];
			$userResponse->lat = (float)$user["lat"];
			$userResponse->updated = $user["updated"];
			array_push($userList, $userResponse);
		}
		return $userList;


	}

}
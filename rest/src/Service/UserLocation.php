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

}
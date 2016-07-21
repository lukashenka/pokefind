<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/22/16
 * Time: 12:26 AM
 */

namespace Rest\Service;


use Rest\Model\UserLoadProgressResponse;
use Rest\Silex\SilexApp;

class UserProgress
{
	public function getProgress()
	{
		$app = SilexApp::getApp();
		$user = $app['userProvider']->getUserSession();
		$db = $app['db'];

		$sql = "SELECT gl.current_step, gl.steps, gl.done, gl.fail
				FROM location_for_update AS lf
				LEFT JOIN generation_log AS gl ON lf.id = gl.update_location_id
				WHERE lf.user_session_id = :userId AND gl.id IS NOT NULL
				ORDER BY lf.created DESC
				LIMIT 1
				";

		$progress = $db->fetchAssoc($sql, ['userId' => $user->id]);
		$progressResponse = new UserLoadProgressResponse();
		if($progress) {
			$progressResponse->userGUID = $user->userGUID;
			$progressResponse->curStep = $progress["steps"];
			$progressResponse->steps = $progress["current_step"];
			$progressResponse->done = $progress["done"];
			$progressResponse->fail = $progress["fail"];
			$progressResponse->isLoading = true;
		}

		return $progressResponse;


	}
}
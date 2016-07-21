<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/22/16
 * Time: 12:26 AM
 */

namespace Rest\Service;


use Rest\Model\UserLoadProgressResponse;
use Rest\Model\UserPositionQueueResponse;
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
		if ($progress) {
			$progressResponse->userGUID = $user->userGUID;
			$progressResponse->curStep = $progress["steps"];
			$progressResponse->steps = $progress["current_step"];
			$progressResponse->done = $progress["done"];
			$progressResponse->fail = $progress["fail"];
			$progressResponse->isLoading = true;
		}

		return $progressResponse;


	}

	public function getQueuePosition()
	{
		$app = SilexApp::getApp();
		$user = $app['userProvider']->getUserSession();
		$db = $app['db'];

		$positionQueue = new UserPositionQueueResponse();


		$sql = "SELECT lf.id
				FROM location_for_update AS lf
				WHERE blocked = 0 AND user_session_id =  :user_id
				ORDER BY lf.created ASC
				LIMIT 1
		";
		$processId = $db->fetchColumn($sql, ["user_id" => $user->id]);

		if (!$processId) {
			return $positionQueue;
		}

		$positionQueue->isLoading = true;

		$totalSql = "
				SELECT COUNT(*)
				FROM location_for_update AS lf
				WHERE blocked = 0
				ORDER BY lf.created ASC

		";
		$total = (int)$db->fetchColumn($totalSql);
		$positionQueue->total = $total;


		$positionSql = "
				SELECT COUNT(*)
				FROM location_for_update AS lf
				WHERE blocked = 0 AND id <= :id
				ORDER BY lf.created ASC

		";
		$position = (int)$db->fetchColumn($positionSql, ['id' => $processId]);
		$positionQueue->position = $position;

		return $positionQueue;

	}
}
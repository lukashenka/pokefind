<?php

namespace Rest\Service;

use Rest\Model\UserSession;
use Rest\Silex\SilexApp;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserProvider
{
	/**
	 * @var UserSession
	 */
	private $userSession;

	public function init(Request $request)
	{

		$user = new UserSession();
		$userGuid = $request->headers->get('Userguid');
		if (!$userGuid) {
			throw new AccessDeniedException('Go away!');
		}
		$ip = $request->getClientIp();
		$user->userGUID = $userGuid;
		$user->ip = $ip;

		$this->userSession = $user;
		$this->setUserSession();



	}

	private function setUserSession()
	{
		$app = SilexApp::getApp();
		$db = $app['db'];
		$sql = "SELECT id FROM user_sessions WHERE guid = :guid";

		$user = $db->fetchColumn($sql, [
			'guid' => $this->userSession->userGUID
		]);
		if ($user) {
			$this->userSession->id = (int)$user;
			$sql = "UPDATE user_sessions SET updated = NOW() WHERE id = :id";
			$db->executeQuery($sql, [
					'id' => $this->userSession->id
				]
			);
		} else {

			$sql = "INSERT INTO user_sessions(guid, ip, ip_string, created, updated)
				VALUES (:guid, INET_ATON(:ipString), :ipString, NOW(), NOW())";


			$db->executeQuery($sql, [
					'guid' => $this->userSession->userGUID,
					"ipString" => $this->userSession->ip
				]
			);
			$this->userSession->id = $db->lastInsertId();
		}
	}

	public function track($lat, $lng)
	{

		$app = SilexApp::getApp();
		$db = $app['db'];
		$sql = "INSERT INTO user_session_track(user_session_id, lat, lng, updated)
				VALUES (:userSessionId, :lat, :lng, NOW())";
		$db->executeQuery($sql, [
				'userSessionId' => $this->userSession->id,
				"lat" => $lat,
				"lng" => $lng
			]
		);
	}

	public function getUserSession()
	{
		return $this->userSession;
	}

}
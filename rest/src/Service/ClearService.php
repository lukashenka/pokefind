<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/20/16
 * Time: 1:42 AM
 */

namespace Rest\Service;


use Rest\Silex\SilexApp;

class ClearService
{

	public function expired() {
		$sql = "DELETE FROM pokemon_location WHERE expired <= NOW() - INTERVAL 20 MINUTE";
		$app = SilexApp::getApp();
		$db = $app['db'];
		$db->exec($sql);
	}

}
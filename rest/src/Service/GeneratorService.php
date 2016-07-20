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

	public function generatePoks($lat, $lng) {
		system(SilexApp::getApp()['project_root']. "/../generator/pokegen.py -a google -u pokemongosukablia@gmail.com -p slowpoke -l \"{$lat}, {$lng}\" -st 10 ");
	}
}
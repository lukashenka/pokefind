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
                $nearJobs = "SELECT (6371 * acos( cos( radians({$lat}) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians({$lng}) ) + sin( radians({$lat}) ) * sin( radians( lat ) ) ) ) AS distance 
                             FROM location_for_update 
                            WHERE blocked = 0
				 HAVING distance < 0.2"
			    
;
                
                $sql = "INSERT IGNORE INTO location_for_update(lat,lng,created) VALUES ({$lat}, {$lng}, NOW())";
                $app = SilexApp::getApp();
                
        
                $db = $app['db'];
                $nearJobs = $db->fetchAll($nearJobs);
                if(count($nearJobs) == 0) {
                        $db->exec($sql);
                }
        }
}

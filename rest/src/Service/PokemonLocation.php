<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/19/16
 * Time: 5:56 PM
 */

namespace Rest\Service;


use Rest\Model\PokemonResponse;
use Rest\Silex\SilexApp;

class PokemonLocation
{
	/**
	 * @param $lat
	 * @param $lng
	 * @param $kilometers
	 * @return PokemonResponse[]
	 */
	public function getNear($lat, $lng)
	{
		$app = SilexApp::getApp();

//		$app['clearService']->expired();


		$genSql = "
		SELECT COUNT(*),
		(6371 * acos(cos(radians(:lat)) * cos(radians(lat)) * cos(radians(lng) - radians(:lng)) + sin( radians(:lat)) * sin(radians(lat)))) AS distance
		FROM pokemon_location AS pl
		LEFT JOIN pokemon AS p ON pl.pokemon_id = p.id
		WHERE pl.expired >= NOW()
		GROUP BY pokemon_id, lat, lng
		HAVING distance < :kilometers
		ORDER BY distance
		";

		$count = (int)$app['db']->fetchColumn($genSql, ['lat' => $lat,
														'lng' => $lng,
														'expired_delta' => $app['app.config']['pokemon_finder']['expired_delta'],
														'kilometers' => $app['app.config']['generator']['min_distance_for_prevent_new_generate']
		]);

		if ($count <= (int) $app['app.config']['generator']['min_poks_for_new_generate']) {
			$app['generator']->addGeneratorTask($lat, $lng);
		}


		$sql = "
		SELECT pl.expired, pl.lat, pl.lng, p.name, p.pokeuid,
		(6371 * acos(cos(radians(:lat)) * cos(radians(lat)) * cos(radians(lng) - radians(:lng)) + sin( radians(:lat)) * sin(radians(lat)))) AS distance
		FROM pokemon_location AS pl
		LEFT JOIN pokemon AS p ON pl.pokemon_id = p.id
		WHERE pl.expired >= NOW()
		GROUP BY pokemon_id, lat, lng
		HAVING distance < :kilometers
		ORDER BY distance
		";

		$poks = $app['db']->fetchAll($sql, ['lat' => $lat,
											'lng' => $lng,
											'expired_delta' => $app['app.config']['pokemon_finder']['expired_delta'],
											'kilometers' => $app['app.config']['pokemon_finder']['get_near_location_range']
		]);


		$pokList = [];
		foreach ($poks as $pok) {
			$pokeResponse = new PokemonResponse();
			$pokeResponse->lng = (float)$pok["lng"];
			$pokeResponse->lat = (float)$pok["lat"];
			$date = new \DateTime($pok["expired"]);
			$pokeResponse->expired = $date->format('c');
			$pokeResponse->name = $pok["name"];
			$pokeResponse->pokeuid = (int)$pok["pokeuid"];
			$pokeResponse->distance = (float)$pok["distance"];
			array_push($pokList, $pokeResponse);
		}
		return $pokList;
	}

	public function getAll()
	{
		$app = SilexApp::getApp();
		$sql = "
		SELECT pl.expired, pl.lat, pl.lng, p.name, p.pokeuid
		FROM pokemon_location AS pl
		LEFT JOIN pokemon AS p ON pl.pokemon_id = p.id
		WHERE pl.expired >= NOW() - INTERVAL :expired_delta MINUTE
		GROUP BY pokemon_id, lat, lng
		";

		$poks = $app['db']->fetchAll($sql, [
			'expired_delta' => $app['app.config']['pokemon_finder']['expired_delta']
		]);
		$pokList = [];
		foreach ($poks as $pok) {
			$pokeResponse = new PokemonResponse();
			$pokeResponse->lng = (float)$pok["lng"];
			$pokeResponse->lat = (float)$pok["lat"];
			$date = new \DateTime($pok["expired"]);
			$pokeResponse->expired = $date->format('c');
			$pokeResponse->name = $pok["name"];
			$pokeResponse->pokeuid = (int)$pok["pokeuid"];
			$pokeResponse->distance = 9999;
			array_push($pokList, $pokeResponse);
		}
		return $pokList;
	}
}
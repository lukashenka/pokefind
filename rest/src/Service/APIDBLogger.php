<?php

namespace Rest\Service;

use Rest\Silex\SilexApp;

class APIDBLogger
{
	protected $tableName = "sm_api_log";

	public function write($url, $requestHeader, $requestBody, $responseHeader, $responseBody, $code)
	{
		$app = SilexApp::getApp();
		$pdo = $app['pdo'];
		$sql = "
			INSERT INTO sm_api_log(url, request_header, request_body, response_header, response_body, code, created)
			VALUES (:url, :request_header, :request_body, :response_header, :response_body, :code, NOW())
		";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			"url" => $url,
			"request_header" => $requestHeader,
			"request_body" => $requestBody,
			"response_header" => $responseHeader,
			"response_body" => $responseBody,
			"code" => $code,
		]);
	}

}
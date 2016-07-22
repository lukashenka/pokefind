<?php
/**
 * Created by PhpStorm.
 * User: karachun
 * Date: 7/22/16
 * Time: 12:25 AM
 */

namespace Rest\Model;


class UserLoadProgressResponse
{

	public $userGUID;
	public $isLoading = false;
	public $steps = 0;
	public $curStep = 0;
	public $done = 0;
	public $fail = 0;
	public $isStarted = false;

}
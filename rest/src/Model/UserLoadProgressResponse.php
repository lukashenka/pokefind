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
	public $steps;
	public $curStep;
	public $done;
	public $fail;

}
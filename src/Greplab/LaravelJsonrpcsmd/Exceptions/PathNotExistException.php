<?php namespace Greplab\LaravelJsonrpcsmd\Exceptions;

class PathNotExistException extends \Exception
{
	public function __construct($path)
	{
		parent::__construct('The path '.$path.' not exist.');
	}
}
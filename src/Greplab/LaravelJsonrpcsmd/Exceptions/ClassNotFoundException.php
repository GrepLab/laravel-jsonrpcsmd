<?php namespace Greplab\LaravelJsonrpcsmd\Exceptions;

class ClassNotFoundException extends \Exception
{
	public function __construct($classname, $filename)
	{
		parent::__construct('The class '.$classname.' was not found in the file '.$filename.'.');
	}
}
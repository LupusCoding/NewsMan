<?php

namespace LC\ILP\NewsMan\Closure;

/**
 * Class Factory
 * @package QUALITUS\ER\Closure
 * @author Ralph Dittrich <dittrich@qualitus.de>
 */
class Factory
{
	/**
	 * @param $object
	 * @return \Closure
	 */
	public function propGetter($object)
	{
		return \Closure::bind(function ($prop) { return $this->$prop; }, $object, $object);
	}

	/**
	 * @param $array
	 * @return \Closure
	 */
	public function safeArrayGetter($array)
	{
		return \Closure::bind(function ($key) { return isset($this->$key) ? $this->$key : ''; }, (object)$array);
	}
}
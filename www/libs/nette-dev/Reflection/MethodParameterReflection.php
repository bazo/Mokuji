<?php

/**
 * Nette Framework
 *
 * @copyright  Copyright (c) 2004, 2010 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com
 * @category   Nette
 * @package    Nette\Reflection
 */



/**
 * Reports information about a method's parameter.
 *
 * @copyright  Copyright (c) 2004, 2010 David Grudl
 * @package    Nette\Reflection
 */
class MethodParameterReflection extends ReflectionParameter
{

	/**
	 * @return MethodParameterReflection
	 * @ignore internal
	 */
	public static function import(ReflectionParameter $ref)
	{
		$method = $ref->getDeclaringFunction();
		return new self($method instanceof ReflectionMethod ? array($ref->getDeclaringClass()->getName(), $method->getName()) : $method->getName(), $ref->getName());
	}



	/**
	 * @return ClassReflection
	 */
	public function getClass()
	{
		return ($ref = parent::getClass()) ? ClassReflection::import($ref) : NULL;
	}



	/**
	 * @return ClassReflection
	 */
	public function getDeclaringClass()
	{
		return ($ref = parent::getDeclaringClass()) ? ClassReflection::import($ref) : NULL;
	}



	/**
	 * @return MethodReflection | Nette\Reflection\FunctionReflection
	 */
	public function getDeclaringFunction()
	{
		return ($ref = parent::getDeclaringFunction()) instanceof ReflectionMethod
			? MethodReflection::import($ref)
			: FunctionReflection::import($ref);
	}



	/********************* Nette\Object behaviour ****************d*g**/



	/**
	 * @return ClassReflection
	 */
	public function getReflection()
	{
		return new ClassReflection($this);
	}



	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}



	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}



	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}



	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}



	public function __unset($name)
	{
		throw new MemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");
	}

}

<?php

namespace mageekguy\atoum\test\adapter;

use
	\mageekguy\atoum\exceptions
;

class caller implements \arrayAccess
{
	protected $currentCall = null;
	protected $closuresByCall = array();

	public function __set($keyword, $mixed)
	{
		switch ($keyword)
		{
			case 'return':
				if ($mixed instanceof \closure === false)
				{
					$mixed = function() use ($mixed) { return $mixed; };
				}

				$this->setClosure($mixed);
				break;

			case 'throw':
				if ($mixed instanceof \closure === false)
				{
					$mixed = function() use ($mixed) { throw $mixed; };
				}

				$this->setClosure($mixed);
				break;

			default:
				throw new exceptions\logic\invalidArgument('Keyword \'' . $keyword . '\' is unknown');
		}
	}

	public function isEmpty()
	{
		return (sizeof($this->closuresByCall) <= 0);
	}

	public function getCurrentCall()
	{
		return $this->currentCall;
	}

	public function setClosure(\closure $closure, $call = 0)
	{
		if ($this->currentCall !== null)
		{
			$call = $this->currentCall;
			$this->currentCall = null;
		}

		static::checkCall($call);

		$this->closuresByCall[$call] = $closure;

		return $this;
	}

	public function getClosure($call = 0)
	{
		static::checkCall($call);

		return (isset($this->closuresByCall[$call]) === false ? null : $this->closuresByCall[$call]);
	}

	public function closureIsSet($call = 0)
	{
		static::checkCall($call);

		return (isset($this->closuresByCall[$call]) === true);
	}

	public function unsetClosure($call = 0)
	{
		if ($this->closureIsSet($call) === false)
		{
			throw new exceptions\logic\invalidArgument('There is no closure defined for call ' . $call);
		}

		unset($this->closuresByCall[$call]);

		return $this;
	}

	public function offsetSet($call, $mixed)
	{
		if ($mixed instanceof \closure === false)
		{
			$mixed = function() use ($mixed) { return $mixed; };
		}

		return $this->setClosure($mixed, $call);
	}

	public function offsetGet($call)
	{
		return $this->getClosure($call);
	}

	public function offsetUnset($call)
	{
		return $this->unsetClosure($call);
	}

	public function offsetExists($call)
	{
		return $this->closureIsSet($call);
	}

	public function invoke(array $arguments = array(), $call = 0)
	{
		if ($this->closureIsSet($call) === false)
		{
			$call = 0;
		}

		if ($this->closureIsSet($call) === false)
		{
			throw new exceptions\logic\invalidArgument('There is no closure defined for call ' . $call);
		}

		return call_user_func_array($this->closuresByCall[$call], $arguments);
	}

	public function atCall($call)
	{
		$this->currentCall = (int) $call;

		return $this;
	}

	protected static function checkCall($call)
	{
		if ($call < 0)
		{
			throw new exceptions\logic\invalidArgument('Call number must be greater than or equal to zero');
		}
	}
}

?>

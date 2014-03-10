<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\variable,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

class constant extends atoum\asserter
{
	protected $diff = null;
	protected $isSet = false;
	protected $value = null;

	public function __construct(asserter\generator $generator = null, variable\analyzer $analyzer = null)
	{
		parent::__construct($generator, $analyzer);

		$this->setDiff();
	}

	public function __toString()
	{
		return $this->getTypeOf($this->value);
	}

	public function __call($method, $arguments)
	{
		switch (strtolower($method))
		{
			case 'equalto':
				return call_user_func_array(array($this, 'isEqualTo'), $arguments);

			default:
				return parent::__call($method, $arguments);
		}
	}

	public function setDiff(diffs\variable $diff = null)
	{
		$this->diff = $diff ?: new diffs\variable();

		return $this;
	}

	public function getDiff()
	{
		return $this->diff;
	}

	public function wasSet()
	{
		return ($this->isSet === true);
	}

	public function setWith($value)
	{
		parent::setWith($value);

		$this->value = $value;
		$this->isSet = true;

		return $this;
	}

	public function reset()
	{
		$this->value = null;
		$this->isSet = false;

		return parent::reset();
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isEqualTo($value, $failMessage = null)
	{
		if ($this->valueIsSet()->value === $value)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not equal to %s', $this, $this->getTypeOf($value)) .  PHP_EOL .  $this->diff->setExpected($this->value)->setActual($value));
		}

		return $this;
	}

	protected function valueIsSet($message = 'Value is undefined')
	{
		if ($this->isSet === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}
}

<?php

namespace mageekguy\atoum\test\annotations;

use mageekguy\atoum;

class extractor extends atoum\annotations\extractor
{
	public function extract($comments)
	{
		$this->reset();

		$annotations = parent::extract($comments)->getAnnotations();

		$this->reset();

		foreach ($annotations as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$this->annotations[$annotation] = strcasecmp($value, 'on') == 0;
					break;

				case 'tags':
					$this->annotations[$annotation] = array_values(array_unique(preg_split('/\s+/', $value)));
					break;
			}
		}

		return $this;
	}

	public function setTest(atoum\test $test, $comments)
	{
		foreach ($this->extract($comments) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$test->ignore($value);
					break;

				case 'tags':
					$test->setClassTags($value);
					break;
			}
		}

		return $this;
	}

	public function setTestMethod(atoum\test $test, $method, $comments)
	{
		return $this;
	}
}

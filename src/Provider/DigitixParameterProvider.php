<?php

namespace Digitix\FrameworkBundle\Provider;

final class DigitixParameterProvider
{
	private $params;
    private $keyName;
    private $valueName;

	public function __construct(array $params, string $keyName, string $valueName = null)
	{
		$this->params = $params;
        $this->keyName = $keyName;
        $this->valueName = $valueName;
	}

    /**
     * Get the value of keyName
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * Set the value of keyName
     *
     * @return  self
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;

        return $this;
    }

    /**
     * Get the value of valueName
     */
    public function getValueName()
    {
        return $this->valueName;
    }

    /**
     * Set the value of valueName
     *
     * @return  self
     */
    public function setValueName($valueName)
    {
        $this->valueName = $valueName;

        return $this;
    }

	/**
	 * Get the value of paramKey
	 */
	public function getParamKey()
	{
		return $this->params[$this->getKeyName()];
	}


    /**
     * Get the value of paramsKeyValue
     */
    public function getParameters()
    {
        if (!isset($this->params[$this->getKeyName()][$this->getValueName()])) {
            $this->params[$this->getKeyName()];
        }

        return $this->params[$this->getKeyName()][$this->getValueName()];
    }
}

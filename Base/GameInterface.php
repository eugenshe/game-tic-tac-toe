<?php

namespace base;

/**
 * Interface GameInterface
 * @package base
 */
interface GameInterface
{
	/**
	 * Constructor 
	 * @param array $config
	 */
	public function __construct(array $config);

	/**
	 * Returns JSON object
	 */
	public function __toString();
} 

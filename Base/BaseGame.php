<?php

namespace base;

use Exception;
use minimax\MinMax;

/**
 * Class BaseGame
 * @package base
 */
abstract class BaseGame implements GameInterface
{
	const PLAYER       = 1;
	const COMPUTER     = 2;
	const DRAW         = 400;
	const IN_PROGRESS  = 401;
	const ALLOWED_MOVE = 2;

	/**
	 * @var array
	 */
	protected $gameBoard = [];

	/**
	 * @var array
	 */
	protected $errorMessage = [];

	/**
	 * @var array
	 */
	protected $move = [];

	/**
	 * BaseGame constructor.
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		if (isset($config['gameBoard'])) {
			$this->setGameBoard($config['gameBoard']);
		} else {
			$this->clearBoard();
		}

		$this->currentPlayer = $config['player'];
		$this->start($config['move']);
	}

	/**
	 * Make move
	 * @return array|bool
	 */
	protected function computerTurn()
	{
		$getMinMaxResult = new MinMax($this);
		if ($getMinMaxResult->move) {
			return $this->move = $getMinMaxResult->move;
		}

		return false;
	}

	/**
	 * Choose players
	 */
	public function setPlayer()
	{
		$this->currentPlayer = (self::PLAYER == $this->currentPlayer) ? self::COMPUTER : self::PLAYER;
	}

	/**
	 * @param array $gameBoard
	 * @return array|bool
	 */
	protected static function isBoardFull(array $gameBoard)
	{
		foreach ($gameBoard as $raw => $columns) {
			foreach ($columns as $position => $player) {
				if (false !== $player) {
					continue;
				}
				$blankPositions[] = [$raw, $position];
			}
		}

		return (!empty($blankPositions)) ? $blankPositions : true;
	}


	/**
	 *    Set player move
	 * @param array   $move
	 * @param boolean $isEmpty
	 * @returns array
	 */
	public function setPosition(array $move, $isEmpty = false)
	{
		if (false === self::isAlreadyTaken($move, $this->gameBoard) || $isEmpty) {
			return $this->gameBoard[reset($move)][next($move)] = $isEmpty ? false : $this->currentPlayer;
		}
	}

	/**
	 * @returns void
	 */
	protected function clearBoard()
	{
		$board = [];
		for ($i = 0; $i < static::BOARD_ROWS; $i++) {
			$board[$i] = [];
			for ($j = 0; $j < static::BOARD_COLS; $j++) {
				array_push($board[$i], false);
			}
		}
		$this->gameBoard = $board;
	}

	/**
	 *    Set game board by request
	 * @param array $gameBoard
	 * @returns void
	 */
	protected function setGameBoard($gameBoard)
	{
		foreach ($gameBoard as $pos1 => $columns) {
			foreach ($columns as $pos2 => $player) {
				$this->gameBoard[$pos1][$pos2] = (!$player) ? false : $player;
			}
		}
	}

	/**
	 * Get current game board
	 * @returns array
	 */
	public function getGameBoard()
	{
		return $this->gameBoard;
	}

	/**
	 * @param array $move
	 * @return bool
	 */
	protected static function isAllowedMove(array $move)
	{
		if (!empty($move) && self::ALLOWED_MOVE == count($move)) {
			return true;
		}

		return false;
	}

	/**
	 * @param array $move
	 * @param array $gameBoard
	 * @return array
	 * @throws Exception
	 */
	private static function isAlreadyTaken(array $move, array $gameBoard)
	{
		if (self::isAllowedMove($move)) {
			return $gameBoard[$move[0]][$move[1]];
		}

		throw new \Exception('Not allowed move taken');
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$result = [];
		if (!empty($this->errorMessage)){
			$result['errorMessage'] = $this->errorMessage;
		}
		if (!empty($this->move)){
			$result['move'] = $this->move;
		}
		return json_encode($result);
	}

	/**
	 * @param array $move
	 * @return void
	 */
	abstract protected function start(array $move = []);

	/**
	 * @return integer
	 */
	abstract public function isGameOver();
}
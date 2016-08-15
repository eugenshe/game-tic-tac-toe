<?php

namespace minimax;

use base\BaseGame;
use base\Game;

/**
 * Class MinMax
 * @package minimax
 */
class MinMax
{
	const DEFAULT_MIN       = -10000;
	const DEFAULT_MAX 	    = 10000;
	const MAX_FUNCTION_NAME = 'maxMove';

	/**
	 *	@var Game $game
	 */
	protected $game;

	/**
	 *	@var array|boolean $move
	*/
	public $move;

	/**
	 * MinMax constructor.
	 * @param BaseGame $game
	 */
	public function __construct(BaseGame $game){
		$this->game = $game;
		$this->move  = $this->minMaxProcessing();
	}
	
	/**
	 * MinMax executor
	 * @return boolean|integer
	 */
	private function minMaxProcessing()
	{
		$gameBoard = $this->game->getGameBoard();
		$choose = self::DEFAULT_MIN;

		foreach($gameBoard as $raw => $columns) {
			foreach($columns as $position => $player){
				if (false !== $player){
					continue; 
				}
				$gameClone = clone $this->game;
				$gameClone->setPosition([$raw, $position]);
				$gameClone->setPlayer();
				$value = $this->minMove($gameClone, 1, $choose, self::DEFAULT_MAX);
				$gameClone->setPosition([$raw, $position], true);
				if ($value > $choose){
					$choose = $value;
					$move = [$raw, $position];
				} 
				$gameClone->setPlayer();
			}
		}
		
		if (!empty($move)){ 
			$this->game->setPosition($move);
			return $move;
		}

		return false;
	}


	/**
	 * @param Game    $gameClone
	 * @param integer $depth
	 * @param integer $alpha
	 * @param integer $beta
	 * @return int
	 */
	private function maxMove(Game $gameClone, $depth, $alpha, $beta)
	{
		$maxFunctionName = 'minMove';
		$value = $this->minMaxProcess($gameClone, $depth, $alpha, $beta, $maxFunctionName);

		return $value;
	}

	/**
	 * @param Game    $gameClone
	 * @param integer $depth
	 * @param integer $alpha
	 * @param integer $beta
	 * @return int
	 */
	private function minMove(Game $gameClone, $depth, $alpha, $beta)
	{
		$maxFunctionName = 'maxMove';
		$value = $this->minMaxProcess($gameClone, $depth, $alpha, $beta, $maxFunctionName);

		return $value;
	}

	/**
	 * @param Game    $gameClone
	 * @param integer $depth
	 * @param integer $alpha
	 * @param integer $beta
	 * @param string  $minMaxFunctionName
	 * @return integer
	 */
	private function minMaxProcess(Game $gameClone, $depth, $alpha, $beta, $minMaxFunctionName)
	{
		$result = $this->score($gameClone,$depth);
		if(Game::IN_PROGRESS != $result){
			return $result;
		}
		$gameBoard = $gameClone->getGameBoard();
		foreach($gameBoard as $raw => $columns){
			foreach($columns as $position => $player){
				if(false !== $player){
					continue ;
				}
				$gameClone->setPosition([$raw, $position]);
				$gameClone->setPlayer();
				$value = $this->$minMaxFunctionName($gameClone, ++$depth, $alpha, $beta);
				$gameClone->setPosition([$raw,$position], true);
				$gameClone->setPlayer();
				if ($minMaxFunctionName == self::MAX_FUNCTION_NAME) {
					if ($value < $beta){
						$beta = $value;
					}
					if ($beta < $alpha){
						return $alpha;
					}
				} else {
					if ($value > $alpha){
						$alpha = $value;
					}
					if ($alpha > $beta){
						return $beta;
					}
				}
			}
		}

		return $value;
	}

	/**
	 *	Get current score
	 *	@param BaseGame $gameClone
	 *	@param integer	$depth
	 *	@returns integer
	 */
	protected function score(BaseGame $gameClone, $depth){
		$result = $gameClone->isGameOver();
		switch($result){
			case Game::COMPUTER:
				return 100 - $depth;
				break;
			case Game::PLAYER:
				return $depth - 100;
				break;
			case Game::DRAW:
				return 0;
				break;
		}
		return Game::IN_PROGRESS;
	}
} 
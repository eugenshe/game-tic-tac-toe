<?php

namespace base;

/**
 * Class Game
 * @package main
 */
class Game extends BaseGame
{
	const BOARD_ROWS = 3; 
	const BOARD_COLS = 3; 
	
	/**
	 * Start the game
	 * @param array $move
	 * @return void
	 */
	protected function start(array $move = [])
	{
		$gameStatus = $this->isGameOver();
		if (Game::IN_PROGRESS != $gameStatus){
			$this->errorMessage[] = $gameStatus;
			return false;
		}
		
		if (Game::PLAYER == $this->currentPlayer){
			$this->setPosition($move); 
			$this->setPlayer();
			$this->computerTurn();
		} else {
			$this->computerTurn();
		}

		$gameStatus = $this->isGameOver();
		if (Game::IN_PROGRESS != $gameStatus) {
			$this->errorMessage[] = $gameStatus;

			return false;
		}
	}

	/**
	 * Check game for results
	 * @return integer
	 */
	public function isGameOver()
	{
		$gameBoardCount = count($this->gameBoard);

		for($i = 0; $i < $gameBoardCount; $i++) {
			if (false !== $this->gameBoard[$i][0] && ($this->gameBoard[$i][0] == $this->gameBoard[$i][1]
				&& $this->gameBoard[$i][1] == $this->gameBoard[$i][2])){
				return $this->gameBoard[$i][0];
			} 
		} 
		
		$gameBoardCount = count($this->gameBoard[0]);

		for($i = 0; $i < $gameBoardCount; $i++) {
			if (false !== $this->gameBoard[0][$i] && ($this->gameBoard[0][$i] == $this->gameBoard[1][$i]
				&& $this->gameBoard[1][$i] == $this->gameBoard[2][$i])){
				return $this->gameBoard[0][$i];
			} 
		} 

		if (($this->gameBoard[0][0] == $this->gameBoard[1][1]
				&& $this->gameBoard[1][1] == $this->gameBoard[2][2])
				|| ($this->gameBoard[0][2] == $this->gameBoard[1][1]
				&& $this->gameBoard[1][1] == $this->gameBoard[2][0])) {
				
			if (false !== $this->gameBoard[1][1]) {
				return $this->gameBoard[1][1];
			} 
		}
		

		if (true === Game::isBoardFull($this->gameBoard)){
			return Game::DRAW; 
		}

		return Game::IN_PROGRESS;
	} 
} 
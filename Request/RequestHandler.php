<?php

namespace request;

use base\Game;

require '../vendor/autoload.php';

$config = [
	'move'	 =>	[$_POST['pos1'], $_POST['pos2']],
	'player' =>	Game::PLAYER,
];

if (isset($_POST['gameBoard'])){
	$config['gameBoard'] = $_POST['gameBoard'];
}

$game = new Game($config);
echo $game;

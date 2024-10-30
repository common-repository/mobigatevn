<?php
class Mobigate_Helper{
	const ADDED_GAME_KEY = "mobigate_added_game";

	public static function addNewGameToOption($requestId, $postId){
		$added_game = self::getAddedGame();

		if(!array_key_exists($requestId, $added_game)){
			$added_game[$requestId] = $postId;
		}

		$new_added_game = maybe_serialize($added_game);
		update_option(self::ADDED_GAME_KEY, $new_added_game);
	}

	public static function getAddedGame(){
		$result = array();
		$add_game = get_option(self::ADDED_GAME_KEY);
		if($add_game){
			$result = maybe_unserialize($add_game);
		}

		return $result;
	}

	public static function isAddedGame($requestId){
		$added_game = self::getAddedGame();
		if(array_key_exists($requestId, $added_game)){
			return true;
		}
		return false;
	}

	public static function getPostIdFromRequestId($requestId){
		$added_game = self::getAddedGame();
		if(array_key_exists($requestId, $added_game)){
			return $added_game[$requestId];
		}
		return 0;
	}

	public static function removeAddedGame($postId){
		$added_game = self::getAddedGame();
		$key = array_search($postId, $added_game);
		if($key != null){
			unset($added_game[$key]);
		}

		$new_added_game = maybe_serialize($added_game);
		update_option(self::ADDED_GAME_KEY, $new_added_game);
	}
}
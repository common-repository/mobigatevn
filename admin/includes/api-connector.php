<?php
class APIConnector
{
	protected $api_key = '';
	public $content = '';
	
	public function __construct()
	{
		$this->res = curl_init();
		$this->ua = $_SERVER['HTTP_USER_AGENT'];
	}

	public function setAPIKey($api_key) { $this->api_key = $api_key; }

	//Return an array of games that ready for game-list-table;
	public function getAllGames(){
		$result = array();

		$transName = 'list-games';
		$cacheTime = 30;

		if(get_option("force_update")){
			delete_transient($transName);
		}
			
		if(false === ($result = get_transient($transName) ) ){
		   	$result_object = $this->getOnePageGameList();

		   	if($result_object){
		   		$result = $this->processArrayOfGames($result_object->items);

		   		if($result_object->pagination->pageCount > 1){
		   			for ($i = 2; $i <= $result_object->pagination->pageCount; $i++) { 
		   				$result_object = $this->getOnePageGameList($i);
		   				$array_games = $this->processArrayOfGames($result_object->items);
		   				foreach ($array_games as $key => $value)
		       				$result[] = $value;
		   			}
		   		}

			   	set_transient($transName, $result, 60 * $cacheTime);
			   
			   	//update option to force get games
		    	update_option("force_update", false);
		   	}
	   		
		}

		
		return $result;
	}

	private function processArrayOfGames($games){
		$result = array();

		foreach ($games as $game) {
		    $game_item = array('id' => $game->contentId, 
		    					'icon' => $game->avatar,
		    					'title' => $game->name,
		    					'desc' => $game->shortDescription,
		    					'download_count' => filter_var($game->download, FILTER_SANITIZE_NUMBER_INT),
		    					'rate' => $game->publisherPercent,
		    					'requestId' => $game->requestId,
		    					'date' => $game->createdTime,
		    					'platform' => $game->supportedOs
		    	);
		    $result[] = $game_item;
		}

		return $result;
	}

	public function getOnePageGameList($page = 1){
		$url = 'http://mobigate.vn/api/export/wordpress/list?apiKey='.$this->api_key.'&page='.$page.'&pageSize=200';
		$result = $this->getRemoteData($url);
		return json_decode($result);
	}

	public function getGameDetails($requestId){
		$url = 'http://mobigate.vn/api/export/wordpress/detail?apiKey='.$this->api_key.'&itemId='.$requestId;
		$result = $this->getRemoteData($url);
		return json_decode($result);
	}

	private function getRemoteData($url){
		$response = wp_remote_get($url);

		if ( is_wp_error($response) || ! isset($response['body']) )
    		return '';
  		return $response['body'];
	}
}

?>

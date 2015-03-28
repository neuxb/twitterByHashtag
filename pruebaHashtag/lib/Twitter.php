<?php 
namespace pruebaHashtag\lib;

class Twitter{
	private $beared=false;
	function __construct(){
		$this->b64secret=base64_encode(\pruebaHashtag\Config::TwitterKey.':'.\pruebaHashtag\Config::TwitterSecret);
		$this->con=new Conexion();
		$this->getBeared();
	}

	function getBeared(){
		$this->con->setPost(array('grant_type'=>'client_credentials'))
			->setHttpHeader(array('Authorization: Basic ' . $this->b64secret,'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'))
			->setAgent(\pruebaHashtag\Config::TwitterAgent);
		$r=$this->con->con('https://api.twitter.com/oauth2/token');
		$ar=json_decode($r);
		if(isset($ar->token_type)&&$ar->token_type=='bearer')
			$this->beared=$ar->access_token;
		else
			die('Error de autentificacion');
	}
	function get($url,$post=''){
		$this->con->setHttpHeader(array('Authorization: Bearer ' . $this->beared))
			->setPost($post);
		return json_decode($this->con->con($url));
	}
	function search($query){
		return $this->get(\pruebaHashtag\Config::TwitterSearchUrl.$query);
	}
	
}
?>
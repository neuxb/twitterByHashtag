<?php
namespace pruebaHashtag\lib;
class Conexion {

	private $options;
	
	function __construct(){
		$this->reset();
	}
	function con($url) {
		$ch = curl_init($url);
		foreach($this->options as $k=>$v){
			curl_setopt($ch,constant($k),$v);
		}
		if(false===$response = curl_exec($ch))return false;
		return $response;
	}
	function reset(){
		$this->options=array('CURLOPT_FORBID_REUSE'=>true,
				'CURLOPT_TIMEOUT'=>10,
				'CURLOPT_AUTOREFERER'=>true,
				'CURLOPT_RETURNTRANSFER'=>true,
				'CURLOPT_FOLLOWLOCATION'=>true,
				'CURLOPT_SSL_VERIFYPEER'=>false,
				'CURLINFO_HEADER_OUT'=>true);
		return $this;
	}
	function setAgent($nav=''){
			$this->options['CURLOPT_USERAGENT']= $nav;
			return $this;
	}
	function setPut($file,$content){
		$this->options['CURLOPT_PUT']=true;
		$this->options['CURLOPT_INFILE']= $file;
		$this->options['CURLOPT_INFILESIZE']= strlen($content);
		return $this;
	}
	function setHttpHeader($header){
		$this->options['CURLOPT_HTTPHEADER']= $header;
		return $this;
	}
	function setPost($post=''){
		if(is_array($post))
			$post=self::getPost($post);
		elseif($post==''){
			$this->options['CURLOPT_POST']=false;
			unset($this->options['CURLOPT_POSTFIELDS']);
			return $this;
		}
		$this->options['CURLOPT_POST']=true;
		$this->options['CURLOPT_POSTFIELDS']= $post;
		return $this;
	}
	function setGet(){
		unset($this->options['CURLOPT_POST']);
		unset($this->options['CURLOPT_POSTFIELDS']);
		$this->options['CURLOPT_HTTPGET']=true;
		return $this;
	}
	function setOption($opt,$val){
		$this->option[$opt]=$val;
		return $this;
	}
	function getPost($postData) {
		$post = array();
		foreach ($postData as $name => $value)
			$post[]= $name . "=" . urlencode($value);
		return implode('&',$post);
	}
}

?>
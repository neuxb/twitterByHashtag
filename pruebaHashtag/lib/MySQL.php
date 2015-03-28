<?php
namespace pruebaHashtag\lib;

class MySQL {
	private $link = false;

	function __construct($params = array()) {
		if (isset ( $params ['equipo'] ))
			$this->conexion ( $params );
	}
	private function conexion($params,$r=false){
		$dat = array ('equipo' => \pruebaHashtag\Config::MySQLServer, 'usuario' => \pruebaHashtag\Config::MySQLUser, 'ppaso' => \pruebaHashtag\Config::MySQLPwd, 'port' => \pruebaHashtag\Config::MySQLPort, 'base_datos' => \pruebaHashtag\Config::MySQLDatabase );
		$params = array_merge( $dat, $params );
		$temp = new \mysqli ($params ['equipo'], $params ["usuario"], $params ['ppaso'], $params ['base_datos'], (int)$params ['port'] );
		if ($temp->connect_errno)
			return false;
		else{
			$temp->set_charset('utf8');
			if(!$r)
				$this->link = $temp;
		}
		return $temp;
	}
	function ejecuta($sql,$return=false,$params=array()) {
		$link=$this->link;
		if (count( $params ) || ! $link) {
			if (false === $link=$this->conexion ( $params,true ))			
				return false;
		}
		if (!$query = $link->query ( $sql ))
			return false;
		if(preg_match('/^select/isU',$sql)&&$query->num_rows==0)return false;
		if ($return) {
			if(preg_match('/^insert/isU',$sql)){
				return (is_object($link)?$link->insert_id:false);
			}
			return $query->fetch_assoc ();
		}
		return $query;
	}
	function getArray($sql,$key='',$params=array()){
		if(false===$fa=$this->ejecuta($sql,false,$params))
			return false;
		$r=array();
		while($f=$fa->fetch_assoc()){
			$temp=$f;
			if($key=='')
				$r[]=$temp;
			else{
				unset($f[$key]);
				$r[$temp[$key]]=$f;
			}
		}
		return $r;
	}
	
	function insert($tabla,$val,$upd=''){
		$val=array_map('addslashes',$val);
		if($upd==''){
			$up=array();
			foreach(array_keys($val) as $key)
				$up[]=$key.'=VALUES('.$key.')';
			$upd=implode(',',$up);
		}
		return $this->ejecuta('insert into '.$tabla.' ('.implode(',',array_keys($val)).') values ("'.implode('","',$val).'") on duplicate key update '.$upd,true);
	}
}

?>
<?php 
namespace pruebaHashtag;

class Config{
	
	const TwitterKey='xxxxxxxxxxxxxxxxx';
	const TwitterSecret='xxxxxxxxxxxxxxxxx';
	const TwitterAgent='xxxxxxxxxxxxxxxxx';
	const TwitterSearchUrl = 'https://api.twitter.com/1.1/search/tweets.json';
	
	const MySQLServer='localhost';
	const MySQLUser='twitter';
	const MySQLPwd='xxxxxxxxxxxxxxxxxxxxx';
	const MySQLPort=3306;
	const MySQLDatabase='twitter';
	
	const MemServer='localhost';
	const MemPort=11211;
	
	static public function mysqlTime($str){
		list($yr,$mo,$da,$hr,$mi,$sd)=sscanf($str,"%d-%d-%d %d:%d:%d");
		return mktime($hr,$mi,$sd,$mo,$da,$yr);
	}
}

?>
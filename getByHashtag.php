<?php 

if(!isset($argv[1])){
	echo "Uso:: php -f getByHastag.php <hastag-sin-#> [<limit>100]\n";
	exit(1);
}
if(!isset($argv[2]))
	$argv[2]=100;

spl_autoload_register(function($className){
	$file=__DIR__.'/'.str_replace('\\','/',$className).'.php';
	if(file_exists($file))
		require_once($file);
});

	$twitter=new \pruebaHashtag\lib\Twitter();
	$objMessages=$twitter->search('?q='.urlencode((($argv[1][0]!='#')?'#'.$argv[1]:$argv[1])));

	$db=new \pruebaHashtag\lib\MySQL();

	$i=0;
	while($i<$argv[2]){
		if(!isset($objMessages->statuses))break;
		$losTenemos=0;
		foreach($objMessages->statuses as $o){

			$id=$db->insert('twitter.posts',array('idtw'=>$o->id,
					'text'=>$o->text,
					'userid'=>$o->user->id,
					'created'=>date('Y-m-d H:i:s',strtotime($o->created_at)),
					'reply_statusid'=>$o->in_reply_to_status_id,
					'reply_userid'=>$o->in_reply_to_user_id,
					'retweet_count'=>$o->retweet_count,
					'favorite_count'=>$o->favorite_count));
			if(!$id){
				$losTenemos++;
				continue;
			}
			$db->insert('twitter.users',array('id'=>$o->user->id,
					'name'=>$o->user->name,
					'screen_name'=>$o->user->screen_name,
					'location'=>$o->user->location,
					'followers'=>$o->user->followers_count,
					'friends'=>$o->user->friends_count,
					'listed'=>$o->user->listed_count,
					'favourites'=>$o->user->favourites_count,
					'created'=>date('Y-m-d H:i:s',strtotime($o->user->created_at)),
					'statuses'=>$o->user->statuses_count,
					'image'=>$o->user->profile_image_url));

			foreach($o->entities->hashtags as $h)
				$db->insert('twitter.hashtags',array('hashtag'=>strtolower(trim($h->text)),'postsid'=>$id),'postsid=concat(postsid,",",VALUES(postsid))',true);
			$i++;
							
		}

		if(!isset($objMessages->search_metadata->next_results)||$losTenemos==count($objMessages->statuses))break;
		$objMessages=$twitter->search($objMessages->search_metadata->next_results);
	}
	echo "Adquiridos $i post nuevos\n\n";
exit(0);
?>
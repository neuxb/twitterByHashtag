<?php 

echo '<form method="get" action="/">Buscar por hastag:<input type="text" size="50" name="q"/><input type="submit" value="Buscar"/></form><br/>';


if(!isset($_REQUEST['q']))
	exit(0);

spl_autoload_register(function($className){
	$file=__DIR__.'/'.str_replace('\\','/',$className).'.php';
	if(file_exists($file))
		require_once($file);
});

	$db=new \pruebaHashtag\lib\MySQL();

	if(false===$hashtag=$db->ejecuta('select postsid from hashtags where hashtag="'.strtolower(preg_replace('/\W/',' ',$_REQUEST['q'])).'"',true)){
		echo 'No dispongo de posts para el hashtag "'.$_REQUEST['q'].'", intentelo de nuevo.<br/>';
		exit(0);
	}

	$postsid=array_unique(explode(',',$hashtag['postsid']));

	$mem=new \Memcached();
	$mem->addServer(\pruebaHashtag\Config::MemServer, \pruebaHashtag\Config::MemPort);
	
	$posts=$mem->getMulti($postsid);
	$areNotCached=array();
	foreach ($postsid as $post){
		if(!isset($posts[$post]))
			$areNotCached[]=$post;
		else
			$orden[$post]=$posts[$post]['c'];
	}

	if(count($areNotCached)){
		$newPosts=$db->getArray('select posts.id,users.image,users.name,users.screen_name,posts.created,posts.text,posts.retweet_count,posts.favorite_count
				from posts
				join users on users.id=posts.userid
				where posts.id in ('.implode(',',$areNotCached).')
				order by posts.created desc','id');
		
		$htmlPosts='<div><div><img src="%s"/></div><div><p><b>%s</b> @%s <span>%s</span></p><p>%s</p><p>RTs::%s   FVs::%s</p></div></div>';
		foreach($newPosts as $id=>$post){
			$r=vsprintf($htmlPosts,$post);
			$mem->set($id,array('c'=>strtotime($post['created']),'t'=>$r));
			echo $r;
		}
	}
	if(count($posts)){
		array_multisort($orden,SORT_DESC,$posts);
		foreach($posts as $post)
			echo $post['t'];
	}
?>
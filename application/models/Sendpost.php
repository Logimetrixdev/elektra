<?php
class Application_Model_Sendpost
{
	function fbpost($access_token,$mess,$friends_social_ids,$user_social_id,$bluelink)
	{
		$docpath =  $_SERVER['DOCUMENT_ROOT'].'/fbsdk3/src/facebook.php';
		$facebook = new Facebook(array(
		'appId'  => '179948478817334',
		'secret' => 'f62d2ab555e830c5ee9f39864c3b5028',
		));
		
		$privacy = array(
			'value' => 'CUSTOM',
			'friends' => 'SOME_FRIENDS',
			'allow' => $friends_social_ids // Change this to your friends ids
		);

		 $params=array();       
		 $params['access_token'] = $access_token;
		 //$params['message'] = '';
		 
		 $params['tags']= $friends_social_ids; //comma separated friends ID's
		 $params['name'] = $bluelink;
		 $params['privacy'] = json_encode($privacy);
		 $params['description'] = $mess;
		 //$params['place']='155021662189'; 
		 //$params['caption'] = 'sfty.no';
		// if($posturl)
		 //{
		//	$params['link'] = $posturl;
		 //}
		 //$params['picture'] = $imgurl;
		 
		 
		if($result = $facebook->api("/$user_social_id/feed/",'post', $params)) 
		{
			return true;
		}else{
			return false;
		}
		
		
	}
	
	function twitterpost($access_token,$mess,$friends_social_ids,$user_social_id,$bluelink)
	{
		require $_SERVER['DOCUMENT_ROOT'].'/twitter/twitteroauth.php';      

		$tmhOAuth = new TwitterOAuth($server->consumerKey,$server->consumerSecret,$access_token,$accessSecret); 

		$response = $tmhOAuth->post('statuses/update', array('status' => $message));
	}	
	
}


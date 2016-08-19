<?php 

function ds_is_channel_parent_check(){

	if(ds_channel_is_parent()){
	
	
		$videos = grab_channel();
	
		$children = $videos[0]->childchannels;
			
		$child_slug = $children[0]->slug;
	
		$current = get_post(get_the_ID());
	
		$category = get_query_var("channel_category", FALSE);
	
		if(!$category){
				
			$category = 'featured';
		
		}
	
		$url = home_url("channels/".$current->post_name."/".$child_slug."/");
	
		wp_redirect( $url );
		die();
	
	}
}

/** MOVE THIS TO THE FUNCTIONS FILE AS SOON AS POSSIBLE **/

function igrab_channel(){
	
	global $post;

	$video = FALSE;
		
	if(ds_channel_is_child()){
			
		$videos = grab_channel();
		
		if(!is_array($videos)){
			
			return array();
			
		}
	
		$channel_title = $videos[0]->title;
	
		$company = $videos[0]->company;
		
		$title = $videos[0]->childchannels[0]->title;
		
		$description = $videos[0]->description;
		
		$actors = $videos[0]->actors;
		
		$writers = $videos[0]->writers;
		
		$directors = $videos[0]->directors;
		
		$category = get_query_var("channel_category", FALSE);
				
		$playlist = $videos[0]->childchannels[0]->playlist;
	
		$channel_parent = get_post( $post->post_parent );
		
		$image_id = $playlist[0]->thumb;
	
		$to_return['playlist'] = $playlist;
		
		$to_return['details'] = array('description' => $description, 'actors' => $actors, 'writers' => $writers, 'directors' => $directors);
	
		$to_return['link_url'] = $url = home_url("channels/".$channel_parent->post_name."/".$post->post_name);
	
		$to_return['count'] = count($playlist);
		
		$video = get_query_var("video", FALSE);
		
		if($video){
			
			$id = get_query_var("video", FALSE);
			
			$url = home_url("channels/".$channel_parent->post_name."/".$post->post_name."/video=$id");
			
			foreach($videos[0]->childchannels[0]->playlist as $pl){
											
				if($pl->_id == $id){
										
					$title = $pl->title;
	
					$duration = round($pl->duration/60);
	
					$description = $pl->description;
					
					$country = $pl->country;
	
					$language = $pl->language;
					
					$image_id = $pl->thumb;
					
					
					
					break;
					
				}
				
			}
			
		}
		
		$to_return['for_meta'] = (object) array('description' => $description, 'url' => $url, 'channel_title' => $channel_title, 'title' => $title, 'image_id' => $image_id);
	
		return $to_return;
	
	} else {
		
		$videos = grab_channel();
		
		if(!is_array($videos)){
			
			return array();
			
		}

		//print_r($videos);
		
		$company = $videos[0]->company;
		
		$channel_title = $title = $videos[0]->title;
		
		$description = $videos[0]->description;
		
		$actors = $videos[0]->actors;
		
		$writers = $videos[0]->writers;
		
		$directors = $videos[0]->directors;
		
		$image_id = $playlist = $videos[0]->videos_thumb;

		$poster = $videos[0]->poster;
	
		$playlist = $videos[0]->playlist;
	
		$category = get_query_var("channel_category", FALSE);
		
		$to_return['playlist'] = $playlist;
		
		$to_return['details'] = array('description' => $description, 'actors' => $actors, 'writers' => $writers, 'directors' => $directors, 'poster' => $poster);
	
		$to_return['link_url'] = $url = home_url("channels/".$post->post_name."/");
		
		$to_return['count'] = count($playlist);
		
		$video = get_query_var("video", FALSE);
		
		if($video){
			
			$id = get_query_var("video", FALSE);
			
			$url = home_url("channels/".$post->post_name."/video=$id");
			
			foreach($videos[0]->playlist as $pl){
											
				if($pl->_id == $id){
										
					$title = $pl->title;
	
					$duration = round($pl->duration/60);
	
					$description = $pl->description;
					
					$country = $pl->country;
	
					$language = $pl->language;
					
					$image_id = $pl->thumb;
					
					break;
					
				}
				
			}
			
		}
		
		$to_return['for_meta'] = (object) array('description' => $description, 'url' => $url, 'channel_title' => $channel_title, 'title' => $title, 'image_id' => $videos[0]->videos_thumb);
		
		
		return $to_return;
		
	}
		
}


function channel_headline_video(){
	
	global $ds_curl;
	
	$video = get_query_var("video", FALSE);
	
	if(ds_channel_is_child()){
		
			$videos = grab_channel();
			
			if(!is_array($videos)){
			
				$videos = new stdClass;
			
				return $videos;
			
			}
			
			$playlist = $videos[0]->childchannels[0]->playlist[0];
				
			$id = $playlist->_id;
		
			$title = $playlist->title;
	
			$duration = round($playlist->duration/60);
	
			$description = isset($playlist->description) ? $playlist->description : '';
	
			$company = isset($videos[0]->company) ? $videos[0]->company : '';
	
			$country = isset($playlist->country) ? $playlist->country : '';
	
			$language = isset($playlist->language) ? $playlist->language : '';
	
			$year = isset($videos[0]->year) ? $videos[0]->year : '';
	
			$rating = isset($videos[0]->rating) ? $videos[0]->rating : '';
		
		if($video){
			$id = get_query_var("video", FALSE);
			
			foreach($videos[0]->childchannels[0]->playlist as $pl){
											
				if($pl->_id == $id){
										
					$title = $pl->title;
	
					$duration = round($pl->duration/60);
	
					$description = $pl->description;
					
					$country = $pl->country;
	
					$language = $pl->language;
					
					break;
					
				}
				
			}
			
		}
		
		$player_url = "https://$company.dotstudiopro.com/player/$id";
		
		$to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $description, 'company' => $company, 'country'  => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);
		
		return $to_return;
		
	} else {
		
		$videos = grab_channel();
		
		if(!is_array($videos)){
			
			$videos = new stdClass;
			
			return $videos;
			
		}
		
		$id = $videos[0]->playlist[0]->_id;
	
		$title = isset($videos[0]->playlist[0]->title) ? $videos[0]->playlist[0]->title : isset($videos[0]->video->title) ? $videos[0]->video->title : '';
	
		$duration = isset($videos[0]->playlist[0]->duration) ? round($videos[0]->playlist[0]->duration/60) : isset($videos[0]->video->duration) ? round($videos[0]->video->duration/60) : '';
	
		$description = isset($videos[0]->description) && !get_query_var("video", false) ? $videos[0]->description : isset($videos[0]->playlist[0]->description) ? $videos[0]->playlist[0]->description : isset($videos[0]->video->country) ? $videos[0]->video->country : '';
	
		$company = isset($videos[0]->company) ? $videos[0]->company : '';
	
		$country = isset($videos[0]->playlist[0]->country) ? $videos[0]->playlist[0]->country : isset($videos[0]->video->country) ? $videos[0]->video->country : '';
	 
		$language = isset($videos[0]->playlist[0]->language) ? $videos[0]->playlist[0]->language : isset($videos[0]->video->language) ? $videos[0]->video->language : '';
	
		$year = isset($videos[0]->year) ? $videos[0]->year : '';
	
		$rating = isset($videos[0]->rating) ? $videos[0]->rating : '';
	
		if($video){
			
			$id = get_query_var("video", FALSE);
			
			foreach($videos[0]->playlist as $pl){
											
				if($pl->_id == $id){
										
					$title = $pl->title;
	
					$duration = round($pl->duration/60);
	
					$description = $pl->description;
					
					$country = $pl->country;
	
					$language = $pl->language;
					
					break;
					
				}
				
			}
			
		}

		if(!$id){

			$id = $videos[0]->video->_id;

		}
		
		$player_slider_color = get_option("ds_player_slider_color");

		if(!$player_slider_color){
			
			$player_slider_color = 'green';
			
		}
	
		$player_url = "https://$company.dotstudiopro.com/player/$id/?skin=%2Fassets%2Fjs%2Flib%2Fjw%2Fskins%2F$player_slider_color.xml&share=true";
		
		$to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $description, 'company' => $company, 'country'  => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);
		
		return $to_return;
		
	}
	
	
}

function get_child_siblings(){
	
	if(!ds_channel_is_child()){
		
		return false;
		
	}
	
	global $post;
	
	$parent = grab_parent_channel();
	
	if(!$parent){
		
		return '';
		
	}
	
	$parent_slug = $parent->slug;
	
	$category = get_query_var("channel_category", FALSE);
	
	$siblings = '';
		
	foreach($parent->childchannels as $ch){
		
		$selected = '';
		
		if($ch->slug==$post->post_name){
			
			$selected = "active";
			
		}
		
		$siblings .= "
		
		<a href='".home_url("channels/".$parent->slug."/".$ch->slug."/")."' class='$selected'>
			<img src='https://image.myspotlight.tv/".$ch->playlist[0]->thumb."/400/225' />
			<h3>".$ch->title."</h3>
		</a>";
				
	}
	
	return $siblings;
	
}

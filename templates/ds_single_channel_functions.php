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
		
		$company = $videos[0]->company;

		$company_id = isset($videos[0]->video->company_id) ? $videos[0]->video->company_id : '';
		
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

			$company_id = isset($videos[0]->playlist[0]->company_id) ? $videos[0]->playlist[0]->company_id : $videos[0]->spotlight_company_id;
		
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
		
		$player_url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=".get_option("ds_player_slider_color", "228b22")."&autostart=".(get_option("ds_player_autostart", 0) == 1 ? "true" : "false")."&sharing=".(get_option("ds_player_sharing", 0) == 1 ? "true" : "false")."&muteonstart=".(get_option("ds_player_mute", 0) == 1 ? "true" : "false")."&disablecontrolbar=".(get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");
		
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
	
		$chdescription = "";

		if(isset($videos[0]->video->description) && !get_query_var("video", false)){

			$chdescription = $videos[0]->video->description;

		} else if(isset($videos[0]->playlist[0]->description)){

			$chdescription = $videos[0]->playlist[0]->description;

		} else if(isset($videos[0]->video->country)){
		
			$chdescription = $videos[0]->video->country;

		}
	
		$company = isset($videos[0]->company) ? $videos[0]->company : '';

		$company_id = isset($videos[0]->playlist[0]->company_id) ? $videos[0]->playlist[0]->company_id : $videos[0]->spotlight_company_id;
	
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
	
		$player_url = "http://player.dotstudiopro.com/player/$id?targetelm=.player&companykey=$company_id&skin=".get_option("ds_player_slider_color", "228b22")."&autostart=".(get_option("ds_player_autostart", 0) == 1 ? "true" : "false")."&sharing=".(get_option("ds_player_sharing", 0) == 1 ? "true" : "false")."&muteonstart=".(get_option("ds_player_mute", 0) == 1 ? "true" : "false")."&disablecontrolbar=".(get_option("ds_player_disable_controlbar", 0) == 1 ? "true" : "false");
		
		$to_return = (object) array('_id' => $id, 'title' => $title, 'duration' => $duration, 'description' => $chdescription, 'company' => $company, 'country'  => $country, 'language' => $language, 'year' => $year, 'rating' => $rating, 'player' => $player_url);
		
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
			<img class='img img-responsive' src='http://image.myspotlight.tv/".$ch->playlist[0]->thumb."/400/225' />
			<h3>".$ch->title."</h3>
		</a>";
				
	}
	
	return $siblings;
	
}

function display_single_channel_extra_info($channel, $post_id){
	if(is_array($channel) && count($channel) > 0){
		
		?>
<div id='primary' class='content-area'>	
	<?php if($channel['count'] > 1){ ?>
	<ul class="ds-tabs">
		
		<li class='ds-tab-link current' data-tab='ds-tab-1'>More Episodes</li>
		<li class='ds-tab-link' data-tab='ds-tab-2'>Details</li>
		<?php if($siblings && strlen($siblings) > 0){ ?>
			<li class='ds-tab-link' data-tab='ds-tab-3'>Seasons</li>
		<?php } ?>
		<li class='ds-tab-link' data-tab='ds-tab-4'>Additional Info</li>
		
		<li class='ds-tab-link'><a href='#ds-comments'>Comments</a></li>
	</ul>
	<?php } ?>
	
	<div id='ds-tab-1' class='ds-tab-content current'>
		<div id="loading"><h5>Loading...</h5></div>
		
		<ul class='ds-video-thumbnails ds-lazyload'>
		<?php 
		
			
		
			$this_post = get_post($post_id);
			
			$channel_parent = '';
			
			$category = get_query_var("channel_category", FALSE);
			
			$counter = 1;
			
		foreach($channel['playlist'] as $pl){
			
			$selected = ''; 
						
			$id =  $pl->_id;
		
			$thumb_id = $pl->thumb;	
			
			$title = isset($pl->title) ? $pl->title : '';
	
			$duration = isset($pl->duration) ? round($pl->duration/60) : '';
	
			$description = isset($pl->description) ? $pl->description : '';
	
			$company = isset($pl->company) ? $pl->company : '';
	
			$country = isset($pl->country) ? $pl->country : '';
	 
			$language = isset($pl->language) ? $pl->language : '';
	
			$year = isset($pl->year) ? $pl->year : '';
	
			$rating = isset($pl->rating) ? $pl->rating : '';
			
			$channel_parent = get_post( $this_post->post_parent );
			
			$epnum = key($pl);
			
			$selected_id = get_query_var("video", FALSE);
					
			if($id == $selected_id || $counter == 1 && !$selected_id){
				
				$selected = "class='selected'";
				
			}
			
			$counter++;
					
			?>
			
			<li <?php echo $selected; ?>>
				<img class="img img-responsive" src='http://image.myspotlight.tv/<?php echo $thumb_id ?>/380/215' />
				<div class='ds-overlay animated fadeIn'>
				
				<?php if(!get_child_siblings()){ ?>
					
					<a href='<?php echo home_url("channels/".$this_post->post_name."/?video=$id&channel_category=$category") ?>'>
					
				<?php } else { ?>
					
					<a href='<?php echo home_url("channels/".$channel_parent->post_name."/".$this_post->post_name."/?video=$id&channel_category=$category") ?>'>
					
				<?php } ?>
				
					
				 <i class='fa fa-play-circle-o fa-3x'></i>
				</a>
				<label class='delay' style='display: inline-block;'><small><?php echo $duration ?> min</small></label>
				</div>
				<h3 class='character-limit-90'><?php echo $title ?></h3>
				<span class='ds-video-year animated fadeIn'><small>Year: <?php echo $year ?></small></span>
				<span class='ds-video-country animated fadeIn'><small>Country: <?php echo $country ?></small></span>
				<span class='ds-video-description character-limit-90 animated fadeIn'><?php echo $description ?></span>
			</li>
			<?php
			
			}
			
			?>
			</ul>
	</div>
	<div id='ds-tab-2' class='ds-tab-content'>
		<span class='ds-video-headliner-description'>
		<?php echo $channel['details']['description']; ?>
		
		Actors: <?php echo implode(", ", $channel['details']['actors']); ?>
		
		Writers: <?php echo implode(", ", $channel['details']['writers']); ?>
		
		Directors: <?php echo implode(", ", $channel['details']['directors']); ?>
		
		</span>
	</div>
	<div id='ds-tab-3' class='ds-tab-content'>
		<?php echo $siblings;?>
	</div>
	<div id='ds-tab-4' class='ds-tab-content'>
	
	<?php 
	
	global $post;
	
	echo $post->post_content;
	
	?>
	
	</div>
	<div class='ds-commenting-sidebar'>
	<?php ds_template_fb_code(); ?>
	</div>
	<?php
		} else { ?>
			
			<h1>This channel is not available in your country.</h1>			
			
		<?php }
}

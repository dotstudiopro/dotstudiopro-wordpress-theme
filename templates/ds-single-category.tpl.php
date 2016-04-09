<?php get_header();






?>
<div id="main">
	<ul class="gridder">


	<?php 
	
	$category = get_post(get_the_ID());
	
	$category_check_grab = get_page_by_path('channel-categories');
	
	$category_parent = $category_check_grab->ID;
	
	$post_slug=$category->post_name;
	
	if($category->post_parent == $category_parent){
		
	$channels = grab_category($post_slug);
				
	if($channels && is_array($channels)){	
			
		foreach($channels as $ch){
					
			$id =  $ch->_id;
		
			$thumb_id = isset( $ch->video->thumb) ?  $ch->video->thumb : '';	
			
			$slug =  $ch->slug;
			
			$title = $ch->title;
			
			$spotlight_poster = $ch->spotlight_poster;
			
			$poster = isset( $ch->poster ) ?  $ch->poster : '';
			
			$year = isset($ch->year) ? $ch->year : '';
			
			$language = isset( $ch->language) ?  $ch->language : '';
			
			$rating = isset($ch->rating) ? $ch->rating : '';
						
			$company = $ch->company;
			
			$description = isset( $ch->video->description ) ?  $ch->video->description : $ch->description;
		
			$children = $ch->childchannels;
			
			$child_urls = '';
			
			if(count($children) > 0){
			
				foreach($children as $kid){
				
					$child_urls .= "<a href='".home_url("channels/$slug/".$kid->slug."/")."' class='ds-button'>".$kid->title."</a>";
								
				}
				
				$description = $ch->description;
			
			}
		
			?>
			
			<li class='gridder-list' data-griddercontent='#<?php echo $slug ?>'>
	        	<img class='channel-spotlight-poster' src='<?php echo $spotlight_poster ?>/400/225'>
	    		<div id='<?php echo $slug ?>' class='gridder-content'>
	    			<div class='og-expander-inner clearfix'>
			    		<a class='og-fullimg' href='<?php echo home_url("channels/$slug/") ?>'><object class='channel-poster animated fadeIn' data='<?php echo $poster ?>/900/506'' type='image/png'></object></a>
			    		<div class="og-mask"></div>	
			    		<div class='ds-details animated fadeInRight'>	
							<h2 class='channel-title'><?php echo $title ?></h2>
							<ul class='ds-channelmetalist'>	
								<li class='channel-year'><?php echo $year ?></li>
								<li class='channel-language'><?php echo $language ?></li>
								<li class='channel-company'><?php echo $company ?></li>
							</ul>
							<span class='ds-channel-description character-limit-300'>Description: <?php echo $description ?></span>
							<span>
							<?php if(count($children) < 1){ ?>
								
								<a href= '<?php echo home_url("channels/$slug/") ?>' class='ds-button'>
									Watch Now
								</a>
							<?php 
							
							} else {
								
								echo $child_urls;
								
							}
							
							?>
							
							</span>
						</div>
					</div>
	    		</div>
    		</li>
				
			
			<?php
			
			}
		
		} else {
				
			echo "No channels to display.";
				
		}
	
	}
	
	
	
	
	
	
	
	
	
	?>
	</ul>
	
	</div><!--main-->


<?php get_footer();?>

<script>
	$(function(){
  $(".character-limit-300").each(function(i){
    len=$(this).text().length;
    if(len>300)
    {
      $(this).text($(this).text().substr(0,300)+'...');
    }
  });       
});

</script>

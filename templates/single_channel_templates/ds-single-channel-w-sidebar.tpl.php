<?php

ds_is_channel_parent_check();

/*********************************************************/

/** MUST BE CALLED BEFORE HEADER FUNCTION! **/

$channel = igrab_channel();

$siblings = get_child_siblings();

$category = get_query_var("channel_category", FALSE);
remove_action( 'wp_head' , 'swp_add_header_meta' , 1 );
add_action('wp_head', 'ds_meta_tags');

/********************************************/


get_header();

?>
<div id="main" class="container">

	<?php

		$headline_video = channel_headline_video();

		if(isset($headline_video->player)){
		?>

			<div class='ds-video-headliner'>
				<div class='ds-video-fluidMedia'>
				<div class="player"></div><script src="<?php echo $headline_video->player ?>"></script>
				</div>
				<div class='ds-metabox'>
					<h1 class='ds-video-headliner-title'><?php echo $headline_video->title ?></h1>
					<span class='ds-video-headliner-duration'>(<?php echo $headline_video->duration ?> min)</span>
					<div class='ds-row'>
						<div class='ds-col-8'>
							<ul class='ds-videometalist'>
				              <li><?php echo $headline_video->country ?></li>
				              <li>Rating:<?php echo $headline_video->rating ?></li>
				              <li><?php echo $headline_video->language ?></li>
				              <li><?php echo $headline_video->year ?></li>
			              	  <li><?php echo $headline_video->company ?></li>
			          		</ul>
		          		</div>
		          		<div class='ds-col-4'>
			          		<?php

							if(is_file( dirname( __FILE__ ) ."/../components/sharing.php" ) ){

								include( dirname( __FILE__ ) ."/../components/sharing.php" );

							} else if( is_file( dirname( __FILE__ ) . "/ds-sharing.php" ) ){

								include( dirname( __FILE__ ) . "/ds-sharing.php" );

							}


							?>
		          		</div>
	          		</div>
	          	</div>
	          	<div class='ds-metabox'>
      				<span class='ds-video-headliner-description'><?php echo $headline_video->description ?></span>
      				<hr>
					<a class='ds-more'>Show More</a>
				</div>
			</div>

		<?php

		}



		display_single_channel_extra_info($channel, get_the_ID());

	?>

	</div>

</div><!--main-->
<?php get_sidebar();?>
<?php get_footer();?>

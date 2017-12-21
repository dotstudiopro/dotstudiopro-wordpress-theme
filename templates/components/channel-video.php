	<?php
		$headline_video = channel_headline_video();
		if(isset($headline_video->player)) {
				$minifyVid = get_option('ds_player_minivid') ?: "0";
				$autoRedir = get_option('ds_player_autoredir') ?: "0";
				$autoPlay = get_option('ds_player_autoplay') ?: "0";
				$recPlaylist = get_option('ds_player_recplaylist') ?: "0";
				$playerSliderColor = get_option('dspremium_player_slider_color') ?: "blue";
				$videoId = $headline_video->_id;
				$show_playlist_above_meta = get_option('ds_show_playlist_above_meta');
		?>


	    <!-- VIDEO PLAYER -->
			<div class='ds-video-headliner'>
					<div id="anibox">&nbsp;</div>
			    <div class='ds-row ds-video-row-container'>
			    		<div class='<?php echo $recPlaylist === '1' ? 'ds-col-9' : 'ds-col-12'?>  ds-video'>
									<div class='ds-video-fluidMedia'>
											<?php if($recPlaylist === '1'): ?><i class='fa fa-arrows-alt fa-2x ds-player-togglemode'></i><?php endif;?>
											<div class="player" data-minifyvid='<?php echo $minifyVid;?>' data-autoredir='<?php echo $autoRedir;?>' data-autoplay='<?php echo $autoPlay;?>' data-recplaylist='<?php echo $recPlaylist ?>'></div>
											<script id='videoloader' src="<?php echo $headline_video->player ?>"></script>
									</div>
			    		</div>
			    		<!-- STANDARD MODE PLAYLIST -->
			    		<div class='ds-col-3 ds-vid-playlist ds-playlist-<?php echo $playerSliderColor ?> ds-playlist-standard-mode active-playlist'>

			    		</div>
			    </div>

			   <?php if($show_playlist_above_meta) {
			   		echo dsppremium_theater_mode_playlist($videoId);
			   	} ?>



							<!-- TITLE -->
							<div class='ds-row ds-metabox'>
									<div class='ds-col-12'>
										<h1 class='ds-video-headliner-title'><?php echo $headline_video->title ?></h1>
									</div>
							</div>

							<!-- DESCRIPTION -->
			        <div class="ds-row">
			        		<div class='ds-col-12 ds-metabox'>
					      		<span class='ds-video-headliner-description'><?php echo $headline_video->description ?></span>
					      		<hr>
										<!-- <a class='ds-more' href='#primary'>Show More</a> -->
			        		</div>
			        </div>

			        <!-- METADATA AND SHARING -->
							<div class='ds-row ds-metabox'>
									<!-- meta -->
									<div class='ds-videometadata'>
										<ul class='ds-videometalist'>
									  			<li><?php echo $headline_video->duration ?> min</li>
						              <li><?php echo $headline_video->country ?></li>
						              <li>Rating:<?php echo $headline_video->rating ?></li>
						              <li><?php echo $headline_video->language ?></li>
						              <li><?php echo $headline_video->year ?></li>
					              	<li><?php echo $headline_video->company ?></li>
					         	</ul>
				       	 	</div>
				       	 	<!-- sharing -->
				       	 	<div class='ds-videosharedata'>
						        <?php
										if(is_file( dirname( __FILE__ ) ."/../components/sharing.php" ) ){
											include( dirname( __FILE__ ) ."/../components/sharing.php" );
										} else if( is_file( dirname( __FILE__ ) . "/ds-sharing.php" ) ){
											include( dirname( __FILE__ ) . "/ds-sharing.php" );
										}
										?>
				       	 	</div>
			        </div>



			   <?php if(!$show_playlist_above_meta) {
			   		echo dsppremium_theater_mode_playlist($videoId);
			   	} ?>

			</div>






		<?php

		}

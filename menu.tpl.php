<?php
 
if(isset($_GET['ds-admin-action']) && $_GET['ds-admin-action'] == 'flush'){
	
	ds_check();
	
}

$colors = array('blue', 'pink', 'purple', 'orange', 'red', 'yellow');

$current_slider_color = get_option('ds_player_slider_color');

$styles = array('light-style', 'dark-style');

$templates = scandir(dirname( __FILE__ ) . '/templates/single_channel_templates/');

unset($templates[0]); 

unset($templates[1]);

$current_style = get_option('ds_plugin_style');

$current_template = get_option('ds_channel_template');

$selector_colors = $template_styles = $template_list = '';

foreach($colors as $v){
	
	if($v == $current_slider_color){
		
		$selector_colors .= "<option value='$v' selected='selected'>".ucfirst($v)."</option>";
		
	} else {
		
		$selector_colors .= "<option value='$v'>".ucfirst($v)."</option>";
		
	}
	
}

foreach($styles as $v){
	
	$exp = explode("-", $v);
	
	if($v == $current_style){
				
		$template_styles .= "<option value='$v' selected='selected'>".ucfirst($exp[0])."</option>";
		
	} else {
		
		$template_styles .= "<option value='$v'>".ucfirst($exp[0])."</option>";
		
	}
	
}

foreach($templates as $v){
	
	$v = str_replace(".tpl.php", "", $v);
	
	$name = ucwords(str_replace("-", " ", str_replace("ds-", "", $v)));
	
	if($v == $current_template){
		
		$template_list .= "<option value='$v' selected='selected'>".$name."</option>";
		
	} else {
		
		$template_list .= "<option value='$v'>".$name."</option>";
		
	}
	
}



?>

<div class='container'>
	<h2>dotstudioPRO Plugin Options</h2>
		<!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/uBVdMFDR38o" frameborder="0" allowfullscreen></iframe> -->
		<form action='' method='POST' enctype='multipart/form-data'>
			<table class='form-table widefat'>
				<thead>
					
				</thead>
				<tbody>
				
					<tr><td colspan=2><b>Please make sure to set as many of these options as you can to ensure a good user experience.</b><br/></td></tr>
				
					<tr><td>dotstudioPRO API Key</b><br/><span class='description'>Don't have an API Key? <a href="https://beta.dotstudiopro.com/user/register" target="_blank">Click Here.</a></span></td><td><input type='text' name='ds_api_key' value='<?php echo get_option('ds_api_key') ?>' /></td></tr>
					
					<tr><td>Flush and Rebuild</b><br/><span class='description'>Use this if you add videos through dostudioPRO</span></td><td><a class='button' href='<?php echo site_url().'/admin.php?page=dot-studioz-options&flush=1'; ?>'>Flush</a></td></tr>
				
					<tr><td>Facebook App ID</b><br/><span class='description'>For Facebook sharing and commenting to work properly you need a Facebook App Id. Don't have one? <a href="http://dotstudiopro.com" target="_blank">Click Here.</a></span></td><td><input type='text' name='ds_fb_app_id' value='<?php echo get_option('ds_fb_app_id') ?>' /></td></tr>
					
					<tr><td>Twitter Handle</td><td><input type='text' name='ds_twitter_handle' value='<?php echo get_option('ds_twitter_handle') ?>' /></td></tr>
					
					<tr><td>Player Slider Color</td><td><select name='ds_player_slider_color'><?php echo $selector_colors ?></select></td></tr>
					
					<tr><td>Template</td><td><select name='ds_channel_template'><?php echo $template_list ?></select></td></tr>
					
					<tr><td>Template Style</td><td><select name='ds_plugin_style'><?php echo $template_styles ?></select></td></tr>
					
					<tr><td colspan=2><b>Development Options</b><br/><span class='description'>Please note: any options set here will override normal settings.  Please make sure to turn these settings off when you are done testing.</span></td></tr>
					
					<tr><td>Development Mode</td><td><input type='checkbox' name='ds_development_check' value='1' <?php echo get_option("ds_development_check") == 1 ? 'checked="checked"' : '' ?> /></td></tr>
					
					<tr><td>Development Country (Abbreviation)</td><td><input type='text' name='ds_development_country' value='<?php echo get_option("ds_development_country") ?>' /></td></tr>
					
					<input type='hidden' name='ds-save-admin-options' value='1' />
					
					</tbody>
				<tfoot>
					<tr><td colspan=2><button>Save</button></td></tr>
				</tfoot>
				</table>
		</form>
		
	
</div>

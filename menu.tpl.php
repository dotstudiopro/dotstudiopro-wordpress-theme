<?php

if(isset($_GET['ds-admin-action']) && $_GET['ds-admin-action'] == 'flush'){

	ds_check();

}

$colors = array('blue', 'pink', 'purple', 'orange', 'red', 'yellow', 'green');

$current_slider_color = get_option('ds_player_slider_color');

$styles = array('light-style', 'dark-style');

$templates = scandir(dirname( __FILE__ ) . '/templates/single_channel_templates/');

unset($templates[0]);

unset($templates[1]);

$current_style = get_option('ds_plugin_style');

$current_template = get_option('ds_channel_template');

$custom_css = get_option('ds_plugin_custom_css');

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

/** Fancy load **/

$fancy_load_option = get_option("ds_fancy_load");

$sel_yes = $fancy_load_option ? 'selected' : '';
$sel_no = !$fancy_load_option ? 'selected' : '';
$fancy_load = "<option value='1' $sel_yes>Yes</option><option value='0' $sel_no>No</option>";

/** End Fancy **/

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

					<tr><td>Flush and Rebuild</b><br/><span class='description'>Use this if you add videos through dostudioPRO</span></td><td><a class='button' href='<?php echo site_url().'/wp-admin/admin.php?page=dot-studioz-options&flush=1'; ?>'>Flush</a></td></tr>

					<tr><td>Facebook App ID</b><br/><span class='description'>For Facebook sharing and commenting to work properly you need a Facebook App Id. Don't have one? <a href="http://dotstudiopro.com" target="_blank">Click Here.</a></span></td><td><input type='text' name='ds_fb_app_id' value='<?php echo get_option('ds_fb_app_id') ?>' /></td></tr>

					<tr><td>Twitter Handle</td><td><input type='text' name='ds_twitter_handle' value='<?php echo get_option('ds_twitter_handle') ?>' /></td></tr>

					<tr><td>Comment Type<br/><span class='description'>Select which comment type will show on pages created by the plugin.</span></td><td><select name='ds_comment_type'><option value='facebook'>Facebook</option><option value='wordpress'>Wordpress</option><option value='none'>None</option></select></td></tr>

					<tr><td>Player Slider Color</td><td><select name='ds_player_slider_color'><?php echo $selector_colors ?></select></td></tr>

					<tr><td>Template</td><td><select name='ds_channel_template'><?php echo $template_list ?></select></td></tr>

					<tr><td>Template Color Style</td><td><select name='ds_plugin_style'><?php echo $template_styles ?></select></td></tr>

					<tr><td>Light Theme Shadow</td><td><select name='ds_light_theme_shadow'><option value='1' <?php echo get_option('ds_light_theme_shadow') == 1 ? 'selected="selected"' : ''; ?>>On</option><option value='0' <?php echo get_option('ds_light_theme_shadow') == 0 ? 'selected="selected"' : ''; ?>>Off</option></select></td></tr>

					<tr><td>Use FancyLoad Iframe Loader</td><td><select name='ds_fancy_load'><?php echo $fancy_load ?></select></td></tr>

					<tr><td>Copy plugin template files to my theme folder</b><br/><span class='description'>For custom template changes.</span></td><td><a class='button' href='<?php echo site_url().'/wp-admin/admin.php?page=dot-studioz-options&templatecopy=1'; ?>'>Copy</a></td></tr>

					<tr><td>Custom CSS</td><td><textarea name='ds_plugin_custom_css'><?php echo $custom_css ?></textarea></td></tr>

					<tr><td colspan=2><b>Development Options</b><br/><span class='description'>Please note: any options set here will override normal settings.  Please make sure to turn these settings off when you are done testing.</span></td></tr>

					<tr><td>Development Mode</td><td><input type='checkbox' name='ds_development_check' value='1' <?php echo get_option("ds_development_check") == 1 ? 'checked="checked"' : '' ?> /></td></tr>

					<tr><td>Development Country (Abbreviation)</td><td><input type='text' name='ds_development_country' value='<?php echo get_option("ds_development_country") ?>' /></td></tr>

					<tr><td>Reset Token on Save<br><span class='description'>Use in case you believe you have issues with token authentication.</span></td><td><input type='checkbox' name='ds_token_reset' value='1' <?php echo get_option("ds_token_reset") == 1 ? 'checked="checked"' : '' ?> /></td></tr>

					<input type='hidden' name='ds-save-admin-options' value='1' />

					</tbody>
				<tfoot>
					<tr><td colspan=2><button>Save</button></td></tr>
				</tfoot>
				</table>
		</form>


</div>

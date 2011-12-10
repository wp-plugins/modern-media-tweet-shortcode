<div class="wrap">
<h2>Embedded Tweet Settings</h2>

<?php 
if (count($errors)){
	?>
	<div class="error">
	<h3>Please correct the following error<?php if(count($errors) > 1) echo 's';?></h3>
	<ul>
	<?php 
	foreach ($errors as $id=>$error) {
		?>
		<li id="error-<?php echo $id?>"><?php echo $error?></li>
		<?php 
	}
	?>
	</ul>
	</div>
	<?php 
}
if (! empty($message)){
	?>
	<div class="message"><p class="message"><?php echo $message;?></p></div>
	<?php 
}
?>



<h3>Settings</h3>
<form method="post">
<?php 
wp_nonce_field(self::PLUGIN_NAMESPACE);
?>
<input type="hidden" name="submitting" value="1" />


<table class="widefat" style="width:50%">
<tbody>
<tr>
	<td style="text-align: right;">
		<input 
			type="checkbox"
			name="add_widgets_script"
			id="add_widgets_script"
			value="1"
			<?php if ($options->add_widgets_script) echo " checked=\"checked\" ";?>
		/>
	</td>
	<td>
		<label for="add_widgets_script">
			Add Twitter's <code>widgets.js</code> script tag in the WordPress header.
		</label>
		<br/><small>
		This script tag is required for your embedded tweets to
		pick up Twitter's tweet styling and functionality.  Check
		this box if the script at 
		<code>https://platform.twitter.com/widgets.js</code>
		is not already included by your theme or another plugin.
		</small>
	</td>

</tr>

<tr>
	<td style="text-align: right;">
		<label for="lang">
			Language:
		</label>
	</td>
	<td>
		<?php 
		$langs = require dirname(__FILE__) .  DIRECTORY_SEPARATOR . "langs.inc.php";
		?>
		<select name="lang" id="lang">
			<?php 
			foreach ($langs as $code=>$label){
				?>
				<option value="<?php echo esc_attr($code)?>"
					<?php if ($options->lang == $code) echo " selected=\"selected\" "?>
				><?php echo $label?></option>
				<?php 
			}
			?>
		
		</select>
		<br/><small>
		The language that you want the Twitter functionality
		(retweet button, etc.) to appear in.  (This doesn't
		translate the tweet.)
		</small>
	</td>

</tr>

<tr>
	<td style="text-align: right;">
		<input
			type="checkbox"
			name="eliminate_element_clear"
			id="eliminate_element_clear"
			value="1"
			<?php if ($options->eliminate_element_clear) echo " checked=\"checked\" ";?>
			
		/> 
		
	</td>
	<td>
		<label for="eliminate_element_clear">
			Eliminate element clearing.
		</label>
		<br/><small>
		Twitter's stylesheet clears the element, so that
		the tweet appears below floated elements appearing before
		it in the HTML.  Check this box if
		your tweets are showing up in the wrong place. 
		</small>
	</td>

</tr>

<tr>
	<td style="text-align: right;">
		<label for="maxwidth">
			Width:
		</label>
	</td>
	<td>
		<input
			type="text"
			name="maxwidth"
			id="maxwidth"
			size="3"
			value="<?php echo esc_attr($options->maxwidth)?>"
		/> px
		<br/><small>
		Set the maximum width of the rendered tweet
		in pixels.  Enter a number between 250 and 550.
		</small>
	</td>

</tr>


</tbody>
</table>

<?php 
submit_button("Save Changes");
?>
</form>

<h3>Clear Cache</h3>
<p>To save API calls and time this plugin 
caches HTML fetched from Twitter. Click below
to clear the cache (all tweets will
be reloaded.)</p>
<form method="post">
<?php 
wp_nonce_field(self::PLUGIN_NAMESPACE);
?>
<input type="hidden" name="clear_cache" value="1" />
<?php 
submit_button("Clear Cache");
?>
</form>

</div>

<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 	die('You are not allowed to call this page directly.'); }

global $flag, $flagdb, $post;
require_once (dirname(__FILE__) . '/get_skin.php');
$i_skins = get_skins();
$flag_custom = get_post_custom($post->ID);
$items_array = isset($flag_custom["mb_items_array"][0])? $flag_custom["mb_items_array"][0] : '';
$skinname = isset($flag_custom["mb_skinname"][0])? $flag_custom["mb_skinname"][0] : '';
$scode = isset($flag_custom["mb_scode"][0])? $flag_custom["mb_scode"][0] : '';
$button_text = isset($flag_custom["mb_button"][0])? $flag_custom["mb_button"][0] : '';
$button_link = isset($flag_custom["mb_button_link"][0])? $flag_custom["mb_button_link"][0] : '';
$bg_link = isset($flag_custom["mb_bg_link"][0])? $flag_custom["mb_bg_link"][0] : '';
$bg_pos = isset($flag_custom["mb_bg_pos"][0])? $flag_custom["mb_bg_pos"][0] : 'center center';
$bg_repeat = isset($flag_custom["mb_bg_repeat"][0])? $flag_custom["mb_bg_repeat"][0] : 'repeat';
$bg_size = isset($flag_custom["mb_bg_size"][0])? $flag_custom["mb_bg_size"][0] : 'auto';
?>
<script type="text/javascript">/*<![CDATA[*/
var i_arr = '<?php echo $items_array; ?>';
jQuery(document).ready(function() {
	if(i_arr){
		i_arr = i_arr.split(',');
		jQuery('#galleries :checkbox').each(function(){
			if(jQuery.inArray(jQuery(this).val(),i_arr) > -1){
				jQuery(this).prop('checked',true);
			}
		});
	} else {
		jQuery('#mb_items_array').val('all');
		jQuery('#galleries input[value="all"]').prop('checked',true).parent().siblings('.row').find('input').prop('checked', false);
	}
	var galleries = 'gid='+jQuery('#mb_items_array').val();
	var skin = jQuery('#mb_skinname option:selected').val();
	if(skin) skin = ' skin='+skin; else skin = '';
	short_code(galleries,skin);
	jQuery('#galleries :checkbox').click(function(){
		var cur, arr, del;
		if(jQuery(this).is(':checked')){
			cur = jQuery(this).val();
			if(cur == 'all') {
				jQuery(this).parent().siblings('.row').find('input').prop('checked', false);
				jQuery('#mb_items_array').val(cur);
			} else {
				jQuery('#galleries input[value="all"]').prop('checked', false);
				arr = jQuery('#mb_items_array').val();
				if(arr && arr != 'all') { del = ','; } else { arr = ''; del = ''; }
				jQuery('#mb_items_array').val(arr+del+cur);
			}
		} else {
			cur = jQuery(this).val();
			arr = jQuery('#mb_items_array').val().split(',');
			arr = jQuery.grep(arr, function(a){ return a != cur; }).join(',');
			if(arr) {
				jQuery('#mb_items_array').val(arr);
			} else {
				jQuery('#galleries input[value="all"]').prop('checked',true);
				jQuery('#mb_items_array').val('all');
			}
		}
		galleries = 'gid='+jQuery('#mb_items_array').val();
		short_code(galleries,skin);
	});
	jQuery('#mb_skinname').change(function(){
		skin = jQuery(this).val();
		if(skin) {
			skin = ' skin='+skin;
		} else {
			skin = '';
		}
		short_code(galleries,skin);
	});
});
function short_code(galleries,skin) {
	jQuery('#mb_scode').val('[flagallery '+galleries+' w=100% h=100%'+skin+' fullwindow=true]');
}
var current_image = '';
function send_to_editor(html){
    var source = html.match(/src=\".*\" alt/);
    source = source[0].replace(/^src=\"/, "").replace(/" alt$/, "");
    jQuery('#mb_bg_link').val(source);
    tb_remove();
}

/*]]>*/</script>
<div class="wrap">
<form id="generator1">
	<table border="0" cellpadding="4" cellspacing="0" style="width: 90%;">
        <tr>
           <td nowrap="nowrap" valign="top" style="width: 10%;"><div><?php _e("Select galleries", 'flash-album-gallery'); ?>:<span style="color:red;"> *</span><br /><small><?php _e("(album categories)", 'flash-album-gallery'); ?></small></div></td>
           <td valign="top"><div id="galleries" style="width: 214px; height: 160px; overflow: auto;">
                   <div class="row"><input type="checkbox" value="all" /> <strong>* - <?php _e("all galleries", 'flash-album-gallery'); ?></strong></div>
			<?php
				$gallerylist = $flagdb->find_all_galleries($flag->options['albSort'], $flag->options['albSortDir']);
				if(is_array($gallerylist)) {
					foreach($gallerylist as $gallery) {
						$name = ( empty($gallery->title) ) ? $gallery->name : $gallery->title;
						if($flag->options['albSort'] == 'gid'){ $name = $gallery->gid.' - '.$name; }
						if($flag->options['albSort'] == 'title'){ $name = $name.' ('.$gallery->gid.')'; }
						echo '<div class="row"><input type="checkbox" value="' . $gallery->gid . '" /> <span>' . $name . '</span></div>' . "\n";
					}
				}
			?>
           </div></td>
        </tr>
        <tr>
           <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><?php _e("Galleries order", 'flash-album-gallery'); ?>: &nbsp; </p></td>
           <td valign="top"><p><input readonly="readonly" type="text" id="mb_items_array" name="mb_items_array" value="<?php echo $items_array; ?>" style="width: 98%;" /></p></td>
        </tr>
        <tr>
            <td nowrap="nowrap" valign="top"><p style="padding-top:3px;"><label for="mb_skinname"><?php _e("Choose skin", 'flash-album-gallery'); ?>:</label></p></td>
            <td valign="top"><p><select id="mb_skinname" name="mb_skinname">
                    <option value="" <?php selected($skinname,''); ?>><?php _e("skin active by default", 'flash-album-gallery'); ?></option>
<?php
	foreach ( (array)$i_skins as $skin_file => $skin_data) {
		echo '<option value="'.dirname($skin_file).'" '.selected($skinname,dirname($skin_file),false).'>'.$skin_data['Name'].'</option>'."\n";
	}
?>
            </select></p>
			<input id="mb_scode" name="mb_scode" type="hidden" style="width: 98%;"  value="<?php echo $scode; ?>" />
			</td>
        </tr>
		<tr>
			<td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Back Button Text", 'flash-album-gallery'); ?>: &nbsp; </div></td>
            <td valign="top"><input id="mb_button" name="mb_button" type="text" style="width: 49%;" placeholder="<?php _e('Go Back', 'flash-album-gallery'); ?>" value="<?php echo $button_text; ?>" /></td>
		</tr>
		<tr>
			<td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Back Button Link", 'flash-album-gallery'); ?>: &nbsp; </div></td>
            <td valign="top"><input id="mb_button_link" name="mb_button_link" type="text" style="width: 49%;" placeholder="<?php echo home_url(); ?>" value="<?php echo $button_link; ?>" /><br />
				<small><?php _e("Leave empty to use referer link", 'flash-album-gallery'); ?></small></td>
		</tr>
		<tr>
			<td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Background Image Link", 'flash-album-gallery'); ?>: &nbsp; </div></td>
            <td valign="top">
                <input id="mb_bg_link" name="mb_bg_link" type="text" style="width: 49%;"  value="<?php echo $bg_link; ?>" />
                <a class="thickbox" href="media-upload.php?type=image&amp;TB_iframe=1&amp;width=640&amp;height=400" title="<?php _e('Add an Image', 'flash-album-gallery'); ?>"><?php _e('assist', 'flash-album-gallery'); ?></a>
            </td>
		</tr>
        <tr>
            <td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Background Position", 'flash-album-gallery'); ?>:</div></td>
            <td valign="top"><select id="mb_bg_pos" name="mb_bg_pos">
                    <option value="center center" <?php selected($bg_pos,'center center'); ?>>center center</option>
                    <option value="left top" <?php selected($bg_pos,'left top'); ?>>left top</option>
                    <option value="left center" <?php selected($bg_pos,'left center'); ?>>left center</option>
                    <option value="left bottom" <?php selected($bg_pos,'left bottom'); ?>>left bottom</option>
                    <option value="center top" <?php selected($bg_pos,'center top'); ?>>center top</option>
                    <option value="center bottom" <?php selected($bg_pos,'center bottom'); ?>>center bottom</option>
                    <option value="right top" <?php selected($bg_pos,'right top'); ?>>right top</option>
                    <option value="right center" <?php selected($bg_pos,'right center'); ?>>right center</option>
                    <option value="right bottom" <?php selected($bg_pos,'right bottom'); ?>>right bottom</option>
            </select></td>
        </tr>
        <tr>
            <td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Background Repeat", 'flash-album-gallery'); ?>:</div></td>
            <td valign="top"><select id="mb_bg_repeat" name="mb_bg_repeat">
                    <option value="repeat" <?php selected($bg_repeat,'repeat'); ?>>repeat</option>
                    <option value="repeat-x" <?php selected($bg_repeat,'repeat-x'); ?>>repeat-x</option>
                    <option value="repeat-y" <?php selected($bg_repeat,'repeat-y'); ?>>repeat-y</option>
                    <option value="no-repeat" <?php selected($bg_repeat,'no-repeat'); ?>>no-repeat</option>
            </select></td>
        </tr>
        <tr>
            <td nowrap="nowrap" valign="top"><div style="padding-top: 3px;"><?php _e("Background Size", 'flash-album-gallery'); ?>:</div></td>
            <td valign="top"><select id="mb_bg_size" name="mb_bg_size">
                    <option value="auto" <?php selected($bg_size,'auto'); ?>>auto</option>
                    <option value="contain" <?php selected($bg_size,'contain'); ?>>contain</option>
                    <option value="cover" <?php selected($bg_size,'cover'); ?>>cover</option>
            </select></td>
        </tr>
    </table>
</form>
</div>

<?php
if ( ! function_exists( 'sanitize_flagname' ) ) {
	function sanitize_flagname( $filename ) {

		//$filename = wp_strip_all_tags( $filename );
		//$filename = remove_accents( $filename );
		// Kill octets
		$filename = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $filename );
		$filename = preg_replace( '/&.+?;/', '', $filename ); // Kill entities
		$filename = preg_replace( '|[^a-zA-Z0-9 _.\-]|i', '', $filename );
		$filename = preg_replace( '/[\s-]+/', '-', $filename );
		$filename = trim( $filename, '.-_ ' );

		return $filename;
	}
}

function flag_skin_options( $act_skin ) {
	$flag_options = get_option( 'flag_options' );

    if(isset($_POST['skin']) && !isset($_POST['reset_skin_settings'])){
        check_admin_referer('skin_settings');

        $skin_options     = $_POST['skin'];
        $skin_options_key = "{$act_skin}_options";
        array_walk_recursive($skin_options, 'esc_attr');
        $flag_options[ $skin_options_key ] = $skin_options;
        // Save options
        update_option('flag_options', $flag_options);
        flagGallery::show_message(__('Update Successfully', 'flash-album-gallery'));
    }

    /**
	 * @var $options_tree
	 * @var $default_options
	 */
	include $flag_options['skinsDirABS'] . $act_skin . '/settings.php';
	$gallery_settings = array();
    if(isset($_POST['reset_skin_settings'])){
        $gallery_settings = array();
        flagGallery::show_message(__('All settings fields reset to default. Click Update button to save default settings.', 'flash-album-gallery'));
    } elseif(isset($flag_options["{$act_skin}_options"])){
        $gallery_settings = maybe_unserialize( $flag_options["{$act_skin}_options"] );
    }
	?>
	<form id="skinOptions" class="wp-core-ui" method="post" style="overflow:hidden; max-width:700px; background-color:#f1f1f1; padding: 20px;">
		<?php
		if ( isset( $options_tree ) ) {
			flag_skin_options_fieldset( $options_tree, $default_options, $gallery_settings );
		}
		wp_nonce_field( 'skin_settings' );
		?>
		<div class="textright" style="padding: 0 10px;">
            <button type="submit" class="button button-secondary" name="reset_skin_settings"><?php _e( 'Reset to Default', 'flash-album-gallery' ) ?></button>
            &nbsp;&nbsp;&nbsp;
            <button type="submit" class="button button-primary"><?php _e( 'Update', 'flash-album-gallery' ) ?></button>
        </div>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#skinOptions [data-type="color"]').wpColorPicker();
			});
		</script>
	</form>
	<?php
}

function flag_xml2array( $xmlObject, $out = array() ) {
	foreach ( (array) $xmlObject as $index => $node ) {
		$out[ $index ] = ( is_object( $node ) || is_array( $node ) ) ? flag_xml2array( $node ) : $node;
	}

	return $out;
}

/**
 * @param       $options_tree
 * @param       $default
 * @param array $value
 */
function flag_skin_options_fieldset( $options_tree, $default, $value = array() ) {
	$i = 0;
	foreach ( $options_tree as $section ) {
		$i ++;
		$pane_class = 'tab-pane';
		?>
		<fieldset id="gallery_settings<?php echo $i; ?>" class="<?php echo $pane_class; ?>"
		          style="margin: 20px 0;padding:10px;">
			<legend><?php echo $section['label']; ?>
				<button type="submit" class="button button-primary button-small alignright"><?php _e( 'Update', 'flash-album-gallery' ) ?></button>
			</legend>
			<?php
			foreach ( $section['fields'] as $name => $field ) {
				if ( 'textblock' == $field['tag'] ) {
					$args = array(
						'id' => $name,
						'field' => $field,
					);
				} else {
					if ( isset( $section['key'] ) ) {
						$key = $section['key'];
						if ( ! isset( $default[ $key ][ $name ] ) ) {
							$default[ $key ][ $name ] = false;
						}
						$val = isset( $value[ $key ][ $name ] ) ? $value[ $key ][ $name ] : $default[ $key ][ $name ];
						$args = array(
							'id' => strtolower( "{$key}_{$name}" ),
							'name' => "skin[{$key}][{$name}]",
							'field' => $field,
							'value' => $val,
							'default' => $default[ $key ][ $name ],
						);
					} else {
						if ( ! isset( $default[ $name ] ) ) {
							$default[ $name ] = false;
						}
						$val = isset( $value[ $name ] ) ? $value[ $name ] : $default[ $name ];
						$args = array(
							'id' => strtolower( $name ),
							'name' => "skin[{$name}]",
							'field' => $field,
							'value' => $val,
							'default' => $default[ $name ],
						);
					}
				}
				flag_skin_options_formgroup( $args );
			}
			?>
		</fieldset>
		<?php
	}
}

/**
 * @param $args
 */
function flag_skin_options_formgroup( $args ) {
	/**
	 * @var $id
	 * @var $name
	 * @var $field
	 * @var $value
	 * @var $default
	 */
	extract( $args );
	if ( 'input' == $field['tag'] ) {
		?>
		<div class="form-group" id="div_<?php echo $id; ?>">
			<label><?php echo $field['label']; ?></label>
			<input <?php echo $field['attr']; ?> id="<?php echo $id; ?>" class="form-control input-sm"
			                                     name="<?php echo $name; ?>" value="<?php echo esc_attr( $value ); ?>"
			                                     data-value="<?php echo $default; ?>"
			                                     placeholder="<?php echo $default; ?>"/>
			<?php if ( ! empty( $field['text'] ) ) {
				echo "<p class='help-block'>{$field['text']}</p>";
			} ?>
		</div>
	<?php } elseif ( 'checkbox' == $field['tag'] ) { ?>
		<div class="form-group" id="div_<?php echo $id; ?>">
			<div class="checkbox">
				<input type="hidden" name="<?php echo $name; ?>" value="0"/>
				<label><input type="checkbox" <?php echo $field['attr']; ?> id="<?php echo $id; ?>"
				              name="<?php echo $name; ?>" value="1"
				              data-value="<?php echo $default; ?>" <?php echo checked( $value, '1' ); ?>/> <?php echo $field['label']; ?>
				</label>
				<?php if ( ! empty( $field['text'] ) ) {
					echo "<p class='help-block'>{$field['text']}</p>";
				} ?>
			</div>
		</div>
	<?php } elseif ( 'select' == $field['tag'] ) { ?>
		<div class="form-group" id="div_<?php echo $id; ?>">
			<label><?php echo $field['label']; ?></label>
			<select <?php echo $field['attr']; ?> id="<?php echo $id; ?>" class="form-control input-sm"
			                                      name="<?php echo $name; ?>" data-value="<?php echo $default; ?>">
				<?php foreach ( $field['choices'] as $choice ) { ?>
					<option value="<?php esc_attr_e( $choice['value'] ); ?>" <?php echo selected( $value, $choice['value'] ); ?>><?php echo $choice['label']; ?></option>
				<?php } ?>
			</select>
			<?php if ( ! empty( $field['text'] ) ) {
				echo "<p class='help-block'>{$field['text']}</p>";
			} ?>
		</div>
	<?php } elseif ( 'textarea' == $field['tag'] ) { ?>
		<div class="form-group" id="div_<?php echo $id; ?>">
			<label><?php echo $field['label']; ?></label>
			<textarea <?php echo $field['attr']; ?> id="<?php echo $id; ?>" class="form-control input-sm"
			                                        name="<?php echo $name; ?>"><?php echo esc_textarea( $value ); ?></textarea>
			<?php if ( ! empty( $field['text'] ) ) {
				echo "<p class='help-block'>{$field['text']}</p>";
			} ?>
		</div>
	<?php } elseif ( 'textblock' == $field['tag'] ) { ?>
		<div class="text-block">
			<?php echo $field['label']; ?>
			<?php echo $field['text']; ?>
		</div>
	<?php } ?>
	<?php
}

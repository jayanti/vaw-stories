<?php
/**
 * tenderSpring Theme Options
 *
 * @package tenderSpring
 * @since tenderSpring 1.0
 */

/**
 * Register the form setting for our tenderSpring_options array.
 *
 * This function is attached to the admin_init action hook.
 *
 * This call to register_setting() registers a validation callback, tenderSpring_theme_options_validate(),
 * which is used when the option is saved, to ensure that our option values are complete, properly
 * formatted, and safe.
 *
 * We also use this function to add our theme option if it doesn't already exist.
 *
 * @since tenderSpring 1.0
 */

function tenderSpring_theme_options_init() {

	// If we have no options in the database, let's add them now.
	if ( false === tenderSpring_get_theme_options() )
		add_option( 'tenderSpring_theme_options', tenderSpring_get_default_theme_options() );

	register_setting(
		'tenderSpring_options',       // Options group, see settings_fields() call in tenderSpring_theme_options_render_page()
		'tenderSpring_theme_options', // Database option, see tenderSpring_get_theme_options()
		'tenderSpring_theme_options_validate' // The sanitization callback, see tenderSpring_theme_options_validate()
	);

	// Register our settings field group
	add_settings_section(
		'general', // Unique identifier for the settings section
		'', // Section title (we don't want one)
		'__return_false', // Section callback (we don't want anything)
		'theme_options' // Menu slug, used to uniquely identify the page; see tenderSpring_theme_options_add_page()
	);

	/* Register our individual settings fields */

	add_settings_field( 'custom_css', __( 'Custom CSS', 'tenderSpring' ), 'tenderSpring_settings_field_custom_css', 'theme_options', 'general' );

	add_settings_field(
		'support', // Unique identifier for the field for this section
		__( 'Support tenderSpring', 'tenderSpring' ), // Setting field label
		'tenderSpring_settings_field_support', // Function that renders the settings field
		'theme_options', // Menu slug, used to uniquely identify the page; see _s_theme_options_add_page()
		'general' // Settings section. Same as the first argument in the add_settings_section() above
	);

}
add_action( 'admin_init', 'tenderSpring_theme_options_init' );

/**
 * Change the capability required to save the 'tenderSpring_options' options group.
 *
 * @see tenderSpring_theme_options_init() First parameter to register_setting() is the name of the options group.
 * @see tenderSpring_theme_options_add_page() The edit_theme_options capability is used for viewing the page.
 *
 * @param string $capability The capability used for the page, which is manage_options by default.
 * @return string The capability to actually use.
 */
function tenderSpring_option_page_capability( $capability ) {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_tenderSpring_options', 'tenderSpring_option_page_capability' );

/**
 * Add our theme options page to the admin menu, including some help documentation.
 *
 * This function is attached to the admin_menu action hook.
 *

 */
function tenderSpring_theme_options_add_page() {
	$theme_page = add_theme_page(
		__( 'Theme Options', 'tenderSpring' ),   // Name of page
		__( 'Theme Options', 'tenderSpring' ),   // Label in menu
		'edit_theme_options',                    // Capability required
		'theme_options',                         // Menu slug, used to uniquely identify the page
		'tenderSpring_theme_options_render_page' // Function that renders the options page
	);

	if ( ! $theme_page )
		return;
}
add_action( 'admin_menu', 'tenderSpring_theme_options_add_page' );


/**
 * Returns the default options for tenderSpring.
 *

 */
function tenderSpring_get_default_theme_options() {
	$default_theme_options = array(
		'custom_css' => '',
		'support' => 0
	);

	return apply_filters( 'tenderSpring_default_theme_options', $default_theme_options );
}

/**
 * Returns the options array for tenderSpring.
 *

 */
function tenderSpring_get_theme_options() {
	return get_option( 'tenderSpring_theme_options', tenderSpring_get_default_theme_options() );
}

/**
 * Renders the Theme Style setting field.
 *

 */
function tenderSpring_settings_field_theme_style() {
	$options = tenderSpring_get_theme_options();

	foreach ( tenderSpring_theme_style() as $button ) {
	?>
	<div class="layout">
		<label class="description">
			<img src="<?php echo get_template_directory_uri() ?>/images/ss/<?php echo $button['value']; ?>.png" alt="<?php echo $button['label']; ?> Style" /><br />
			<input type="radio" name="tenderSpring_theme_options[theme_style]" value="<?php echo esc_attr( $button['value'] ); ?>" <?php checked( $options['theme_style'], $button['value'] ); ?> />
			<?php echo $button['label']; ?>
		</label>
	</div>
	<?php
	}
}


/**
 * Renders the Custom CSS setting field.
 *

 */
function tenderSpring_settings_field_custom_css() {
	$options = tenderSpring_get_theme_options();
	?>
	<textarea class="large-text" type="text" name="tenderSpring_theme_options[custom_css]" id="custom_css" cols="50" rows="10" /><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
	<label class="description" for="custom_css"><?php _e( 'Add any custom CSS rules here so they will persist through theme updates.', 'tenderSpring' ); ?></label>
	<?php
}

/**
 * Renders the Support setting field.
 */
function tenderSpring_settings_field_support() {
	$options = tenderSpring_get_theme_options();

	if ( $options['support'] !== 'on' || !isset( $options['support'] ) ) {

	?>
	<label>
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=PUKAB93RNE83S" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="PayPal - The safer, easier way to pay online!" class="alignright"></a>

		<?php _e( 'If you enjoy my themes, please consider making a secure donation using the PayPal button to your right. Anything is appreciated!', 'tenderSpring' ); ?>

		<br /><input type="checkbox" name="tenderSpring_theme_options[support]" id="support" <?php checked( 'on', $options['support'] ); ?> />
		<label class="description" for="support">
			<?php _e( 'No, thank you! Dismiss this message.', 'tenderSpring' ); ?>
		</label>
	</label>
	<?php
	}
	else { ?>
		<label class="description" for="support">
			<?php _e( 'Hide Donate Button', 'tenderSpring' ); ?>
		</label>
		<input type="checkbox" name="tenderSpring_theme_options[support]" id="support" <?php checked( 'on', $options['support'] ); ?> />

	</td>

	<?php
	}

}

/**
 * Returns the options array for tenderSpring.
 *

 */
function tenderSpring_theme_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Options', 'tenderSpring' ), wp_get_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'tenderSpring_options' );
				do_settings_sections( 'theme_options' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}


/**
 * Enqueue theme options styles
 */
function tenderSpring_admin_layout_styles( $hook_suffix ) {

	if ( $hook_suffix != 'appearance_page_theme_options' )
		return; ?>

	<style type="text/css">
		.layout .description { width: 300px; float: left; text-align: center; margin-bottom: 10px; padding: 10px; }
	</style>

<?php

}

add_action( 'admin_enqueue_scripts', 'tenderSpring_admin_layout_styles' );


/**
 * Returns layout defaults
 */
function tenderSpring_get_layout_defaults() {
	$options = tenderSpring_get_theme_options();
	$theme_style = $options['theme_style'];

	$default_theme_options = tenderSpring_get_default_theme_options();
	$default_theme_style = $default_theme_options['theme_style'];

	$theme_style_values = tenderSpring_theme_style();

	if ( $theme_style ) {
		$defaults = $theme_style_values[$theme_style]['defaults'];
	} else {
		$defaults = $theme_style_values[$default_theme_style]['defaults'];
	}

	return apply_filters( 'tenderSpring_get_layout_defaults', $defaults );
}


/**
 * Sanitize and validate form input. Accepts an array, return a sanitized array.
 *
 * @see tenderSpring_theme_options_init()
 * @todo set up Reset Options action
 *

 */
function tenderSpring_theme_options_validate( $input ) {
	$output = $defaults = tenderSpring_get_default_theme_options();

	// The sample Theme Styles value must be in our array of Theme Styles values
	if ( isset( $input['theme_style'] ) && array_key_exists( $input['theme_style'], tenderSpring_theme_style() ) )
		$output['theme_style'] = $input['theme_style'];

	// The Support field should either be on or off
	if ( ! isset( $input['support'] ) )
		$input['support'] = 'off';
	$output['support'] = ( $input['support'] == 'on' ? 'on' : 'off' );

	// The Custom CSS must be safe text with the allowed tags for posts
	if ( isset( $input['custom_css'] ) )
		$output['custom_css'] = wp_filter_nohtml_kses($input['custom_css'] );

	return apply_filters( 'tenderSpring_theme_options_validate', $output, $input, $defaults );
}
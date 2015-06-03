<?php
	class JW_Fancy_Color {
		const VERSION = '0.2.0';
		public function hooks() {
			add_action( 'cmb2_render_rgba_colorpicker', array( $this, 'render_color_picker' ), 10, 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
		}
		public function render_color_picker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
			echo $field_type_object->input( array(
				'class'              => 'cmb2-colorpicker color-picker',
				'data-default-color' => $field->args( 'default' ),
				'data-alpha'         => 'true',
			) );
		}
		public function setup_admin_scripts() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'jw-cmb2-rgba-picker-js', plugin_dir_url( __FILE__ ) . '/js/jw-cmb2-rgba-picker.js', array( 'wp-color-picker' ), self::VERSION, true );
		}
	}
	$jw_fancy_color = new JW_Fancy_Color();
	$jw_fancy_color->hooks();
?>
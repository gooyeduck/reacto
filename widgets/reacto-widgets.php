<?php

if ( ! class_exists( 'Widget_Base' ) ) {
	class Elementor_Reacto_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'reacto_widget';
		}

		public function get_title() {
			return esc_html__( 'Reacto Widget', 'elementor-addon' );
		}

		public function get_icon() {
			return 'eicon-nerd';
		}

		public function get_categories() {
			return ['basic'];
		}

		public function get_keywords() {
			return ['reacto'];
		}

		protected function render() {

			$shortcode = do_shortcode( shortcode_unautop( '[custom_reactions]' ) );
			?>
				<div class="elementor-shortcode"><?php echo $shortcode; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?></div>
			<?php
		}
	}
}
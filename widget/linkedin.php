<?php

/**
 * EazyMatchLinkedinWidget Class
 */
class emol_widget_linkedin extends emol_widget {
	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - LinkedIN' );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		global $trailingData;
		//get arguments
		extract( $args );

		//set title var
		$title = '';

		//get emol
		$api = eazymatch_connect();

		//get instance
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}


		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}


		echo "<div class=\"emol_widget clearfix\" id=\"eazymatch_linkedin_widget\">";

		/**
		 * logged on part
		 */
		if ( ! emol_session::isValidId( 'applicant_id' ) ) {
			//url applying

			$url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/0/open/';
			echo $instance['text'];
			echo '<img src="https://linkedin.eazymatch.cloud/connect-to-linkedin.png" class="emol-linkedin-logo" onclick="emol_connect_linkedin(\'' . $url . '\',\'' . $api->instanceName . '\');" />
        ';
		} else {
			echo EMOL_ALREADY_ONBOARD;
		}

		echo "</div>";
		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['text']  = strip_tags( $new_instance['text'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		} else {
			$title = '';
		}
		if ( isset( $instance['text'] ) ) {
			$text = esc_attr( $instance['text'] );
		} else {
			$text = '';
		}
		?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                                                name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                                                value="<?php echo $title; ?>"/></label>

            <label for="<?php echo $this->get_field_id( 'text' ); ?>">
				<?php _e( 'Text:' ); ?> <textarea class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>"
                                                  name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo $text; ?></textarea>

        </p>
		<?php
	}

}

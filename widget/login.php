<?php

/**
 * EazyMatchLoginWidget Class
 */
class emol_widget_login extends emol_widget {
	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_LOGIN );
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


		echo "<div class=\"emol_widget clearfix\" id=\"eazymatch_login_widget\">";

		/**
		 * logged on part
		 */
		if ( emol_session::isValidId( 'company_id' ) ) {
			$userInfo = $api->person->getCurrent();
			emol_view_show( 'applicant/account/widget/logedin.contact.php', array(
				'userInfo'     => $userInfo,
				'trailingData' => $trailingData
			) );
		} elseif ( emol_session::isValidId( 'applicant_id' ) ) {
			$userInfo = $api->person->getCurrent();
			$pic      = $api->person->getPicture( $userInfo['id'] );

			emol_view_show( 'applicant/account/widget/logedin.applicant.php', array(
				'userInfo'     => $userInfo,
				'trailingData' => $trailingData,
				'pic'          => $pic
			) );
		} else {
			emol_view_show( 'applicant/account/widget/login.php' );
		}

		echo "</div>";
		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		} else {
			$title = '';
		}
		?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                                                name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                                                value="<?php echo $title; ?>"/></label></p>
		<?php
	}

}

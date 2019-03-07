<?php

/**
 * emol_widget_job_tags Class
 *
 * a random published selection of competences
 */
class emol_widget_job_tags extends emol_widget {
	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_TAGS );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		//get arguments
		extract( $args );

		//check slases etc
		global $trailingData;

		//set title var
		$title = '';

		//get emol
		$api = eazymatch_connect();

		//get instance
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}

		//get instance
		$pretext = '';
		if ( isset( $instance['pretext'] ) ) {
			$pretext = $instance['pretext'];
		}

		//get instance
		$limit = 10;
		if ( isset( $instance['limit'] ) ) {
			$limit = (int) $instance['limit'];
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="emol_widget" id="emol_tag_widget">';

		$filterOptions = emol_jobfilter_factory::createDefault()->getFilterArray();

		try {
			$cpt            = $api->get( 'job' );
			$competenceList = $cpt->getPublishedCompetenceTree( $filterOptions );
			// var_dump($jobs);
		} catch ( SoapFault $e ) {
			eazymatch_trow_error( 'Fout in SOAP request EazyMatch -> jobs' );
			die();
		}

		foreach ( $competenceList as $cl ) {
			foreach ( $cl['children'] as $cl2 ) {
				if ( count( $cl2['children'] ) > 0 ) {
					echo '<div class="emol-tag-wrapper tag-group-' . $cl2['id'] . '"><h6 class="emol-tag-header">' . htmlentities( $cl2['name'] ) . '</h6>';
					$i = 0;
					foreach ( $cl2['children'] as $cl3 ) {
						if ( $i >= $limit ) {
							continue;
						}
						$i ++;
						echo '<a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/competence,' . $cl3['id'] . $trailingData . '" class="emol-tagcloud-' . rand( 1, 6 ) . '">' . $pretext . ' ' . htmlentities( $cl3['name'] ) . '</a> ';
					}
					echo "</div>";
				}
			}
		}

		//echo $text;

		echo '</div>';

		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance            = $old_instance;
		$instance['pretext'] = $new_instance['pretext'];
		$instance['title']   = strip_tags( $new_instance['title'] );
		$instance['limit']   = (int) ( $new_instance['limit'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		} else {
			$title = '';
		}

		if ( isset( $instance['pretext'] ) ) {
			$pretext = esc_attr( $instance['pretext'] );
		} else {
			$pretext = '';
		}

		if ( isset( $instance['limit'] ) ) {
			$limit = esc_attr( $instance['limit'] );
		} else {
			$limit = 10;
		}
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo $title; ?>"/></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>"
                       name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text"
                       value="<?php echo $limit; ?>"/></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'pretext' ); ?>"><?php _e( 'Pre-text:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'pretext' ); ?>"
                       name="<?php echo $this->get_field_name( 'pretext' ); ?>" type="text"
                       value="<?php echo $pretext; ?>"/></label>
        </p>
		<?php
	}

} // class emol_widget_job_tags

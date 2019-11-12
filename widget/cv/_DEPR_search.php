<?php

/**
 * Widget CV Class
 *
 *
 *
 *    DEPRECATED
 *
 *
 *    USE widget_search
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
class emol_widget_cv_search extends emol_widget {

	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_CVSEARCH );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		global $emol_side;
		global $trailingData;

		extract( $args );
		$title = '';
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}

		$reset = '';
		if ( isset( $instance['reset'] ) ) {
			$reset = apply_filters( 'widget_reset', $instance['reset'] );
		}

		$searchLabel = '';
		if ( isset( $instance['searchLabel'] ) ) {
			$searchLabel = apply_filters( 'widget_searchLabel', $instance['searchLabel'] );
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}


		try {
			$api = eazymatch_connect();

			//check if there are any filters,
			//if so, also filter the lists on only jobs that have this
			$filterOptions = array();
			$filterStatus  = unserialize( get_option( 'emol_filter_app_options' ) );
			if ( is_array( $filterStatus ) && count( $filterStatus ) > 0 ) {
				foreach ( $filterStatus as $status ) {
					$filterOptions[] = (int) $status;
				}
			}
			$cpt            = $api->get( 'applicant' );
			$competenceList = $cpt->getPublishedApplicantCompetence( $filterOptions );

		} catch ( SoapFault $e ) {
			eazymatch_trow_error( 'EazyMatch fout.' );
			if ( $emol_isDebug ) {
				var_dump( $e );
			}
		}

		$lists = array();

		$setUrl = get_option( 'emol_cv_search_url' );


		if ( count( $competenceList ) > 0 ) {
			$lists = new emol_Level2Listboxes( $competenceList, $setUrl, 'emol-search-cv-competence' );
		} else {
			$lists = array();
		}

		?>
        <div class="emol-widget emol-search-widget" id="emol-search-cv-widget">
            <input type="hidden" id="baseUrlCV" value="<?php echo $setUrl ?>">

            <div class="emol-free-cv-search">
                <label for="emol-free-search-cv-input"><?php echo EMOL_WIDGET_FREE_SEARCH ?></label>

                <div id="emol-free-cv-input">
                    <input type="text" value="<?php echo urldecode( emol_session::get( 'freeSearch' ) ) ?>"
                           class="emol-text-cv-input" name="emol-free-cv-search" id="emol-free-search-cv-input"/>
                </div>
            </div>
			<?php
			//checked values
			$val5  = '';
			$val10 = '';
			$val15 = '';
			$val25 = '';
			$val50 = '';

			//range
			switch ( urldecode( emol_session::get( 'locationSearchRange' ) ) ) {
				case '5':
					$val5 = 'selected="selected"';
					break;
				case '10':
					$val10 = 'selected="selected"';
					break;
				case '15':
					$val15 = 'selected="selected"';
					break;
				case '25':
					$val25 = 'selected="selected"';
					break;
				case '50':
					$val50 = 'selected="selected"';
					break;
			}
			?>
            <div class="emol-location-search">
                <label for="emol-zipcode-search-cv-input"><?php echo EMOL_WIDGET_LOCATION_SEARCH ?></label>
                <input type="text" value="<?php echo urldecode( emol_session::get( 'locationSearchZipcode' ) ) ?>"
                       class="emol-text-cv-input" name="emol-zipcode-cv-search" id="emol-zipcode-search-cv-input"/>
                <select class="emol-text-input" name="emol-range-cv-search" id="emol-range-search-cv-input">
                    <option value="5" <?php echo $val5 ?>><?php echo EMOL_KM ?></option>
                    <option value="10" <?php echo $val10 ?>><?php echo EMOL_KM ?></option>
                    <option value="15" <?php echo $val15 ?>><?php echo EMOL_KM ?></option>
                    <option value="25" <?php echo $val25 ?>><?php echo EMOL_KM ?></option>
                    <option value="50" <?php echo $val50 ?>><?php echo EMOL_KM ?></option>
                </select>
            </div>
            <hr/>
			<?php

			if ( isset( $lists->lists ) ) {
				echo $lists->lists;
			}
			$base =  get_option( 'emol_job_search_page' ) ? get_option( 'emol_job_search_page' ) : get_option( 'emol_job_search_url' ) . '/all';

			?>
            <div class="emol-submit-wrapper">
                <span class="emol-reset-button">
                    <a href="<?php echo $base ?>/<?php echo $trailingData ?>"
                       class="emol-altbutton emol-button-reset"><?php echo $reset ?></a>
                </span>
                <button onclick="emolSearchCv();"
                        class="emol-button emol-button-search"><?php echo $searchLabel ?></button>
            </div>
        </div>

		<?php
		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['reset']       = strip_tags( $new_instance['reset'] );
		$instance['searchLabel'] = strip_tags( $new_instance['searchLabel'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		}
		if ( isset( $instance['reset'] ) ) {
			$reset = esc_attr( $instance['reset'] );
		}
		if ( isset( $instance['searchLabel'] ) ) {
			$searchLabel = esc_attr( $instance['searchLabel'] );
		}
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo $title; ?>"/>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'searchLabel' ); ?>"><?php _e( 'Label:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'searchLabel' ); ?>"
                       name="<?php echo $this->get_field_name( 'searchLabel' ); ?>" type="text"
                       value="<?php echo $searchLabel; ?>"/>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'reset' ); ?>"><?php _e( 'Reset:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'reset' ); ?>"
                       name="<?php echo $this->get_field_name( 'reset' ); ?>" type="text"
                       value="<?php echo $reset; ?>"/>
            </label>
        </p>
		<?php
	}

}

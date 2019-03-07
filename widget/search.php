<?php

/**
 * Widget Class
 */
class emol_widget_search extends emol_widget {

	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_SEARCH );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		global $emol_side;

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

		$locationSearchEnabled = true;
		if ( isset( $instance['locationSearchEnabled'] ) ) {
			$locationSearchEnabled = $instance['locationSearchEnabled'];
		}

		$checkBoxSearch = false;
		if ( isset( $instance['checkBoxSearch'] ) ) {
			$checkBoxSearch = $instance['checkBoxSearch'];
		}


		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}


		$showProvince = $setUrl = get_option( 'emol_cv_search_url' );

		// type get filters for jobs/applicants
		$filters = emol_jobfilter_factory::createDefault()->getFilterArray();

		// make sure EazyMatch connection is initialized
		eazymatch_connect();

		$trunk = new EazyTrunk();

		$provinceList = array();

		if ( $emol_side == 'company' ) {
			$competenceList = &$trunk->request( 'competence', 'getPublishedTree', array( false, true ) );
		} else {
			$competenceList = &$trunk->request( 'job', 'getPublishedCompetenceTree', array( $filters ) );

			if ( $locationSearchEnabled ) {
				$provinceList = &$trunk->request( 'job', 'getPublishedProvinces', array( $filters ) );
			}
		}

		// execute the trunk request
		$trunk->execute();

		$lists = array();

		if ( $emol_side == 'company' ) {
			$setUrl = get_option( 'emol_cv_search_url' );
		} else {
			$setUrl = get_option( 'emol_job_search_url' );
		}

		if ( count( $competenceList ) > 0 ) {
			if ( $checkBoxSearch == 1 ) {
				$lists = new emol_Level2Checkboxes( $competenceList, $setUrl, 'search_competences_checkboxes' );
			} else {
				$lists = new emol_Level2Listboxes( $competenceList, $setUrl, 'search_competences' );
			}
		} else {
			$lists = array();
		}

		echo '<div class="emol_widget" id="emol_search_widget">';

		//check multi slugs
		$completeBase = explode( '/', get_bloginfo( 'wpurl' ) );
		if ( count( $completeBase ) > 3 ) {
			$setUrl = array_pop( $completeBase ) . '/' . $setUrl;
		}

		echo '<form onsubmit="emolSearch(\'' . $setUrl . '\'); return false;">';

		echo '<div class="emol-free-search">
            <label for="emol-free-search-input">' . EMOL_WIDGET_FREE_SEARCH . '</label>
            <div id="emol-free-input">
                    <input type="text" value="' . urldecode( emol_session::get( 'freeSearch' ) ) . '" class="emol-text-input noautosubmit" name="emol-free-search" id="emol-free-search-input" />
            </div>
        </div>';

		if ( $locationSearchEnabled == true ) {
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

			//selectbox for range
			$rangeBox = '<select class="emol-text-input" name="emol-range-search" id="emol-range-search-input">
                <option value="5" ' . $val5 . '>5 ' . EMOL_KM . '</option>
                <option value="10" ' . $val10 . '>10 ' . EMOL_KM . '</option>
                <option value="15" ' . $val15 . '>15 ' . EMOL_KM . '</option>
                <option value="25" ' . $val25 . '>25 ' . EMOL_KM . '</option>
                <option value="50" ' . $val50 . '>50 ' . EMOL_KM . '</option>
                </select>';

			echo '<div class="emol-location-search">
                    <label for="emol-zipcode-search-input">' . EMOL_WIDGET_LOCATION_SEARCH . '</label>
                        <div id="emol-location-input">
                        <input type="text" value="' . urldecode( emol_session::get( 'locationSearchZipcode' ) ) . '" class="emol-text-input" name="emol-zipcode-search" id="emol-zipcode-search-input" />
                        ' . $rangeBox . '
                        </div>
                    </div>';

			if ( count( $provinceList ) > 0 ) {
				$currentProvince = emol_session::get( 'locationSearchProvince' );

				echo '<div class="emol-province-search">';
				echo '<label for="emol-province-search-input">' . EMOL_WIDGET_PROVINCE_SEARCH . '</label>';
				echo '<div id="emol-province-input">';
				echo '<select class="emol-province-search-input" name="emol-province-search-input" id="emol-province-search-input">';
				echo '<option></option>';

				foreach ( $provinceList as $province ) {
					$selected = $currentProvince == $province['id'] ? ' selected="selected"' : '';

					echo '<option value="' . $province['id'] . '"' . $selected . '>' . $province['name'] . '</option>';
				}

				echo '</select>';
				echo ' </div></div>';
			}
		}
		if ( isset( $lists->lists ) && ! is_array( $lists->lists ) ) {
			echo $lists->lists;
		}

		//$allUrl = str_replace('//','/',$setUrl.'/'.get_option( 'emol_job_search_url' ));
		echo '
            <div class="emol-submit-wrapper">
                <span class="emol-reset-button"><a href="' . get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_job_search_url' ) . '/all/" class="emol-altbutton emol-button-reset">' . $reset . '</a></span>
                <button onclick="emolSearch(\'/' . $setUrl . '/\');" class="emol-button emol-button-search">' . $searchLabel . '</button>
            </div>';

		echo "</form>";

		echo "</div>";

		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['reset']                 = strip_tags( $new_instance['reset'] );
		$instance['searchLabel']           = strip_tags( $new_instance['searchLabel'] );
		$instance['locationSearchEnabled'] = strip_tags( $new_instance['locationSearchEnabled'] );
		$instance['checkBoxSearch']        = strip_tags( $new_instance['checkBoxSearch'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title       = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$reset       = isset( $instance['title'] ) ? esc_attr( $instance['reset'] ) : '';
		$searchLabel = isset( $instance['searchLabel'] ) ? esc_attr( $instance['searchLabel'] ) : '';


		$locationSearchEnabled = 1;
		if ( isset( $instance['locationSearchEnabled'] ) ) {
			$locationSearchEnabled = esc_attr( $instance['locationSearchEnabled'] );
		}
		$checkBoxSearch = 0;
		if ( isset( $instance['checkBoxSearch'] ) ) {
			$checkBoxSearch = esc_attr( $instance['checkBoxSearch'] );
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
            <label for="<?php echo $this->get_field_id( 'checkBoxSearch' ); ?>-yes"><?php _e( 'CheckBox Search on:' ); ?>
                <input class="widefat" style="width: 20px !important;"
                       id="<?php echo $this->get_field_id( 'checkBoxSearch' ); ?>-yes"
                       name="<?php echo $this->get_field_name( 'checkBoxSearch' ); ?>"
                       type="radio" <?php if ( $checkBoxSearch == 1 ) {
					echo 'checked="checked"';
				} ?> value="1"/>
            </label>
            <br/>
            <label for="<?php echo $this->get_field_id( 'checkBoxSearch' ); ?>-no"><?php _e( 'CheckBox Search off:' ); ?>
                <input class="widefat" style="width: 20px !important;"
                       id="<?php echo $this->get_field_id( 'checkBoxSearch' ); ?>-no"
                       name="<?php echo $this->get_field_name( 'checkBoxSearch' ); ?>" <?php if ( $checkBoxSearch == 0 ) {
					echo 'checked="checked"';
				} ?> type="radio" value="0"/>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'locationSearchEnabled' ); ?>-yes"><?php _e( 'Location on:' ); ?>
                <input class="widefat" style="width: 20px !important;"
                       id="<?php echo $this->get_field_id( 'locationSearchEnabled' ); ?>-yes"
                       name="<?php echo $this->get_field_name( 'locationSearchEnabled' ); ?>"
                       type="radio" <?php if ( $locationSearchEnabled == 1 ) {
					echo 'checked="checked"';
				} ?> value="1"/>
            </label>
            <br/>
            <label for="<?php echo $this->get_field_id( 'locationSearchEnabled' ); ?>-no"><?php _e( 'Location off:' ); ?>
                <input class="widefat" style="width: 20px !important;"
                       id="<?php echo $this->get_field_id( 'locationSearchEnabled' ); ?>-no"
                       name="<?php echo $this->get_field_name( 'locationSearchEnabled' ); ?>" <?php if ( $locationSearchEnabled == 0 ) {
					echo 'checked="checked"';
				} ?> type="radio" value="0"/>
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

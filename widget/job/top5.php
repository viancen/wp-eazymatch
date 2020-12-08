<?php

/**
 * EazyMatchLoginWidget Class
 *
 * fetches the last 5 jobs in the system
 */
class emol_widget_job_top5 extends WP_Widget {
	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_TOP5 );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		//get arguments
		extract( $args );

		//check slases etc
		global $trailingData;


		//get emol
		$api = eazymatch_connect();

		//set title var
		$title = '';

		//get instance
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}


		//set title var
		$limit = 5;

		//get instance
		if ( isset( $instance['limit'] ) ) {
			$limit = $instance['limit'];
		}

		if ( isset( $instance['show_description'] ) && $instance['show_description'] == 1 ) {
			$show_description = 1;
		} else {
			$show_description = 0;
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="emol_widget" id="emol_top5jobs_widget">';

		try {
			$filterOptions = emol_jobfilter_factory::createDefault()->getFilterArray();

			$wsJob = $api->get( 'job' );
			$jobs  = $wsJob->getPublished( $limit, $filterOptions );
			// var_dump($jobs);
		} catch ( SoapFault $e ) {
			eazymatch_trow_error( 'Fout in request EazyMatch -> jobs' );
			die();
		}


		//navigation
		$total = !empty($jobs) ? count( $jobs ) : 0;

		//check if the description may be visbile
		$descVisible  = get_option( 'emol_job_search_desc' );
		$regioVisible = get_option( 'emol_job_search_region' );

		$i = 0;
		if ( $total > 0 ) {
			echo '<ul>';
			foreach ( $jobs as $job ) {
				$i ++;
				$job_url   = emol_get_job_url( $job );
				$apply_url = get_bloginfo( 'wpurl' ) . '/' . get_option( 'emol_apply_url' ) . '/' . $job['id'] . '/' . eazymatch_friendly_seo_string( $job['name'] ) . $trailingData;

				echo '<li class="' . ( $i % 2 == 0 ? 'even' : 'odd' ) . '"><a href="' . $job_url . '">' . $job['name'] . '</a>';

				if ( $show_description == 1 && ! empty( $job['description'] ) ) {
					echo '<div class="emol_typejobs_description">' . strip_tags( $job['description'] ) . '</div>';
				}
				if ( $regioVisible == 1 && isset( $job['Address']['Region'] ) ) {
					echo '<div class="emol_top5jobs_region">' . $job['Address']['Region']['name'] . '</div>';
				}

				//$text .= '<div class="emol_top5jobs_apply"><a href="'.$apply_url.'">'.EMOL_JOBSEARCH_APPLY.'</a></div>';
				echo '</li>';
			}
			echo '</ul>';

			$setUrl = get_option('emol_job_search_page');
			if (empty($setUrl)) {
				$setUrl = get_option('emol_job_search_url');
			} else {
				$setUrl = '/' . $setUrl;
			}
			if(substr($setUrl,0,1) != '/'){
				$setUrl = '/' . $setUrl;
            }

			echo '
                <div id="emol_top5jobs_findmore">
                    <div class="emol-submit-wrapper">
                        <a href="' . $setUrl . '" class="emol-button emol-button-showalljobs">' . EMOL_JOBSEARCH_MORE . '</a>
                    </div>
                </div>';

		} else {
			echo '<ul><li><span>' . get_option( 'emol_job_no_result' ) . '</span></li></ul>';
		}

		//echo $text;

		echo '</div>';

		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['limit']            = is_numeric( $new_instance['limit'] ) ? $new_instance['limit'] : 5;
		$instance['show_description'] = (int) ( $new_instance['show_description'] );

		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = esc_attr( $instance['title'] );
		} else {
			$title = '';
		}


		if ( isset( $instance['limit'] ) ) {
			$limit = esc_attr( $instance['limit'] );
		} else {
			$limit = 5;
		}


		if ( isset( $instance['show_description'] ) && $instance['show_description'] == 1 ) {
			$show_description = 1;
		} else {
			$show_description = 0;
		}
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo $title; ?>"/></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'show_description' ); ?>"><?php _e( 'Beschrijving tonen' ); ?>
                <select id="<?php echo $this->get_field_id( 'show_description' ); ?>"
                        name="<?php echo $this->get_field_name( 'show_description' ); ?>">
                    <option value="0" <?php if ( $show_description == 0 ) {
						echo 'selected="selected"';
					} ?>>Nee
                    </option>
                    <option value="1" <?php if ( $show_description == 1 ) {
						echo 'selected="selected"';
					} ?>>Ja
                    </option>

                </select>
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>"
                       name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text"
                       value="<?php echo $limit; ?>"/></label>
        </p>
		<?php
	}

} // class EazyMatchLoginWidget
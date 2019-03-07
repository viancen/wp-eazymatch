<?php

/**
 * EazyMatchTypeJobsWidget Class
 */
class emol_widget_job_typelist extends emol_widget {
	/** constructor */
	function __construct() {
		parent::__construct( false, $name = 'EazyMatch - ' . EMOL_WIDGET_JOBTYPES );
	}

	/** @see WP_Widget::widget */
	function widget( $args, $instance ) {
		//get arguments
		extract( $args );

		//set title var
		$title  = '';
		$filter = '';

		if ( ! get_option( 'emol_instance' ) ) {
			echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">
                FIRST SETUP YOUR CREDENTIALS UNDER GLOBAL</div>";
		}

		//get emol
		$api = eazymatch_connect();


		//get title
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}

		//get filter
		$filter = 0;
		if ( isset( $instance['filter'] ) ) {
			$filter = $instance['filter'];
		}
		if ( (string) $filter == '' ) {
			$filter = 0;
		}


		if ( isset( $instance['show_description'] ) && $instance['show_description'] == 1 ) {
			$show_description = 1;
		} else {
			$show_description = 0;
		}

		//set limit var
		$limit = 5;
		if ( isset( $instance['limit'] ) ) {
			$limit = $instance['limit'];
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '<div class="emol_widget" id="emol_typejobs_widget">';

		try {
			$filterOptions                = emol_jobfilter_factory::createDefault()->getFilterArray();
			$filterOptions['valuestatus'] = array( $filter );

			$wsJob = $api->get( 'job' );
			$jobs  = $wsJob->siteSearch( $filterOptions, 0, $limit );
			// var_dump($jobs);
		} catch ( SoapFault $e ) {
			eazymatch_trow_error( 'Fout in SOAP request EazyMatch -> jobs' );
			die();
		}


		//navigation
		$total = count( $jobs );

		//check if the description may be visbile
		$descVisible  = get_option( 'emol_job_search_desc' );
		$regioVisible = get_option( 'emol_job_search_region' );

		$i = 0;
		if ( $total > 0 ) {
			echo '<ul>';
			foreach ( $jobs as $job ) {
				$i ++;
				$job_url = emol_get_job_url( $job );

				echo '<li class="' . ( $i % 2 == 0 ? 'even' : 'odd' ) . '"><a href="' . $job_url . '">' . $job['name'] . '</a>';

				if ( $show_description == 1 && ! empty( $job['description'] ) ) {
					echo '<div class="emol_typejobs_description">' . strip_tags( $job['description'] ) . '</div>';
				}
				if ( $regioVisible == 1 && isset( $job['Address']['Region'] ) ) {
					echo '<div class="emol_typejobs_region">' . $job['Address']['Region']['name'] . '</div>';
				}


				echo '</li>';
			}
			echo '</ul>';


			echo '
                <div id="emol_typejobs_findmore">
                    <div class="emol-submit-wrapper">
                        <a href="/' . get_option( 'emol_job_search_url' ) . '/all/" class="emol-button emol-button-showalljobs">' . EMOL_JOBSEARCH_MORE . '</a>
                    </div>
                </div>';

		} else {
			echo '<ul><li><span>' . get_option( 'emol_job_no_result' ) . '</span></li></ul>';
		}


		echo '</div>';

		echo $after_widget;

	}

	/** @see WP_Widget::update */
	function update( $new_instance, $old_instance ) {
		$instance                     = $old_instance;
		$instance['title']            = strip_tags( $new_instance['title'] );
		$instance['limit']            = is_numeric( $new_instance['limit'] ) ? $new_instance['limit'] : 5;
		$instance['filter']           = (int) ( $new_instance['filter'] );
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


		if ( isset( $instance['filter'] ) ) {
			$filter = esc_attr( $instance['filter'] );
		} else {
			$filter = '';
		}


		if ( isset( $instance['show_description'] ) && $instance['show_description'] == 1 ) {
			$show_description = 1;
		} else {
			$show_description = 0;
		}

		if ( isset( $instance['limit'] ) ) {
			$limit = esc_attr( $instance['limit'] );
		} else {
			$limit = 5;
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
            <label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>"
                       name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text"
                       value="<?php echo $limit; ?>"/></label>
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
            <label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Type/Valuestatus:' ); ?>
				<?php
				//get emol
				$api = eazymatch_connect();
				try {
					$wsJob          = $api->get( 'tree' );
					$jobValueStatus = $wsJob->tree( 'Jobvaluestatus' );
				} catch ( SoapFault $e ) {
					echo "<div style=\"background-color:#ffebeb;border:1px solid red;margin-top:10px;text-align:center;font-weight:bold;padding:15px;\">SOAP ERROR.</div>";
					exit();
				}


				?>
                <br/>
                <select id="<?php echo $this->get_field_id( 'filter' ); ?>"
                        name="<?php echo $this->get_field_name( 'filter' ); ?>" style="width:100%;">
					<?php
					//create filterlist
					if ( is_array( $jobValueStatus ) && count( $jobValueStatus ) > 0 ) {
						$list = '<ul id="emol-admin-filter-list">';
						foreach ( $jobValueStatus as $status ) {
							foreach ( $status['children'] as $firstChildren ) {
								$checked = '';

								if ( $firstChildren['id'] == (int) $filter ) {
									$checked = 'selected="selected"';
								}
								$list .= "<option value=\"" . $firstChildren['id'] . "\" $checked> " . $firstChildren['name'] . '</option>';
								if ( isset( $firstChildren['children'] ) && count( $firstChildren['children'] ) > 0 ) {

									foreach ( $firstChildren['children'] as $secondChildren ) {
										$checked2 = '';
										if ( $secondChildren['id'] == (int) $filter ) {
											$checked2 = 'selected="selected"';
										}
										$list .= "<option value=\"" . $secondChildren['id'] . "\" $checked2> " . $secondChildren['name'] . '</option>';
									}
								}
							}
						}
					}
					echo $list;
					?>
                </select>
            </label>
        </p>
		<?php
	}

} // class EazyMatchLoginWidget

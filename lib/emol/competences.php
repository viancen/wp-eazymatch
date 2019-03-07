<?php

class emol_competences {
	public static function generateTree( $competences ) {
		if ( ! is_array( $competences ) || count( $competences ) == 0 || $competences[0] == false ) {
			return false;
		}

		$output                      = '';
		$emol_job_competence_exclude = get_option( 'emol_job_competence_exclude' );
		if ( ! empty( $emol_job_competence_exclude ) ) {
			$emol_job_competence_exclude = explode( PHP_EOL, $emol_job_competence_exclude );
			$emol_job_competence_exclude = array_map( 'trim', $emol_job_competence_exclude );
		} else {
			$emol_job_competence_exclude = array();
		}
		foreach ( $competences as $competence ) {
			$level = $competence['level'];
			$name  = $competence['name'];
			if ( ! in_array( trim( $name ), $emol_job_competence_exclude ) ) {
				$children = isset( $competence['children'] ) ? $competence['children'] : array();

				switch ( $level ) {
					case 0:
						$start = '<div class="competence-level-0">';
						$end   = '</div>';
						break;
					case 1:
						$start = '<div class="competence-level-1"><h4>' . $name . '</h4>';
						$end   = '</div>';

						break;
					default:
						$start = '<li class="competence-level-' . $level . '">' . $name;
						$end   = '</li>';
						break;
				}
			}


			$children = self::generateTree( $children );

			if ( $children === false && $level <= 1 ) {
				continue;
			} elseif ( $children !== false && $level >= 1 ) {
				$children = '<ul>' . $children . '</ul>';
			} elseif ( $children === false ) {
				$children = '';
			}

			$output .= $start . $children . $end;
		}

		return empty( $output ) ? false : $output;
	}
}
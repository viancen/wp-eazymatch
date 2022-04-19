<?php

class emol_jobfilter_factory {
	public static function create( $filterName = 'default' ) {
        return match ($filterName) {
            'default' => emol_jobfilter_factory::createDefault(),
            default => new emol_jobfilter_instance(),
        };
	}

	public static function createDefault() {
		$filter = get_option( 'emol_jobfilter_default', false );

		// if not filter is found, automatically add the filter
		if ( $filter == false ) {
			$filter = new emol_jobfilter_instance();
			update_option( 'emol_jobfilter_default', $filter );
		}

		// transform legacy filter
		$legacyStatus = get_option( 'emol_filter_options', false );

		if ( $legacyStatus !== false ) {
			$filter->setStatus( unserialize( $legacyStatus ) );
			update_option( 'emol_jobfilter_default', $filter );

			// delete legacy option
			delete_option( 'emol_filter_options' );
		}


		return $filter;
	}
}
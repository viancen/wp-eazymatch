<?php

class emol_document {


	static public function download( $document, $decode = true ) {
		ob_clean();

		if ( $decode ) {
			$document['content'] = base64_decode( $document['content'] );
		}

		// discover size of document if not set
		$document['size'] = isset( $document['size'] ) ? $document['size'] : strlen( $document['content'] );

		header( "Cache-Control: public, must-revalidate" );
		header( "Pragma: hack" ); // oh well, it works...
		header( "Content-Type: " . $document['type'] );
		header( "Content-Length: " . $document['size'] );

		// try to dertermine the best file name
		if ( isset( $document['originalname'] ) && isset( $document['extension'] ) ) {
			header( 'Content-Disposition: attachment; filename="' . $document['originalname'] . '.' . $document['extension'] . '"' );
		} elseif ( isset( $document['name'] ) ) {
			header( 'Content-Disposition: attachment; filename="' . $document['name'] . '"' );
		} else {
			header( 'Content-Disposition: attachment' );
		}

		header( "Content-Transfer-Encoding: binary\n" );

		ob_flush(); // notify the browser a file is comming

		echo $document['content'];

		exit();
	}

	static public function display( $document, $decode = true ) {
		ob_clean();

		if ( $decode ) {
			$document['content'] = base64_decode( $document['content'] );
		}

		// discover size of document if not set
		$document['size'] = isset( $document['size'] ) ? $document['size'] : strlen( $document['content'] );

		header( "Cache-Control: public, must-revalidate" );
		header( "Pragma: hack" ); // oh well, it works...
		header( "Content-Type: " . $document['type'] );
		header( "Content-Length: " . $document['size'] );
		header( "Content-Transfer-Encoding: binary\n" );

		ob_flush(); // notify the browser a file is comming

		echo $document['content'];

		exit();
	}
}
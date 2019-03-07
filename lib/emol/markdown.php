<?php

class emol_markdown {
	public static $listBeginnings;

	public static function getListBeginning() {
		if ( empty( self::$listBeginnings ) ) {
			self::$listBeginnings = array(
				'• ',
				'•	',
				'•	',
				'- ',
				'-	',
				'* ',
				'*	',
				chr( 226 ) . chr( 128 ) . chr( 162 ), // word list characters
			);
		}

		return self::$listBeginnings;
	}

	/**
	 * parse lists automatically
	 */
	public static function parseLists( $text ) {
		// custom implementation
		$lines = explode( "\n", $text );


		$output = '';
		$inList = false;

		foreach ( $lines as $line ) {
			if ( strlen( $line ) > 3 ) {
				$isList     = false;
				$trimedLine = trim( $line );

				foreach ( self::getListBeginning() as $listBeginning ) {
					if ( substr( $trimedLine, 0, strlen( $listBeginning ) ) == $listBeginning ) {
						$isList = true;
						break;
					}
				}

				if ( $isList ) {
					if ( ! $inList ) {
						$inList = true;
						$output .= '<ul>';
					}

					$line   = '<li>' . substr( $trimedLine, strlen( $listBeginning ) ) . '</li>';
					$output .= $line;
					continue;
				}
			}

			if ( $inList ) {
				$inList = false;
				$output .= '</ul>' . $line;
			} else {
				$output .= $line . "\n";
			}
		}

		if ( $inList ) {
			$output .= '</ul>';
		}

		return trim( $output, "\n" );
	}

	/**
	 * parser for complete markdown specification
	 * @experimental
	 */
	public static $markdownparser;

	public static function parse( $text ) {
		# Setup static parser variable.
		if ( empty( self::$markdownparser ) ) {
			self::$markdownparser = new Markdown_Parser();
		}

		# Transform text using parser.
		return self::$markdownparser->transform( $text );
	}
}
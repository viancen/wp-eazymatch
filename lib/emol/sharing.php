<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

/**
 * Sharing and job-feed helpers for Connectiviteit.
 */
class emol_sharing {

	/**
	 * Options that may be saved from the Connectiviteit screen.
	 *
	 * @return array<string, true>
	 */
	public static function optionKeys() {
		return array(
			'emol_sharing_links'      => true,
			'emol_sharing_googlejobs' => true,
			'emol_sharing_indeed'     => true,
			'emol_sharing_jooble'     => true,
			'emol_sharing_adzuna'     => true,
			'emol_sharing_trovit'     => true,
			'emol_sharing_rss'        => true,
			'emol_sharing_rssfull'    => true,
			'emol_sharing_atom'       => true,
			'emol_sharing_json'       => true,
			'emol_sharing_sitemap'    => true,
		);
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	public static function isOn( $option ) {
		return (string) get_option( $option ) === '1';
	}

	/**
	 * Public URL of an em-jobfeed endpoint.
	 *
	 * @param string $slug
	 *
	 * @return string
	 */
	public static function feedUrl( $slug ) {
		return home_url( '/em-jobfeed/' . ltrim( $slug, '/' ) );
	}

	/**
	 * Share buttons for a job page. Opens official platform share dialogs
	 * (LinkedIn / Facebook / X login = OAuth at the provider).
	 *
	 * @param string $title
	 * @param string $url
	 *
	 * @return string
	 */
	public static function buttonsHtml( $title, $url ) {
		if ( ! self::isOn( 'emol_sharing_links' ) ) {
			return '';
		}

		$title = html_entity_decode( wp_strip_all_tags( (string) $title ), ENT_QUOTES, 'UTF-8' );
		$url   = (string) $url;
		$text  = $title !== '' ? $title : get_bloginfo( 'name' );
		$encU  = rawurlencode( $url );
		$encT  = rawurlencode( $text );

		$items = array(
			array(
				'id'    => 'linkedin',
				'label' => 'LinkedIn',
				'href'  => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encU,
				'mode'  => 'popup',
			),
			array(
				'id'    => 'facebook',
				'label' => 'Facebook',
				'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $encU,
				'mode'  => 'popup',
			),
			array(
				'id'    => 'x',
				'label' => 'X',
				'href'  => 'https://x.com/intent/tweet?url=' . $encU . '&text=' . $encT,
				'mode'  => 'popup',
			),
			array(
				'id'    => 'whatsapp',
				'label' => 'WhatsApp',
				'href'  => 'https://api.whatsapp.com/send?text=' . rawurlencode( $text . ' ' . $url ),
				'mode'  => 'direct',
			),
			array(
				'id'    => 'email',
				'label' => 'E-mail',
				'href'  => 'mailto:?subject=' . $encT . '&body=' . rawurlencode( $url ),
				'mode'  => 'direct',
			),
		);

		$html  = '<div class="emol-sharing-section" data-share-title="' . esc_attr( $text ) . '" data-share-url="' . esc_attr( $url ) . '">';
		$html .= '<div class="emol-share-label">Delen</div>';
		$html .= '<div id="emol-share-btns" class="emol-share-btns">';
		$html .= '<button type="button" class="emol-share-btn emol-share-btn-native" data-share="native">' . esc_html__( 'Delen', 'Emol-3.0-identifier' ) . '</button>';

		foreach ( $items as $item ) {
			$html .= '<a class="emol-share-btn emol-share-btn-' . esc_attr( $item['id'] ) . '" href="' . esc_url( $item['href'] ) . '" data-share="' . esc_attr( $item['mode'] ) . '" rel="noopener noreferrer nofollow" target="_blank">';
			$html .= '<span>' . esc_html( $item['label'] ) . '</span>';
			$html .= '</a>';
		}

		$html .= '</div></div>';

		return $html;
	}
}

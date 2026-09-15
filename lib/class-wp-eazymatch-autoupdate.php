<?php

class WP_EazyMatch_Updater {

	private $slug = ''; // plugin basename
	private $pluginData = array(); // plugin data
	private $username; // GitHub username
	private $repo; // GitHub repo name
	private $pluginFile; // __FILE__ of our plugin
	private $githubAPIResult = null; // holds data from GitHub
	private $accessToken; // GitHub private repo token

	//__C
	function __construct( $pluginFile, $gitHubUsername, $gitHubProjectName, $accessToken = '' ) {

		add_filter( "pre_set_site_transient_update_plugins", array( $this, "EMOL_setTransitent" ) );
		add_filter( "plugins_api", array( $this, "EMOL_setPluginInfo" ), 10, 3 );
		add_filter( "upgrader_post_install", array( $this, "EMOL_postInstall" ), 10, 3 );
		add_action( 'in_plugin_update_message-' . plugin_basename( $pluginFile ), array( $this, 'EMOL_pluginUpdateMessage' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'EMOL_breakingAdminNotice' ) );

		$this->pluginFile  = $pluginFile;

		$this->username    = $gitHubUsername;
		$this->repo        = $gitHubProjectName;
		$this->accessToken = $accessToken;

	}

	// Get information regarding our plugin from WordPress
	private function EMOL_initPluginData() {
		// code here
		$this->slug       = plugin_basename( $this->pluginFile );
		$this->pluginData = get_plugin_data( $this->pluginFile );

	}

	// Get information regarding our plugin from GitHub
	private function EMOL_getRepoReleaseInfo() {
		if ( null !== $this->githubAPIResult ) {
			return is_object( $this->githubAPIResult );
		}

		$url = "https://api.github.com/repos/{$this->username}/{$this->repo}/releases";
		$args = array(
			'timeout' => 10,
			'headers' => array(
				'Accept' => 'application/vnd.github+json',
			),
		);

		if ( ! empty( $this->accessToken ) ) {
			$args['headers']['Authorization'] = 'Bearer ' . $this->accessToken;
		}

		$request = wp_remote_get( $url, $args );
		if ( is_wp_error( $request ) || 200 !== wp_remote_retrieve_response_code( $request ) ) {
			$this->githubAPIResult = false;
			return false;
		}

		$releases = json_decode( wp_remote_retrieve_body( $request ) );
		if ( ! is_array( $releases ) || empty( $releases[0] ) || ! is_object( $releases[0] ) ) {
			$this->githubAPIResult = false;
			return false;
		}

		$this->githubAPIResult = $releases[0];
		return true;
	}

	// Push in plugin version information to get the update notification
	public function EMOL_setTransitent( $transient ) {
		// code here
		// If we have checked the plugin data before, don't re-check
		if ( ! is_object( $transient ) || empty( $transient->checked ) || ! is_array( $transient->checked ) ) {
			return $transient;
		}
		// Get plugin & GitHub release information
		$this->EMOL_initPluginData();
		if ( ! $this->EMOL_getRepoReleaseInfo() || ! isset( $transient->checked[ $this->slug ] ) ) {
			return $transient;
		}

		// Check the versions if we need to do an update
		$doUpdate = isset( $this->githubAPIResult->tag_name )
			&& version_compare( $this->githubAPIResult->tag_name, $transient->checked[ $this->slug ], '>' );

		// Update the transient to include our updated plugin data
		if ( $doUpdate ) {
			if ( empty( $this->githubAPIResult->zipball_url ) ) {
				return $transient;
			}

			$package = $this->githubAPIResult->zipball_url;

			// Include the access token for private GitHub repos
			if ( ! empty( $this->accessToken ) ) {
				$package = add_query_arg( array( "access_token" => $this->accessToken ), $package );
			}

			$obj                                = new stdClass();

			$obj->slug                          = $this->EMOL_getPluginSlug();
			$obj->plugin                        = $this->slug;
			$obj->new_version                   = $this->githubAPIResult->tag_name;
			$obj->url                           = isset( $this->pluginData['PluginURI'] ) ? $this->pluginData['PluginURI'] : '';
			$obj->package                       = $package;
			$obj->upgrade_notice                = $this->EMOL_parseBreakingChanges(
				isset( $this->githubAPIResult->body ) ? $this->githubAPIResult->body : ''
			);
			$transient->response[ $this->slug ] = $obj;
		}

		return $transient;
	}

	// Push in plugin version information to display in the details lightbox
	public function EMOL_setPluginInfo( $false, $action, $response ) {
		if ( 'plugin_information' !== $action || ! is_object( $response ) ) {
			return $false;
		}

		$this->EMOL_initPluginData();
		if (
			empty( $response->slug )
			|| ! in_array( $response->slug, array( $this->slug, $this->EMOL_getPluginSlug() ), true )
			|| ! $this->EMOL_getRepoReleaseInfo()
		) {
			return $false;
		}

		$pluginInfo = new stdClass();
		$pluginInfo->last_updated = isset( $this->githubAPIResult->published_at ) ? $this->githubAPIResult->published_at : '';
		$pluginInfo->slug         = $this->EMOL_getPluginSlug();
		$pluginInfo->name         = isset( $this->pluginData['Name'] ) ? $this->pluginData['Name'] : 'EazyMatch';
		$pluginInfo->plugin_name  = $pluginInfo->name;
		$pluginInfo->version      = isset( $this->githubAPIResult->tag_name ) ? $this->githubAPIResult->tag_name : '';
		$pluginInfo->author       = isset( $this->pluginData['AuthorName'] ) ? $this->pluginData['AuthorName'] : '';
		$pluginInfo->homepage     = isset( $this->pluginData['PluginURI'] ) ? $this->pluginData['PluginURI'] : '';

		// This is our release download zip file
		$downloadLink = isset( $this->githubAPIResult->zipball_url ) ? $this->githubAPIResult->zipball_url : '';

		// Include the access token for private GitHub repos
		if ( ! empty( $this->accessToken ) ) {
			$downloadLink = add_query_arg(
				array( "access_token" => $this->accessToken ),
				$downloadLink
			);
		}
		$pluginInfo->download_link = $downloadLink;

		// We're going to parse the GitHub markdown release notes, include the parser
		require_once( plugin_dir_path( __FILE__ ) . "Parsedown.php" );

		// Create tabs in the lightbox
		$releaseBody = isset( $this->githubAPIResult->body ) ? $this->githubAPIResult->body : '';
		$breaking    = $this->EMOL_parseBreakingChanges( $releaseBody );
		$changelog   = $this->EMOL_changelogBody( $releaseBody );
		$pluginInfo->sections = array(
			'description' => isset( $this->pluginData['Description'] ) ? $this->pluginData['Description'] : '',
			'changelog'   => class_exists( "Parsedown" )
				? Parsedown::instance()->parse( $changelog )
				: $changelog,
		);
		if ( $breaking !== '' ) {
			$pluginInfo->sections['upgrade_notice'] = '<p><strong>'
				. esc_html( $this->EMOL_breakingLabel() )
				. '</strong></p><p>'
				. nl2br( esc_html( $breaking ) )
				. '</p>';
			$pluginInfo->upgrade_notice = $breaking;
		}

		$requires = $this->EMOL_parseReleaseField( $releaseBody, 'requires' );
		if ( $requires !== '' ) {
			$pluginInfo->requires = $requires;
		}

		$tested = $this->EMOL_parseReleaseField( $releaseBody, 'tested' );
		if ( $tested !== '' ) {
			$pluginInfo->tested = $tested;
		}

		return $pluginInfo;
	}

	/**
	 * Extra warning under the plugin update row on Plugins.
	 *
	 * @param array  $plugin_data Plugin headers.
	 * @param object $response    Update payload from the transient.
	 */
	public function EMOL_pluginUpdateMessage( $plugin_data, $response ) {
		$breaking = ( is_object( $response ) && ! empty( $response->upgrade_notice ) )
			? (string) $response->upgrade_notice
			: '';

		if ( $breaking === '' ) {
			return;
		}

		echo '<br><span class="emol-breaking-update-notice" style="display:block;margin:.6em 0 0;padding:.6em .75em;border-left:4px solid #d63638;background:#fcf0f1;">';
		echo '<strong>' . esc_html( $this->EMOL_breakingLabel() ) . '</strong> ';
		echo esc_html( $breaking );
		echo '</span>';
	}

	/**
	 * Admin banner while a breaking update is available.
	 */
	public function EMOL_breakingAdminNotice() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$breaking = $this->EMOL_pendingBreakingNotice();
		if ( $breaking === '' ) {
			return;
		}

		if ( ! $this->EMOL_shouldShowBreakingNotice() ) {
			return;
		}

		$version = $this->EMOL_pendingUpdateVersion();

		echo '<div class="notice notice-warning"><p><strong>';
		echo esc_html( $this->EMOL_breakingLabel() );
		if ( $version !== '' ) {
			echo ' (' . esc_html( $version ) . ')';
		}
		echo '</strong> ' . esc_html( $breaking ) . '</p></div>';
	}

	/**
	 * Single-line metadata from a GitHub/GitLab release body.
	 *
	 * Supports the same markers already used for WordPress compatibility:
	 * `requires: 4.3`, `tested: 7.1`, `breaking: ...`
	 *
	 * @param string $body  Release notes.
	 * @param string $field Field name without colon.
	 *
	 * @return string
	 */
	private function EMOL_parseReleaseField( $body, $field ) {
		$matches = null;
		$pattern = '/^' . preg_quote( $field, '/' ) . ':\s*(.+)$/im';
		if ( ! preg_match( $pattern, (string) $body, $matches ) ) {
			return '';
		}

		return trim( $matches[1] );
	}

	/**
	 * Breaking-change text from the release notes.
	 *
	 * Prefers `breaking: ...`. Falls back to a `## Breaking changes` section.
	 *
	 * @param string $body Release notes.
	 *
	 * @return string
	 */
	private function EMOL_parseBreakingChanges( $body ) {
		$body    = (string) $body;
		$inline  = $this->EMOL_parseReleaseField( $body, 'breaking' );
		if ( $inline !== '' ) {
			return $inline;
		}

		if ( ! preg_match( '/^#{1,3}\s*breaking(?:\s+changes)?\s*$/im', $body, $heading, PREG_OFFSET_CAPTURE ) ) {
			return '';
		}

		$start = $heading[0][1] + strlen( $heading[0][0] );
		$rest  = substr( $body, $start );
		if ( preg_match( '/^#{1,3}\s+/m', $rest, $next, PREG_OFFSET_CAPTURE ) ) {
			$rest = substr( $rest, 0, $next[0][1] );
		}

		return trim( $rest );
	}

	/**
	 * Release notes without machine fields, for the changelog tab.
	 *
	 * @param string $body Release notes.
	 *
	 * @return string
	 */
	private function EMOL_changelogBody( $body ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $body );
		$kept  = array();

		foreach ( $lines as $line ) {
			if ( preg_match( '/^(requires|tested|breaking)\s*:/i', trim( $line ) ) ) {
				continue;
			}
			$kept[] = $line;
		}

		return trim( implode( "\n", $kept ) );
	}

	/**
	 * @return string
	 */
	private function EMOL_breakingLabel() {
		if ( defined( 'EMOL_ADMIN_BREAKING_UPDATE' ) ) {
			return EMOL_ADMIN_BREAKING_UPDATE;
		}

		return 'This update contains breaking changes:';
	}

	/**
	 * @return string
	 */
	private function EMOL_pendingBreakingNotice() {
		$update = $this->EMOL_pendingUpdate();

		return ( $update && ! empty( $update->upgrade_notice ) )
			? (string) $update->upgrade_notice
			: '';
	}

	/**
	 * @return string
	 */
	private function EMOL_pendingUpdateVersion() {
		$update = $this->EMOL_pendingUpdate();

		return ( $update && ! empty( $update->new_version ) )
			? (string) $update->new_version
			: '';
	}

	/**
	 * @return object|null
	 */
	private function EMOL_pendingUpdate() {
		$this->EMOL_initPluginData();
		$transient = get_site_transient( 'update_plugins' );
		if (
			! is_object( $transient )
			|| empty( $transient->response )
			|| ! is_array( $transient->response )
			|| empty( $transient->response[ $this->slug ] )
			|| ! is_object( $transient->response[ $this->slug ] )
		) {
			return null;
		}

		return $transient->response[ $this->slug ];
	}

	/**
	 * @return bool
	 */
	private function EMOL_shouldShowBreakingNotice() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( is_object( $screen ) && $screen->id === 'update-core' ) {
			return true;
		}

		return isset( $_GET['page'] ) && strpos( sanitize_key( wp_unslash( $_GET['page'] ) ), 'emol-' ) === 0;
	}

	private function EMOL_getPluginSlug() {
		$directory = dirname( $this->slug );

		return '.' === $directory
			? basename( $this->slug, '.php' )
			: $directory;
	}

	// Perform additional actions to successfully install our plugin
	public function EMOL_postInstall( $true, $hook_extra, $result ) {
		$this->EMOL_initPluginData();
		if (
			! is_array( $hook_extra )
			|| empty( $hook_extra['plugin'] )
			|| $hook_extra['plugin'] !== $this->slug
			|| ! is_array( $result )
			|| empty( $result['destination'] )
		) {
			return $result;
		}

		// Remember if our plugin was previously activated
		$wasActivated = is_plugin_active( $this->slug );
		// Since we are hosted in GitHub, our plugin folder would have a dirname of

		// reponame-tagname change it to our original one:
		global $wp_filesystem;

		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname( $this->slug );
		if ( $result['destination'] !== $pluginFolder ) {
			$wp_filesystem->move( $result['destination'], $pluginFolder, true );
		}
		$result['destination'] = $pluginFolder;
		// Re-activate plugin if needed
		if ( $wasActivated ) {
			activate_plugin( $this->slug );
		}

		return $result;
	}
}

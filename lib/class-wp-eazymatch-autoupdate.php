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
		$pluginInfo->sections = array(
			'description' => isset( $this->pluginData['Description'] ) ? $this->pluginData['Description'] : '',
			'changelog'   => class_exists( "Parsedown" )
				? Parsedown::instance()->parse( $releaseBody )
				: $releaseBody
		);
		// Gets the required version of WP if available
		$matches = null;
		preg_match( "/requires:\s([\d\.]+)/i", $releaseBody, $matches );
		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$pluginInfo->requires = $matches[1];
				}
			}
		}

// Gets the tested version of WP if available
		$matches = null;
		preg_match( "/tested:\s([\d\.]+)/i", $releaseBody, $matches );
		if ( ! empty( $matches ) ) {
			if ( is_array( $matches ) ) {
				if ( count( $matches ) > 1 ) {
					$pluginInfo->tested = $matches[1];
				}
			}
		}

		return $pluginInfo;
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

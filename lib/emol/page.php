<?php

/**
 * abstract page class for emol pages inside the wordrpess plugin
 */

class emol_page {
	/**
	 * The slug for the fake post.  This is the URL for your plugin, like:
	 * http://site.com/about-me or http://site.com/?page_id=about-me
	 * @var string
	 */
	var $page_slug = '';

	/**
	 * @var string
	 */
	var $request_vars = '';

	/**
	 * The title for your fake post.
	 * @var string
	 */
	var $page_title = '';

	/**
	 * Allow pings?
	 * @var string
	 */

	var $ping_status = 'open';

	/**
	 * EazyMatch 3.0 Api
	 *
	 * @var emol_connectManager
	 */
	var $emolApi;


	/**
	 * Class constructor
	 */
	function __construct( $slug, $requestVars = '' ) {

		$this->emolApi = emol_connectManager::getInstance()->getConnection();

		$this->page_slug    = $slug . '/' . $requestVars;
		$this->request_vars = $requestVars;

		/**
		 * We'll wait til WordPress has looked for posts, and then
		 * check to see if the requested url matches our target.
		 */
		add_filter( 'the_posts', array( &$this, 'detectPost' ) );
	}

	function detectPost( $posts ) {
		global $wp;
		global $wp_query;
		/**
		 * Check if the requested page matches our target
		 */
		if ( strtolower( $wp->request ) == strtolower( $this->page_slug ) || $wp->query_vars['page_id'] == $this->page_slug ) {
			//Add the fake post
			$posts = null;

			$hasPost = $this->preparePost();

			if ( $hasPost === false ) {
				return $posts;
			}

			$posts[] = $this->createPost();

			/**
			 * Trick wp_query into thinking this is a page (necessary for wp_title() at least)
			 * Not sure if it's cheating or not to modify global variables in a filter
			 * but it appears to work and the codex doesn't directly say not to.
			 */
			$wp_query->is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset( $wp_query->query["error"] );
			$wp_query->query_vars["error"] = "";
			$wp_query->is_404              = false;

		}

		return $posts;
	}

	/**
	 * Called by the 'detectPost' action
	 */
	function createPost() {


		$post = get_emol_dummy_post_object();

		/**
		 * The safe name for the post.  This is the post slug.
		 */
		$post->post_name = $this->page_slug;


		/**
		 * Not sure if this is even important.  But gonna fill it up anyway.
		 */
		$post->guid = get_bloginfo( 'wpurl' ) . '/' . $this->page_slug;

		/**
		 * The title of the page.
		 */
		$post->post_title = $this->page_title;

		/**
		 * This is the content of the post.  This is where the output of
		 * your plugin should go.  Just store the output from all your
		 * plugin function calls, and put the output into this var.
		 */
		$post->post_content = $this->getContent();

		return ( $post );
	}

	protected function loadView( $viewName, $data = array() ) {
		$viewManager = emol_view::getInstance();

		return $viewManager->load( $viewName, $data );
	}

	//protected function preparePost();

	//public function getContent();
}

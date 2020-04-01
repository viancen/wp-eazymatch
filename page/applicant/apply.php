<?php
if (!defined('EMOL_DIR')) {
	die('no direct access');
}

/**
 * Applying to a job or open or whatever
 */
class emol_page_applicant_apply extends emol_page
{

	/**
	 * Function to be executed in eazymatch
	 *
	 * @var mixed
	 */
	var $emol_function = '';

	/**
	 * EazyMatch 3.0 Api
	 *
	 * @var mixed
	 */
	var $emolApi;
	var $jobApi;
	var $toolApi;
	var $competenceApi;

	/**
	 * When initialized this will be the handled job
	 *
	 * @var mixed
	 */
	var $job;
	var $competences;
	var $jobId = 0;

	/**
	 * @param emol_captcha
	 */
	var $captcha;

	/**
	 * Class constructor
	 */
	function __construct($slug, $function = '')
	{

		global $trailingData;

		$this->page_slug = $slug . '/' . $function;

		$this->emol_function = $function;

		//first connect to the api
		$this->emolApi = eazymatch_connect();

		if (!$this->emolApi) {
			eazymatch_trow_error();
		}

		//split up the variables given
		$urlVars = explode('/', $this->page_slug);
		$jobId = $urlVars[1];

		//get competences
		//$this->competenceApi    = $this->emolApi->get('competence');
		//$this->competences         = $this->competenceApi->tree();
		$this->toolApi = $this->emolApi->get('tool');

		if (is_numeric($jobId) && $jobId > 0) {

			//initialize wsdls
			$this->jobApi = $this->emolApi->get('job');

			//get the job
			$this->job = $this->jobApi->getFullPublished($jobId);

			if (empty($this->job)) {

				header("HTTP/1.0 404 Not Found");
				header('Location: ' . get_bloginfo('wpurl') . '/' . get_option('emol_job_search_url') . $trailingData);
				exit();
			} else {
				$this->jobId = $this->job['id'];
			}


			//set the page variables    
			$this->page_title = EMOL_APPLY . ' "' . $this->job['name'] . '"';
		} else {

			$this->jobId = 'open';
			$this->page_title = EMOL_JOB_APPLY_FREE;
		}
		/**
		 * We'll wait til WordPress has looked for posts, and then
		 * check to see if the requested url matches our target.
		 */
		add_filter('the_posts', array(&$this, 'detectPost'));
	}


	/**
	 * Called by the 'detectPost' action
	 */
	function createPost()
	{

		/**
		 * Create a fake post.
		 */
		$post = get_emol_dummy_post_object();

		/**
		 * The safe name for the post.  This is the post slug.
		 */
		$post->post_name = $this->page_slug;

		/**
		 * Not sure if this is even important.  But gonna fill it up anyway.
		 */
		$post->guid = get_bloginfo('wpurl') . '/' . $this->page_slug;

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


		return ($post);
	}

	/**
	 * creates the fake content
	 */
	function getContent($defaultData = array())
	{

		// remove auto line breaks
		remove_filter('the_content', 'wpautop');
		return emol_get_apply_form($this->job);

		//wp_redirect( $suUrl );
	}

	/**
	 * userd by the initialisation
	 */
	function detectPost($posts)
	{
		global $wp;
		global $wp_query;

		/**
		 * Check if the requested page matches our target
		 */

		if (strtolower($wp->request) == strtolower($this->page_slug) || @$wp->query_vars['page_id'] == $this->page_slug) {
			//Add the fake post
			$posts = null;
			$posts[] = $this->createPost();

			/**
			 * Trick wp_query into thinking this is a page (necessary for wp_title() at least)
			 * Not sure if it's cheating or not to modify global variables in a filter
			 * but it appears to work and the codex doesn't directly say not to.
			 */
			$wp_query->is_page = true;
			//Not sure if this one is necessary but might as well set it like a true page
			$wp_query->is_singular = true;
			$wp_query->is_home = false;
			$wp_query->is_archive = false;
			$wp_query->is_category = false;
			//Longer permalink structures may not match the fake post slug and cause a 404 error so we catch the error here
			unset($wp_query->query["error"]);
			$wp_query->query_vars["error"] = "";
			$wp_query->is_404 = false;

		}

		return $posts;
	}
}

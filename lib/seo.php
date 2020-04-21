<?php

function emol_meta_description()
{
	global $post;
	global $jobInfo;
	if (strstr($post->post_content, '[eazymatch view="job"')) {
		if (empty($jobInfo)) {
			$emol_api = eazymatch_connect();
			$emol_job_id = (get_query_var('emol_job_id'));
			$emol_job_id = explode('-', $emol_job_id);
			$emol_job_id = array_pop($emol_job_id);

			$jobInfo = $emol_api->get('job')->getFullPublished($emol_job_id);
		}
		$pre = get_option('emol_job_header');
		$meta = trim($pre . ' ' . str_replace(['\'', '"', PHP_EOL], " ", $jobInfo['description']));
		echo "<meta name='description' content='" . $meta . "'/>";
		echo '<meta property="og:title" content="' . $jobInfo['name'] . '" />';
		echo '<meta property="og:description" content="' . $meta . '" />';
	}

}

function emol_page_title($title)
{
	global $post;
	global $jobInfo;
	if (stristr($post->post_content, '[eazymatch view="job"')) {
		if (empty($jobInfo)) {
			$emol_api = eazymatch_connect();
			$emol_job_id = (get_query_var('emol_job_id'));
			$emol_job_id = explode('-', $emol_job_id);
			$emol_job_id = array_pop($emol_job_id);

			$jobInfo = $emol_api->get('job')->getFullPublished($emol_job_id);
		}
		$pre = get_option('emol_job_header');
		if ($pre) {
			return $pre . ': ' . $jobInfo['name'] . ' (Ref. ' . $jobInfo['id'] . ')';
		} else {
			return $jobInfo['name'] . ' (Ref. ' . $jobInfo['id'] . ')';
		}

	}
	return $title;
}

add_filter('pre_get_document_title', 'emol_page_title', 500000);
add_action('wp_head', 'emol_meta_description', 500000);
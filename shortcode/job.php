<?php
if (!defined('EMOL_DIR')) {
    die('no direct access');
}

/**
 * view a single job [eazymatch view="job"]
 */
class emol_shortcode_job
{

    var $job;
    var $jobTexts;
    var $jobCompetences;

    function getContent()
    {

        global $emol_side;
        global $trailingData;

        //isset in functions.php, by the custom title function
        global $emol_job;

        if (is_null($emol_job)) {

            eazymatch_connect();

            $emol_job_id = (get_query_var('emol_job_id'));
            if (!empty($emol_job_id)) {

                $emol_job_id = explode('-', $emol_job_id);
                $emol_job_id = array_pop($emol_job_id);

                $trunk = new emol_trunk();

                // create a response array and add all the requests to the trunk
                $emol_job['job'] = &$trunk->request('job', 'getFullPublished', array($emol_job_id));
                $emol_job['jobTexts'] = &$trunk->request('job', 'getCustomTexts', array($emol_job_id));
                $emol_job['jobCompetences'] = &$trunk->request('job', 'getCompetenceTree', array($emol_job_id));


                // execute the trunk request
                $trunk->execute();
            }
        }


        if (!isset($emol_job['job']['id'])) {
            $sp = get_option('emol_job_search_page');
            if (!empty($sp)) {
                $searchUrl = get_bloginfo('wpurl') . '/' . get_option('emol_job_search_page');
            } else {
                $searchUrl = get_bloginfo('wpurl') . '/' . get_option('emol_job_search_url');
            }

            return '<p class="eazymatch-view-job-404">De opgevraagde vacature is helaas vervuld. Bekijk hier ons laatste aanbod: <a id="eazymatch-404-link-vacature" href="' . $searchUrl . '">Vacature overzicht.</a></p>';

        } else {

            $this->job = $emol_job['job'];
            $this->jobTexts = isset($emol_job['jobTexts']) ? $emol_job['jobTexts'] : array();
            $this->jobCompetences = isset($emol_job['jobCompetences']) ? $emol_job['jobCompetences'] : array();

            if ((!is_array($this->jobTexts) || count($this->jobTexts) === 0) && !empty($this->job['id'])) {
                $api = eazymatch_connect();
                if ($api) {
                    $this->jobTexts = $api->get('job')->getCustomTexts($this->job['id']);
                }
            }

            $emol_side = 'applicant';

            //shortcode
            if (!isset($this->job['shortcode'])) {
                $this->job['shortcode'] = 'V' . sprintf("%05d", $this->job['id']);
            }

            $class = '';
            $class2 = '';
            if (!empty($this->job['Statusses'])) {
                foreach ($this->job['Statusses'] as $aroStat) {
                    $class .= ' emol-job-status-' . $aroStat['jobstatus_id'];
                    $class2 .= ' emol-job-status-sub-' . $aroStat['jobstatus_id'];
                }
            }

            $jobHtml = '<div id="emol-job-container" class="' . $class . '">';
            $jobHtml .= '<div class="' . $class2 . '"></div>';
            $jobHtml .= '<h2 class="emol-job-heading emol-job-title">' . $this->job['name'] . '</h2>';

            $jobHtml .= '<div id="emol-job-body">';
            if (isset($this->job['Company']['Logo']) && $this->job['Company']['Logo']['content'] > '' && get_option('emol_job_search_logo') == 1) {
                $jobHtml .= '<div class="emol-job-picture"><img src="data:image/png;base64,' . $this->job['Company']['Logo']['content'] . '" /></div>';
            }
            if (emol_jobtext::descriptionVisible(emol_jobtext::CONTEXT_DETAIL) && !empty($this->job['description'])) {
                $jobHtml .= emol_jobtext::renderDescriptionHtml($this->job['description']);
            }
            $jobHtml .= '<table>';

            //code of job
            $jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_CODE . '</td>';
            $jobHtml .= '<td class="emol-job-body-col2">' . $this->job['shortcode'] . '</td></tr>';

            if (isset($this->job['Address']['Region']['name']) && $this->job['Address']['Region']['name'] != '') {
                $addRegion = '';

                if (isset($this->job['Address']['Region']['name'])) {
                    $addRegion = '' . $this->job['Address']['Region']['name'] . '';
                }
                $jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_PLACE . '</td>';
                //$jobHtml .= '<td>'.$this->job['Address']['city'].' '.$addRegion.'</td></tr>';
                $jobHtml .= '<td>' . $addRegion . '</td></tr>';
            }
            $jdate = '';

            if (isset($this->job['startpublished']) && !empty($this->job['startpublished'])) {
                $jdate = mysql2date(get_option('date_format'), $this->job['startpublished']);
            } elseif (isset($this->job['created']) && !empty($this->job['created'])) {
                $jdate = mysql2date(get_option('date_format'), $this->job['created']);
            }

            $jobHtml .= '<tr><td class="emol-job-body-col1">' . EMOL_JOB_DATE . '</td>';
            $jobHtml .= '<td>' . $jdate . '</td></tr>';

            $jobHtml .= '</table></div>';

            $appUrl = get_option('emol_apply_url');
            if (!empty($appUrl)) {
                $appUrl = get_bloginfo('wpurl') . '/' . $appUrl . '/' . $this->job['id'] . '/' . eazymatch_friendly_seo_string($this->job['name']);
            } else {
                $appUrl = get_option('emol_apply_page');
                $appUrl = get_bloginfo('wpurl') . '/' . $appUrl . '/' . eazymatch_friendly_seo_string($this->job['name']) . '-' . $this->job['id'];
            }

            $jobHtml .= '<div class="emol-job-apply emol-job-apply-top">
                <a href="' . $appUrl . '" class="emol-button emol-button-apply emol-apply-button" rel="nofollow">' . EMOL_JOB_APPLY . '</a></div>';

            /**
             * Text blocks
             *
             * @var mixed
             */

            $jobHtml .= emol_jobtext::renderHtml($this->jobTexts, emol_jobtext::CONTEXT_DETAIL);

            /**
             * Add Competences
             */
            $cHtml = emol_competences::generateTree($this->jobCompetences);

            if ($cHtml !== false) {
                $jobHtml .= '<div class="emol-job-competences">';
                $jobHtml .= '<h3 class="emol-job-heading">' . EMOL_JOB_COMPETENCES . '</h3>';
                $jobHtml .= $cHtml;
                $jobHtml .= '</div>';
            }

            //check manager info / contact part
            $manager_view_check = get_option('emol_view_manager_contact');
            if ($manager_view_check == 1) {
                $manager_options = get_option('emol_manager_settings');
                if (!empty($manager_options)) {
                    $wordpressManagerSettings = unserialize(get_option('emol_manager_settings'));

                    if (array_key_exists($this->job['manager_id'], $wordpressManagerSettings)) {
                        $manager_settings = $wordpressManagerSettings[$this->job['manager_id']];
                        $jobHtml .= '<div class="emol-manager-contact-table" >';
                        $jobHtml .= '<h3 class="emol-job-heading">' . get_option('emol_view_manager_heading') . '</h3>';
                        // $jobHtml .= '<p class="emol-job-paragraph">';


                        if (!empty($manager_settings['photo'])) {
                            $jobHtml .= '<div class="emol-manager-photo-container"><img class="emol-manager-picture" src="' . $manager_settings['photo'] . '" ></div>';
                        }
                        $jobHtml .= '
                        <div class="emol-manager-contact-info" >
                            <div class="emol-manager-name">' . $manager_settings['displayname'] . '</div>
                            <div class="emol-manager-email">' . ($manager_settings['email']) . '</div>
                            <div class="emol-manager-phone">' . $manager_settings['phone'] . '</div>
                            <div class="emol-manager-contact-text">' . nl2br($manager_settings['text']) . '</div>
                        </div>
                        ';

                        $jobHtml .= '</div>';
                        $jobHtml .= '<div class="emol-manager-bottom"></div>';
                        // $jobHtml .= '</p><div class="clear"></div> ';

                    }
                }
            }

            $jobHtml .= '<div class="emol-job-apply emol-job-apply-bottom">
                <a href="' . $appUrl . '" class="emol-button emol-button-apply emol-apply-button" rel="nofollow">' . EMOL_JOB_APPLY . '</a></div>';

            $jobHtml .= '</div>'; //job-container

            $jobHtml .= emol_sharing::buttonsHtml($this->job['name'], emol_get_job_url($this->job));

            $jobHtml .= emol_get_google_jobs($this->job);

        }

        return $jobHtml;

    }

}

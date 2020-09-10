<?php
if (!defined('EMOL_DIR')) {
    die('no direct access');
}
if (!emol_session::isValidId('company_id')) {


    //populate
    $checkMale = (isset($data['gender']) && $data['gender'] == 'm') ? ' checked="checked"' : '';
    $checkFeMale = (isset($data['gender']) && $data['gender'] == 'f') ? ' checked="checked"' : '';

    if (empty($checkMale) && empty($checkFeMale)) {
        $checkMale = ' checked="checked"';
    }

    $reactHtml .= '
                <div class="emol-react-row" id="emol-gender-row">
                    <div class="emol-label-wrapper">
                        ' . EMOL_REACT_GENDER . '
                    </div>
                  <div class="emol-input-wrapper">
                        <input type="radio" class="emol-radio-input" name="gender" value="m" ' . $checkMale . ' id="emol-gender-male" />  <label for="emol-gender-male">' . EMOL_REACT_MALE . '</label>
                        <input type="radio" class="emol-radio-input" name="gender" value="f" ' . $checkFeMale . ' id="emol-gender-female" />  <label for="emol-gender-female">' . EMOL_REACT_FEMALE . '</label>
                    </div>
                </div>
                ';

    //cNAMES
    $frm = '';
    $option = get_option('emol_frm_com_cname');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                     <div class="emol-react-row" id="emol-companyname-row">
                        <div class="emol-label-wrapper">
                            <label for="name">' . EMOL_REACT_COMPANY . '</label>
                        </div>
                       <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input validate[required]" value="' . $data['name'] . '" name="name" id="name" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;


    //NAMES
    $frm = '';
    $option = get_option('emol_frm_com_name');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-firstname-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-firstname">' . EMOL_FIRSTNAME . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' value="' . $data['firstname'] . '" name="firstname" id="emol-firstname" />
                        </div>
                    </div>

                    <div class="emol-react-row" id="emol-lastname-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-middlename">' . EMOL_MIDDLENAME . ' &amp;</label> <label for="emol-lastname">' . EMOL_LASTNAME . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input emol-small" value="' . $data['middlename'] . '" name="middlename" id="emol-middlename" />
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . '  value="' . $data['lastname'] . '" name="lastname" id="emol-lastname" />
                        </div>
                    </div>
                    ';
    } else {
        $option = get_option('emol_frm_com_firstname');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }
            $frm .= '
                        <div class="emol-react-row" id="emol-firstname-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-firstname">' . EMOL_FIRSTNAME . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="firstname" value="' . $data['firstname'] . '"  id="emol-firstname" />
                        </div>
                        </div>';
        }

        $option = get_option('emol_frm_com_middlename');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }
            $frm .= '
                        <div class="emol-react-row" id="emol-middlename-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-middlename">' . EMOL_MIDDLENAME . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="middlename" value="' . $data['middlename'] . '"  id="emol-middlename" />
                        </div>
                        </div>';
        }

        $option = get_option('emol_frm_com_lastname');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }
            $frm .= '
                        <div class="emol-react-row" id="emol-lastname-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-lastname">' . EMOL_LASTNAME . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="lastname" value="' . $data['lastname'] . '" id="emol-lastname" />
                        </div>
                        </div>';
        }
    }
    $reactHtml .= $frm;

    //EMOL_ADDRESS
    $frm = '';
    $option = get_option('emol_frm_com_addr');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-address-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-zipcode">' . EMOL_ZIPCODE . ' + </label>
                            <label for="emol-housenumber">' . EMOL_HOUSENUMBER . '  + </label>
                            <label for="emol-extension">' . EMOL_EXTENSION . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input emol-small ' . $req . '" ' . $req . ' value="' . $data['zipcode'] . '"  minlength=6 name="zipcode" id="emol-zipcode"  />
                            <input type="text" class="emol-text-input emol-small" name="housenumber" value="' . $data['housenumber'] . '" id="emol-housenumber" />
                            <input type="text" class="emol-text-input emol-small" name="extension" value="' . $data['extension'] . '" id="emol-extension" />
                        </div>
                    </div>
                    ';
    } else {
        $option = get_option('emol_frm_com_street');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }

            $frm .= '<div class="emol-react-row" id="emol-street-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-street">' . EMOL_STREET . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="street" value="' . $data['street'] . '"  id="emol-street"  />
                        </div>
                        </div>';
        }
        $option = get_option('emol_frm_com_housenr');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }

            $frm .= '<div class="emol-react-row" id="emol-housenumber-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-housenumber">' . EMOL_HOUSENUMBER . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="housenumber" value="' . $data['housenumber'] . '"  id="emol-housenumber"  />
                        </div>
                        </div>';
        }
        $option = get_option('emol_frm_com_extension');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }

            $frm .= '<div class="emol-react-row" id="emol-extension-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-extension">' . EMOL_EXTENSION . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="extension" value="' . $data['extension'] . '"  id="emol-extension"  />
                        </div>
                        </div>';
        }
        $option = get_option('emol_frm_com_zipcode');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }

            $frm .= '<div class="emol-react-row" id="emol-zipcode-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-zipcode">' . EMOL_ZIPCODE . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="zipcode" value="' . $data['zipcode'] . '" id="emol-zipcode"  />
                        </div>
                        </div>';
        }
        $option = get_option('emol_frm_com_city');
        if ($option != '') {
            $req = '';
            $asterix = '';
            if ($option == 'yes_req') {
                $req = 'required';
                $asterix = ' <strong class="emol-required-asterix">*</strong>';
            }

            $frm .= '<div class="emol-react-row" id="emol-city-row">
                        <div class="emol-label-wrapper">
                        <label for="emol-city">' . EMOL_CITY . ' ' . $asterix . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                        <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="city" value="' . $data['city'] . '"  id="emol-city"  />
                        </div>
                        </div>';
        }

    }
    $reactHtml .= $frm;

    //Email address
    $frm = '';
    $option = get_option('emol_frm_com_email');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required email';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-email-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-email">' . EMOL_EMAIL . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . '  name="email" value="' . $data['email'] . '"  id="emol-email" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;

    //phone
    $frm = '';
    $option = get_option('emol_frm_com_phone');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-phone-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-phonenumber">' . EMOL_PHONE . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="phonenumber" value="' . $data['phonenumber'] . '" id="emol-phonenumber" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;


    //department
    $frm = '';
    $option = get_option('emol_frm_com_dept');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-department-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-department">' . EMOL_REACT_DEPARTMENT . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="department" value="' . $data['department'] . '" id="emol-department" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;


    //coc
    $frm = '';
    $option = get_option('emol_frm_com_coc');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-coc-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-coc">' . EMOL_REACT_COC . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . '  name="coc" value="' . $data['coc'] . '" id="emol-coc" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;


    //jobName
    $frm = '';
    $option = get_option('emol_frm_com_job');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-jobName-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-jobName">' . EMOL_REACT_JOB . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="text" class="emol-text-input ' . $req . '" ' . $req . ' name="jobName" value="' . $data['jobName'] . '" id="emol-jobName" />
                        </div>
                    </div>
                    <div class="emol-react-row" id="emol-jobDocument-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-jobDocument">' . EMOL_REACT_JOBDOC . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="file" class="emol-text-input emol-file" name="jobDocument" id="emol-jobDocument" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;


    //logo
    $frm = '';
    $option = get_option('emol_frm_com_logo');
    if ($option != '') {

        $req = '';
        if ($option == 'yes_req') {
            $req = 'required';
        }
        $frm = '
                    <div class="emol-react-row" id="emol-logo-row">
                        <div class="emol-label-wrapper">
                            <label for="emol-logo">' . EMOL_REACT_LOGO . '</label>
                        </div>
                        <div class="emol-input-wrapper">
                            <input type="file" class="emol-text-input emol-file ' . $req . '" ' . $req . ' name="logo" id="emol-logo" />
                        </div>
                    </div>
                    ';
    }
    $reactHtml .= $frm;

} else {
    //logged on
    $api = $this->emolApi->get('company');
    $comp = $api->getSummaryPrivate();

    $api = $this->emolApi->get('job');
    $jobs = $api->getPublishedByCompany(emol_session::get('company_id'), array());

    $items = '';

    if (count($jobs) > 0) {
        $items = '<select name="jobId">';
        foreach ($jobs as $job) {
            $items .= '<option value=' . $job['id'] . '>' . $job['name'] . '</option>';
        }
        $items .= '</select>';
    }

    if ($items != '') {
        $items = ' <div class="emol-react-row" id="emol-jobitem-row">
                                <div class="emol-input-wrapper">
                                ' . EMOL_REACT_JOB . '
                                </div>
                                <div class="emol-input-wrapper">
                                ' . $items . '
                                </div>
                                </div>';
    }

    $reactHtml .= '
                 <div class="emol-react-row" id="emol-reactonbehalf-row">
                <div class="emol-input-wrapper">
                ' . $comp['Person']['fullname'] . '
                </div>
                <div class="emol-input-wrapper">
                ' . EMOL_REACT_BEHALF . ' ' . ucfirst($comp['Company']['name']) . '
                </div>
                </div>
                ' . $items;
}


$reactHtml .= '
            <div class="emol-react-row" id="emol-motivation-row">
                <div class="emol-label-wrapper">
                    <label for="emol-motivation">' . EMOL_REACT_MESSAGE . '</label>
                </div>
                <div class="emol-input-wrapper">
                    <textarea class="emol-text-input emol-textarea" name="motivation" id="emol-motivation">' . $data['motivation'] . '</textarea>
                </div>
            </div>';


if (!empty($data['captcha-error'])) {
    $reactHtml .= '<div class="emol-apply-row" id="captcha-error"><div class="emol-input-wrapper">' . EMOL_CAPTCHA_INCORRECT . '</div></div>';
}

if (get_option('emol_frm_google_captcha_sitekey') && get_option('emol_frm_google_captcha_secret')) {

    $reactHtml .= '
        <div class="emol-apply-row" id="emol-captcha-row">
        <div class="emol-input-wrapper">&nbsp;</div>
        <div class="emol-input-wrapper"><div class="g-recaptcha" data-sitekey="' . get_option('emol_frm_google_captcha_sitekey') . '"></div></div>
        </div>';
} else {

    $reactHtml .= '
        <div class="emol-apply-row" id="emol-captcha-row">

        <div class="emol-input-wrapper">' . $this->captcha->getImageTag() . '</div>
        <div class="emol-input-wrapper">' . $this->captcha->getFormFields() . '</div>
        </div>';
}

$reactHtml .= '<div class="emol-react-row" id="emol-submit-row">
                <div class="emol-label-wrapper">
                    &nbsp;
                </div>
                <div class="emol-input-wrapper">
                    <input type="submit" class="emol-button emol-button-submit emol-button-react" value="' . EMOL_REACT_SEND . '" />
                </div>
            </div>';
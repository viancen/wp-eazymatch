<?php
if (!defined('EMOL_DIR')) {
	die('no direct access');
}

if (!emol_session::isValidId('applicant_id')) {

	//create the form
	$applyHtml .= '
		<div id="eazymatch-wait-modal" class="eazymatch-modal">Eén moment geduld, uw sollicitatie wordt verwerkt.</div>

        <div class="emol-label-wrapper" id="emol-connect-widget">
			<a href="javascript:;" class="emol-login-logo emol-button" onclick="emolLoginPopup();">Login met EazyMatch</a> 
			<span class="emol-app-log-form">|</span> 
			<a class="emol-linkedin-logo emol-button" onclick="emol_connect_linkedin(\'' . $url . '\',\'' . $api->instanceName . '\');" href="javascript:;">Solliciteer met LinkedIN</a>
        </div>
        ';

	//
	$gen1 = (isset($data['gender']) && $data['gender'] = 'm') ? 'selected="selected"' : '';
	$gen2 = (isset($data['gender']) && $data['gender'] = 'f') ? 'selected="selected"' : '';
	$frmGender = '
        <div class="emol-apply-row" id="emol-gender-row">
        <div class="emol-label-wrapper">
        <label for="emol-gender-male">' . EMOL_REACT_GENDER . '</label>
        </div>
        <div class="emol-input-wrapper">
        <select name="gender" class="emol-select-input">
            <option id="emol-gender-male" value="m" ' . $gen1 . '>' . EMOL_REACT_MALE . '</option>
            <option id="emol-gender-female" value="f" ' . $gen2 . '>' . EMOL_REACT_FEMALE . '</option>
        </select>
        </div>
        </div>';
	$applyHtml .= $frmGender;

	//NAMES
	$frm = '';
	$option = get_option('emol_frm_app_name');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$frm = '
            <div class="emol-apply-row" id="emol-firstname-row">
				<div class="emol-label-wrapper">
					<label for="emol-firstname">' . EMOL_FIRSTNAME . ' ' . $asterix . '</label>
				</div>
				<div class="emol-input-wrapper">
				 <input type="text"  class="emol-text-input ' . $req . '" placeholder="' . EMOL_FIRSTNAME . '" name="firstname" id="emol-firstname" value="' . $data['firstname'] . '" />
				</div>
            </div>

            <div class="emol-apply-row" id="emol-lastname-row">
				<div class="emol-label-wrapper">
					<label for="emol-middlename">' . EMOL_MIDDLENAME . ' &amp;</label> <label for="emol-lastname">' . EMOL_LASTNAME . '  ' . $asterix . '</label>
				</div>
				<div class="emol-input-wrapper">
					<input type="text" class="emol-text-input emol-small"  placeholder="' . EMOL_MIDDLENAME . '" name="middlename" value="' . $data['middlename'] . '"  id="emol-middlename" />
					<input type="text" class="emol-text-input ' . $req . '"  placeholder="' . EMOL_LASTNAME . '"  value="' . $data['lastname'] . '" name="lastname" id="emol-lastname" />
				</div>
            </div>';
	} else {
		$option = get_option('emol_frm_app_firstname');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}
			$frm .= '
                <div class="emol-apply-row" id="emol-firstname-row">
					<div class="emol-label-wrapper">
						<label for="emol-firstname">' . EMOL_FIRSTNAME . ' ' . $asterix . '</label>
					</div>
					<div class="emol-input-wrapper">
					<input type="text" class="emol-text-input ' . $req . '"  placeholder="' . EMOL_FIRSTNAME . '" name="firstname" id="emol-firstname" value="' . $data['firstname'] . '" />
					</div>
                </div>';
		}

		$option = get_option('emol_frm_app_middlename');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}
			$frm .= '
                <div class="emol-apply-row" id="emol-middlename-row">
                <div class="emol-label-wrapper">
                <label for="emol-middlename">' . EMOL_MIDDLENAME . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '"  placeholder="' . EMOL_MIDDLENAME . '" name="middlename" id="emol-middlename" value="' . $data['middlename'] . '" />
                </div>
                </div>';
		}

		$option = get_option('emol_frm_app_lastname');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}
			$frm .= '
                <div class="emol-apply-row" id="emol-lastname-row">
                <div class="emol-label-wrapper">
                <label for="emol-lastname">' . EMOL_LASTNAME . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '"  placeholder="' . EMOL_LASTNAME . '" name="lastname" id="emol-lastname" value="' . $data['lastname'] . '" />
                </div>
                </div>';
		}
	}

	$applyHtml .= $frm;


	//ADDRESS
	$frm = '';
	$option = get_option('emol_frm_app_address');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$frm = '<div class="emol-apply-row" id="emol-address-row">
            <div class="emol-label-wrapper">
            <label for="emol-zipcode">' . EMOL_ZIPCODE . ' + </label>
            <label for="emol-housenumber">' . EMOL_HOUSENUMBER . '  + </label>
            <label for="emol-extension">' . EMOL_EXTENSION . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" class="emol-text-input emol-small ' . $req . '" placeholder="' . EMOL_ZIPCODE . '" minlength=6 name="zipcode" value="' . $data['zipcode'] . '" id="emol-zipcode"  />
            <input type="text" class="emol-text-input emol-small"  placeholder="' . EMOL_HOUSENUMBER . '" name="housenumber" id="emol-housenumber" value="' . $data['housenumber'] . '" />
            <input type="text" class="emol-text-input emol-small" name="extension"  placeholder="' . EMOL_EXTENSION . '" id="emol-extension" value="' . $data['extension'] . '"  />
            </div>
            </div>';
	} else {
		$option = get_option('emol_frm_app_street');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$frm .= '<div class="emol-apply-row" id="emol-street-row">
                <div class="emol-label-wrapper">
                <label for="emol-street">' . EMOL_STREET . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '" placeholder="' . EMOL_STREET . '" name="street" value="' . @$data['street'] . '" id="emol-street"  />
                </div>
                </div>';
		}
		$option = get_option('emol_frm_app_housenr');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$frm .= '<div class="emol-apply-row" id="emol-housenumber-row">
                <div class="emol-label-wrapper">
                <label for="emol-housenumber">' . EMOL_HOUSENUMBER . ' ' . $asterix . '</label>
                </div>
               <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '"  placeholder="' . EMOL_HOUSENUMBER . '" name="housenumber" value="' . $data['housenumber'] . '" id="emol-housenumber"  />
                </div>
                </div>';
		}
		$option = get_option('emol_frm_app_extension');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$frm .= '<div class="emol-apply-row" id="emol-extension-row">
                <div class="emol-label-wrapper">
                <label for="emol-extension">' . EMOL_EXTENSION . ' ' . $asterix . '</label>
                </div>
               <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '" name="extension"  placeholder="' . EMOL_EXTENSION . '" value="' . $data['extension'] . '" id="emol-extension"  />
                </div>
                </div>';
		}
		$option = get_option('emol_frm_app_zipcode');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$frm .= '<div class="emol-apply-row" id="emol-zipcode-row">
                <div class="emol-label-wrapper">
                <label for="emol-zipcode">' . EMOL_ZIPCODE . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '" name="zipcode" placeholder="' . EMOL_ZIPCODE . '" value="' . $data['zipcode'] . '" id="emol-zipcode"  />
                </div>
                </div>';
		}
		$option = get_option('emol_frm_app_city');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$frm .= '<div class="emol-apply-row" id="emol-city-row">
                <div class="emol-label-wrapper">
                <label for="emol-city">' . EMOL_CITY . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                <input type="text" class="emol-text-input ' . $req . '" name="city"  placeholder="' . EMOL_CITY . '" value="' . $data['city'] . '" id="emol-city"  />
                </div>
                </div>';
		}
		$option = get_option('emol_frm_app_country');
		if ($option != '') {
			$req = '';
			$asterix = '';
			if ($option == 'yes_req') {
				$req = 'required';
				$asterix = ' <strong class="emol-required-asterix">*</strong>';
			}

			$selectBox = '<select name="country_id" id="emol-country_id" class="emol-select-input ' . $req . '"><option></option>';
			$options = emol_data_list::get('countries');
			foreach ($options as $opt) {
				if ($data['country_id'] == $opt['id']) {
					$selectBox .= '<option value="' . $opt['id'] . '" selected="selected">' . $opt['name'] . '</option>';
				} else {
					$selectBox .= '<option value="' . $opt['id'] . '">' . $opt['name'] . '</option>';
				}
			}
			$selectBox .= '</select>';

			$frm .= '<div class="emol-apply-row" id="emol-country-row">
                <div class="emol-label-wrapper">
                <label for="emol-country">' . EMOL_COUNTRY . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                ' . $selectBox . '
                </div>
                </div>';
		}
	}
	$applyHtml .= $frm;


	//emol_frm_app_nationality
	$frm = '';
	$option = get_option('emol_frm_app_nationality');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$selectBox = '<select name="nationality_id" id="emol-nationality_id" class="emol-select-input ' . $req . '"><option></option>';
		$options = emol_data_list::get('nationalities');
		foreach ($options as $opt) {
			if ($data['nationality_id'] == $opt['id']) {
				$selectBox .= '<option value="' . $opt['id'] . '" selected="selected">' . $opt['name'] . '</option>';
			} else {
				$selectBox .= '<option value="' . $opt['id'] . '">' . $opt['name'] . '</option>';
			}
		}
		$selectBox .= '</select>';


		$frm = '<div class="emol-apply-row" id="emol-nationality_id-row">
            <div class="emol-label-wrapper">
            <label for="emol-nationality_id">' . EMOL_NATIONALITY . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            ' . $selectBox . '
            </div>
            </div>';
	}
	$applyHtml .= $frm;

	//maritalStatusses
	$frm = '';
	$option = get_option('emol_frm_app_maritalstatus');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$selectBox = '<select name="maritalstatus_id" id="emol-maritalstatus_id" class="emol-select-input ' . $req . '"><option></option>';
		$options = emol_data_list::get('maritalStatusses');
		foreach ($options as $opt) {
			if ($data['maritalstatus_id'] == $opt['id']) {
				$selectBox .= '<option value="' . $opt['id'] . '" selected="selected">' . $opt['name'] . '</option>';
			} else {
				$selectBox .= '<option value="' . $opt['id'] . '">' . $opt['name'] . '</option>';
			}
		}
		$selectBox .= '</select>';


		$frm = ' <div class="emol-apply-row" id="emol-maritalstatus-row">
            <div class="emol-label-wrapper">
            <label for="emol-maritalstatus">' . EMOL_ACCOUNT_APP_MARITALSTATUS . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            ' . $selectBox . '
            </div>
            </div>';
	}
	$applyHtml .= $frm;


	//Function
	$frm = '';
	$option = get_option('emol_frm_app_title');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$frm = ' <div class="emol-apply-row" id="emol-title-row">
            <div class="emol-label-wrapper">
            <label for="emol-title">' . EMOL_ACCOUNT_APP_TITLE . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text"  placeholder="' . EMOL_ACCOUNT_APP_TITLE . '" class="emol-text-input ' . $req . '" name="title" value="' . $data['title'] . '" id="emol-title" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;

	//PHONE
	$frm = '';
	$option = get_option('emol_frm_app_phone');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$frm = ' <div class="emol-apply-row" id="emol-phone-row">
            <div class="emol-label-wrapper">
            <label for="emol-phonenumber">' . EMOL_PHONE . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" placeholder="' . EMOL_PHONE . '" class="emol-text-input ' . $req . '" name="phonenumber" value="' . $data['phonenumber'] . '"  id="emol-phonenumber" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;

	//PHONE 2
	$frm = '';
	$option = get_option('emol_frm_app_phone2');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$frm = ' <div class="emol-apply-row" id="emol-phone2-row">
            <div class="emol-label-wrapper">
            <label for="emol-phonenumber2">' . EMOL_PHONE . ' 2 ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" placeholder="' . EMOL_PHONE . '" class="emol-text-input ' . $req . '" name="phonenumber2" value="' . $data['phonenumber2'] . '"  id="emol-phonenumber2" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;

	//EMAIL
	$frm = '';
	$frm = ' <div class="emol-apply-row" id="emol-email-row">
            <div class="emol-label-wrapper">
            <label for="emol-email">' . EMOL_EMAIL . '  <strong class="emol-required-asterix">*</strong></label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" required placeholder="' . EMOL_EMAIL . '" class="emol-text-input required email" value="' . $data['email'] . '" name="email" id="emol-email" />
            </div>
            </div>';

	$applyHtml .= $frm;

	//password
	$frm = '';
	$option = get_option('emol_frm_app_password');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$frm = ' <div class="emol-apply-row" id="emol-email-row">
            <div class="emol-label-wrapper">
            <label for="emol-password">' . EMOL_ACCOUNT_APP_PASSWORD . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="password" placeholder="' . EMOL_ACCOUNT_APP_PASSWORD . '" class="emol-text-input ' . $req . '" name="password" id="emol-password" />
            </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	//BIRTHDATE
	$frm = '';
	$option = get_option('emol_frm_app_birthdate');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}


		$datePickerBirth = emol_dateselector('birthdate', $data['birthdate'], $req);

		$frm = '
            <div class="emol-apply-row" id="emol-birthdate-row">
            <div class="emol-label-wrapper">
            <label for="emol-birthdate">' . EMOL_BIRTHDATE . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            ' . $datePickerBirth['day'] . ' ' . $datePickerBirth['month'] . ' ' . $datePickerBirth['year'] . '
            </div>
            </div>';
	}
	$applyHtml .= $frm;


	//bsn
	$frm = '';
	$option = get_option('emol_frm_app_ssn');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$frm = ' <div class="emol-apply-row" id="emol-ssn-row">
            <div class="emol-label-wrapper">
            <label for="emol-ssn">' . EMOL_BSN . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="ssn" placeholder="' . EMOL_BSN . '" class="emol-text-input ' . $req . '" name="ssn" id="emol-ssn" />
            </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	//CV
	$frm = '';
	$option = get_option('emol_frm_app_cv');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$frm = ' <div class="emol-apply-row" id="emol-resume-row">
            <div class="emol-label-wrapper">
            <label for="emol-cv">' . EMOL_APPLY_CV . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="file" class="emol-text-input emol-file ' . $req . '" name="cv" id="emol-cv" />
            </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	//PHOTO
	$frm = '';
	$option = get_option('emol_frm_app_photo');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$frm = ' <div class="emol-apply-row" id="emol-picture-row">
            <div class="emol-label-wrapper">
            <label for="emol-picture">' . EMOL_APPLY_PICTURE . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="file" class="emol-text-input emol-file ' . $req . '" name="picture" id="emol-picture" />
            </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	//LOCATION / HOLDING
	$frm = '';
	$option = get_option('emol_frm_app_managercompany');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$trunk = new EazyTrunk();
		$companylist = &$trunk->request('licence', 'allCompanys');
		$trunk->execute();

		$cOptions = '';
		foreach ($companylist as $lItem) {
			$cOptions .= '<option value="' . $lItem['id'] . '">' . $lItem['name'] . '</option>';
		}

		$frm = ' <div class="emol-apply-row" id="emol-managercompany-row">
            <div class="emol-label-wrapper">
            <label for="emol-managercompany_id">' . EMOL_ACCOUNT_APP_MANAGERCOMPANY . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                    <select class="emol-text-input ' . $req . '" name="managercompany_id" id="emol-managercompany_id">' . $cOptions . '</select>
                </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	// Competences
	$competenceElements = get_option('emol_frm_app_competence', array());

	if (count($competenceElements) > 0) {
		// get all competence childs via trunk request
		$trunk = new EazyTrunk();

		foreach ($competenceElements as &$competence) {
			$competence['list'] = &$trunk->request('competence', 'getChildren', array($competence['competence_id']));
		}

		// execute the trunk request
		$trunk->execute();

		foreach ($competenceElements as &$competence) {
			$formEl = '';

			$req = ($competence['required'] == 'yes' ? ' required' : '');
			$asterix = ($req == 'required') ? '<strong class="emol-required-asterix">*</strong>' : '';

			switch ($competence['formelement']) {
				case 'selectbox':

					$formEl = '<select name="competence' . $competence['competence_id'] . '[]" id="emol-competence-' . $competence['competence_id'] . '" class="emol-select-input' . $req . '"><option></option>';
					foreach ($competence['list'] as $competenceItem) {
						$formEl .= '<option value="' . $competenceItem['id'] . '">' . $competenceItem['name'] . '</option>';
					}
					$formEl .= '</select>';
					break;

				case 'checkbox':

					$firstCheckbox = true;
					foreach ($competence['list'] as $competenceItem) {
						$formEl .= '<input
                            type="checkbox" 
                            name="competence' . $competence['competence_id'] . '[]" 
                            value="' . $competenceItem['id'] . '"
                            id="emol-competence-' . $competenceItem['id'] . '"
                            /> ';

						$formEl .= '<label for="emol-competence-' . $competenceItem['id'] . '">' . $competenceItem['name'] . '</label>';
						$formEl .= '<br />';
						$firstCheckbox = false;
					}
					break;
			}


			$applyHtml .= ' <div class="emol-apply-row" id="emol-competence-row-' . $competence['competence_id'] . '">
                <div class="emol-label-wrapper">
                <label for="emol-competence-' . $competence['competence_id'] . '">' . $competence['label'] . ' ' . $asterix . '</label>
                </div>
                <div class="emol-input-wrapper">
                ' . $formEl . '
                </div>
                </div>
                ';
		}
	}

	// Highest schooling type
	$frm = '';
	$option = get_option('emol_frm_app_schoolingtype_id');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$selectBox = '<select name="schoolingtype_id" id="emol-schoolingtype_id" class="emol-select-input ' . $req . '"><option></option>';
		$options = emol_data_list::get('schoolingTypes');
		foreach ($options as $opt) {
			if ($data['schoolingtype_id'] == $opt['id']) {
				$selectBox .= '<option value="' . $opt['id'] . '" selected="selecte">' . $opt['name'] . '</option>';
			} else {
				$selectBox .= '<option value="' . $opt['id'] . '">' . $opt['name'] . '</option>';
			}
		}
		$selectBox .= '</select>';

		$frm = ' <div class="emol-apply-row" id="emol-schoolingtype_id-row">
            <div class="emol-label-wrapper">
            <label for="emol-schoolingtype_id">' . EMOL_ACCOUNT_APP_SCHOOLING_TYPE_HIGH_LABEL . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            ' . $selectBox . '
            </div>
            </div>
            ';
	}
	$applyHtml .= $frm;


	// SEARCHLOCATION
	$frm = '';
	$option = get_option('emol_frm_app_searchlocation');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$sl = isset($data['searchlocation']) ? $data['searchlocation'] : '';
		$frm = ' <div class="emol-apply-row" id="emol-searchlocation-row">
            <div class="emol-label-wrapper">
            <label for="emol-searchlocation">' . EMOL_ACCOUNT_APP_SEARCHLOCATION . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" class="emol-text-input ' . $req . '" name="searchlocation" value="' . $sl . '"  id="emol-searchlocation" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;


	// AVAILABLE hours
	$frm = '';
	$option = get_option('emol_frm_app_availablehours');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}

		$sl = isset($data['availablehours']) ? $data['availablehours'] : '';
		$frm = ' <div class="emol-apply-row" id="emol-availablehours-row">
            <div class="emol-label-wrapper">
            <label for="emol-availablehours">' . EMOL_ACCOUNT_APP_AVAILABLEHOURS . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
            <input type="text" class="emol-text-input ' . $req . ' number" name="availablehours" value="' . $sl . '"  id="emol-availablehours" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;

	// SALARY
	$frm = '';
	$option = get_option('emol_frm_app_salary');
	if ($option != '') {
		$req = '';
		$asterix = '';
		if ($option == 'yes_req') {
			$req = 'required';
			$asterix = ' <strong class="emol-required-asterix">*</strong>';
		}
		$sl = isset($data['salary']) ? $data['salary'] : '';
		$frm = ' <div class="emol-apply-row" id="emol-salary-row">
            <div class="emol-label-wrapper">
           	 <label for="emol-salary">' . EMOL_ACCOUNT_APP_SALARY . ' ' . $asterix . '</label>
            </div>
            <div class="emol-input-wrapper">
           	 <input type="text" class="emol-text-input ' . $req . '" name="salary" value="' . $sl . '"  id="emol-salary" />
            </div>
            </div>';
	}
	$applyHtml .= $frm;


	//CONTACT VIA
	$frm = '';
	$option = get_option('emol_frm_app_contactvia');
	if (trim($option) != '') {


		$selectBox = '<select name="contactvia" id="emol-contactvia" class="emol-select-input required"><option></option>';
		$options = explode(',', $option);
		foreach ($options as $opt) {
			//strip all spaces
			$opt = trim($opt);
			$selectBox .= '<option value="' . $opt . '">' . $opt . '</option>';
		}
		$selectBox .= '</select>';

		$frm = '
            <div class="emol-apply-row" id="emol-contactvia-row">
                <div class="emol-label-wrapper">
                    <label for="emol-contactvia">' . EMOL_CONTACTVIA . ' <strong class="emol-required-asterix">*</strong></label>
                </div>
                <div class="emol-input-wrapper">
                ' . $selectBox . '
                </div>
            </div>
            ';
	}
	$applyHtml .= $frm;

	//AVG
	$frm = '';
	$option = get_option('emol_frm_avg');
	if (trim($option) != '') {
		$option = str_replace(array(
			'{avg_phone}',
			'{avg_email}',
			'{avg_name}',
		), array(
			get_option('emol_frm_avg_phone'),
			get_option('emol_frm_avg_email'),
			get_option('emol_frm_avg_name'),
		), $option);
		$selectBox = '<label><input type="checkbox" id="emol-avg-check"  name="emol-avg-check" required class="required"> 
            <a href="javascript:;" id="emol-read-ps">' . EMOL_AVG_READ_CONFIRM . '</a></label>';
		$frm = '
            <div class="emol-apply-row" id="emol-avg-row">
                <div class="emol-label-wrapper">
                    <label for="emol-avg-check">' . EMOL_AVG_TITLE . ' <strong class="emol-required-asterix">*</strong></label>
                </div>
                <div class="emol-input-wrapper">
                    ' . $selectBox . '
                </div>
            </div>
            <div id="emolAvgStatement" style="display:none;">
            ' . ($option) . '
            </div>
            ';
	}
	$applyHtml .= $frm;


} else { //else if logged on:

	$api = $this->emolApi->get('applicant');
	$app = $api->getSummaryPrivate();

	//check if applicant has already did an application on this job
	$checkMediations = $this->emolApi->get('mediation')->byApplicantPrivate();

	if (is_array($checkMediations)) {
		foreach ($checkMediations as $mediated) {
			if ($mediated['job_id'] == $this->job['id']) {
				$applyHtml .= '
                    <div id="emol_warning_already_applyed">' . get_option('emol_warning_already_applyed') . '</div>
                    ';
			}
		}
	}

	$applyHtml .= '
        <div class="emol-label-row">
            <div class="emol-label-wrapper">
            	<input type="hidden" name="applicant_id" value="' . $app['id'] . '">Sollicitant
            </div>
            <div class="emol-input-wrapper">
            ' . $app['Person']['fullname'] . '
            </div>
        </div>
        ';
}


if (!isset($data)) {
	$data = array();
}

$option = get_option('emol_frm_app_motivation');
if ($option != '') {

	$req = '';
	$asterix = '';
	if ($option == 'yes_req') {
		$req = 'required';
		$asterix = ' <strong class="emol-required-asterix">*</strong>';
	}

	if (!isset($data['description'])) {
		$data['description'] = '';
	} elseif (is_array($data['description'])) {
		$data['description'] = count($data['description']) == 0 ? '' : $data['description'][0];
	}

	$applyHtml .= '
        <div class="emol-apply-row" id="emol-motivation-row">
			<div class="emol-label-wrapper">
				<label for="emol-motivation">' . EMOL_APPLY_MOTIVATION . ' ' . $asterix . '</label>
			</div>
			<div class="emol-input-wrapper">
			 	<textarea class="emol-text-input emol-textarea ' . $req . '" name="motivation" id="emol-motivation">' . $data['description'] . '</textarea>
			</div>
        </div>';
}

if (!empty($data['captcha-error'])) {
	$applyHtml .= '<div class="emol-apply-row" id="captcha-error">
						<div class="emol-input-wrapper">' . EMOL_CAPTCHA_INCORRECT . '</div>
				</div>';
}

if (get_option('emol_frm_google_captcha_sitekey') && get_option('emol_frm_google_captcha_secret')) {

	$applyHtml .= '
        <div class="emol-apply-row" id="emol-captcha-row">
			<div class="emol-input-wrapper">&nbsp;</div>
			
			<div class="emol-input-wrapper">
				<div class="g-recaptcha" data-callback="emolRecaptchaCallback" data-sitekey="' . get_option('emol_frm_google_captcha_sitekey') . '">
			</div>
        </div>';

	$applyHtml .= '
    <div class="emol-apply-row" id="emol-submit-row">
    <div class="emol-input-wrapper">
    &nbsp;
    </div>
		<div class="emol-input-wrapper">
			<input type="button" class="emol-button emol-form-submit" disabled id="emol-apply-submit-button" value="' . EMOL_APPLY_SEND . '" />
			<input type="button" class="emol-button" id="emol-apply-back-button" value="' . EMOL_BACK . '" onclick="history.go(-1)" />
		</div>
    </div>';

} else {
	$applyHtml = 'Stel eerst de google Captcha Keys in.';
}

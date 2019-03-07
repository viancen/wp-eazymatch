<?php
$img = '';
if ( $pic > '' ) {
	$img = '<div id="emol-account-pic-widget"><img src="data:image/png;base64,' . $pic . '" /></div>';
}

//saved in CMS
$forms = get_option( 'emol_forminstances' );

echo '<div id="emol-loggedinas">' . EMOL_LOGGEDINAS . ' <span class="emol-loginname">' . $userInfo['fullname'] . '</span></div>';
echo $img;
echo "<ul id=\"emol-applicant-account-menu\">";

if ( isset( $forms ) && is_array( $forms ) ) {
	foreach ( $forms['instances'] as $fpr ) {
		echo "<li id=\"emol-applicant-account-" . $fpr['formType'] . "-menu\" >
                <a href=\"/" . get_option( 'emol_account_url' ) . "/edit?f=" . $fpr['id'] . "\">" . $fpr['label'] . "</a>
            </li>";
	}
} else {
	echo "<li id=\"emol-applicant-account-naw-menu\" ><a href=\"/" . get_option( 'emol_account_url' ) . "/edit" . $trailingData . "\">" . EMOL_MENU_APP_NAW . "</a></li>";
}
echo "<li ><a href=\"/" . get_option( 'emol_account_url' ) . "/logout" . $trailingData . "\">" . EMOL_MENU_LOGOUT . "</a></li>";
echo "</ul>";
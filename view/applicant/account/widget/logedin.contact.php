<?php
echo '<div id="emol-loggedinas">' . EMOL_LOGGEDINAS . ' <span class="emol-loginname">' . $userInfo['fullname'] . '</span></div>';
echo "<ul>";
echo "<li><a href=\"/" . get_option( 'emol_company_account_url' ) . "/edit/\">" . EMOL_MENU_COMP_NAW . "</a></li>";
//echo "<li><a href=\"/".get_option( 'emol_company_account_url' )."/jobs/\">".EMOL_MENU_COMP_JOBS."</a></li>";
//echo "<li><a href=\"/".get_option( 'emol_company_account_url' )."/applications/\">".EMOL_MENU_COMP_APLIC."</a></li>";
echo "<li><a href=\"/" . get_option( 'emol_company_account_url' ) . "/logout" . $trailingData . "\">" . EMOL_MENU_LOGOUT . "</a></li>";
echo "</ul>";
<?php
echo "<form method=\"post\" action=\"/" . get_option( 'emol_account_url' ) . "/login/\">";
echo "<input type=\"hidden\" value=\"EMOL_LOGIN\" name=\"EMOL_LOGIN\">";
echo "<input type=\"text\" class=\"emol-text-input\" value=\"" . EMOL_LOGIN_USER . "\" onfocus=\"if(this.value == '" . EMOL_LOGIN_USER . "'){this.value='';}\" name=\"username\"><br>";
echo "<input type=\"password\" class=\"emol-text-input\" value=\"" . EMOL_LOGIN_PASS . "\" onfocus=\"if(this.value == '" . EMOL_LOGIN_PASS . "'){this.value='';}\" name=\"password\">";
echo "<div class=\"emol-submit-wrapper\"><button type=\"submit\" class=\"emol-button emol-button-login\">" . EMOL_MENU_LOGIN . "</button>";
echo "</div></form>";
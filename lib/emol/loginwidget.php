<?php
if ( ! defined( 'EMOL_DIR' ) ) {
	die( 'no direct access' );
}

$loginWidget = '<form id="emolLoginDialog" action="/' . get_option( 'emol_account_url' ) . '/login/" method="post">

  <fieldset>

  	<legend class="legend">' . EMOL_WIDGET_LOGIN . '</legend>

    <div class="input">
    	<input type="text" name="username" placeholder="Username" required />
    </div>

    <div class="input">
    	<input type="password" name="password"  placeholder="Password" required />
    </div>


  </fieldset>

</form>';


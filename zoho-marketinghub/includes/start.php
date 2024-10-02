<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . wp_create_nonce( 'mh-ajax-nonce' ) . '" />';
?>
<div class="zmhpopupgen" id="mh_disconnect_popup" style="display: none;">
    <div class="">
        <img src="<?php echo esc_url( plugins_url('../assets/images/alert-circle.svg', __FILE__ ) ); ?>" />
    </div>
    <p>Are you sure you want to disconnect Zoho Marketing Automation plugin?</p>
    <div class="zmhconfsigcont zmhcenter" >
        <button class="zmhbtn zmhpri zmhmb35 zmhmr20" id="mh_remove">Disconnect</button>
        <button class="zmhbtn zmhcan zmhmb35 zmhmr20" onclick="confirm_activate(0);">Cancel</button>
    </div>
</div>

<div class="zmhcontaainer">
	<div class="zmhaccoutname">
	    <div class="zmhaccoutright">
		    <div class="zmhaccname">
			    <span class="email"><?php echo esc_attr(get_option("zmhub_user_email")); ?></span> 
			    <?php $time = intval(get_option('zmhub_connect_time'));?>
			    <span class="timedate"> Connected On <?php echo get_date_from_gmt(date('Y-m-d H:i:s' , $time), get_option('date_format') .' '. get_option('time_format') ); ?></span>
		    </div>
	    	<input type="submit" id="mh-disconnect" value="Disconnect Account">
	    </div>
	</div>
	<div class="zmhtextland">
	    <img src="<?php echo esc_url( plugins_url('../assets/images/MHL_01B.svg', __FILE__ ) ); ?>">
	    <h1>Welcome to Zoho Marketing Automation</h1>
	    <p>You have now connected your Zoho Marketing Automation account</p>
	    <div class="zmhauthenticatecont">
	    	<div class="zmhauthenticatebox">
	            <img src="<?php echo esc_url( plugins_url('../assets/images/zc-wa-empty.svg', __FILE__ ) ); ?>">
	            <h1>Web Assistant</h1>
	            <p>Get the Web Assistant tracking code from your Zoho Marketing Automation account to track your pages and posts.</p>
	            <button class="zmhbtn zmhpri zmhmb35" onclick="window.location = 'admin.php?page=mh-wa'">Go To Web Assistant</button>
	        </div>
	        <div class="zmhauthenticatebox">
	            <img src="<?php echo esc_url( plugins_url('../assets/images/zc-library-signup-form.svg', __FILE__ ) ); ?>">
	            <h1>Signup Form</h1>
	            <p>Bring your Zoho Marketing Automation's signup forms and embed them in your website using the forms' short code.</p>
	            <button class="zmhbtn zmhpri zmhmb35" onclick="window.location = 'admin.php?page=mh-forms'">Go To Forms</button>
	        </div>
	    </div>
	</div>
</div>
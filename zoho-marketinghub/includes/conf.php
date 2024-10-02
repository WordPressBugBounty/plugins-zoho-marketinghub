<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . wp_create_nonce( 'mh-ajax-nonce' ) . '" />';
?>
<div class="zmhcontainer">
    <div class="zmhtextland">
        <img src="<?php echo esc_url( plugins_url('../assets/images/MHL_01B.svg', __FILE__ ) ); ?>" class="landlogo">
        <h1 class="landtit" >Welcome to Zoho Marketing Automation</h1>
        <p>Integrate with Zoho Marketing Automation and benefit from signup forms and webassistant.</p>
        <div class="zmhbtncont">
			<button class="zmhbtn zmhpri zmhconnect" name="zh-submit" id="submit" value="Connect">Connect</button>
        </div>
    </div>
</div>

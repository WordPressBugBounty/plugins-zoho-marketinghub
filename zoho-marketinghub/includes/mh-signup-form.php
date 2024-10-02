<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
if ( ! current_user_can( 'manage_options' ) ) {
     die();
}
?>
<div class="zmhflodwa">
    <div class="zhmworcont">
        <div class="zhmworlod">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
        <h1>Please wait while we update your signup forms</h1>
    </div>
</div>
<div class="zmhtit">
    <img src="<?php echo esc_url( plugins_url('../assets/images/MHL_01B.svg', __FILE__ ) ); ?>">
    <h1>Zoho Marketing Automation - Signup Forms</h1>
</div>
 <div class="zhmalertmsg greenband" style="display:none" onclick="closeBand()" >
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
    </div>

   <div class="zhmalertmsg redband" style="display:none" onclick="closeBand()">
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>"></button>
    </div>
<?php 
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . wp_create_nonce( 'mh-ajax-nonce' ) . '" />';
global $wpdb, $table_prefix;
$tblname = $table_prefix . 'zmhub_forms';
$sql = "SELECT * FROM $tblname WHERE id > 0 and status != 0 ORDER BY created_time DESC";
$allforms = $wpdb->get_results($sql , ARRAY_A);

if( !$allforms) { ?>
    <div class="zmhcontainer">
        <div class="zmhsignupland">
            <img src="<?php echo esc_url( plugins_url('../assets/images/zc-library-signup-form.svg', __FILE__ ) ); ?>">
            <h1>Signup Forms</h1>
            <p>Bring your Zoho Marketing Automation's signup forms and embed them in your website using the forms' short code.</p>
            <div class="zmhbtncont">
                <input type="button" id="getForms" class="zmhbtn zmhpri" value="Fetch Signup Forms">
                <div class="zhmworcont" style="display: none;">
                    <div class="zhmworlod">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
	<div class="zmhpopupgen" id="formRefreshPopup" style="display: none;">
        <div class="">
            <img src="<?php echo esc_url( plugins_url('../assets/images/alert-circle.svg', __FILE__ ) ); ?>" />
        </div>
        <p>New forms created in Zoho Marketing Automation will be added and existing forms details will be updated. Are you sure you want to continue?</p>
        <div class="zmhconfsigcont zmhcenter">
            <button class="zmhbtn zmhpri zmhmb35 zmhmr20" onclick="mh_refresh_forms();">Refresh</button>
            <button class="zmhbtn zmhcan zmhmb35" onclick="confirm_activate(0)">Cancel</button>
        </div>
    </div>
	<div id="mh_form_body">
     <div class="tc f15" style="margin: 20px 0 20px;float: left;width: 100%;">Copy signup form's short code and insert it in your pages to embed the form. You can choose to hide or show your forms using the toggle corresponding to it.</div>
     <div class="zmhsignrefrsh">
        <div class="zmhsignrefrshinr">
            <a id="mhrefreshForm" style="cursor: pointer;"><img src="<?php echo esc_url( plugins_url('../assets/images/refresh.svg', __FILE__ ) ); ?>"><span>Refresh</span></a>
        </div>
    </div>
    <div class="zmhsignformlst">
  <?php  $flag=0; foreach ($allforms as $singleform) { ?>
        <div class="zmhsiforlstcont">
            <div class="zmhsiforlstlft">
                <div class="zmhsigtit ">
                    <div class="zmhsiforarr <?php if($flag==0) echo 'rota90'; ?> \" >
                        <a><svg style="width:20px;height:20px" viewBox="0 0 24 24">
                                <path fill="#7a7a7a" d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"></path>
                            </svg></a>
                    </div>
                    <div class="zmhsiforico">
                        <img src="<?php echo esc_url( plugins_url('../assets/images/zc_signup_li.svg', __FILE__ ) ); ?>">
                    </div>
                    <div class="zmhsifornamtim">
                        <h1><?php echo esc_html($singleform['form_name']); ?></h1>
                        <p>
                            <span class="<?php if($singleform['status'] == 2) echo "active"; else if($singleform['status'] == 1) echo "inactive"; else if($singleform['status'] == 3) echo "hidden"; ?>"></span>
                            <span><?php if($singleform['status'] == 2) echo "Visible"; else if($singleform['status'] == 1) echo "Not used";  else if($singleform['status'] == 3) echo "Hidden"; ?></span>
                            <span>|</span>
                            <span>Created on <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', substr($singleform['created_time'], 0 , -3)), get_option('date_format')); ?></span>
                            <div class="zmhformtogche animOff" style ="display: <?php if($singleform['status'] == 2 || $singleform['status'] == 3) echo 'block;'; else echo 'none;';?>"><label class="zmhcodechecklab mhlab animOff <?php if($singleform['status'] == 2) echo "active"?>" mhid="<?php echo esc_html($singleform['id'])?>"></label></div>
                        </p>
                    </div>
                </div>
                <div class="zmhsigfomdet" <?php if($flag == 0) echo ' style="display: block;"'; ?> >
                    <div class="zmhsigfomdetinr">
                        <span>Type</span>
                        <span><?php echo esc_html($singleform['form_type']); ?></span>
                    </div>
                    <div class="zmhsigfomdetinr">
                        <span>Short Code</span>
                        <?php if($singleform['status'] == 2 || $singleform['status'] == 3) {?>
                        <span>[zmauto id = <?php echo esc_html($singleform['id']) ?>]</span> 
                    <?php } else { ?>
                        <span class="mh_sc animOff" style= "background-color: transparent;color: #009ad5;cursor: pointer; text-decoration: underline;" mhid="<?php echo esc_html($singleform['id'])?>" >Click to generate</span>  <?php }?>
                        <button data-balloon="Copy code" data-balloon-pos="up" style ="display: <?php if($singleform['status'] == 2 || $singleform['status'] == 3) echo 'block;'; else echo 'none;';?>"><img class="animOff mhclip" src="<?php echo esc_url( plugins_url('../assets/images/code-copy.svg', __FILE__ ) ); ?>" mhtext="[zmauto id = <?php echo esc_html($singleform['id']) ?>]"></button>
                        <span style="display:none;" id="zmh_code_msg">Copied</span>
                    </div>
                    <div class="zmhsigfomdetinr">
                        <span>List</span>
                        <span class="zmhlabsig"><img src="<?php echo esc_url( plugins_url('../assets/images/addressbook.svg', __FILE__ ) ); ?>"><?php echo esc_html(strlen($singleform['list_name']) > 50 ? substr($singleform['list_name'],0,50)."..." : $singleform['list_name']); ?></span>
                    </div>
                </div>
            </div>
            <div class="zmhsiforlstrit zmhloading" <?php if($flag == 0) echo ' style="display: block;"';?>>
                <iframe id="formview" style="width: 100%; <?php if(strpos($singleform['form_type'], 'Horizontal') != false) echo 'margin-top: 5%;'; else echo 'max-height: 250px; min-height: 250px;';?>" <?php if($flag == 0) {echo "src =" .esc_url($singleform['url']) .'&m=p'; echo ' loaded = true'; } else echo 'loaded = false'?> srcval="<?php echo esc_url($singleform['url']) .'&m=p'; ?>"> </iframe>
            </div>
        </div>
    <?php $flag = 1; } ?>
    </div>
    <!--  <div class="zmhnotesbtm">
        <p><b>Note:</b>New signup forms created in Automation will take some time to be updated here. Signup forms deleted from
            Automation will be removed from the plugin.</p>
    </div> -->
</div>
<?php } ?>

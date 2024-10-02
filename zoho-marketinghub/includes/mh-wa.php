<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
if ( ! current_user_can( 'manage_options' ) ) {
     die();
}
?>

<div class="zmhtit">
    <img src="<?php echo esc_url( plugins_url('../assets/images/MHL_01B.svg', __FILE__ ) ); ?>">
        <h1>Zoho Marketing Automation - Web Assistant</h1>
    </div>

    <div class="zhmalertmsg greenband" style="display:none">
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>" onclick="closeBand()"></button>
    </div>

   <div class="zhmalertmsg redband" style="display:none">
        <p></p>
        <button><img src="<?php echo esc_url( plugins_url('../assets/images/close.svg', __FILE__ ) ); ?>" onclick="closeBand()"></button>
    </div>
    <div class="zhmalertmsg flash" style="display: none">
        <p>Settings has been saved successfully.</p>
    </div>
<?php
echo '<input type="hidden" name="mh-ajax-nonce" id="mh-ajax-nonce" value="' . wp_create_nonce( 'mh-ajax-nonce' ) . '" />';
$mh_script = esc_html(trim(get_option('zmhub_script')));
if(!$mh_script) { ?>
<div id="mhstart">
    <div class="zmhcontainer">
        <div class="zmhsignupland">
            <img src="<?php echo esc_url( plugins_url('../assets/images/zc-wa-empty.svg', __FILE__ ) ); ?>">
            <h1>Web Assistant</h1>
            <p>Get the Web Assistant tracking code from your Zoho Marketing Automation account to track your pages and posts.</p>
            <div class="zmhbtncont">
                <input type="button" id="getCode" class="zmhbtn zmhpri" value="Get Code">
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
</div>
<?php }
else { if(isset($_GET['saved'])){
      if($_GET['saved'] == 'false')
      { ?>
         <script>jQuery('.redband').find('p').text("An internal error occured while saving the settings.");
         jQuery('.redband').show(); </script> <?php
      }
      else {?>
      <script>jQuery('.flash').show().delay(2000).fadeOut(); </script> <?php
      }
    }
   $page_scripts = ''; if(get_option('zmhub_script_setting')){
    $page_scripts =unserialize(get_option('zmhub_script_setting')); } 
    else {?> <script>mh_success_msg("Code fetched from Automation. Save your settings here. Only then your pages and posts will be tracked.");</script> <?php }?>
<div class="zmhpopupgen" id="webAutoStatusPopup" style="display: none;">
        <div class="">
            <img src="<?php echo esc_url( plugins_url('../assets/images/alert-circle.svg', __FILE__ ) ); ?>" />
        </div>
        <p>Are you sure you do not want to track your pages and posts?</p>
        <div class="zmhconfsigcont zmhcenter">
            <button class="zmhbtn zmhpri zmhmb35 zmhmr20" onclick="confirm_activate(1)">Disable Tracking</button>
            <button class="zmhbtn zmhcan zmhmb35" onclick="confirm_activate(0)">Cancel</button>
        </div>
    </div>

<div style="margin-left:-20px; position: relative; " id="wa_body">
<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="zmhub_form" >
            <input type="hidden" name="action" value="zmhub_save_settings"> 
            <div class=" p40" style="position: relative; z-index: 1;">
            <div class="tc" style="margin: 80px 0 20px;float: left;width: 90%;">Monitor the activities in your web pages and posts, and analyze their performance using the web assistant tracking code.</div>
            <div class="cmpfrm uslct w70 mt50" id ="zcwp_page" style="display:<?php if(!$mh_script){ echo "none";} ?>">
                
                   <ul>
                  <li class="vt" style="width: 170px;"><label>Tracking code</label></li>
                  <li>
                  <div id="test" style="position:relative">
                  <textarea class="w100" id="mhsnippet" name="mhsnippet" style="align-content:center; font-family: monospace; font-size: 16px;" readonly="true"><?php echo preg_replace('/\s+/', '', trim($mh_script)); ?></textarea>
                  </div>
                  <div class="errdiv mt5" id="testErr" style="display: none;">Please paste your code snippet</div>
                  </li>
                  </ul>
                  <ul>
                    <li></li>
                    <li>
                       <?php if($page_scripts && $page_scripts['zmhub_status'] == 0) { ?>
                          <label class="zmhbtnonoff">
                     <?php } else {  ?>
                          <label class="zmhbtnonoff zhmtoglabon">
                       <?php }?>
                    <span></span>
                    </label>
                    <span>Your pages and posts will not be tracked if you turn this OFF.</span>
                    </li>
                  </ul>

                  <ul>
                  <li><label>Track<span class="reqird">*</span></label></li>
                  <li>
                  <div id="add_page_tracking_script_global" class="f15 txtcnt nwrp">
                        <?php if($page_scripts && isset($page_scripts['zmhub_code_loc'])) { $var = $page_scripts['zmhub_code_loc']; if(!strcmp($var,'global') || empty($var)) { ?>
                        <i class="zcicon-radiobox-marked vm"  value ="global" id = "global" onclick="changevalue('global')"></i> <span style="vertical-align: middle;">All pages and posts</span>
                        <span class="ml30">
                       <i class="zcicon-radiobox-blank vm" value ="specific" id = "specific" onclick="changevalue('specific')"></i> <span style="vertical-align: middle;">Specific pages and posts</span></span>
                        <span class="ml30"> <i class="zcicon-radiobox-blank vm" value ="cateogry" id ="Cat" onclick="changevalue('cateogry')"></i> <span style="vertical-align: middle;">Specific category of posts</span></span>
                        <?php } ?>  
                         <?php $var =  $page_scripts['zmhub_code_loc']; if(!strcmp($var,'specific')) { ?>
                        <i class="zcicon-radiobox-blank vm"  value ="global" id = "global" onclick="changevalue('global')"></i> <span style="vertical-align: middle;">All pages and posts</span>
                        <span class="ml30">
                        <i class="zcicon-radiobox-marked vm"  value ="specific" id ="specific" onclick="changevalue('specific')" ></i> <span style="vertical-align: middle;">Specific pages and posts</span></span>
                        <span class="ml30"><i class="zcicon-radiobox-blank vm"  value ="cateogry" id ="Cat"  onclick="changevalue('cateogry')"></i> <span style="vertical-align: middle;">Specific category of posts</span></span>
                        <?php } ?> 
                        <?php $var =  $page_scripts['zmhub_code_loc']; if(!strcmp($var,'cateogry')) { ?>
                        <i class="zcicon-radiobox-blank vm" value ="global" id = "global" onclick="changevalue('global')"></i> <span style="vertical-align: middle;">All pages and posts</span>
                       <span class="ml30">
                       <i class="zcicon-radiobox-blank vm" value ="specific" id ="specific" onclick="changevalue('specific')"></i> <span style="vertical-align: middle;">Specific pages and posts</span></span>
                         <span class="ml30"> <i class="zcicon-radiobox-marked vm" value ="cateogry" id ="Cat" onclick="changevalue('cateogry')" ></i> <span style="vertical-align: middle;">Specific category of posts</span></span>
                        <?php } } else{ ?>
                            <i class="zcicon-radiobox-marked vm"  value ="global" id = "global" onclick="changevalue('global')"></i> <span style="vertical-align: middle;">All pages and posts</span>
                            <span class="ml30">
                            <i class="zcicon-radiobox-blank vm" value ="specific" id = "specific" onclick="changevalue('specific')"></i> <span style="vertical-align: middle;">Specific pages and posts</span></span>
                            <span class="ml30"> <i class="zcicon-radiobox-blank vm" value ="cateogry" id ="Cat" onclick="changevalue('cateogry')"></i> <span style="vertical-align: middle;">Specific category of posts</span></span>
                        <?php }?>     
                        <input type="hidden" name="zmhub_code_loc" id = "zmhub_code_loc" value= '<?php if($page_scripts && isset($page_scripts['zmhub_code_loc'])) { echo esc_html($page_scripts['zmhub_code_loc']);} else echo esc_html("global"); ?>'>
                        <input type="hidden" name="zmhub_postvalue" id = "zmhub_postvalue" value= '<?php if($page_scripts && isset($page_scripts['zmhub_postvalue']) && $page_scripts['zmhub_postvalue'] !=0) { echo esc_html($page_scripts['zmhub_postvalue']);} else echo ""; ?>'>
                        <input type="hidden" name="zmhub_pagevalue" id = "zmhub_pagevalue" value= '<?php if($page_scripts && isset($page_scripts['zmhub_pagevalue']) && $page_scripts['zmhub_pagevalue'] !=0) { echo esc_html($page_scripts['zmhub_pagevalue']);} else echo ""; ?>'>
                        <input type="hidden" name="zmhub_status" id = "zmhub_status" value= '<?php if($page_scripts && $page_scripts['zmhub_status']==0) { echo esc_html($page_scripts['zmhub_status']);} else echo 1; ?>'>
                        <input type="hidden" name="zmhub_cateogry" id = "zmhub_cateogry" value= '<?php if($page_scripts && isset($page_scripts['zmhub_cateogry'])) { echo esc_html($page_scripts['zmhub_cateogry']);} else echo ""; ?>'>
                        <input type="hidden" name="ldsubmit" id="ldsubmit">
                        <input type="hidden" name="zmhub_change" id="zmhub_change" value = "<?php echo 0;?>">
                        <div class="errdiv mt5" id="add_page_tracking_script_globalErr" style="display: none;">Please select at least one page or post to proceed</div>
            </div>
                  </li>
                  </ul>

                   <ul id="selectpo" style="display:none;">
                  <li><label>Content type<span class="reqird">*</span></label></li>
                  <li>
                  <div><i class="zcicon-checkbox-blank-outline vm" id ="pagebutton" onclick="showpage(this)"></i> 
                 <span style="vertical-align: middle;">&nbsp;Pages</span>
                </div>
                  <div  id = "selectedpage" style="display:none; background-color: #fff;" >
                   <ul>
                          <li class="vt"><label></label></li>
                          <li><div class="w100">
                           <ul class="mlslct">
                           <li id="selectedpagelist">   
                            <?php $pagesId[] =""; if($page_scripts && isset($page_scripts['zmhub_pagevalue'])  && $page_scripts['zmhub_pagevalue'] !=0) {
                                  $pagesId = explode(",", $page_scripts['zmhub_pagevalue']);

                                  foreach ($pagesId as $Id) { ?>
                                     <div id="copy_<?php echo $Id; ?>"><span> <i class= "zcicon-closex fr f18 csrpntr" onclick="remove_Content('page','<?php echo $Id; ?>')"></i></span><?php echo get_the_title($Id);?></div> 
                                   <?php }}?>                 
                           </li>
                           <li><input placeholder="Search pages" type="text" onclick= "showContent('pageList',event)" onkeyup="hglghttxt(this,'searchpages')"></li>
                            <div class="clr"></div>
                           </ul>
                           <div>
                           </div> 
                           </div></li>
                           <div class="clr"></div>
                  </ul>
                          <div class="rel allfltrdrpdwns" id="pageList" style="display:none;height:40px;margin-top:-6px;">
                              <div class="mlslctmlist" style="overflow-y:scroll;z-index:10">
                                  <div id="searchpages" class="drpdnmnulstcntr">
                                      <ul id="pagecheck">
                                                     <?php 
                                                       $args = array(
                                                        'post_type' => 'page',
                                                        'posts_per_page' => -1,
                                                        'orderby' => 'time',
                                                        'order' => 'DESC',
                                                        'post_status' => 'publish'
                                                    );
                                                    $wa_post_list = new WP_Query($args);
                                                    $count_pages = wp_count_posts('page');
                                                    $position = 0;?>
                                                    <li visible = "false" style="display:none"><?php echo "No matches found";?> </li>
                                                   <?php
                                                    while($wa_post_list->have_posts()) : $wa_post_list->the_post(); 
                                                    ?>
                                                    <li value="<?php echo get_the_ID();?>" count="<?php echo $count_pages->publish; ?>" visible = "true" onclick="select_Content(<?php echo get_the_ID(); ?>,'<?php echo get_the_title();?>','page',event)" id="<?php echo get_the_ID(); ?>"> <?php echo ucwords(get_the_title()); ?></li>
                                                    <?php 
                                                    if (in_array(get_the_ID(), $pagesId))
                                                          { ?>
                                                          <script> 
                                                          jQuery("#<?php echo get_the_ID();?>").hide(); 
                                                          jQuery("#<?php echo get_the_ID();?>").attr("visible","false");
                                                          </script>
                                                    <?php  
                                                    $position++; } endwhile;  ?>  
                                      </ul>
                                  </div>
                                  <div class="bdrbtm"></div>
                              </div>
                          </div>
              </div>
            <div class="mt15"><i class="vm  zcicon-checkbox-blank-outline vm" id="postbutton" onclick="showpost(this)"></i>  <span style="vertical-align: middle;">&nbsp;Posts</span> </div>
                <div  id = "selectedpost" style="display:none; background-color: #fff;">
                  <ul>
                                <li class="vt"><label></label></li>
                                <li><div class="w100">
                                 <ul class="mlslct">
                                 <li id = "selectedpostlist" >
                                  <?php  $postsId[] =""; if($page_scripts && isset($page_scripts['zmhub_postvalue']) && $page_scripts['zmhub_postvalue']!=0) {

                                  $postsId = explode(",", $page_scripts['zmhub_postvalue']);

                                  foreach ($postsId as $Id) { ?>
                                     <div id="copy_<?php echo $Id; ?>"><span> <i class= "zcicon-closex fr f18 csrpntr" onclick="remove_Content('post','<?php echo $Id; ?>')"></i></span><?php echo get_the_title($Id);?></div> 
                                   <?php }}?>
                                 </li>
                                 <li><input placeholder="Search posts" type="text" onclick = "showContent('postList',event)" onkeyup="hglghttxt(this,'searchposts')"></li>
                                  <div class="clr"></div>
                                 </ul>
                                 <div>
                                 </div> 
                                 </div></li>
                        </ul>
                        <div class="rel allfltrdrpdwns" id="postList" style="display:none;height:40px;margin-top:-6px;">
                              <div class="mlslctmlist" style="overflow-y:scroll;z-index:10">
                                  <div id="searchposts" class="drpdnmnulstcntr">
                                      <ul>           
                                                  <?php 
                                                  $count_pages = wp_count_posts(); 
                                                       $args = array(
                                                        'post_type' => 'post',
                                                        'posts_per_page' => -1,
                                                        'orderby' => 'time',
                                                        'order' => 'DESC',
                                                        'post_status' => 'publish'
                                                    );
                                                    $wa_post_list = new WP_Query($args);
                                                    
                                                    $position = 0; ?>
                                                    <li visible = "false" style="display:none"><?php echo "No matches found";?> </li>
                                                    <?php while($wa_post_list->have_posts()) : $wa_post_list->the_post(); 
                                                                                                       ?>
                                                    <li value="<?php echo get_the_ID();?>" count="<?php echo $count_pages->publish; ?>" visible = "true" onclick="select_Content(<?php echo get_the_ID(); ?>,'<?php echo get_the_title();?>','post',event)" id="<?php echo get_the_ID(); ?>"> <?php echo ucwords(get_the_title()); ?></li>
                                                    <?php  if (in_array(get_the_ID(), $postsId))
                                                          { ?>
                                                          <script> jQuery("#<?php echo get_the_ID();?>").hide();
                                                                   jQuery("#<?php echo get_the_ID();?>").attr("visible","false");
                                                          </script>
                                                        <?php    $position++;  
                                                          } endwhile; ?>                    
                                      </ul>
                                  </div>
                                  <div class="bdrbtm"></div>
                              </div>
                          </div>
                    </div> 
                  </li>
                  </ul>

            <ul id ="Cateogry" style="display:none;">
                  <li class="vm"><label>Select a category<span class="reqird">*</span></label></li>
                  <li class="vm"><div class="w100">
                            <div class="drpdnmnu" id="cateogrylist" onclick="showContent('postcateogry',event)"> <span class="fr ml10"><i class="zcicon-chevron-down f20" ></i></span> <p id="catname" style="margin: 0px; font-size:14px;">Select a cateogry</p></div>
                            <div class="drpdwnmnulst allfltrdrpdwns" style="z-index: 2; display:none;" id="postcateogry" >
                            <div class="drpdnmnulstcntr"><ul>

                            <?php 
                              $var ="";
                             if($page_scripts && isset($page_scripts['zmhub_cateogry'])) { $var =  $page_scripts['zmhub_cateogry']; ?>

                              <script> 
                                  jQuery("#catname").html('<?php echo $var ?>');
                                  jQuery("#cateogrylist").attr("value",'<?php echo $var ?>');
                                  jQuery("#zmhub_cateogry").attr("value",'<?php echo $var ?>');
                                </script>
                                
                            <?php } ?>
                          
              <?php 
                                $wa_cateogries= get_categories( array(
                                'orderby' => 'name',
                                'order'   => 'ASC'
                                  ) );
                               // var_dump($wa_cateogries);
                                foreach($wa_cateogries as $wa_cateogry) {    
                                    if($wa_cateogry->name != $var)
                                    {
                                   
                            ?>
                            <li value= "<?php echo $wa_cateogry->term_id;?>" id= "<?php echo $wa_cateogry->name; ?>" onclick="changeActionValue(this)"><a> <?php echo ucwords($wa_cateogry->name); ?></a></li>
                            <?php  }}?>
                            </ul></div></div>
                            </div></li>
              </ul>

           <ul id ="Date">
              <li class="vm"><label>Track pages and posts created after</label></li>
              <li class="vm"><div class="w35 rel fl zcdatetime">
                <div class="actionbg"><i class="zcicon-calendar f20 vm"></i></div>
                <?php if($page_scripts && isset($page_scripts['zmhub_date'])) {
                    ?>
                     <input type="text" id ="datepicker" class="datepicker" name="datepicker" autocomplete="off" readonly = "true" placeholder="Select date" value="<?php echo gmdate( get_option('date_format'), $page_scripts['zmhub_date']); ?>"/>
                <?php  }
                 else { ?>
                <input type="text" id ="datepicker" class="datepicker" name="datepicker" autocomplete="off" readonly = "true" value="" placeholder="Select date"/>
                <?php } ?>
        </div></li>
          </ul>
           <?php if($page_scripts) { $var =  $page_scripts['zmhub_code_loc']; if(!strcmp($var,'specific')) { ?>
                  <script> 
                    jQuery("#selectpo").show();
                    jQuery("#Date").hide();
                  </script>
                 <?php }?>
                  <?php if(isset($page_scripts['zmhub_pagevalue']) && $page_scripts['zmhub_pagevalue'] !=0) { ?>
                  <script> 
                    jQuery("#selectedpage").show();
                    jQuery("#pagebutton").removeClass("zcicon-checkbox-blank-outline").addClass("zcicon-checkbox-marked");
                  </script>
                 <?php }?>
                 <?php if(isset($page_scripts['zmhub_postvalue']) && $page_scripts['zmhub_postvalue'] != 0) { ?>
                  <script> 
                    jQuery("#selectedpost").show();
                    jQuery("#postbutton").removeClass("zcicon-checkbox-blank-outline").addClass("zcicon-checkbox-marked");
                  </script>
             <?php }?>
              <?php $var ='';  if(isset($page_scripts['zmhub_code_loc'])) $var = $page_scripts['zmhub_code_loc']; if(!strcmp($var,'cateogry')) { ?>
                  <script> 
                    jQuery("#Cateogry").show();
                  </script>
                 <?php } }?>
                  <ul>
                  <li></li>
                  <li>
                  <div class="mt40 tc w100 "> <button type ="button" id="zmhub_submit" value='save' class="zmhbtn zmhpri">Save
                  </button>  <button type ="button" class="zmhbtn zmhcan ml20" onclick="window.location = 'admin.php?page=mh-start'" value='cancel' >Cancel</button> </div>
                  </li>
                  
                  </ul>
                  </div>
            </div>
            <?php wp_nonce_field('save_wa', 'mhnonce'); ?>
   </form>
<?php } ?>
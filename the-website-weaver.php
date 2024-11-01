<?php
/*
Plugin Name: The Website Weaver
Plugin URI: http://www.spiderwebpress.com
Description: Weave Your Own Websites
Version: 1.0.1
Author: SpiderWeb Press
Author URI: http://www.spiderwebpress.com
Copyright: Copyright (c) 2011, Kezz Bracey
*/

function ww_admin_bar() {
if ( !is_super_admin() || !is_admin_bar_showing() )
		return;

	global $wp_admin_bar;
        $wp_admin_bar->add_menu( array(
        'id' => 'web_weaver',
        'title' => __('Customize Theme with Website Weaver'),
        'href' => admin_url( 'themes.php?page=the-website-weaver.php')
    ) );
}
add_action( 'wp_before_admin_bar_render', 'ww_admin_bar' );


add_action('admin_menu', 'webweave_add_admin');

function webweave_add_admin() {
	
	initStuff();
	
	global $uploadedfile;

    global $saveoptions;

    if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) {
	
		if (get_magic_quotes_gpc()) {
		$_POST = array_map('stripslashes_deep', $_POST);
		$_GET = array_map('stripslashes_deep', $_GET);
		$_COOKIE = array_map('stripslashes_deep', $_COOKIE);
		$_REQUEST = array_map('stripslashes_deep', $_REQUEST);
		}
		

		

		
		if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'save' ) {
		
				if ( empty($_POST) || !wp_verify_nonce($_POST['weaver_nonce_save'],'weaver_save') ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				
				foreach ($saveoptions as $value) { update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
                foreach ($saveoptions as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
						} else { delete_option( $value['id'] ); 
					} 
				}
                header("Location: themes.php?page=the-website-weaver.php&saved=true");
                die;
				   
				}
    
        } else if ( isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' ) {
		
				if ( empty($_POST) || ( !wp_verify_nonce($_POST['weaver_nonce_update'],'weaver_update') && !wp_verify_nonce($_POST['weaver_nonce_update_bot'],'weaver_update_bot') ) ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
				
                writeThemeSetup();
				do_action('write_swp_theme');
                header("Location: themes.php?page=the-website-weaver.php&updated=true");
                die;
				   
				}

        } else if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'upfile' ) {
					
				$field_id = $_REQUEST['optionname'];         
				$target_path = TEMPLATEPATH."/images/";			
				$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
				
				if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
					$uploadedfile = basename( $_FILES['uploadedfile']['name']);
					update_option( $field_id, $uploadedfile );
				} else{
					echo "There was an error uploading the file. Please make sure it was no larger than 500kb then hit the back button and try again!";
				}
				
				header("Location: themes.php?page=the-website-weaver.php&upfile=true&optionname=".$field_id."&filename=".$uploadedfile."");
				die;

        } else if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'gendef' ) {
		
				if ( empty($_POST) || ( !wp_verify_nonce($_POST['weaver_nonce_gendef'],'weaver_gendef') && !wp_verify_nonce($_POST['weaver_nonce_gendef_bot'],'weaver_gendef_bot') ) ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
					
				writeThemeSetup();
				writeThemeDefaults();				
				header("Location: themes.php?page=the-website-weaver.php&gendef=true");
				die;
				   
				}

        } else if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'reset' ) {
		
				if ( empty($_POST) || ( !wp_verify_nonce($_POST['weaver_nonce_reset'],'weaver_reset') && !wp_verify_nonce($_POST['weaver_nonce_reset_bot'],'weaver_reset_bot') ) ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {
					

				foreach ($saveoptions as $value) { delete_option( $value['id'] ); }
				writeThemeSetup();
				header("Location: themes.php?page=the-website-weaver.php&reset=true");
				die;
				   
				}

        }
    }

    add_theme_page("The Website Weaver", "The Website Weaver", 'edit_themes', basename(__FILE__), 'webweave_admin');

}

function writeThemeDefaults(){

global $saveoptions;
foreach ($saveoptions as $value) {if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); }}

$cat=get_template();

$optionsMainFile = TEMPLATEPATH."/options/options_main.php";
$optionsMainHandle = fopen($optionsMainFile, 'w') or die("can't open file");
$optionsMainData = "<?php
return \$options = array (
	array(	'type' => 'openbgoptions','name' => 'Site Background &amp; General Options'),		
	array(	'type' => 'opendropdown','name' => 'Select Google Fonts to Make Available'),	
	array(	'type' => 'gfont','name' => 'Droid Sans','id' => \$cat.'_droidsans','std' => '".${$cat."_droidsans"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Lobster','id' => \$cat.'_lobster','std' => '".${$cat."_lobster"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'PT Sans','id' => \$cat.'_ptsans','std' => '".${$cat."_ptsans"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Droid Serif','id' => \$cat.'_droidserif','std' => '".${$cat."_droidserif"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Ubuntu','id' => \$cat.'_ubuntu','std' => '".${$cat."_ubuntu"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Yanone Kaffeesatz','id' => \$cat.'_yanonekaffeesatz','std' => '".${$cat."_yanonekaffeesatz"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Nobile','id' => \$cat.'_nobile','std' => '".${$cat."_nobile"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Reenie Beanie','id' => \$cat.'_reeniebeanie','std' => '".${$cat."_reeniebeanie"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Molengo','id' => \$cat.'_molengo','std' => '".${$cat."_molengo"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Arvo','id' => \$cat.'_arvo','std' => '".${$cat."_arvo"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Coming Soon','id' => \$cat.'_comingsoon','std' => '".${$cat."_comingsoon"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Crafty Girls','id' => \$cat.'_craftygirls','std' => '".${$cat."_craftygirls"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Calligraffitti','id' => \$cat.'_calligraffitti','std' => '".${$cat."_calligraffitti"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Tangerine','id' => \$cat.'_tangerine','std' => '".${$cat."_tangerine"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Cherry Cream Soda','id' => \$cat.'_cherrycreamsoda','std' => '".${$cat."_cherrycreamsoda"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'OFL Sorts Mill Goudy TT','id' => \$cat.'_oflsortsmillgoudytt','std' => '".${$cat."_oflsortsmillgoudytt"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Cantarell','id' => \$cat.'_cantarell','std' => '".${$cat."_cantarell"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Rock Salt','id' => \$cat.'_rocksalt','std' => '".${$cat."_rocksalt"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Vollkorn','id' => \$cat.'_vollkorn','std' => '".${$cat."_vollkorn"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Covered By Your Grace','id' => \$cat.'_coveredbyyourgrace','std' => '".${$cat."_coveredbyyourgrace"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Chewy','id' => \$cat.'_chewy','std' => '".${$cat."_chewy"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Luckiest Guy','id' => \$cat.'_luckiestguy','std' => '".${$cat."_luckiestguy"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Dancing Script','id' => \$cat.'_dancingscript','std' => '".${$cat."_dancingscript"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Bangers','id' => \$cat.'_bangers','std' => '".${$cat."_bangers"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Philosopher','id' => \$cat.'_philosopher','std' => '".${$cat."_philosopher"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Fontdiner Swanky','id' => \$cat.'_fontdinerswanky','std' => '".${$cat."_fontdinerswanky"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Slackey','id' => \$cat.'_slackey','std' => '".${$cat."_slackey"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Permanent Marker','id' => \$cat.'_permanentmarker','std' => '".${$cat."_permanentmarker"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Cabin Sketch','id' => \$cat.'_cabinsketch','std' => '".${$cat."_cabinsketch"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Michroma','id' => \$cat.'_michroma','std' => '".${$cat."_michroma"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Unkempt','id' => \$cat.'_unkempt','std' => '".${$cat."_unkempt"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Allan','id' => \$cat.'_allan','std' => '".${$cat."_allan"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Corben','id' => \$cat.'_corben','std' => '".${$cat."_corben"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Mountains of Christmas','id' => \$cat.'_mountainsofchristmas','std' => '".${$cat."_mountainsofchristmas"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'gfont','name' => 'Maiden Orange','id' => \$cat.'_maidenorange','std' => '".${$cat."_maidenorange"}."','options' => array('Make Available', 'Leave Out')),
	array(	'type' => 'text','name' => '<p><strong>Add extra <a href=http://www.google.com/webfonts target=_blank>Google Font</a> names to make available.</strong></p><p>Use + instead of spaces. Put | after each font name.</p>','id' => \$cat.'_extragfonts','std' => '".${$cat."_extragfonts"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Default Text Settings'),
	array(	'type' => 'font','name' => 'Default font','id' => \$cat.'_default_font','std' => '".${$cat."_default_font"}."'),
	array(	'type' => 'text','name' => 'Default font size','id' => \$cat.'_default_size','std' => '".${$cat."_default_size"}."'),
	array(	'type' => 'font','name' => 'Heading 1 Default font','id' => \$cat.'_h1default_font','std' => '".${$cat."_h1default_font"}."'),
	array(	'type' => 'text','name' => 'Heading 1 Default font size','id' => \$cat.'_h1default_size','std' => '".${$cat."_h1default_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Heading 1 Default color','style' => 'color','id' => \$cat.'_h1default_color','std' => '".${$cat."_h1default_color"}."'),
	array(	'type' => 'font','name' => 'Heading 2 Default font','id' => \$cat.'_h2default_font','std' => '".${$cat."_h2default_font"}."'),
	array(	'type' => 'text','name' => 'Heading 2 Default font size','id' => \$cat.'_h2default_size','std' => '".${$cat."_h2default_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Heading 2 Default color','style' => 'color','id' => \$cat.'_h2default_color','std' => '".${$cat."_h2default_color"}."'),
	array(	'type' => 'font','name' => 'Heading 3 Default font','id' => \$cat.'_h3default_font','std' => '".${$cat."_h3default_font"}."'),
	array(	'type' => 'text','name' => 'Heading 3 Default font size','id' => \$cat.'_h3default_size','std' => '".${$cat."_h3default_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Heading 3 Default color','style' => 'color','id' => \$cat.'_h3default_color','std' => '".${$cat."_h3default_color"}."'),
	array(	'type' => 'font','name' => 'Heading 4 Default font','id' => \$cat.'_h4default_font','std' => '".${$cat."_h4default_font"}."'),
	array(	'type' => 'text','name' => 'Heading 4 Default font size','id' => \$cat.'_h4default_size','std' => '".${$cat."_h4default_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Heading 4 Default color','style' => 'color','id' => \$cat.'_h4default_color','std' => '".${$cat."_h4default_color"}."'),
	array(	'type' => 'font','name' => 'Heading 5 Default font','id' => \$cat.'_h5default_font','std' => '".${$cat."_h5default_font"}."'),
	array(	'type' => 'text','name' => 'Heading 5 Default font size','id' => \$cat.'_h5default_size','std' => '".${$cat."_h5default_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Heading 5 Default color','style' => 'color','id' => \$cat.'_h5default_color','std' => '".${$cat."_h5default_color"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Background &amp; Favicon Settings'),
	array(	'type' => 'status','name' => 'Block backgrounds (e.g. menu bg) site width or full width (100%)','id' => \$cat.'_bg_width','std' => '".${$cat."_bg_width"}."','options' => array('Site Width','Full Width')),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Full Screen Background'),
	array(	'type' => 'bgcolor','name' => 'Full Screen background color','id' => \$cat.'_bodybg_color','std' => '".${$cat."_bodybg_color"}."'),	
	array(  'name' => 'Upload Full Screen Background Image (example use: large background ad or window filling tiling image)','id' => \$cat.'_fullbgimg','type' => 'upload','std' => '".${$cat."_fullbgimg"}."'),
	array(	'type' => 'status','name' => 'Full Screen Background Image Tiling','id' => \$cat.'_fullbgimg_tiling','std' => '".${$cat."_fullbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Site Background'),
	array(  'name' => 'Upload Site Background Image (example use: creating the appearance of drop shadows either side of your site)','id' => \$cat.'_bgimg','type' => 'upload','std' => '".${$cat."_bgimg"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_bgimg_tiling','std' => '".${$cat."_bgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'text','name' => 'Background Link','id' => \$cat.'_afflink','std' => '".${$cat."_afflink"}."'),
	array(	'type' => 'hr'),
	array(  'name' => 'Upload Favicon Image (must be 16 x 16 and named favicon.ico)','id' => \$cat.'_faviconurl','type' => 'upload','std' => '".${$cat."_faviconurl"}."'),
	array(	'type' => 'closedropdown'),				
	array(	'type' => 'closebgoptions'),
	array(	'type' => 'widthandtopspace','name' => 'Space above site','id' => \$cat.'_topad_size','std' => '".${$cat."_topad_size"}."'),		
	'sitewidth' => array(	'type' => 'openwrap','name' => 'Width of site','id' => \$cat.'_bodywidth','std' => '".${$cat."_bodywidth"}."'),
	array(	'type' => 'opensortable'),
	array(	'name' => 'Row One','id' => \$cat.'_row_01','type' => 'sortable','std' => '".${$cat."_row_01"}."'),	
	array(	'name' => 'Row Two','id' => \$cat.'_row_02','type' => 'sortable','std' => '".${$cat."_row_02"}."'),	
	array(	'name' => 'Row Three','id' => \$cat.'_row_03','type' => 'sortable','std' => '".${$cat."_row_03"}."'),	
	array(	'name' => 'Row Four','id' => \$cat.'_row_04','type' => 'sortable','std' => '".${$cat."_row_04"}."'),	
	array(	'name' => 'Row Five','id' => \$cat.'_row_05','type' => 'sortable','std' => '".${$cat."_row_05"}."'),	
	array(	'name' => 'Row Six','id' => \$cat.'_row_06','type' => 'sortable','std' => '".${$cat."_row_06"}."'),	
	array(	'name' => 'Row Seven','id' => \$cat.'_row_07','type' => 'sortable','std' => '".${$cat."_row_07"}."'),	
	array(	'name' => 'Row Eight','id' => \$cat.'_row_08','type' => 'sortable','std' => '".${$cat."_row_08"}."'),
	array(	'type' => 'closesortable'),
	array(	'type' => 'closewrap'),	
	array(	'type' => 'closebody')
);?>"
;
fwrite($optionsMainHandle, $optionsMainData); 
fclose($optionsMainHandle);

$optionsHeaderFile = TEMPLATEPATH."/options/options_header.php";
$optionsHeaderHandle = fopen($optionsHeaderFile, 'w') or die("can't open file");
$optionsHeaderData = "<?php
return \$options_header = array (	
	array(	'type' => 'height','name' => 'Header','id' => \$cat.'_header_height','std' => '".${$cat."_header_height"}."'),
	array(	'type' => 'openoptions','name' => 'Header Options'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_header_status_home','std' => '".${$cat."_header_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_header_status_blog','std' => '".${$cat."_header_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_header_status_single','std' => '".${$cat."_header_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_header_status_pages','std' => '".${$cat."_header_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),			
	array(	'type' => 'opendropdown','name' => 'Header Background Settings'),			
	array(	'type' => 'bgcolor','name' => 'Header Background color','id' => \$cat.'_headbg_color','std' => '".${$cat."_headbg_color"}."'),
	array(  'name' => 'Upload Header Background Image','id' => \$cat.'_headerbgurl','type' => 'upload','std' => '".${$cat."_headerbgurl"}."'),
	array(	'type' => 'status','name' => 'Header Background Image Tiling','id' => \$cat.'_headerbgimg_tiling','std' => '".${$cat."_headerbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Logo, Site Title, Tagline, RSS &amp; Extra Icon Settings'),
	array(	'type' => 'header','name' => 'Logo','tooltip' => 'Drag in the preview above to the position you want'),
	array(  'name' => 'Upload Logo Image','id' => \$cat.'_headerlogourl','type' => 'upload','std' => '".${$cat."_headerlogourl"}."'),
	array(	'type' => 'hidden','name' => 'Logo pos top','id' => \$cat.'_logopos_top','std' => '".${$cat."_logopos_top"}."'),		
	array(	'type' => 'hidden','name' => 'Logo pos left','id' => \$cat.'_logopos_left','std' => '".${$cat."_logopos_left"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Site Title','tooltip' => 'Drag in the preview above to the position you want'),
	array(	'type' => 'status','name' => 'Show or hide site title','id' => \$cat.'_sitetitle_status','std' => '".${$cat."_sitetitle_status"}."','options' => array('Show', 'Hide')),
	array(	'type' => 'font','name' => 'Site title font','id' => \$cat.'_sitetitle_font','std' => '".${$cat."_sitetitle_font"}."'),	
	array(	'type' => 'blockcolor','name' => 'Site title color','style' => 'color','id' => \$cat.'_sitetitle_color','std' => '".${$cat."_sitetitle_color"}."'),			
	array(	'type' => 'text','name' => 'Site title font size','id' => \$cat.'_sitetitletext_size','std' => '".${$cat."_sitetitletext_size"}."'),
	array(	'type' => 'status','name' => 'Site title weight','id' => \$cat.'_sitetitle_weight','std' => '".${$cat."_sitetitle_weight"}."','options' => array('Normal', 'Bold')),
	array(	'type' => 'hidden','name' => 'Site title pos top','id' => \$cat.'_sitetitlepos_top','std' => '".${$cat."_sitetitlepos_top"}."'),		
	array(	'type' => 'hidden','name' => 'Site title pos left','id' => \$cat.'_sitetitlepos_left','std' => '".${$cat."_sitetitlepos_left"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Tagline','tooltip' => 'Drag in the preview above to the position you want'),
	array(	'type' => 'status','name' => 'Show or hide tagline','id' => \$cat.'_tagline_status','std' => '".${$cat."_tagline_status"}."','options' => array('Show', 'Hide')),
	array(	'type' => 'font','name' => 'Tagline font','id' => \$cat.'_tagline_font','std' => '".${$cat."_tagline_font"}."'),				
	array(	'type' => 'blockcolor','name' => 'Tagline color','style' => 'hover','id' => \$cat.'_tagline_color','std' => '".${$cat."_tagline_color"}."'),			
	array(	'type' => 'text','name' => 'Tagline font size','id' => \$cat.'_taglinetext_size','std' => '".${$cat."_taglinetext_size"}."'),
	array(	'type' => 'status','name' => 'Tagline weight','id' => \$cat.'_tagline_weight','std' => '".${$cat."_tagline_weight"}."','options' => array('Normal', 'Bold')),
	array(	'type' => 'hidden','name' => 'Tagline pos top','id' => \$cat.'_taglinepos_top','std' => '".${$cat."_taglinepos_top"}."'),		
	array(	'type' => 'hidden','name' => 'Tagline pos left','id' => \$cat.'_taglinepos_left','std' => '".${$cat."_taglinepos_left"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'RSS Icon','tooltip' => 'Drag in the preview above to the position you want'),
	array(  'name' => 'Upload RSS Icon Image','id' => \$cat.'_rssiconurl','type' => 'upload','std' => '".${$cat."_rssiconurl"}."'),
	array(	'type' => 'hidden','name' => 'RSS pos top','id' => \$cat.'_rssiconpos_top','std' => '".${$cat."_rssiconpos_top"}."'),
	array(	'type' => 'hidden','name' => 'RSS pos left','id' => \$cat.'_rssiconpos_left','std' => '".${$cat."_rssiconpos_left"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Extra Icon, e.g. for &quot;Contact Us&quot;','tooltip' => 'Drag in the preview above to the position you want'),
	array(  'name' => 'Upload Extra Icon Image','id' => \$cat.'_extraiconurl','type' => 'upload','std' => '".${$cat."_extraiconurl"}."'),
	array(	'type' => 'text','name' => 'Extra Icon Link','id' => \$cat.'_extraiconlink','std' => '".${$cat."_extraiconlink"}."'),
	array(	'type' => 'hidden','name' => 'Extra pos top','id' => \$cat.'_extraiconpos_top','std' => '".${$cat."_extraiconpos_top"}."'),
	array(	'type' => 'hidden','name' => 'Extra pos left','id' => \$cat.'_extraiconpos_left','std' => '".${$cat."_extraiconpos_left"}."'),
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsHeaderHandle, $optionsHeaderData); 
fclose($optionsHeaderHandle);

$optionsMainmenuFile = TEMPLATEPATH."/options/options_mainmenu.php";
$optionsMainmenuHandle = fopen($optionsMainmenuFile, 'w') or die("can't open file");
$optionsMainmenuData = "<?php
return \$options_mainmenu = array (
	array(	'type' => 'height','name' => 'Main Menu','id' => \$cat.'_mainmenu_height','std' => '".${$cat."_mainmenu_height"}."'),
	array(	'type' => 'openoptions','name' => 'Main Menu Options'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_mainmenu_status_home','std' => '".${$cat."_mainmenu_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_mainmenu_status_blog','std' => '".${$cat."_mainmenu_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_mainmenu_status_single','std' => '".${$cat."_mainmenu_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_mainmenu_status_pages','std' => '".${$cat."_mainmenu_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Menu Bar Background, Font and Color Settings'),
	array(	'type' => 'font','name' => 'Main menu font','id' => \$cat.'_mainmenu_font','std' => '".${$cat."_mainmenu_font"}."'),
	array(	'type' => 'text','name' => 'Font size','id' => \$cat.'_mainmenutext_size','std' => '".${$cat."_mainmenutext_size"}."'),	
	array(	'type' => 'blockcolor','name' => 'Menu text color','style' => 'color','id' => \$cat.'_mainmenutext_color','std' => '".${$cat."_mainmenutext_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Menu Background color','id' => \$cat.'_mainmenubg_color','std' => '".${$cat."_mainmenubg_color"}."'),
	array(  'name' => 'Upload Menu Background Image','id' => \$cat.'_mainmenubgurl','type' => 'upload','std' => '".${$cat."_mainmenubgurl"}."'),
	array(	'type' => 'status','name' => 'Menu Background Image Tiling','id' => \$cat.'_mainmenubgimg_tiling','std' => '".${$cat."_mainmenubgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Dropdown Background and Color Settings'),	
	array(	'type' => 'blockcolor','name' => 'Hover text color','style' => 'hover','id' => \$cat.'_mainmenuhover_color','std' => '".${$cat."_mainmenuhover_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Hover / Rollover Background color','id' => \$cat.'_mainmenuhoverbg_color','std' => '".${$cat."_mainmenuhoverbg_color"}."'),	
	array(  'name' => 'Upload Hover Background Image','id' => \$cat.'_mainmenuhoverbgurl','type' => 'upload','std' => '".${$cat."_mainmenuhoverbgurl"}."'),
	array(	'type' => 'status','name' => 'Hover Background Image Tiling','id' => \$cat.'_mainmenuhoverbgimg_tiling','std' => '".${$cat."_mainmenuhoverbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),				
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Flyout Background and Color Settings'),
	array(	'type' => 'blockcolor','name' => 'Dropdown text color','style' => 'hover','id' => \$cat.'_mainmenudrop_color','std' => '".${$cat."_mainmenudrop_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Dropdown Background color','id' => \$cat.'_mainmenudropbg_color','std' => '".${$cat."_mainmenudropbg_color"}."'),	
	array(  'name' => 'Upload Dropdown Background Image','id' => \$cat.'_mainmenudropbgurl','type' => 'upload','std' => '".${$cat."_mainmenudropbgurl"}."'),
	array(	'type' => 'status','name' => 'Dropdown Background Image Tiling','id' => \$cat.'_mainmenudropbgimg_tiling','std' => '".${$cat."_mainmenudropbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),		
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsMainmenuHandle, $optionsMainmenuData); 
fclose($optionsMainmenuHandle);

$optionsFeaturedoneFile = TEMPLATEPATH."/options/options_featuredone.php";
$optionsFeaturedoneHandle = fopen($optionsFeaturedoneFile, 'w') or die("can't open file");
$optionsFeaturedoneData = "<?php
return \$options_featuredone = array (
	array(	'type' => 'height','name' => 'Spotlights','id' => \$cat.'_featuredone_height','std' => '".${$cat."_featuredone_height"}."'),
	array(	'type' => 'openoptions','name' => 'Spotlights Options'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_featuredone_status_home','std' => '".${$cat."_featuredone_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_featuredone_status_blog','std' => '".${$cat."_featuredone_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_featuredone_status_single','std' => '".${$cat."_featuredone_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_featuredone_status_pages','std' => '".${$cat."_featuredone_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Background color','id' => \$cat.'_featuredonebg_color','std' => '".${$cat."_featuredonebg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_featonebgurl','type' => 'upload','std' => '".${$cat."_featonebgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_featonebgimg_tiling','std' => '".${$cat."_featonebgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Text and Link Settings'),			
	array(	'type' => 'blockcolor','name' => 'Text color','style' => 'color','id' => \$cat.'_featuredslidetext_color','std' => '".${$cat."_featuredslidetext_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text link color','id' => \$cat.'_featuredslidelink_color','std' => '".${$cat."_featuredslidelink_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text hover color','id' => \$cat.'_featuredslidehover_color','std' => '".${$cat."_featuredslidehover_color"}."'),		
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Slider Settings'),
	array(	'type' => 'status','name' => 'Show or Hide Arrows','id' => \$cat.'_arrows_status','std' => '".${$cat."_arrows_status"}."','options' => array('Show', 'Hide')),
	array(	'type' => 'status','name' => 'Show or Hide Buttons','id' => \$cat.'_buttons_status','std' => '".${$cat."_buttons_status"}."','options' => array('Show', 'Hide')),
	array(	'type' => 'text','name' => 'Time between slide rotations (1000 = 1 second)','id' => \$cat.'_interval','std' => '".${$cat."_interval"}."'),
	array(	'type' => 'text','name' => 'Speed of slide rotations (1000 = 1 second)','id' => \$cat.'_transition_speed','std' => '".${$cat."_transition_speed"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsFeaturedoneHandle, $optionsFeaturedoneData); 
fclose($optionsFeaturedoneHandle);

$optionsSecondmenuFile = TEMPLATEPATH."/options/options_secondmenu.php";
$optionsSecondmenuHandle = fopen($optionsSecondmenuFile, 'w') or die("can't open file");
$optionsSecondmenuData = "<?php
return \$options_secondmenu = array (
	array(	'type' => 'height','name' => 'Second Menu','id' => \$cat.'_secondmenu_height','std' => '".${$cat."_secondmenu_height"}."'),
	array(	'type' => 'openoptions','name' => 'Second Menu Options'),		
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_secondmenu_status_home','std' => '".${$cat."_secondmenu_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_secondmenu_status_blog','std' => '".${$cat."_secondmenu_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_secondmenu_status_single','std' => '".${$cat."_secondmenu_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_secondmenu_status_pages','std' => '".${$cat."_secondmenu_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'opendropdown','name' => 'Menu Bar Background, Font and Color Settings'),
	array(	'type' => 'font','name' => 'Second menu font','id' => \$cat.'_secondmenu_font','std' => '".${$cat."_secondmenu_font"}."'),
	array(	'type' => 'text','name' => 'Font size','id' => \$cat.'_secondmenutext_size','std' => '".${$cat."_secondmenutext_size"}."'),	
	array(	'type' => 'blockcolor','name' => 'Menu text color','style' => 'color','id' => \$cat.'_secondmenutext_color','std' => '".${$cat."_secondmenutext_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Menu Background color','id' => \$cat.'_secondmenubg_color','std' => '".${$cat."_secondmenubg_color"}."'),
	array(  'name' => 'Upload Menu Background Image','id' => \$cat.'_secondmenubgurl','type' => 'upload','std' => '".${$cat."_secondmenubgurl"}."'),
	array(	'type' => 'status','name' => 'Menu Background Image Tiling','id' => \$cat.'_secondmenubgimg_tiling','std' => '".${$cat."_secondmenubgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Dropdown Background and Color Settings'),	
	array(	'type' => 'blockcolor','name' => 'Hover text color','style' => 'hover','id' => \$cat.'_secondmenuhover_color','std' => '".${$cat."_secondmenuhover_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Hover / Rollover Background color','id' => \$cat.'_secondmenuhoverbg_color','std' => '".${$cat."_secondmenuhoverbg_color"}."'),	
	array(  'name' => 'Upload Hover Background Image','id' => \$cat.'_secondmenuhoverbgurl','type' => 'upload','std' => '".${$cat."_secondmenuhoverbgurl"}."'),
	array(	'type' => 'status','name' => 'Hover Background Image Tiling','id' => \$cat.'_secondmenuhoverbgimg_tiling','std' => '".${$cat."_secondmenuhoverbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),				
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Flyout Background and Color Settings'),
	array(	'type' => 'blockcolor','name' => 'Dropdown text color','style' => 'hover','id' => \$cat.'_secondmenudrop_color','std' => '".${$cat."_secondmenudrop_color"}."'),
	array(	'type' => 'bgcolor','name' => 'Dropdown Background color','id' => \$cat.'_secondmenudropbg_color','std' => '".${$cat."_secondmenudropbg_color"}."'),	
	array(  'name' => 'Upload Dropdown Background Image','id' => \$cat.'_secondmenudropbgurl','type' => 'upload','std' => '".${$cat."_secondmenudropbgurl"}."'),
	array(	'type' => 'status','name' => 'Dropdown Background Image Tiling','id' => \$cat.'_secondmenudropbgimg_tiling','std' => '".${$cat."_secondmenudropbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),		
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsSecondmenuHandle, $optionsSecondmenuData); 
fclose($optionsSecondmenuHandle);

$optionsFeaturedtwoFile = TEMPLATEPATH."/options/options_featuredtwo.php";
$optionsFeaturedtwoHandle = fopen($optionsFeaturedtwoFile, 'w') or die("can't open file");
$optionsFeaturedtwoData = "<?php
return \$options_featuredtwo = array (
	array(	'type' => 'height','name' => 'Featured Strip','id' => \$cat.'_featuredtwo_height','std' => '".${$cat."_featuredtwo_height"}."'),
	array(	'type' => 'openoptions','name' => 'Featured Strip Options'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_featuredtwo_status_home','std' => '".${$cat."_featuredtwo_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_featuredtwo_status_blog','std' => '".${$cat."_featuredtwo_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_featuredtwo_status_single','std' => '".${$cat."_featuredtwo_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_featuredtwo_status_pages','std' => '".${$cat."_featuredtwo_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Background color','id' => \$cat.'_featuredtwobg_color','std' => '".${$cat."_featuredtwobg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_feattwobgurl','type' => 'upload','std' => '".${$cat."_feattwobgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_feattwobgimg_tiling','std' => '".${$cat."_feattwobgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Text and Link Settings'),			
	array(	'type' => 'blockcolor','name' => 'Text color','style' => 'color','id' => \$cat.'_featuredtwotext_color','std' => '".${$cat."_featuredtwotext_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text link color','id' => \$cat.'_featuredtwolink_color','std' => '".${$cat."_featuredtwolink_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text hover color','id' => \$cat.'_featuredtwohover_color','std' => '".${$cat."_featuredtwohover_color"}."'),		
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsFeaturedtwoHandle, $optionsFeaturedtwoData); 
fclose($optionsFeaturedtwoHandle);

$optionsContentareaFile = TEMPLATEPATH."/options/options_contentarea.php";
$optionsContentareaHandle = fopen($optionsContentareaFile, 'w') or die("can't open file");
$optionsContentareaData = "<?php
return \$options_contentarea = array (
	array(	'type' => 'colposition','name' => 'Sidebar area width','id' => \$cat.'_sidebar_area_width','std' => '".${$cat."_sidebar_area_width"}."'),
	array(	'type' => 'openoptions','name' => 'Content Area Options'),
	array(	'type' => 'header','name' => 'Content Area Settings','tooltip' => 'This block contains the post area and sidebar'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_contentarea_status_home','std' => '".${$cat."_contentarea_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_contentarea_status_blog','std' => '".${$cat."_contentarea_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_contentarea_status_single','std' => '".${$cat."_contentarea_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_contentarea_status_pages','std' => '".${$cat."_contentarea_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Content Area Background Settings','tooltip' => 'Set a background for the entire content area block - will appear behind your sidebar and posts area'),	
	array(	'type' => 'bgcolor','name' => 'Background color','id' => \$cat.'_contentareabg_color','std' => '".${$cat."_contentareabg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_contentareabgurl','type' => 'upload','std' => '".${$cat."_contentareabgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_contentareabgimg_tiling','std' => '".${$cat."_contentareabgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'header','name' => 'Sidebar Settings'),
	array(	'type' => 'status','name' => 'Sidebar on right or left?','id' => \$cat.'_sidebar_float','std' => '".${$cat."_sidebar_float"}."','options' => array('Right','Left')),
	array(	'type' => 'opendropdown','name' => 'Show sidebar on which parts of your site?'),
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_sidebar_status_home','std' => '".${$cat."_sidebar_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_sidebar_status_blog','std' => '".${$cat."_sidebar_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_sidebar_status_single','std' => '".${$cat."_sidebar_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_sidebar_status_pages','std' => '".${$cat."_sidebar_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),		
	array(	'type' => 'opendropdown','name' => 'Sidebar Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Sidebar background color','id' => \$cat.'_sidebarbg_color','std' => '".${$cat."_sidebarbg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_sidebarbgurl','type' => 'upload','std' => '".${$cat."_sidebarbgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_sidebarbgimg_tiling','std' => '".${$cat."_sidebarbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),		
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'opendropdown','name' => 'Widget Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Widget background color','id' => \$cat.'_widgetbg_color','std' => '".${$cat."_widgetbg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_widgetbgurl','type' => 'upload','std' => '".${$cat."_widgetbgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_widgetbgimg_tiling','std' => '".${$cat."_widgetbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),		
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Sidebar Text and Link Settings'),
	array(	'type' => 'font','name' => 'Widget heading font','id' => \$cat.'_widgetheading_font','std' => '".${$cat."_widgetheading_font"}."'),
	array(	'type' => 'text','name' => 'Widget heading size','id' => \$cat.'_widgetheading_size','std' => '".${$cat."_widgetheading_size"}."'),
	array(	'type' => 'status','name' => 'Widget heading  weight','id' => \$cat.'_widgetheading_weight','std' => '".${$cat."_widgetheading_weight"}."','options' => array('Normal', 'Bold')),
	array(	'type' => 'blockcolor','name' => 'Widget heading color','id' => \$cat.'_widgetheading_color','std' => '".${$cat."_widgetheading_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Widget heading underline color','id' => \$cat.'_widgetheadingunderline_color','std' => '".${$cat."_widgetheadingunderline_color"}."'),
	array(	'type' => 'hr'),		
	array(	'type' => 'blockcolor','name' => 'Sidebar text color','id' => \$cat.'_sidebartext_color','std' => '".${$cat."_sidebartext_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Sidebar link color','id' => \$cat.'_sidebarlink_color','std' => '".${$cat."_sidebarlink_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Sidebar hover color','id' => \$cat.'_sidebarhover_color','std' => '".${$cat."_sidebarhover_color"}."'),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Add Social Icons'),
	array(	'type' => 'header','name' => 'Icon 1'),
	array(  'name' => 'Upload Icon 1','id' => \$cat.'_iconone','type' => 'upload','std' => '".${$cat."_iconone"}."'),
	array(	'type' => 'text','name' => 'Icon 1 Link','id' => \$cat.'_icononelink','std' => '".${$cat."_icononelink"}."'),
	array(	'type' => 'text','name' => 'Hover Message (e.g. Follow Me)','id' => \$cat.'_icononemsg','std' => '".${$cat."_icononemsg"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Icon 2'),
	array(  'name' => 'Upload Icon 2','id' => \$cat.'_icontwo','type' => 'upload','std' => '".${$cat."_icontwo"}."'),	
	array(	'type' => 'text','name' => 'Icon 2 Link','id' => \$cat.'_icontwolink','std' => '".${$cat."_icontwolink"}."'),
	array(	'type' => 'text','name' => 'Hover Message (e.g. Follow Me)','id' => \$cat.'_icontwomsg','std' => '".${$cat."_icontwomsg"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Icon 3'),
	array(  'name' => 'Upload Icon 3','id' => \$cat.'_iconthree','type' => 'upload','std' => '".${$cat."_iconthree"}."'),	
	array(	'type' => 'text','name' => 'Icon 3 Link','id' => \$cat.'_iconthreelink','std' => '".${$cat."_iconthreelink"}."'),
	array(	'type' => 'text','name' => 'Hover Message (e.g. Follow Me)','id' => \$cat.'_iconthreemsg','std' => '".${$cat."_iconthreemsg"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'header','name' => 'Icon 4'),
	array(  'name' => 'Upload Icon 4','id' => \$cat.'_iconfour','type' => 'upload','std' => '".${$cat."_iconfour"}."'),	
	array(	'type' => 'text','name' => 'Icon 4 Link','id' => \$cat.'_iconfourlink','std' => '".${$cat."_iconfourlink"}."'),
	array(	'type' => 'text','name' => 'Hover Message (e.g. Follow Me)','id' => \$cat.'_iconfourmsg','std' => '".${$cat."_iconfourmsg"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'header','name' => 'Post Settings'),		
	array(	'type' => 'text','name' => 'Number of post columns','id' => \$cat.'_pcolnum','std' => '".${$cat."_pcolnum"}."'),
	array(	'type' => 'opendropdown','name' => 'Posts Area Background Settings','tooltip' => 'Set a background that will appear behind your individual posts but not in the sidebar'),		
	array(	'type' => 'bgcolor','name' => 'Posts area background color','id' => \$cat.'_postsareabg_color','std' => '".${$cat."_postsareabg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_postsareabgurl','type' => 'upload','std' => '".${$cat."_postsareabgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_postsareabgimg_tiling','std' => '".${$cat."_postsareabgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Individual Post Background Settings','tooltip' => 'Set a background contained inside your individual posts'),	
	array(	'type' => 'bgcolor','name' => 'Individual post background color','id' => \$cat.'_postbg_color','std' => '".${$cat."_postbg_color"}."'),	
	array(  'name' => 'Upload Background Image','id' => \$cat.'_postbgurl','type' => 'upload','std' => '".${$cat."_postbgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_postbgimg_tiling','std' => '".${$cat."_postbgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),		
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Post Text and Link Settings'),		
	array(	'type' => 'font','name' => 'Post heading font','id' => \$cat.'_postheading_font','std' => '".${$cat."_postheading_font"}."'),
	array(	'type' => 'text','name' => 'Post heading size','id' => \$cat.'_postheading_size','std' => '".${$cat."_postheading_size"}."'),
	array(	'type' => 'status','name' => 'Post heading  weight','id' => \$cat.'_postheading_weight','std' => '".${$cat."_postheading_weight"}."','options' => array('Normal', 'Bold')),
	array(	'type' => 'blockcolor','name' => 'Posts heading color','id' => \$cat.'_postheading_color','std' => '".${$cat."_postheading_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Posts heading hover color','id' => \$cat.'_postheadinghover_color','std' => '".${$cat."_postheadinghover_color"}."'),
	array(	'type' => 'hr'),
	array(	'type' => 'blockcolor','name' => 'Posts text color','id' => \$cat.'_poststext_color','std' => '".${$cat."_poststext_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Posts link color','id' => \$cat.'_postslink_color','std' => '".${$cat."_postslink_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Posts hover color','id' => \$cat.'_postshover_color','std' => '".${$cat."_postshover_color"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Blockquote Background Settings'),
	array(	'type' => 'blockcolor','name' => 'Blockquote background color','id' => \$cat.'_blockquotebg_color','std' => '".${$cat."_blockquotebg_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Blockquote border color','id' => \$cat.'_blockquoteborder_color','std' => '".${$cat."_blockquoteborder_color"}."'),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Blockquote Text and Link Settings'),
	array(	'type' => 'font','name' => 'Blockquote font','id' => \$cat.'_blockquote_font','std' => '".${$cat."_blockquote_font"}."'),
	array(	'type' => 'text','name' => 'Blockquote text size','id' => \$cat.'_blockquote_size','std' => '".${$cat."_blockquote_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Blockquote text color','id' => \$cat.'_blockquotetext_color','std' => '".${$cat."_blockquotetext_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Blockquote link color','id' => \$cat.'_blockquotelink_color','std' => '".${$cat."_blockquotelink_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Blockquote hover color','id' => \$cat.'_blockquotehover_color','std' => '".${$cat."_blockquotehover_color"}."'),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'header','name' => 'Post Info & Pagination Settings','tooltip' => 'Post Info =  author, time, categories, comments, tags'),
	array(	'type' => 'opendropdown','name' => 'Show Pagination or &quot;Next&quot; and &quot;Previous&quot; post links on which parts of your site?'),
	array(	'type' => 'status','name' => 'Front Page Pagination','id' => \$cat.'_postlinks_status_home','std' => '".${$cat."_postlinks_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog Pagination','id' => \$cat.'_postlinks_status_blog','std' => '".${$cat."_postlinks_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Post Next &amp; Previous Links','id' => \$cat.'_postlinks_status_single','std' => '".${$cat."_postlinks_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages Next &amp; Previous Links','id' => \$cat.'_postlinks_status_pages','std' => '".${$cat."_postlinks_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Show author, time, categories, comments, tags on which parts of your site?'),
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_postmeta_status_home','std' => '".${$cat."_postmeta_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_postmeta_status_blog','std' => '".${$cat."_postmeta_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_postmeta_status_single','std' => '".${$cat."_postmeta_status_single"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'opendropdown','name' => 'Post Info Background Settings'),
	array(	'type' => 'blockcolor','name' => 'Post info background color','id' => \$cat.'_postmetabg_color','std' => '".${$cat."_postmetabg_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Post info border color','id' => \$cat.'_postmetaborder_color','std' => '".${$cat."_postmetaborder_color"}."'),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Post Info Text and Link Settings'),
	array(	'type' => 'text','name' => 'Post info text size','id' => \$cat.'_postmeta_size','std' => '".${$cat."_postmeta_size"}."'),
	array(	'type' => 'blockcolor','name' => 'Post info text color','id' => \$cat.'_postmetatext_color','std' => '".${$cat."_postmetatext_color"}."'),	
	array(	'type' => 'blockcolor','name' => 'Post info link color','id' => \$cat.'_postmetalink_color','std' => '".${$cat."_postmetalink_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Post info hover color','id' => \$cat.'_postmetahover_color','std' => '".${$cat."_postmetahover_color"}."'),				
	array(	'type' => 'closedropdown'),
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsContentareaHandle, $optionsContentareaData); 
fclose($optionsContentareaHandle);

$optionsFooteroneFile = TEMPLATEPATH."/options/options_footerone.php";
$optionsFooteroneHandle = fopen($optionsFooteroneFile, 'w') or die("can't open file");
$optionsFooteroneData = "<?php
return \$options_footerone = array (
	array(	'type' => 'height','name' => 'Footer One','id' => \$cat.'_footerone_height','std' => '".${$cat."_footerone_height"}."'),
	array(	'type' => 'openoptions','name' => 'Footer One Options'),			
	array(	'type' => 'text','name' => 'Number of widget columns','id' => \$cat.'_fonecolnum','std' => '".${$cat."_fonecolnum"}."'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_footerone_status_home','std' => '".${$cat."_footerone_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_footerone_status_blog','std' => '".${$cat."_footerone_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_footerone_status_single','std' => '".${$cat."_footerone_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_footerone_status_pages','std' => '".${$cat."_footerone_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'opendropdown','name' => 'Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Background color','id' => \$cat.'_footeronebg_color','std' => '".${$cat."_footeronebg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_footeronebgurl','type' => 'upload','std' => '".${$cat."_footeronebgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_footeronebgimg_tiling','std' => '".${$cat."_footeronebgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Text and Link Settings'),
	array(	'type' => 'font','name' => 'Footer widget heading font','id' => \$cat.'_footwidgetheading_font','std' => '".${$cat."_footwidgetheading_font"}."'),
	array(	'type' => 'text','name' => 'Footer widget heading size','id' => \$cat.'_footwidgetheading_size','std' => '".${$cat."_footwidgetheading_size"}."'),
	array(	'type' => 'status','name' => 'Footer widget heading  weight','id' => \$cat.'_footwidgetheading_weight','std' => '".${$cat."_footwidgetheading_weight"}."','options' => array('Normal', 'Bold')),
	array(	'type' => 'blockcolor','name' => 'Footer widget heading color','id' => \$cat.'_footwidgetheading_color','std' => '".${$cat."_footwidgetheading_color"}."'),
	array(	'type' => 'blockcolor','name' => 'Text color','style' => 'color','id' => \$cat.'_footeronetext_color','std' => '".${$cat."_footeronetext_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text link color','id' => \$cat.'_footeronelink_color','std' => '".${$cat."_footeronelink_color"}."'),			
	array(	'type' => 'blockcolor','name' => 'Text hover color','id' => \$cat.'_footeronehover_color','std' => '".${$cat."_footeronehover_color"}."'),	
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsFooteroneHandle, $optionsFooteroneData);
fclose($optionsFooteroneHandle);

$optionsFootertwoFile = TEMPLATEPATH."/options/options_footertwo.php";
$optionsFootertwoHandle = fopen($optionsFootertwoFile, 'w') or die("can't open file");
$optionsFootertwoData = "<?php
return \$options_footertwo = array (
	array(	'type' => 'height','name' => 'Footer Two','id' => \$cat.'_footertwo_height','std' => '".${$cat."_footertwo_height"}."'),
	array(	'type' => 'openoptions','name' => 'Footer Two Options'),
	array(	'type' => 'opendropdown','name' => 'Set where to show this block'),	
	array(	'type' => 'status','name' => 'Front Page','id' => \$cat.'_footertwo_status_home','std' => '".${$cat."_footertwo_status_home"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Blog','id' => \$cat.'_footertwo_status_blog','std' => '".${$cat."_footertwo_status_blog"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Single Posts','id' => \$cat.'_footertwo_status_single','std' => '".${$cat."_footertwo_status_single"}."','options' => array('Show', 'Hide')),	
	array(	'type' => 'status','name' => 'Pages','id' => \$cat.'_footertwo_status_pages','std' => '".${$cat."_footertwo_status_pages"}."','options' => array('Show', 'Hide')),			
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Background Settings'),
	array(	'type' => 'bgcolor','name' => 'Background color','id' => \$cat.'_footertwobg_color','std' => '".${$cat."_footertwobg_color"}."'),
	array(  'name' => 'Upload Background Image','id' => \$cat.'_footertwobgurl','type' => 'upload','std' => '".${$cat."_footertwobgurl"}."'),
	array(	'type' => 'status','name' => 'Background Image Tiling','id' => \$cat.'_footertwobgimg_tiling','std' => '".${$cat."_footeronebgimg_tiling"}."','options' => array('No Tiling', 'Left to right', 'Top to bottom', 'Both directions')),
	array(	'type' => 'closedropdown'),
	array(	'type' => 'opendropdown','name' => 'Text and Link Settings'),			
	array(	'type' => 'blockcolor','name' => 'Text color','style' => 'color','id' => \$cat.'_footertwotext_color','std' => '".${$cat."_footertwotext_color"}."'),		
	array(	'type' => 'blockcolor','name' => 'Text link color','id' => \$cat.'_footertwolink_color','std' => '".${$cat."_footertwolink_color"}."'),	
	array(	'type' => 'blockcolor','name' => 'Text hover color','id' => \$cat.'_footertwohover_color','std' => '".${$cat."_footertwohover_color"}."'),		
	array(	'type' => 'closedropdown'),	
	array(	'type' => 'closeoptions')
);?>"
;
fwrite($optionsFootertwoHandle, $optionsFootertwoData); 
fclose($optionsFootertwoHandle);

}

function getDynamicStyles($imageprefix){
global $saveoptions,$socialcode,$bodybgcss,$bgwidthsetting,$contentareabgcolorcss,$contentareabgcss,$postsareabgcss,$postsareabgcolorcss,$postbgcss,$postbgcolorcss,$sidebarbgcss,$sidebarbgcolorcss,$widgetbgcss,$widgetbgcolorcss,$bodywrapbgcss,$headerbgcss,$headerbgcolorcss,$mainmenubgcss,$mainmenubgcolorcss,$mainmenudropbgcss,$mainmenudropbgcolorcss,$mainmenuhoverbgcss,$mainmenuhoverbgcolorcss,$featonebgcss,$featonebgcolorcss,$secondmenubgcss,$secondmenubgcolorcss,$secondmenudropbgcss,$secondmenudropbgcolorcss,$secondmenuhoverbgcss,$secondmenuhoverbgcolorcss,$feattwobgcss,$feattwobgcolorcss,$footeronebgcss,$footeronebgcolorcss,$footertwobgcss,$footertwobgcolorcss;
foreach ($saveoptions as $value) {if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); }}
				
$cat=get_template();

/* full screen bg*/
if (${$cat.'_fullbgimg'} != 'noimage') {
	if (${$cat.'_fullbgimg_tiling'} == 'No Tiling') {
		$bodybgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_fullbgimg_tiling'} == 'Left to right') {
		$bodybgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_fullbgimg_tiling'} == 'Top to bottom') {
		$bodybgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_fullbgimg_tiling'} == 'Both directions') {
		$bodybgrepeatcss = '';
	}
$bodybgcss = "background-image:url('".$imageprefix."images/".${$cat.'_fullbgimg'}."');".$bodybgrepeatcss."background-position:center top;";
} else { $bodybgcss = ''; }

/* site width*/
if (${$cat."_bg_width"}=='Site Width'){
$bgwidthsetting = ${$cat."_bodywidth"}."px";
} else {
$bgwidthsetting = "100%";
}

/* site bg*/
if (${$cat.'_bgimg'} != 'noimage') {
	if (${$cat.'_bgimg_tiling'} == 'No Tiling') {
		$bodywrapbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_bgimg_tiling'} == 'Left to right') {
		$bodywrapbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_bgimg_tiling'} == 'Top to bottom') {
		$bodywrapbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_bgimg_tiling'} == 'Both directions') {
		$bodywrapbgrepeatcss = '';
	}
$bodywrapbgcss = ".bodywrap {background-image:url('".$imageprefix."images/".${$cat.'_bgimg'}."');".$bodywrapbgrepeatcss."background-position:center top;}";
} else { $bodywrapbgcss = ''; }


/* header bg*/
if (${$cat.'_headerbgurl'} != 'noimage') {
	if (${$cat.'_headerbgimg_tiling'} == 'No Tiling') {
		$headerbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_headerbgimg_tiling'} == 'Left to right') {
		$headerbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_headerbgimg_tiling'} == 'Top to bottom') {
		$headerbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_headerbgimg_tiling'} == 'Both directions') {
		$headerbgrepeatcss = '';
	}
$headerbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_headerbgurl'}."');".$headerbgrepeatcss."background-position:center top;";
} else { $headerbgcss = ''; }

if (${$cat.'_headbg_color'} != 'none') {
	$headerbgcolorcss = "background-color:#".${$cat."_headbg_color"}.";";
} else { $headerbgcolorcss = ''; }


/* main menu bg*/
if (${$cat.'_mainmenubgurl'} != 'noimage') {
	if (${$cat.'_mainmenubgimg_tiling'} == 'No Tiling') {
		$mainmenubgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_mainmenubgimg_tiling'} == 'Left to right') {
		$mainmenubgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_mainmenubgimg_tiling'} == 'Top to bottom') {
		$mainmenubgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_mainmenubgimg_tiling'} == 'Both directions') {
		$mainmenubgrepeatcss = '';
	}
$mainmenubgcss = "background-image:url('".$imageprefix."images/".${$cat.'_mainmenubgurl'}."');".$mainmenubgrepeatcss."background-position:center top;";

} else { $mainmenubgcss = ''; }

if (${$cat.'_mainmenubg_color'} != 'none') {
	$mainmenubgcolorcss = "background-color:#".${$cat."_mainmenubg_color"}.";";
} else { $mainmenubgcolorcss = ''; }

/* main menu drop bg*/
if (${$cat.'_mainmenudropbgurl'} != 'noimage') {
	if (${$cat.'_mainmenudropbgimg_tiling'} == 'No Tiling') {
		$mainmenudropbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_mainmenudropbgimg_tiling'} == 'Left to right') {
		$mainmenudropbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_mainmenudropbgimg_tiling'} == 'Top to bottom') {
		$mainmenudropbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_mainmenudropbgimg_tiling'} == 'Both directions') {
		$mainmenudropbgrepeatcss = '';
	}
$mainmenudropbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_mainmenudropbgurl'}."');".$mainmenudropbgrepeatcss."background-position:center top;";
} else { $mainmenudropbgcss = ''; }

if (${$cat.'_mainmenudropbg_color'} != 'none') {
	$mainmenudropbgcolorcss = "background-color:#".${$cat."_mainmenudropbg_color"}.";";
} else { $mainmenudropbgcolorcss = ''; }

/* main menu hover bg*/
if (${$cat.'_mainmenuhoverbgurl'} != 'noimage') {
	if (${$cat.'_mainmenuhoverbgimg_tiling'} == 'No Tiling') {
		$mainmenuhoverbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_mainmenuhoverbgimg_tiling'} == 'Left to right') {
		$mainmenuhoverbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_mainmenuhoverbgimg_tiling'} == 'Top to bottom') {
		$mainmenuhoverbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_mainmenuhoverbgimg_tiling'} == 'Both directions') {
		$mainmenuhoverbgrepeatcss = '';
	}
$mainmenuhoverbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_mainmenuhoverbgurl'}."');".$mainmenuhoverbgrepeatcss."background-position:center top;";
} else { $mainmenuhoverbgcss = ''; }

if (${$cat.'_mainmenuhoverbg_color'} != 'none') {
	$mainmenuhoverbgcolorcss = "background-color:#".${$cat."_mainmenuhoverbg_color"}.";";
} else { $mainmenuhoverbgcolorcss = ''; }


/* featured one bg*/
if (${$cat.'_featonebgurl'} != 'noimage') {
	if (${$cat.'_featonebgimg_tiling'} == 'No Tiling') {
		$featonebgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_featonebgimg_tiling'} == 'Left to right') {
		$featonebgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_featonebgimg_tiling'} == 'Top to bottom') {
		$featonebgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_featonebgimg_tiling'} == 'Both directions') {
		$featonebgrepeatcss = '';
	}
$featonebgcss = "background-image:url('".$imageprefix."images/".${$cat.'_featonebgurl'}."');".$featonebgrepeatcss."background-position:center top;";
} else { $featonebgcss = ''; }

if (${$cat.'_featuredonebg_color'} != 'none') {
	$featonebgcolorcss = "background-color:#".${$cat."_featuredonebg_color"}.";";
} else { $featonebgcolorcss = ''; }


/* second menu bg*/
if (${$cat.'_secondmenubgurl'} != 'noimage') {
	if (${$cat.'_secondmenubgimg_tiling'} == 'No Tiling') {
		$secondmenubgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_secondmenubgimg_tiling'} == 'Left to right') {
		$secondmenubgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_secondmenubgimg_tiling'} == 'Top to bottom') {
		$secondmenubgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_secondmenubgimg_tiling'} == 'Both directions') {
		$secondmenubgrepeatcss = '';
	}
$secondmenubgcss = "background-image:url('".$imageprefix."images/".${$cat.'_secondmenubgurl'}."');".$secondmenubgrepeatcss."background-position:center top;";
} else { $secondmenubgcss = ''; }

if (${$cat.'_secondmenubg_color'} != 'none') {
	$secondmenubgcolorcss = "background-color:#".${$cat."_secondmenubg_color"}.";";
} else { $secondmenubgcolorcss = ''; }

/* second menu drop bg*/
if (${$cat.'_secondmenudropbgurl'} != 'noimage') {
	if (${$cat.'_secondmenudropbgimg_tiling'} == 'No Tiling') {
		$secondmenudropbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_secondmenudropbgimg_tiling'} == 'Left to right') {
		$secondmenudropbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_secondmenudropbgimg_tiling'} == 'Top to bottom') {
		$secondmenudropbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_secondmenudropbgimg_tiling'} == 'Both directions') {
		$secondmenudropbgrepeatcss = '';
	}
$secondmenudropbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_secondmenudropbgurl'}."');".$secondmenudropbgrepeatcss."background-position:center top;";
} else { $secondmenudropbgcss = ''; }

if (${$cat.'_secondmenudropbg_color'} != 'none') {
	$secondmenudropbgcolorcss = "background-color:#".${$cat."_secondmenudropbg_color"}.";";
} else { $secondmenudropbgcolorcss = ''; }

/* second menu hover bg*/
if (${$cat.'_secondmenuhoverbgurl'} != 'noimage') {
	if (${$cat.'_secondmenuhoverbgimg_tiling'} == 'No Tiling') {
		$secondmenuhoverbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_secondmenuhoverbgimg_tiling'} == 'Left to right') {
		$secondmenuhoverbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_secondmenuhoverbgimg_tiling'} == 'Top to bottom') {
		$secondmenuhoverbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_secondmenuhoverbgimg_tiling'} == 'Both directions') {
		$secondmenuhoverbgrepeatcss = '';
	}
$secondmenuhoverbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_secondmenuhoverbgurl'}."');".$secondmenuhoverbgrepeatcss."background-position:center top;";
} else { $secondmenuhoverbgcss = ''; }

if (${$cat.'_secondmenuhoverbg_color'} != 'none') {
	$secondmenuhoverbgcolorcss = "background-color:#".${$cat."_secondmenuhoverbg_color"}.";";
} else { $secondmenuhoverbgcolorcss = ''; }


/* featured two bg*/
if (${$cat.'_feattwobgurl'} != 'noimage') {
	if (${$cat.'_feattwobgimg_tiling'} == 'No Tiling') {
		$feattwobgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_feattwobgimg_tiling'} == 'Left to right') {
		$feattwobgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_feattwobgimg_tiling'} == 'Top to bottom') {
		$feattwobgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_feattwobgimg_tiling'} == 'Both directions') {
		$feattwobgrepeatcss = '';
	}
$feattwobgcss = "background-image:url('".$imageprefix."images/".${$cat.'_feattwobgurl'}."');".$feattwobgrepeatcss."background-position:center top;";
} else { $feattwobgcss = ''; }

if (${$cat.'_featuredtwobg_color'} != 'none') {
	$feattwobgcolorcss = "background-color:#".${$cat."_featuredtwobg_color"}.";";
} else { $feattwobgcolorcss = ''; }


/* content area bg*/
if (${$cat."_contentareabgurl"} != 'noimage') {
	if (${$cat.'_contentareabgimg_tiling'} == 'No Tiling') {
		$contentareabgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_contentareabgimg_tiling'} == 'Left to right') {
		$contentareabgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_contentareabgimg_tiling'} == 'Top to bottom') {
		$contentareabgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_contentareabgimg_tiling'} == 'Both directions') {
		$contentareabgrepeatcss = '';
	}
$contentareabgcss = "background-image:url('".$imageprefix."images/".${$cat.'_contentareabgurl'}."');".$contentareabgrepeatcss."background-position:center top;";
} else { $contentareabgcss = ''; }

if (${$cat.'_contentareabg_color'} != 'none') {
	$contentareabgcolorcss = "background-color:#".${$cat."_contentareabg_color"}.";";
} else { $contentareabgcolorcss = ''; }

/* posts area bg */
if (${$cat."_postsareabgurl"} != 'noimage') {
	if (${$cat.'_postsareabgimg_tiling'} == 'No Tiling') {
		$postsareabgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_postsareabgimg_tiling'} == 'Left to right') {
		$postsareabgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_postsareabgimg_tiling'} == 'Top to bottom') {
		$postsareabgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_postsareabgimg_tiling'} == 'Both directions') {
		$postsareabgrepeatcss = '';
	}
$postsareabgcss = "background-image:url('".$imageprefix."images/".${$cat.'_postsareabgurl'}."');".$postsareabgrepeatcss."background-position:center top;";
} else { $postsareabgcss = ''; }

if (${$cat.'_postsareabg_color'} != 'none') {
	$postsareabgcolorcss = "background-color:#".${$cat."_postsareabg_color"}.";";
} else { $postsareabgcolorcss = ''; }

/* individual post bg */
if (${$cat."_postbgurl"} != 'noimage') {
	if (${$cat.'_postbgimg_tiling'} == 'No Tiling') {
		$postbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_postbgimg_tiling'} == 'Left to right') {
		$postbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_postbgimg_tiling'} == 'Top to bottom') {
		$postbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_postbgimg_tiling'} == 'Both directions') {
		$postbgrepeatcss = '';
	}
$postbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_postbgurl'}."');".$postbgrepeatcss."background-position:center top;";
} else { $postbgcss = ''; }

if (${$cat.'_postbg_color'} != 'none') {
	$postbgcolorcss = "background-color:#".${$cat."_postbg_color"}.";";
} else { $postbgcolorcss = ''; }

/* sidebar bg */
if (${$cat."_sidebarbgurl"} != 'noimage') {
	if (${$cat.'_sidebarbgimg_tiling'} == 'No Tiling') {
		$sidebarbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_sidebarbgimg_tiling'} == 'Left to right') {
		$sidebarbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_sidebarbgimg_tiling'} == 'Top to bottom') {
		$sidebarbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_sidebarbgimg_tiling'} == 'Both directions') {
		$sidebarbgrepeatcss = '';
	}
$sidebarbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_sidebarbgurl'}."');".$sidebarbgrepeatcss."background-position:center top;";
} else { $sidebarbgcss = ''; }

if (${$cat.'_sidebarbg_color'} != 'none') {
	$sidebarbgcolorcss = "background-color:#".${$cat."_sidebarbg_color"}.";";
} else { $sidebarbgcolorcss = ''; }

/* widget bg */
if (${$cat."_widgetbgurl"} != 'noimage') {
	if (${$cat.'_widgetbgimg_tiling'} == 'No Tiling') {
		$widgetbgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_widgetbgimg_tiling'} == 'Left to right') {
		$widgetbgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_widgetbgimg_tiling'} == 'Top to bottom') {
		$widgetbgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_widgetbgimg_tiling'} == 'Both directions') {
		$widgetbgrepeatcss = '';
	}
$widgetbgcss = "background-image:url('".$imageprefix."images/".${$cat.'_widgetbgurl'}."');".$widgetbgrepeatcss."background-position:center top;";
} else { $widgetbgcss = ''; }

if (${$cat.'_widgetbg_color'} != 'none') {
	$widgetbgcolorcss = "background-color:#".${$cat."_widgetbg_color"}.";";
} else { $widgetbgcolorcss = ''; }

/* footer one bg*/
if (${$cat.'_footeronebgurl'} != 'noimage') {
	if (${$cat.'_footeronebgimg_tiling'} == 'No Tiling') {
		$footeronebgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_footeronebgimg_tiling'} == 'Left to right') {
		$footeronebgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_footeronebgimg_tiling'} == 'Top to bottom') {
		$footeronebgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_footeronebgimg_tiling'} == 'Both directions') {
		$footeronebgrepeatcss = '';
	}
$footeronebgcss = "background-image:url('".$imageprefix."images/".${$cat.'_footeronebgurl'}."');".$footeronebgrepeatcss."background-position:center top;";
} else { $footeronebgcss = ''; }

if (${$cat.'_footeronebg_color'} != 'none') {
	$footeronebgcolorcss = "background-color:#".${$cat."_footeronebg_color"}.";";
} else { $footeronebgcolorcss = ''; }


/* footer two bg*/
if (${$cat.'_footertwobgurl'} != 'noimage') {
	if (${$cat.'_footertwobgimg_tiling'} == 'No Tiling') {
		$footertwobgrepeatcss = 'background-repeat:no-repeat;';
	} else if (${$cat.'_footertwobgimg_tiling'} == 'Left to right') {
		$footertwobgrepeatcss = 'background-repeat:repeat-x;';
	} else if (${$cat.'_footertwobgimg_tiling'} == 'Top to bottom') {
		$footertwobgrepeatcss = 'background-repeat:repeat-y;';
	} else if (${$cat.'_footertwobgimg_tiling'} == 'Both directions') {
		$footertwobgrepeatcss = '';
	}
$footertwobgcss = "background-image:url('".$imageprefix."images/".${$cat.'_footertwobgurl'}."');".$footertwobgrepeatcss."background-position:center top;";
} else { $footertwobgcss = ''; }

if (${$cat.'_footertwobg_color'} != 'none') {
	$footertwobgcolorcss = "background-color:#".${$cat."_footertwobg_color"}.";";
} else { $footertwobgcolorcss = ''; }
}

function getStylesheetData($setimageprefix){
global $saveoptions,$importgfonts,$socialcode,$customStyleData,$contentareabgcolorcss,$contentareabgcss,$postsareabgcss,$postsareabgcolorcss,$postbgcss,$postbgcolorcss,$sidebarbgcss,$sidebarbgcolorcss,$widgetbgcss,$widgetbgcolorcss,$bodybgcss,$bgwidthsetting,$bodywrapbgcss,$headerbgcss,$headerbgcolorcss,$mainmenubgcss,$mainmenubgcolorcss,$mainmenudropbgcss,$mainmenudropbgcolorcss,$mainmenuhoverbgcss,$mainmenuhoverbgcolorcss,$featonebgcss,$featonebgcolorcss,$secondmenubgcss,$secondmenubgcolorcss,$secondmenudropbgcss,$secondmenudropbgcolorcss,$secondmenuhoverbgcss,$secondmenuhoverbgcolorcss,$feattwobgcss,$feattwobgcolorcss,$footeronebgcss,$footeronebgcolorcss,$footertwobgcss,$footertwobgcolorcss,$swp_default_font;

$passimageprefix = $setimageprefix;
getDynamicStyles($passimageprefix);

foreach ($saveoptions as $value) {if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); }}
				
$cat=get_template();

/* get gfont includes */
if (${$cat."_droidsans"}=="Make Available"){
	$droidsans = "Droid+Sans:regular,bold|";
} else {
	$droidsans = "";
}
if (${$cat."_lobster"}=="Make Available"){
	$lobster = "Lobster|";
} else {
	$lobster = "";
}
if (${$cat."_ptsans"}=="Make Available"){
	$ptsans = "PT+Sans:regular,bold|";
} else {
	$ptsans = "";
}
if (${$cat."_droidserif"}=="Make Available"){
	$droidserif = "Droid+Serif:regular,bold|";
} else {
	$droidserif = "";
}
if (${$cat."_ubuntu"}=="Make Available"){
	$ubuntu = "Ubuntu:regular,bold|";
} else {
	$ubuntu = "";
}
if (${$cat."_yanonekaffeesatz"}=="Make Available"){
	$yanonekaffeesatz = "Yanone+Kaffeesatz:regular,bold|";
} else {
	$yanonekaffeesatz = "";
}
if (${$cat."_nobile"}=="Make Available"){
	$nobile = "Nobile:regular,bold|";
} else {
	$nobile = "";
}
if (${$cat."_reeniebeanie"}=="Make Available"){
	$reeniebeanie = "Reenie+Beanie|";
} else {
	$reeniebeanie = "";
}
if (${$cat."_molengo"}=="Make Available"){
	$molengo = "Molengo|";
} else {
	$molengo = "";
}
if (${$cat."_arvo"}=="Make Available"){
	$arvo = "Arvo:regular,bold|";
} else {
	$arvo = "";
}
if (${$cat."_comingsoon"}=="Make Available"){
	$comingsoon = "Coming+Soon|";
} else {
	$comingsoon = "";
}
if (${$cat."_craftygirls"}=="Make Available"){
	$craftygirls = "Crafty+Girls|";
} else {
	$craftygirls = "";
}
if (${$cat."_calligraffitti"}=="Make Available"){
	$calligraffitti = "Calligraffitti|";
} else {
	$calligraffitti = "";
}
if (${$cat."_tangerine"}=="Make Available"){
	$tangerine = "Tangerine|";
} else {
	$tangerine = "";
}
if (${$cat."_cherrycreamsoda"}=="Make Available"){
	$cherrycreamsoda = "Cherry+Cream+Soda|";
} else {
	$cherrycreamsoda = "";
}
if (${$cat."_oflsortsmillgoudytt"}=="Make Available"){
	$oflsortsmillgoudytt = "OFL+Sorts+Mill+Goudy+TT|";
} else {
	$oflsortsmillgoudytt = "";
}
if (${$cat."_cantarell"}=="Make Available"){
	$cantarell = "Cantarell:regular,bold|";
} else {
	$cantarell = "";
}
if (${$cat."_rocksalt"}=="Make Available"){
	$rocksalt = "Rock+Salt|";
} else {
	$rocksalt = "";
}
if (${$cat."_vollkorn"}=="Make Available"){
	$vollkorn = "Vollkorn:regular,bold|";
} else {
	$vollkorn = "";
}
if (${$cat."_coveredbyyourgrace"}=="Make Available"){
	$coveredbyyourgrace = "Covered+By+Your+Grace|";
} else {
	$coveredbyyourgrace = "";
}
if (${$cat."_chewy"}=="Make Available"){
	$chewy = "Chewy|";
} else {
	$chewy = "";
}
if (${$cat."_luckiestguy"}=="Make Available"){
	$luckiestguy = "Luckiest+Guy|";
} else {
	$luckiestguy = "";
}
if (${$cat."_dancingscript"}=="Make Available"){
	$dancingscript = "Dancing+Script|";
} else {
	$dancingscript = "";
}
if (${$cat."_bangers"}=="Make Available"){
	$bangers = "Bangers|";
} else {
	$bangers = "";
}
if (${$cat."_philosopher"}=="Make Available"){
	$philosopher = "Philosopher|";
} else {
	$philosopher = "";
}
if (${$cat."_fontdinerswanky"}=="Make Available"){
	$fontdinerswanky = "Fontdiner+Swanky|";
} else {
	$fontdinerswanky = "";
}
if (${$cat."_slackey"}=="Make Available"){
	$slackey = "Slackey|";
} else {
	$slackey = "";
}
if (${$cat."_permanentmarker"}=="Make Available"){
	$permanentmarker = "Permanent+Marker|";
} else {
	$permanentmarker = "";
}
if (${$cat."_cabinsketch"}=="Make Available"){
	$cabinsketch = "Cabin+Sketch:bold|";
} else {
	$cabinsketch = "";
}
if (${$cat."_michroma"}=="Make Available"){
	$michroma = "Michroma|";
} else {
	$michroma = "";
}
if (${$cat."_unkempt"}=="Make Available"){
	$unkempt = "Unkempt|";
} else {
	$unkempt = "";
}
if (${$cat."_allan"}=="Make Available"){
	$allan = "Allan:bold|";
} else {
	$allan = "";
}
if (${$cat."_corben"}=="Make Available"){
	$corben = "Corben:bold|";
} else {
	$corben = "";
}
if (${$cat."_mountainsofchristmas"}=="Make Available"){
	$mountainsofchristmas = "Mountains+of+Christmas|";
} else {
	$mountainsofchristmas = "";
}
if (${$cat."_maidenorange"}=="Make Available"){
	$maidenorange = "Maiden+Orange|";
} else {
	$maidenorange = "";
}
if (${$cat."_extragfonts"}!=""){
	$extragfonts = ${$cat."_extragfonts"};
} else {
	$extragfonts = "";
}


$importgfonts = "@import 'http://fonts.googleapis.com/css?family=".$droidsans.$lobster.$ptsans.$droidserif.$ubuntu.$yanonekaffeesatz.$nobile.$reeniebeanie.$arvo.$comingsoon.$craftygirls.$calligraffitti.$tangerine.$cherrycreamsoda.$oflsortsmillgoudytt.$cantarell.$rocksalt.$vollkorn.$coveredbyyourgrace.$chewy.$luckiestguy.$dancingscript.$bangers.$philosopher.$fontdinerswanky.$slackey.$permanentmarker.$cabinsketch.$michroma.$unkempt.$allan.$corben.$mountainsofchristmas.$maidenorange.$extragfonts."';";

/* post width calculations */
$paddingamount = (${$cat."_pcolnum"}*30)+10;
$withsidepostarea = (${$cat."_bodywidth"} - ${$cat."_sidebar_area_width"}-10);
$colsidepostarea = $withsidepostarea - $paddingamount;
$colnosidepostarea = ${$cat."_bodywidth"} - $paddingamount;

$swp_nocolpostwidth =( $withsidepostarea-40 );
$swp_nocolpostfullwidth =( ${$cat."_bodywidth"}-40 );

$swp_colsidepostwidth = round ( $colsidepostarea / ${$cat."_pcolnum"} );
$swp_colnosidepostwidth = round ( $colnosidepostarea / ${$cat."_pcolnum"} );

global $swp_colsidemasonrywidth;
$swp_colsidemasonrywidth = $swp_colsidepostwidth + 30;
global $swp_colnosidemasonrywidth;
$swp_colnosidemasonrywidth = $swp_colnosidepostwidth + 30;

/*sidebar right side padding */

if (${$cat."_sidebar_float"} == "Left") {
$sbmarg = "padding-left:10px;";
$postareafloat = "right";
} else {
$sbmarg = "padding-right:10px;";
$postareafloat = "left";
}

/* footer one widget width calculations */
$footonepaddingamount = (${$cat."_fonecolnum"}*20)+20;
$footeonewidgetwidth = round((${$cat."_bodywidth"}-$footonepaddingamount)/${$cat."_fonecolnum"});

/* menu arrow positions */
$mainmenuarrow  = round(${$cat."_mainmenu_height"}/2)-5;
$secondmenuarrow  = round(${$cat."_secondmenu_height"}/2)-5;

/* search form width */
$searchformwidth = ${$cat."_sidebar_area_width"} - 52;

/* post image widths */
$colsidepostimgwd = $swp_colsidepostwidth - 20;
$colnosidepostimgwd = $swp_colnosidepostwidth - 20;

/* titles and icons css */
if (${$cat."_headerlogourl"}!='noimage'){
	$logocss = ".logopos {position:absolute;top:".${$cat."_logopos_top"}."px;left:".${$cat."_logopos_left"}."px;z-index:1;}";
}
if (${$cat."_sitetitle_status"}=='Show'){
	$titlecss = ".titlepos {position:absolute;top:".${$cat."_sitetitlepos_top"}."px;left:".${$cat."_sitetitlepos_left"}."px;z-index:2;}";
} 
if (${$cat."_tagline_status"}=='Show'){
	$taglinecss = ".taglinepos {position:absolute;top:".${$cat."_taglinepos_top"}."px;left:".${$cat."_taglinepos_left"}."px;z-index:3;}";
}
if (${$cat."_rssiconurl"}!='noimage'){
	$rssiconcss = ".rssiconpos {position:absolute;top:".${$cat."_rssiconpos_top"}."px;left:".${$cat."_rssiconpos_left"}."px;z-index:4;}";
}
if (${$cat."_extraiconurl"}!='noimage'){
	$extraiconcss = ".extraiconpos {position:absolute;top:".${$cat."_extraiconpos_top"}."px;left:".${$cat."_extraiconpos_left"}."px;z-index:5;}";
}

$customStyleData = 
$importgfonts."
".".fullscreen {background-color:#".${$cat."_bodybg_color"}.";".$bodybgcss."}
".$bodywrapbgcss."
.wrap {width:".$bgwidthsetting.";padding:0;font-family:".${$cat."_default_font"}.";font-size:".${$cat."_default_size"}."px;}

h1 {font-family:".${$cat."_h1default_font"}.";font-size:".${$cat."_h1default_size"}."px;color:#".${$cat."_h1default_color"}.";}
h2 {font-family:".${$cat."_h2default_font"}.";font-size:".${$cat."_h2default_size"}."px;color:#".${$cat."_h2default_color"}.";}
h3 {font-family:".${$cat."_h3default_font"}.";font-size:".${$cat."_h3default_size"}."px;color:#".${$cat."_h3default_color"}.";}
h4 {font-family:".${$cat."_h4default_font"}.";font-size:".${$cat."_h4default_size"}."px;color:#".${$cat."_h4default_color"}.";}
h5 {font-family:".${$cat."_h5default_font"}.";font-size:".${$cat."_h5default_size"}."px;color:#".${$cat."_h5default_color"}.";}
.inner {width:".${$cat."_bodywidth"}."px;}
.leftcol {margin-right:".(${$cat."_bodywidth"}/2)."px;}
.rightcol {margin-left:".(${$cat."_bodywidth"}/2)."px;}
.topad {height:".${$cat."_topad_size"}."px;width:".${$cat."_bodywidth"}."px;}
.header {".$headerbgcolorcss."".$headerbgcss."height:".${$cat."_header_height"}."px;}
".$logocss." ".$titlecss." ".$taglinecss." ".$rssiconcss." ".$extraiconcss."
.header a:link .titlepos, .header a:visited .titlepos, .header a:hover .titlepos {color:#".${$cat."_sitetitle_color"}.";font-size:".${$cat."_sitetitletext_size"}."px;height:".${$cat."_sitetitletext_size"}."px;margin:0;padding:0;font-family:".${$cat."_sitetitle_font"}.";line-height:normal;font-weight:".${$cat."_sitetitle_weight"}.";}
.header a:link .taglinepos, .header a:visited .taglinepos, .header a:hover .taglinepos {color:#".${$cat."_tagline_color"}.";font-size:".${$cat."_taglinetext_size"}."px;height:".${$cat."_taglinetext_size"}."px;margin:0;padding:0;font-family:".${$cat."_tagline_font"}.";line-height:normal;font-weight:".${$cat."_tagline_weight"}.";}

.sf-menu li.sfHover ul {top:100%;}
.mainmenu a > .sf-sub-indicator, .mainmenu .sf-sub-indicator {top:".$mainmenuarrow."px;}
.secondmenu a > .sf-sub-indicator, .secondmenu .sf-sub-indicator {top:".$secondmenuarrow."px;}

/*main bg */
.mainmenu {".$mainmenubgcss."".$mainmenubgcolorcss."height:".${$cat."_mainmenu_height"}."px;z-index:999;font-size:".${$cat."_mainmenutext_size"}."px;}

.mainmenu ul.sf-menu {height:".${$cat."_mainmenu_height"}."px;}
.mainmenu .page_item {line-height:".${$cat."_mainmenu_height"}."px;height:".${$cat."_mainmenu_height"}."px;}

/*main link color */
.mainmenu .sf-menu a, .mainmenu .sf-menu a:visited  { color:#".${$cat."_mainmenutext_color"}.";line-height:".${$cat."_mainmenu_height"}."px;height:".${$cat."_mainmenu_height"}."px;font-family:".${$cat."_mainmenu_font"}.";}

/*dropdown bg and color */
.mainmenu .sf-menu li li, .mainmenu .sf-menu li li li, .mainmenu .sf-menu li li:hover, .mainmenu .sf-menu li li:hover a, .mainmenu .sf-menu li li.sfHover, .mainmenu .sf-menu li a:focus, .mainmenu .sf-menu li a:hover, .mainmenu li .sf-menu a:active {".$mainmenudropbgcss."".$mainmenudropbgcolorcss."color:#".${$cat."_mainmenudrop_color"}.";}

/*hover bg and color */
.mainmenu .sf-menu li:hover, .mainmenu .sf-menu li:hover a, .mainmenu .sf-menu li.sfHover, .mainmenu .sf-menu a:focus, .mainmenu .sf-menu a:hover, .mainmenu .sf-menu a:active {".$mainmenuhoverbgcss."".$mainmenuhoverbgcolorcss."color:#".${$cat."_mainmenuhover_color"}.";}

/*second bg */
.secondmenu {".$secondmenubgcss."".$secondmenubgcolorcss."height:".${$cat."_secondmenu_height"}."px;z-index:999;font-size:".${$cat."_secondmenutext_size"}."px;}

.secondmenu ul.sf-menu {height:".${$cat."_secondmenu_height"}."px;}
.secondmenu .page_item {line-height:".${$cat."_secondmenu_height"}."px;height:".${$cat."_secondmenu_height"}."px;}

/*second link color */
.secondmenu .sf-menu a, .secondmenu .sf-menu a:visited  { color:#".${$cat."_secondmenutext_color"}.";line-height:".${$cat."_secondmenu_height"}."px;height:".${$cat."_secondmenu_height"}."px;font-family:".${$cat."_secondmenu_font"}.";}

/*dropdown bg and color */
.secondmenu .sf-menu li li, .secondmenu .sf-menu li li li, .secondmenu .sf-menu li li:hover, .secondmenu .sf-menu li li:hover a, .secondmenu .sf-menu li li.sfHover, .secondmenu .sf-menu li a:focus, .secondmenu .sf-menu li a:hover, .secondmenu li .sf-menu a:active {".$secondmenudropbgcss."".$secondmenudropbgcolorcss."color:#".${$cat."_secondmenudrop_color"}.";}

/*hover bg and color */
.secondmenu .sf-menu li:hover, .secondmenu .sf-menu li:hover a, .secondmenu .sf-menu li.sfHover, .secondmenu .sf-menu a:focus, .secondmenu .sf-menu a:hover, .secondmenu .sf-menu a:active {".$secondmenuhoverbgcss."".$secondmenuhoverbgcolorcss."color:#".${$cat."_secondmenuhover_color"}.";}


.featuredone {".$featonebgcolorcss."".$featonebgcss."color:#".${$cat."_featuredslidetext_color"}.";height:".${$cat."_featuredone_height"}."px;}
.featuredone a:link, .featuredone a:visited {color:#".${$cat."_featuredslidelink_color"}.";}
.featuredone a:hover {color:#".${$cat."_featuredslidehover_color"}.";}
.featuredone img {max-width:".${$cat."_bodywidth"}."px;}

.featuredtwo {".$feattwobgcolorcss."".$feattwobgcss."color:#".${$cat."_featuredtwotext_color"}.";min-height:".${$cat."_featuredtwo_height"}."px;}
.featuredtwo a:link, .featuredtwo a:visited {color:#".${$cat."_featuredtwolink_color"}.";}
.featuredtwo a:hover {color:#".${$cat."_featuredtwohover_color"}.";}
.featuredtwo img {max-width:".${$cat."_bodywidth"}."px;}

.content {".$contentareabgcolorcss."".$contentareabgcss."}
.postarea {".$postsareabgcss."".$postsareabgcolorcss."color:#".${$cat."_poststext_color"}.";float:".$postareafloat.";}
.postarea a:link, .postarea a:visited {color:#".${$cat."_postslink_color"}.";}
.postarea a:hover {color:#".${$cat."_postshover_color"}.";}

h1.posttitle, h1.pagetitle {font-size:".${$cat."_postheading_size"}."px;font-family:".${$cat."_postheading_font"}.";font-weight:".${$cat."_postheading_weight"}.";}
h1.posttitle a:link, h1.posttitle a:visited, h1.pagetitle a:link, h1.pagetitle a:visited {font-size:".${$cat."_postheading_size"}."px;color:#".${$cat."_postheading_color"}.";font-family:".${$cat."_postheading_font"}.";font-weight:".${$cat."_postheading_weight"}.";}
h1.posttitle a:hover , h1.pagetitle a:hover{color:#".${$cat."_postheadinghover_color"}.";}

.post {".$postbgcss."".$postbgcolorcss."}
.post #searchform input {border:1px solid #".${$cat."_postmetaborder_color"}.";width:".$searchformwidth."px;}

.colside, .nocolside {width:".$withsidepostarea."px;}
.colnoside, .nocolnoside {width:".${$cat."_bodywidth"}."px;}

.nocolsidepost {width:".$swp_nocolpostwidth."px;}
.nocolnosidepost {width:".$swp_nocolpostfullwidth."px;}
.colsidepost {width:".$swp_colsidepostwidth."px;} .colsidepost img {max-width:".$colsidepostimgwd."px;}
.colnosidepost {width:".$swp_colnosidepostwidth."px;} .colnosidepost img {max-width:".$colnosidepostimgwd."px;}

.postmeta {border:1px solid #".${$cat."_postmetaborder_color"}.";background:#".${$cat."_postmetabg_color"}.";color:#".${$cat."_postmetatext_color"}.";font-size:".${$cat."_postmeta_size"}."px;}
.postmeta a:link, .postmeta a:visited {color:#".${$cat."_postmetalink_color"}.";}
.postmeta a:hover {color:#".${$cat."_postmetahover_color"}.";}

.postmetatwo {border-bottom:1px solid #".${$cat."_postmetaborder_color"}.";font-size:".${$cat."_postmeta_size"}."px;;}

.post blockquote {border:1px solid #".${$cat."_blockquoteborder_color"}.";background:#".${$cat."_blockquotebg_color"}.";font-family:".${$cat."_blockquote_font"}.";font-size:".${$cat."_blockquote_size"}."px;color:#".${$cat."_blockquotetext_color"}.";}
.post blockquote a:link, .post blockquote a:visited {color:#".${$cat."_blockquotelink_color"}.";} .post blockquote a:hover {color:#".${$cat."_blockquotehover_color"}.";}

ul.commentlist li {border:1px dotted #".${$cat."_postmetaborder_color"}.";}
ul.commentlist li ul li {border:1px dashed #".${$cat."_postmetaborder_color"}.";}

p.form-submit input#submit {border:1px solid #".${$cat."_postmetaborder_color"}.";background:#".${$cat."_postmetabg_color"}.";color:#".${$cat."_postmetalink_color"}.";}
p.form-submit input#submit:hover {color:#".${$cat."_postmetahover_color"}.";}

.emm-paginate a, .emm-paginate a:link, .emm-paginate a:visited {background:#".${$cat."_postmetabg_color"}."; border:1px solid #".${$cat."_postmetaborder_color"}."; color:#".${$cat."_postmetatext_color"}.";}
.emm-paginate a:hover, .emm-paginate a:active {color:#".${$cat."_postslink_color"}.";}
.emm-paginate .emm-current {border:1px solid #".${$cat."_postmetaborder_color"}.";}

.sidebar {".$sidebarbgcss."".$sidebarbgcolorcss."float:".${$cat."_sidebar_float"}.";".$sbmarg."width:".${$cat."_sidebar_area_width"}."px;color:#".${$cat."_sidebartext_color"}.";}
.widget {".$widgetbgcss."".$widgetbgcolorcss."}
.sidebar a:link, .sidebar a:visited {color:#".${$cat."_sidebarlink_color"}.";} .sidebar a:hover {color:#".${$cat."_sidebarhover_color"}.";}
.sidebar h4.widgetheading {border-bottom:1px solid #".${$cat."_widgetheadingunderline_color"}.";font-size:".${$cat."_widgetheading_size"}."px;color:#".${$cat."_widgetheading_color"}.";font-family:".${$cat."_widgetheading_font"}.";font-weight:".${$cat."_widgetheading_weight"}.";}
.sidebar h4.widgetheading a:link, .sidebar h4.widgetheading a:visited, .sidebar h4.widgetheading a:hover {font-size:".${$cat."_widgetheading_size"}."px;color:#".${$cat."_widgetheading_color"}.";}
.widget #searchform input {border:1px solid #".${$cat."_widgetheadingunderline_color"}.";width:".(${$cat."_sidebar_area_width"}-42)."px;}

.footerone {".$footeronebgcss."".$footeronebgcolorcss."min-height:".${$cat."_footerone_height"}."px;color:#".${$cat."_footeronetext_color"}.";}
.footerone h4.widgetheading, .footerone h4.widgetheading a:link, .footerone h4.widgetheading a:visited, .footerone h4.widgetheading a:hover {font-family:".${$cat."_footwidgetheading_font"}.";font-weight:".${$cat."_footwidgetheading_weight"}.";color:#".${$cat."_footwidgetheading_color"}.";font-size:".${$cat."_footwidgetheading_size"}."px;}
.footerone a:link, .footerone a:visited {color:#".${$cat."_footeronelink_color"}.";}
.footerone a:hover {color:#".${$cat."_footeronehover_color"}.";}
.footwidget {width:".$footeonewidgetwidth."px;}
.footerone #searchform input {".$footeronebgcolorcss."color:#".${$cat."_footeronetext_color"}.";width:".($footeonewidgetwidth-22)."px;}

.footertwo {".$footertwobgcss."".$footertwobgcolorcss."height:".${$cat."_footertwo_height"}."px;color:#".${$cat."_footertwotext_color"}.";}
.footertwo a:link, .footertwo a:visited {color:#".${$cat."_footertwolink_color"}.";}
.footertwo a:hover {color:#".${$cat."_footertwohover_color"}.";}
";

}


function writeThemeSetup(){
global $saveoptions,$importgfonts,$customStyleData,$socialcode,$bodybgcss,$bgwidthsetting,$bodywrapbgcss,$headerbgcss,$headerbgcolorcss,$mainmenubgcss,$mainmenudropbgcss,$mainmenudropbgcolorcss,$mainmenuhoverbgcss,$mainmenuhoverbgcolorcss,$featonebgcss,$featonebgcolorcss,$secondmenubgcss,$secondmenubgcolorcss,$secondmenudropbgcss,$secondmenudropbgcolorcss,$secondmenuhoverbgcss,$secondmenuhoverbgcolorcss,$feattwobgcss,$feattwobgcolorcss,$footeronebgcss,$footeronebgcolorcss,$footertwobgcss,$footertwobgcolorcss;

/*fetch data to write to stylesheet */
getStylesheetData('');

foreach ($saveoptions as $value) {if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); }}
				
$cat=get_template();

$customStyleSheet = TEMPLATEPATH."/swstyleops.css";
$customStyleHandle = fopen($customStyleSheet, 'w') or die("can't open file");
fwrite($customStyleHandle, $customStyleData); 
fclose($customStyleHandle);


/* write theme files */

/* get block rows */
$row_01= ${$cat."_row_01"};
$row_02= ${$cat."_row_02"};
$row_03= ${$cat."_row_03"};
$row_04= ${$cat."_row_04"};
$row_05= ${$cat."_row_05"};
$row_06= ${$cat."_row_06"};
$row_07= ${$cat."_row_07"};
$row_08= ${$cat."_row_08"};


/* get single sidebar status */
if (${$cat."_sidebar_status_single"} == "Show") {
$singleside = "-side";
} else {
$singleside = "-noside";
}

/* get single file includes */
if (${$cat."_".$row_01."_status_single"}=="Show"){
	if ($row_01 == "contentarea"){
	$singlerow01 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow01 = "get_template_part( '/templateparts/el_".$row_01."' );";
	}
} else { 
	$singlerow01 = "";
}
if (${$cat."_".$row_02."_status_single"}=="Show"){
	if ($row_02 == "contentarea"){
	$singlerow02 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow02 = "get_template_part( '/templateparts/el_".$row_02."' );";
	}
} else { 
	$singlerow02 = "";
}
if (${$cat."_".$row_03."_status_single"}=="Show"){
	if ($row_03 == "contentarea"){
	$singlerow03 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow03 = "get_template_part( '/templateparts/el_".$row_03."' );";
	}
} else { 
	$singlerow03 = "";
}
if (${$cat."_".$row_04."_status_single"}=="Show"){
	if ($row_04 == "contentarea"){
	$singlerow04 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow04 = "get_template_part( '/templateparts/el_".$row_04."' );";
	}
} else { 
	$singlerow04 = "";
}
if (${$cat."_".$row_05."_status_single"}=="Show"){
	if ($row_05 == "contentarea"){
	$singlerow05 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow05 = "get_template_part( '/templateparts/el_".$row_05."' );";
	}
} else { 
	$singlerow05 = "";
}
if (${$cat."_".$row_06."_status_single"}=="Show"){
	if ($row_06 == "contentarea"){
	$singlerow06 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow06 = "get_template_part( '/templateparts/el_".$row_06."' );";
	}
} else { 
	$singlerow06 = "";
}
if (${$cat."_".$row_07."_status_single"}=="Show"){
	if ($row_07 == "contentarea"){
	$singlerow07 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow07 = "get_template_part( '/templateparts/el_".$row_07."' );";
	}
} else { 
	$singlerow07 = "";
}
if (${$cat."_".$row_08."_status_single"}=="Show"){
	if ($row_08 == "contentarea"){
	$singlerow08 = "get_template_part( '/templateparts/el_contentarea-single".$singleside."' );";
	} else {
	$singlerow08 = "get_template_part( '/templateparts/el_".$row_08."' );";
	}
} else { 
	$singlerow08 = "";
}

/* write single file includes */
$singleFileSheet = TEMPLATEPATH."/single.php";
$singleFileHandle = fopen($singleFileSheet, 'w') or die("can't open file");
$singleFileData = "<?php get_header();".$singlerow01.$singlerow02.$singlerow03.$singlerow04.$singlerow05.$singlerow06.$singlerow07.$singlerow08."get_footer(); ?>";
fwrite($singleFileHandle, $singleFileData); 
fclose($singleFileHandle);


/* get front page  sidebar status */
if (${$cat."_sidebar_status_home"} == "Show") {
$homeside = "-side";
} else {
$homeside = "-noside";
}

/* get front page file includes */
if (${$cat."_".$row_01."_status_home"}=="Show"){
	if ($row_01 == "contentarea"){
	$homerow01 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow01 = "get_template_part( '/templateparts/el_".$row_01."' );";
	}
} else { 
	$homerow01 = "";
}
if (${$cat."_".$row_02."_status_home"}=="Show"){
	if ($row_02 == "contentarea"){
	$homerow02 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow02 = "get_template_part( '/templateparts/el_".$row_02."' );";
	}
} else { 
	$homerow02 = "";
}
if (${$cat."_".$row_03."_status_home"}=="Show"){
	if ($row_03 == "contentarea"){
	$homerow03 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow03 = "get_template_part( '/templateparts/el_".$row_03."' );";
	}
} else { 
	$homerow03 = "";
}
if (${$cat."_".$row_04."_status_home"}=="Show"){
	if ($row_04 == "contentarea"){
	$homerow04 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow04 = "get_template_part( '/templateparts/el_".$row_04."' );";
	}
} else { 
	$homerow04 = "";
}
if (${$cat."_".$row_05."_status_home"}=="Show"){
	if ($row_05 == "contentarea"){
	$homerow05 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow05 = "get_template_part( '/templateparts/el_".$row_05."' );";
	}
} else { 
	$homerow05 = "";
}
if (${$cat."_".$row_06."_status_home"}=="Show"){
	if ($row_06 == "contentarea"){
	$homerow06 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow06 = "get_template_part( '/templateparts/el_".$row_06."' );";
	}
} else { 
	$homerow06 = "";
}
if (${$cat."_".$row_07."_status_home"}=="Show"){
	if ($row_07 == "contentarea"){
	$homerow07 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow07 = "get_template_part( '/templateparts/el_".$row_07."' );";
	}
} else { 
	$homerow07 = "";
}
if (${$cat."_".$row_08."_status_home"}=="Show"){
	if ($row_08 == "contentarea"){
	$homerow08 = "get_template_part( '/templateparts/el_contentarea-home".$homeside."' );";
	} else {
	$homerow08 = "get_template_part( '/templateparts/el_".$row_08."' );";
	}
} else { 
	$homerow08 = "";
}

/* write front-page file includes */
$frontpageFileSheet = TEMPLATEPATH."/front-page.php";
$frontpageFileHandle = fopen($frontpageFileSheet, 'w') or die("can't open file");
$frontpageFileData = "<?php get_header();".$homerow01.$homerow02.$homerow03.$homerow04.$homerow05.$homerow06.$homerow07.$homerow08."get_footer(); ?>";
fwrite($frontpageFileHandle, $frontpageFileData); 
fclose($frontpageFileHandle);


/* get home / blog / index sidebar status */
if (${$cat."_sidebar_status_blog"} == "Show") {
$blogside = "-side";
} else {
$blogside = "-noside";
}

/* get home / blog / index file includes */
if (${$cat."_".$row_01."_status_blog"}=="Show"){
	if ($row_01 == "contentarea"){
	$indexrow01 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow01 = "get_template_part( '/templateparts/el_".$row_01."' );";
	}
} else { 
	$indexrow01 = "";
}
if (${$cat."_".$row_02."_status_blog"}=="Show"){
	if ($row_02 == "contentarea"){
	$indexrow02 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow02 = "get_template_part( '/templateparts/el_".$row_02."' );";
	}
} else { 
	$indexrow02 = "";
}
if (${$cat."_".$row_03."_status_blog"}=="Show"){
	if ($row_03 == "contentarea"){
	$indexrow03 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow03 = "get_template_part( '/templateparts/el_".$row_03."' );";
	}
} else { 
	$indexrow03 = "";
}
if (${$cat."_".$row_04."_status_blog"}=="Show"){
	if ($row_04 == "contentarea"){
	$indexrow04 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow04 = "get_template_part( '/templateparts/el_".$row_04."' );";
	}
} else { 
	$indexrow04 = "";
}
if (${$cat."_".$row_05."_status_blog"}=="Show"){
	if ($row_05 == "contentarea"){
	$indexrow05 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow05 = "get_template_part( '/templateparts/el_".$row_05."' );";
	}
} else { 
	$indexrow05 = "";
}
if (${$cat."_".$row_06."_status_blog"}=="Show"){
	if ($row_06 == "contentarea"){
	$indexrow06 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow06 = "get_template_part( '/templateparts/el_".$row_06."' );";
	}
} else { 
	$indexrow06 = "";
}
if (${$cat."_".$row_07."_status_blog"}=="Show"){
	if ($row_07 == "contentarea"){
	$indexrow07 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow07 = "get_template_part( '/templateparts/el_".$row_07."' );";
	}
} else { 
	$indexrow07 = "";
}
if (${$cat."_".$row_08."_status_blog"}=="Show"){
	if ($row_08 == "contentarea"){
	$indexrow08 = "get_template_part( '/templateparts/el_contentarea-blog".$blogside."' );";
	} else {
	$indexrow08 = "get_template_part( '/templateparts/el_".$row_08."' );";
	}
} else { 
	$indexrow08 = "";
}

/* write index file includes */
$indexFileSheet = TEMPLATEPATH."/index.php";
$indexFileHandle = fopen($indexFileSheet, 'w') or die("can't open file");
$indexFileData = "<?php get_header();".$indexrow01.$indexrow02.$indexrow03.$indexrow04.$indexrow05.$indexrow06.$indexrow07.$indexrow08."get_footer(); ?>";
fwrite($indexFileHandle, $indexFileData); 
fclose($indexFileHandle);

/* write home file includes */
$homeFileSheet = TEMPLATEPATH."/home.php";
$homeFileHandle = fopen($homeFileSheet, 'w') or die("can't open file");
$homeFileData = "<?php get_header();".$indexrow01.$indexrow02.$indexrow03.$indexrow04.$indexrow05.$indexrow06.$indexrow07.$indexrow08."get_footer(); ?>";
fwrite($homeFileHandle, $homeFileData); 
fclose($homeFileHandle);


/* get page sidebar status */
if (${$cat."_sidebar_status_pages"} == "Show") {
$pagesside = "-side";
} else {
$pagesside = "-noside";
}

/* get page file includes */
if (${$cat."_".$row_01."_status_pages"}=="Show"){
	if ($row_01 == "contentarea"){
	$pagerow01 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow01 = "get_template_part( '/templateparts/el_".$row_01."' );";
	}
} else { 
	$pagerow01 = "";
}
if (${$cat."_".$row_02."_status_pages"}=="Show"){
	if ($row_02 == "contentarea"){
	$pagerow02 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow02 = "get_template_part( '/templateparts/el_".$row_02."' );";
	}
} else { 
	$pagerow02 = "";
}
if (${$cat."_".$row_03."_status_pages"}=="Show"){
	if ($row_03 == "contentarea"){
	$pagerow03 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow03 = "get_template_part( '/templateparts/el_".$row_03."' );";
	}
} else { 
	$pagerow03 = "";
}
if (${$cat."_".$row_04."_status_pages"}=="Show"){
	if ($row_04 == "contentarea"){
	$pagerow04 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow04 = "get_template_part( '/templateparts/el_".$row_04."' );";
	}
} else { 
	$pagerow04 = "";
}
if (${$cat."_".$row_05."_status_pages"}=="Show"){
	if ($row_05 == "contentarea"){
	$pagerow05 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow05 = "get_template_part( '/templateparts/el_".$row_05."' );";
	}
} else { 
	$pagerow05 = "";
}
if (${$cat."_".$row_06."_status_pages"}=="Show"){
	if ($row_06 == "contentarea"){
	$pagerow06 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow06 = "get_template_part( '/templateparts/el_".$row_06."' );";
	}
} else { 
	$pagerow06 = "";
}
if (${$cat."_".$row_07."_status_pages"}=="Show"){
	if ($row_07 == "contentarea"){
	$pagerow07 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow07 = "get_template_part( '/templateparts/el_".$row_07."' );";
	}
} else { 
	$pagerow07 = "";
}
if (${$cat."_".$row_08."_status_pages"}=="Show"){
	if ($row_08 == "contentarea"){
	$pagerow08 = "get_template_part( '/templateparts/el_contentarea-pages".$pagesside."' );";
	} else {
	$pagerow08 = "get_template_part( '/templateparts/el_".$row_08."' );";
	}
} else { 
	$pagerow08 = "";
}

/* write page file includes */
$pageFileSheet = TEMPLATEPATH."/page.php";
$pageFileHandle = fopen($pageFileSheet, 'w') or die("can't open file");
$pageFileData = "<?php get_header();".$pagerow01.$pagerow02.$pagerow03.$pagerow04.$pagerow05.$pagerow06.$pagerow07.$pagerow08."get_footer(); ?>";
fwrite($pageFileHandle, $pageFileData); 
fclose($pageFileHandle);


global $swp_colsidemasonrywidth;
global $swp_colnosidemasonrywidth;

$themeOptionsSheet = TEMPLATEPATH."/themeoptions.php";
$themeOptionsHandle = fopen($themeOptionsSheet, 'w') or die("can't open file");
$themeOptionsData = "<?php
\$swp_colsidemasonrywidth=".$swp_colsidemasonrywidth.";
\$swp_colnosidemasonrywidth=".$swp_colnosidemasonrywidth.";
\$swp_postmeta_status_single='".${$cat."_postmeta_status_single"}."';
\$swp_postmeta_status_home='".${$cat."_postmeta_status_home"}."';
\$swp_postmeta_status_blog='".${$cat."_postmeta_status_blog"}."';
\$swp_postlinks_status_home='".${$cat."_postlinks_status_home"}."';
\$swp_postlinks_status_blog='".${$cat."_postlinks_status_blog"}."';
\$swp_postlinks_status_single='".${$cat."_postlinks_status_single"}."';
\$swp_postlinks_status_pages='".${$cat."_postlinks_status_pages"}."';
?>";
fwrite($themeOptionsHandle, $themeOptionsData);
fclose($themeOptionsHandle);

if (${$cat."_arrows_status"}=='Show'){
$arrowstatus = 'true';
} else {
$arrowstatus = 'false';
}
if (${$cat."_buttons_status"}=='Show'){
$buttonsstatus = 'true';
} else {
$buttonsstatus = 'false';
}

$sliderOptionsSheet = TEMPLATEPATH."/slideroptions.php";
$sliderOptionsHandle = fopen($sliderOptionsSheet, 'w') or die("can't open file");
$sliderOptionsData = "\$('#showcase').awShowcase({
width:".${$cat.'_bodywidth'}.",
height:".${$cat."_featuredone_height"}.",
interval:".${$cat.'_interval'}.",
arrows:".$arrowstatus.",
buttons:".$buttonsstatus.",
transition_speed:".${$cat.'_transition_speed'}."
});";
fwrite($sliderOptionsHandle, $sliderOptionsData);
fclose($sliderOptionsHandle);


if (${$cat."_headerlogourl"}!='noimage'){
	$logohtml = "<div class='logopos'><img src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_headerlogourl"}."' alt='<?php bloginfo('name'); ?>' /></div>";
}
if (${$cat."_sitetitle_status"}=='Show'){
	$titlehtml = "<div class='titlepos'><?php bloginfo('name'); ?></div>";
} 
if (${$cat."_tagline_status"}=='Show'){
	$taglinehtml = "<div class='taglinepos'><?php bloginfo('description'); ?></div>";
}
if (${$cat."_rssiconurl"}!='noimage'){
	$rssiconhtml = "<a href='<?php bloginfo('rss2_url'); ?>' title='Subscribe Via RSS'><div class='rssiconpos'><img src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_rssiconurl"}."' alt='Subscribe Via RSS' /></div></a>";
}
if (${$cat."_extraiconurl"}!='noimage'){
	$extraiconhtml = "<a href='".${$cat."_extraiconlink"}."'><div class='extraiconpos'><img src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_extraiconurl"}."' /></div></a>";
}

if (${$cat."_afflink"}!=''){
	$brandingcode = "<a href='".${$cat."_afflink"}."' target='_blank' rel='nofollow'><div class='leftcol'></div></a>
<a href='".${$cat."_afflink"}."' target='_blank' rel='nofollow'><div class='rightcol'></div></a>
<a href='".${$cat."_afflink"}."' target='_blank' rel='nofollow'><div class='topad'></div></a>";
} else {
$brandingcode = "<div class='topad'></div>";
}
$brandingElementSheet = TEMPLATEPATH."/branding.php";
$brandingElementHandle = fopen($brandingElementSheet, 'w') or die("can't open file");
$brandingElementData = $brandingcode;
fwrite($brandingElementHandle, $brandingElementData); 
fclose($brandingElementHandle);

$headerElementSheet = TEMPLATEPATH."/headercontent.php";
$headerElementHandle = fopen($headerElementSheet, 'w') or die("can't open file");
$headerElementData = "<a href='<?php bloginfo('home'); ?>'>".$logohtml.$titlehtml.$taglinehtml."</a>".$rssiconhtml.$extraiconhtml;
fwrite($headerElementHandle, $headerElementData); 
fclose($headerElementHandle);

if (${$cat."_iconone"}!='' && ${$cat."_iconone"}!='noimage'){
	$socialone = "<a href='".${$cat."_icononelink"}."' target='_blank' title='".${$cat."_icononemsg"}."'>
<img style='float:left;' src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_iconone"}."' alt='".${$cat."_icononemsg"}."' /></a>";
	if (${$cat."_icontwo"}!='' && ${$cat."_icontwo"}!='noimage'){
	$socialtwo = "<a href='".${$cat."_icontwolink"}."' target='_blank' title='".${$cat."_icontwomsg"}."'>
<img style='float:left;' src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_icontwo"}."' alt='".${$cat."_icontwomsg"}."' /></a>";
	} else {
	$socialtwo = "";
	}
	if (${$cat."_iconthree"}!='' && ${$cat."_iconthree"}!='noimage'){
	$socialthree = "<a href='".${$cat."_iconthreelink"}."' target='_blank' title='".${$cat."_iconthreemsg"}."'>
<img style='float:left;' src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_iconthree"}."' alt='".${$cat."_iconthreemsg"}."' /></a>";
	} else {
	$socialthree = "";
	}
	if (${$cat."_iconfour"}!='' && ${$cat."_iconfour"}!='noimage'){
	$socialfour = "<a href='".${$cat."_iconfourlink"}."' target='_blank' title='".${$cat."_iconfourmsg"}."'>
<img style='float:left;' src='<?php bloginfo('template_directory'); ?>/images/".${$cat."_iconfour"}."' alt='".${$cat."_iconfourmsg"}."' /></a>";
	} else {
	$socialfour = "";
	}
	$socialcode = "<div class='widget'><div class='socialicons'>".$socialone.$socialtwo.$socialthree.$socialfour."<div class='clear'></div></div></div>";
} else {
$socialcode = "";
}
$socialElementSheet = TEMPLATEPATH."/socialicons.php";
$socialElementHandle = fopen($socialElementSheet, 'w') or die("can't open file");
$socialElementData = $socialcode;
fwrite($socialElementHandle, $socialElementData); 
fclose($socialElementHandle);

$edstyleOptionsSheet = TEMPLATEPATH."/editor-style.css";
$edstyleOptionsHandle = fopen($edstyleOptionsSheet, 'w') or die("can't open file");
$edstyleOptionsData = $importgfonts."@import 'customeditor-style.css';
".".mceContentBody {max-width:".$swp_nocolpostfullwidth."px;".$postbgcolorcss.";min-height:350px}
* {font-family:".${$cat."_default_font"}.";font-size:".${$cat."_default_size"}."px;color:#".${$cat."_poststext_color"}.";}
h1, h1 a {font-family:".${$cat."_h1default_font"}.";font-size:".${$cat."_h1default_size"}."px;color:#".${$cat."_h1default_color"}.";font-weight:normal;}
h2, h2 a {font-family:".${$cat."_h2default_font"}.";font-size:".${$cat."_h2default_size"}."px;color:#".${$cat."_h2default_color"}.";font-weight:normal;}
h3, h3 a {font-family:".${$cat."_h3default_font"}.";font-size:".${$cat."_h3default_size"}."px;color:#".${$cat."_h3default_color"}.";font-weight:normal;}
h4, h4 a {font-family:".${$cat."_h4default_font"}.";font-size:".${$cat."_h4default_size"}."px;color:#".${$cat."_h4default_color"}.";font-weight:normal;}
h5, h5 a {font-family:".${$cat."_h5default_font"}.";font-size:".${$cat."_h5default_size"}."px;color:#".${$cat."_h5default_color"}.";font-weight:normal;}
.mceContentBody a:link, .mceContentBody a:visited {color:#".${$cat."_postslink_color"}.";text-decoration:none;}
.mceContentBody a:hover {color:#".${$cat."_postshover_color"}.";text-decoration:none;}
.mceContentBody blockquote {border:1px solid #".${$cat."_blockquoteborder_color"}.";background:#".${$cat."_blockquotebg_color"}.";margin:20px;padding:10px;font-family:".${$cat."_blockquote_font"}."px;font-size:".${$cat."_blockquote_size"}.";color:#".${$cat."_blockquotetext_color"}.";}
.mceContentBody blockquote a:link, .post blockquote a:visited {color:#".${$cat."_blockquotelink_color"}.";} .mceContentBody blockquote a:hover {color:#".${$cat."_blockquotehover_color"}.";}
.clear {clear:both;margin:0;padding:0;height:0;}
em, i, .italicfont {font-style:italic;}
strong, b, .boldfont {font-weight:bold;}";
fwrite($edstyleOptionsHandle, $edstyleOptionsData);
fclose($edstyleOptionsHandle);

}

function setThemeOptions(){
$cat=get_template();

	global $options;
	if (file_exists(TEMPLATEPATH."/options/options_main.php")) {
	$options = include (TEMPLATEPATH."/options/options_main.php");
	}
	
	global $options_header;
	if (file_exists(TEMPLATEPATH."/options/options_header.php")) {
	$options_header = include (TEMPLATEPATH."/options/options_header.php");
	}
	
	global $options_mainmenu;
	if (file_exists(TEMPLATEPATH."/options/options_mainmenu.php")) {
	$options_mainmenu = include (TEMPLATEPATH."/options/options_mainmenu.php");
	}
	
	global $options_featuredone;
	if (file_exists(TEMPLATEPATH."/options/options_featuredone.php")) {
	$options_featuredone = include (TEMPLATEPATH."/options/options_featuredone.php");
	}
	
	global $options_secondmenu;
	if (file_exists(TEMPLATEPATH."/options/options_secondmenu.php")) {
	$options_secondmenu = include (TEMPLATEPATH."/options/options_secondmenu.php");
	}
	
	global $options_featuredtwo;
	if (file_exists(TEMPLATEPATH."/options/options_featuredtwo.php")) {
	$options_featuredtwo = include (TEMPLATEPATH."/options/options_featuredtwo.php");
	}
	
	global $options_contentarea;
	if (file_exists(TEMPLATEPATH."/options/options_contentarea.php")) {
	$options_contentarea = include (TEMPLATEPATH."/options/options_contentarea.php");
	}
	
	global $options_footerone;
	if (file_exists(TEMPLATEPATH."/options/options_footerone.php")) {
	$options_footerone = include (TEMPLATEPATH."/options/options_footerone.php");
	}
	
	global $options_footertwo;
	if (file_exists(TEMPLATEPATH."/options/options_footertwo.php")) {
	$options_footertwo = include (TEMPLATEPATH."/options/options_footertwo.php");
	}
}

function initStuff(){

setThemeOptions();

global $saveoptions, $options, $options_header, $options_mainmenu, $options_secondmenu, $options_featuredone, $options_featuredtwo, $options_contentarea, $options_footerone, $options_footertwo;

$filename = TEMPLATEPATH."/options/options_main.php";
	if (file_exists($filename)) {
		$saveoptions = array_merge($options, $options_header, $options_mainmenu,$options_secondmenu, $options_featuredone, $options_featuredtwo, $options_contentarea, $options_footerone, $options_footertwo);
	}
}


/*block preview functions */

function header_preview(){ 

$cat=get_template();

global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }

	if (${$cat."_headerlogourl"}!='noimage'){?>
	<div class="logopos" style="cursor:move;"><img src="<?php bloginfo('template_directory'); ?>/images/<?php echo ${$cat."_headerlogourl"}; ?>" alt="<?php bloginfo('name'); ?>" /></div>
	<?php } 
	if (${$cat."_sitetitle_status"}=='Show'){?>
    <a href="#"><div class="titlepos" style="padding-right:20px;cursor:move;"><?php bloginfo('name'); ?></div></a>
	<?php } 
	if (${$cat."_tagline_status"}=='Show'){ ?>
    <a href="#"><div class="taglinepos" style="padding-right:20px;cursor:move;"><?php bloginfo('description'); ?></div></a> 
	<?php }
	if (${$cat."_rssiconurl"}!='noimage'){?>
	<div class="rssiconpos" style="cursor:move;"><img src="<?php bloginfo('template_directory'); ?>/images/<?php echo ${$cat."_rssiconurl"}; ?>" alt="Subscribe Via RSS" /></div>
	<?php }
	if (${$cat."_extraiconurl"}!='noimage'){?>
	<div class="extraiconpos" style="cursor:move;"><img src="<?php bloginfo('template_directory'); ?>/images/<?php echo ${$cat."_extraiconurl"}; ?>" /></div>
	<?php }
}

function mainmenu_preview(){ 
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template(); ?>
<ul class="sf-menu <?php echo ${$cat."_mainmenu_font"}; ?>" style="z-index:50;">
  <li><a class="sf-with-ul" style="padding-right:25px;z-index:50;" href="#">Menu Item</a>
    <ul class="sub-menu">
      <li><a href="#">Sub Menu Item</a></li>
      <li><a href="#">Sub Menu Item</a></li>
    </ul>
  </li>
  <li><a class="sf-with-ul" style="padding-right:25px;z-index:50;" href="#">Menu Item</a>
    <ul class="sub-menu">
      <li><a href="#">Sub Menu Item</a>
        <ul class="sub-menu">
          <li><a href="#">Sub Sub Menu Item</a></li>
        </ul>
      </li>
    </ul>
  </li>
  <li><a href="#">Menu Item</a></li>
</ul>
<?php }

function featuredone_preview(){ ?>
    <p>Example Text <a href="#">Example Link</a></p>
<?php }

function secondmenu_preview(){ 
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template(); ?>
<ul class="sf-menu <?php echo ${$cat."_secondmenu_font"}; ?>" style="z-index:40;">
  <li><a class="sf-with-ul" style="padding-right:25px;z-index:50;" href="#">Menu Item</a>
    <ul class="sub-menu">
      <li><a href="#">Sub Menu Item</a></li>
      <li><a href="#">Sub Menu Item</a></li>
    </ul>
  </li>
  <li><a class="sf-with-ul" style="padding-right:25px;z-index:50;" href="#">Menu Item</a>
    <ul class="sub-menu">
      <li><a href="#">Sub Menu Item</a>
        <ul class="sub-menu">
          <li><a href="#">Sub Sub Menu Item</a></li>
        </ul>
      </li>
    </ul>
  </li>
  <li><a href="#">Menu Item</a></li>
</ul>
<?php }

function featuredtwo_preview(){ ?>
    <p>Example Text <a href="#">Example Link</a></p>
<?php }

function posts_preview(){ 
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template(); ?>

    <div class="post colsidepost" id="postresize">
	<h1 class="posttitle"><a href="#">The Post Title</a></h3>
    <div class="postmeta">Posted by The Author on December 21, 2012 in <a href="#">The Category</a> with <a href="#">7 Comments</a></div>
	<p>Example Text <a href="#">Example Link</a></p><p>Example Text <a href="#">Example Link</a></p><p>Example Text <a href="#">Example Link</a></p>
    <div class="postmetatwo"><a href="#">Continue Reading</a></div>
    </div>
    <div class="post colsidepost" id="postresize">
	<h1 class="posttitle"><a href="#">The Post Title</a></h3>
    <div class="postmeta">Posted by The Author on December 21, 2012 in <a href="#">The Category</a> with <a href="#">7 Comments</a></div>
	<h1 style="display:block;">Example Heading 1</h1><h2>Example Heading 2</h2><h3>Example Heading 3</h3><h4>Example Heading 4</h4><h5>Example Heading 5</h5>
    <div class="postmetatwo"><a href="#">Continue Reading</a></div>
    </div>
    <div class="post colsidepost" id="postresize">
	<h1 class="posttitle"><a href="#">The Post Title</a></h3>
    <div class="postmeta">Posted by The Author on December 21, 2012 in <a href="#">The Category</a> with <a href="#">7 Comments</a></div>
	<blockquote>Example Text in a blockquote. <a href="#">Example Link in a blockquote.</a>. Example Text in a blockquote. <a href="#">Example Link in a blockquote.</a>. Example Text in a blockquote. <a href="#">Example Link in a blockquote.</a></blockquote>
    <div class="postmetatwo"><a href="#">Continue Reading</a></div>
    </div>
    <div class="clear"></div>

<?php }

function widget_preview(){ 
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template(); 

if (${$cat."_iconone"}!='' && ${$cat."_iconone"}!='noimage'){
	$socialone = "<a href='".${$cat."_icononelink"}."' target='_blank' title='".${$cat."_icononemsg"}."'>
<img style='float:left;' src='".get_bloginfo('template_directory')."/images/".${$cat."_iconone"}."' alt='".${$cat."_icononemsg"}."' /></a>";
	if (${$cat."_icontwo"}!='' && ${$cat."_icontwo"}!='noimage'){
	$socialtwo = "<a href='".${$cat."_icontwolink"}."' target='_blank' title='".${$cat."_icontwomsg"}."'>
<img style='float:left;' src='".get_bloginfo('template_directory')."/images/".${$cat."_icontwo"}."' alt='".${$cat."_icontwomsg"}."' /></a>";
	} else {
	$socialtwo = "";
	}
	if (${$cat."_iconthree"}!='' && ${$cat."_iconthree"}!='noimage'){
	$socialthree = "<a href='".${$cat."_iconthreelink"}."' target='_blank' title='".${$cat."_iconthreemsg"}."'>
<img style='float:left;' src='".get_bloginfo('template_directory')."/images/".${$cat."_iconthree"}."' alt='".${$cat."_iconthreemsg"}."' /></a>";
	} else {
	$socialthree = "";
	}
	if (${$cat."_iconfour"}!='' && ${$cat."_iconfour"}!='noimage'){
	$socialfour = "<a href='".${$cat."_iconfourlink"}."' target='_blank' title='".${$cat."_iconfourmsg"}."'>
<img style='float:left;' src='".get_bloginfo('template_directory')."/images/".${$cat."_iconfour"}."' alt='".${$cat."_iconfourmsg"}."' /></a>";
	} else {
	$socialfour = "";
	}
	$socialcode = "<div class='widget' style='padding:10px;margin:0 0 20px 0;-moz-border-radius:0;border:none;'><div class='socialicons'>".$socialone.$socialtwo.$socialthree.$socialfour."<div class='clear'></div></div></div>";
} else {
$socialcode = "";
} 
echo $socialcode;?>

	<div class="widget" style="padding:10px;margin:0 0 20px 0;-moz-border-radius:0;border:none;">
    <h4 class="widgetheading" style="margin:0 0 8px 0;padding:0 0 8px 0;font-size:<?php echo ${$cat."_widgetheading_size"}; ?>px;">The Widget Title</h4>
    <ul>
	<li><a href="#">The widget linked item</a></li>
	<li><a href="#">The widget linked item</a></li>
	<li><a href="#">The widget linked item</a></li>
	<li><a href="#">The widget linked item</a></li>
	<li><a href="#">The widget linked item</a></li>
	<li><a href="#">The widget linked item</a></li>
	</ul>
    </div>
<?php }

function footerone_preview(){
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template(); 
	$count = ${$cat."_fonecolnum"};
	$i = 0;
	while ($i < $count) {
	echo "<div class='footwidget footerone' style='float:left;font-size:14px;margin:20px 0 20px 20px;background-image:none;background:transparent;'>
    <h4 style='font-size:18px;padding:0 0 8px 0;color:#".${$cat."_footwidgetheading_color"}.";'>The Foot Widget Title</h4>
    <ul>
	<li><a href='#'>The widget linked item</a></li>
	<li><a href='#'>The widget linked item</a></li>
	<li><a href='#'>The widget linked item</a></li>
	<li><a href='#'>The widget linked item</a></li>
	<li><a href='#'>The widget linked item</a></li>
	<li><a href='#'>The widget linked item</a></li>
	</ul>
    </div>";
	$i++; }
}

function footertwo_preview(){ ?>
    <p>Example Text <a href="#">Example Link</a></p>
<?php }

function custom_style_display () { 
global $saveoptions; foreach ($saveoptions as $value) { $$value['id'] = get_option( $value['id'], $value['std'] ); }
$cat=get_template();

?>

/*Start Custom Style*/
.fullscreen a:link, .fullscreen a:visited, .fullscreen a:hover {text-decoration:none;}

a.tooltip span {display:none; padding:2px 3px; margin-left:8px; width:300px;}
a.tooltip:hover span{display:inline; position:absolute; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;font-size:12px;font-family:Arial;}

h2.pageheader {font:italic 24px/36px Georgia,'Times New Roman','Bitstream Charter',Times,serif;margin:20px 0 10px 0;display:block;color:#121212;}
h3.sectionheader {font: 18px/24px Georgia,'Times New Roman','Bitstream Charter',Times,serif;margin:10px 0;color:#21759B;}

#inlinefontpicker a:link, #inlinefontpicker a:visited {font-size:28px;font-weight:normal;text-decoration:none;color:#333;} #inlinefontpicker a:hover {color:#f00;}
#inlinefontpicker div {display:block;margin-bottom:15px;padding-bottom:15px;border-bottom:1px dotted #ccc;}

code {color:#333;background:url("<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/white-opacity-40.png");}

.handle {margin:0;padding:0;background:#ccc;border:1px solid #666;cursor:move;}

.sortable-list {list-style: none;margin: 0;	min-height: 60px;}

.sortable-item {display:block;}

.placeholder {border:1px dotted #666;background-color:#F7B64B;display:block;height:auto;height:60px;}

hr {color: #F9F9F9;background-color: #F9F9F9;height: 1px;border-bottom:1px dotted #999;border-top:none;border-right:none;border-left:none;}

.dropdowncontent{border:1px solid #ddd;background:#f9f9f9;padding:10px;}
.ui-widget-header{margin-bottom:0;font-family:Arial;}
.clearfix{clear:both;}
.bottomright{position:absolute;bottom:0;right:0;}
.bottomleft{position:absolute;bottom:0;left:0;}
.topright{position:absolute;top:0;right:0;width:auto;}
.topcenter{position:absolute;top:0;left:<?php echo (${$cat."_bodywidth"}/2); ?>px;width:auto;}
.topleft{position:absolute;top:0;left:0;}
.ui-widget-header{font-size:11px;padding:0 5px;}
.ui-corner-all{padding:1px;}
.floatleft{float:left;}
.floatright{float:right;}
.whitebg{background-color:#FFF;padding:2px 5px;border:1px solid #666;}

.console {position:relative;margin:0 20px 20px 0;min-height:20px;}

.openbody {margin:20px 20px 20px 0;border:2px outset #000;position:relative;}
.blockoptions {padding:5px 10px 25px 10px;background:#fff;border:1px solid #fc0;}
.bgoptions {padding:10px 10px 25px 10px;background:#fff;border:1px solid #fc0;}

.topad {height:<?php echo ${$cat."_topad_size"};?>px;width:<?php echo (${$cat."_bodywidth"}-1);?>px;margin:-1px auto 0 auto;border-bottom:1px dashed #999;}

.openwrap {margin:0 auto;width:<?php echo ${$cat."_bodywidth"};?>px;border-right:1px dashed #999;}

.logopos {position:absolute;top:<?php echo ${$cat."_logopos_top"};?>px;left:<?php echo ${$cat."_logopos_left"};?>px;z-index:1;}
.titlepos {position:absolute;top:<?php echo ${$cat."_sitetitlepos_top"};?>px;left:<?php echo ${$cat."_sitetitlepos_left"};?>px;z-index:2;}
.taglinepos {position:absolute;top:<?php echo ${$cat."_taglinepos_top"};?>px;left:<?php echo ${$cat."_taglinepos_left"};?>px;z-index:3;}

.socialicons img {float:left;}

#headerstyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_header_height"}-1);?>px;position:relative;}
#headerstyle a:link, #headerstyle a:hover, #headerstyle a:visited {text-decoration:none;}

.mainmenu .sf-menu a {padding:0 1em;text-decoration:none;}
#mainmenustyle{border-bottom:1px dashed #999;position:relative;min-height:10px;height:<?php echo (${$cat."_mainmenu_height"}-1);?>px;}
#mainmenustyle li {font-size:<?php echo ${$cat."_mainmenutext_size"};?>px;position:relative;height:<?php echo (${$cat."_mainmenu_height"}-1);?>px;}
#mainmenustyle li a {font-family:<?php echo ${$cat."_mainmenu_font"};?>;font-size:<?php echo ${$cat."_mainmenutext_size"};?>px;height:<?php echo (${$cat."_mainmenu_height"}-1);?>px;}
#mainmenustyle li a cufon.cufon {margin-top:<?php echo ((${$cat."_mainmenu_height"}/2)-(${$cat."_mainmenutext_size"}/2)-1);?>px;}

#featuredonestyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_featuredone_height"}-1);?>px;position:relative;}
#featuredonestyle p {font-family:<?php echo ${$cat."_default_font"};?>;font-size:<?php echo ${$cat."_default_size"};?>px;padding:19px 0 0 10px;margin:0;z-index:62;}

.secondmenu .sf-menu a {padding:0 1em;text-decoration:none;}
#secondmenustyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_secondmenu_height"}-1);?>px;position:relative;}
#secondmenustyle li {height:<?php echo (${$cat."_secondmenu_height"}-1);?>px;font-size:<?php echo ${$cat."_secondmenutext_size"};?>px;position:relative;}
#secondmenustyle li a {font-family:<?php echo ${$cat."_secondmenu_font"};?>;font-size:<?php echo ${$cat."_secondmenutext_size"};?>px;height:<?php echo (${$cat."_secondmenu_height"}-1);?>px;}
#secondmenustyle li a cufon.cufon {margin-top:<?php echo ((${$cat."_secondmenu_height"}/2)-(${$cat."_secondmenutext_size"}/2)-1);?>px;}

#featuredtwostyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_featuredtwo_height"}-1);?>px;position:relative;}
#featuredtwostyle p {font-family:<?php echo ${$cat."_default_font"};?>;font-size:<?php echo ${$cat."_default_size"};?>px;padding:19px 0 0 10px;margin:0;z-index:62;}

#footeronestyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_footerone_height"}-1);?>px;position:relative;overflow:hidden;}
#footeronestyle li {font-family:<?php echo ${$cat."_default_font"};?>;font-size:<?php echo ${$cat."_default_size"};?>px;}

#footertwostyle{border-bottom:1px dashed #999;min-height:10px;height:<?php echo (${$cat."_footertwo_height"}-1);?>px;position:relative;}
#footertwostyle p {font-family:<?php echo ${$cat."_default_font"};?>;font-size:<?php echo ${$cat."_default_size"};?>px;padding:19px 0 0 10px;margin:0;z-index:62;}

.contentwrap {width:<?php echo ${$cat."_bodywidth"}; ?>px;padding-bottom:50px;overflow:hidden;font-family:<?php echo ${$cat."_default_font"};?>;}
.contentwrap li, .contentwrap p {font-family:<?php echo $swp_default_font;?>;font-size:<?php echo ${$cat."_default_size"};?>px;}

.bgoptionswrap {width:<?php echo ${$cat."_bodywidth"}; ?>px;margin:0 auto;position:relative;}

#contentwrap-frame > div.contentwrap { padding: 10px !important; }

#sidebar{overflow:hidden;}

#postsarea{overflow:hidden;float:<?php if (${$cat."_sidebar_float"}=="Right"){?>left<?php } else {?>right<?php }?>;}

.postmeta {margin-bottom:10px;padding:4px 10px;}
.postmeta a:link, .postmeta a:visited {text-decoration:none;}
.postmeta a:hover {text-decoration:none;}
.postmetatwo {padding:5px 0;}
.post blockquote {margin:20px;padding:10px;}

h3.posttitle {margin:0 0 10px 0;}

.block {position:relative;}

h3.toptrigger {margin:0;padding:0;font-size:11px;font-weight:normal;width:auto;height:15px;position:absolute;top:0;right:0;z-index:9998;font-family:Arial;}

h3.optionstrigger {margin:0;padding:0;font-size:11px;font-weight:normal;width:auto;height:15px;position:absolute;bottom:0;right:0;z-index:999;font-family:Arial;}

h3.ui-widget-header {text-align:right;}
h1, h2, h3, h4, h5 {font-weight:normal;}

.post {margin:0 0 10px 10px;padding:10px;float:left;}

.post h3 {line-height:1;}

.auto {height:auto;width:auto;position:relative;}

.topbtnwrap {width:750px;position:relative;}

.defaultswrap {position:absolute;top:0;right:0;}

/* Menu styles */
.sf-menu, .sf-menu * {margin:0;padding:0;list-style:none;}
.sf-menu {}
.sf-menu ul {position:absolute;top:-999em;width:10em;}
.sf-menu ul li {width:100%;}
.sf-menu li:hover {visibility:inherit;}
.sf-menu li {float:left;position:relative;}
.sf-menu a {display:block;position:relative;}
.sf-menu li:hover ul, .sf-menu li.sfHover ul {left:0;z-index:99;}
ul.sf-menu li:hover li ul, ul.sf-menu li.sfHover li ul {top:-999em;}
ul.sf-menu li li:hover ul, ul.sf-menu li li.sfHover ul {left:10em;top:0;}
ul.sf-menu li li:hover li ul, ul.sf-menu li li.sfHover li ul {top:-999em;}
ul.sf-menu li li li:hover ul, ul.sf-menu li li li.sfHover ul {left:10em;top:0;}
.sf-menu a.sf-with-ul {padding-right:2.25em;min-width:1px;}
.sf-sub-indicator {position:absolute;display:block;right:.75em;width:10px;height:10px;text-indent:-999em;overflow:hidden;background:url(<?php bloginfo('template_directory'); ?>/images/arrows-ffffff.png) no-repeat -10px -100px;}
a > .sf-sub-indicator {background-position: 0 -100px;}
a:focus > .sf-sub-indicator, a:hover > .sf-sub-indicator, a:active > .sf-sub-indicator, li:hover > a > .sf-sub-indicator, li.sfHover > a > .sf-sub-indicator { 	background-position: -10px -100px;}
.sf-menu ul .sf-sub-indicator {background-position:-10px 0;}
.sf-menu ul a > .sf-sub-indicator {background-position:0 0;}
.sf-menu ul a:focus > .sf-sub-indicator, .sf-menu ul a:hover > .sf-sub-indicator, .sf-menu ul a:active > .sf-sub-indicator, .sf-menu ul li:hover > a > .sf-sub-indicator, .sf-menu ul li.sfHover > a > .sf-sub-indicator {background-position: -10px 0;}
.sf-shadow ul {background:url(<?php bloginfo('template_directory'); ?>/images/shadow.png) no-repeat bottom right;padding: 0 8px 9px 0;-moz-border-radius-bottomleft: 17px;-moz-border-radius-topright: 17px;-webkit-border-top-right-radius: 17px;	-webkit-border-bottom-left-radius: 17px;}
.sf-shadow ul.sf-shadow-off {background: transparent;}
/*End Custom Style*/
<?php }

function webweave_admin() {
    
/* check for compatible theme */

	$filename = TEMPLATEPATH."/options/options_main.php";
	if (file_exists($filename)) {

/* start admin page if theme compatible */

	$cat=get_template();
	
	$theme_name = get_current_theme();

    global $uploadedfile, $imageprefix, $customStyleData, $options, $options_header, $options_mainmenu, $options_secondmenu, $options_featuredone, $options_featuredtwo, $options_contentarea, $options_footerone, $options_footertwo;

    if ( isset($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>Current settings saved. IMPORTANT: To commit saved settings to '.$theme_name.' click Update Theme With Current Settings.</strong></p></div>';
    if ( isset($_REQUEST['updated']) ) echo '<div id="message" class="updated fade"><p><strong>'.$theme_name.' theme updated with current setting.</strong></p></div>';
    if ( isset($_REQUEST['gendef']) ) echo '<div id="message" class="updated fade"><p><strong>'.$theme_name.' theme defaults generated.</strong></p></div>';
    if ( isset($_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>'.$theme_name.' theme settings reset to default.</strong></p></div>';
    if ( isset($_REQUEST['upfile']) ) echo '<div id="message" class="updated fade"><p><strong>Image file "'.$_REQUEST['filename'].'" uploaded.</strong></p></div>';

?>
	
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/jquery-ui-1.8.7.custom.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/css/ui-lightness/jquery-ui.custom.css" media="screen" />
    
    <script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/swp.js"></script>

    <script type="text/javascript">jQuery(function(){jQuery('ul.sf-menu').supersubs({minWidth:12,extraWidth:1}).superfish();});</script>

	<script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/colorpicker.js"></script>
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/eye.js"></script>
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/utils.js"></script>
    <script type="text/javascript" src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/js/layout.js?ver=1.0.2"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/css/gfonts.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/css/colorpicker.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/css/layout.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/css/colorbox.css" media="screen" />

	<style type="text/css" media="screen">
    <?php $previmageprefix = get_bloginfo('template_directory')."/"; getStylesheetData($previmageprefix); echo $customStyleData; ?>
    <?php custom_style_display (); ?>
	</style>

	<script type="text/javascript">
	$(document).load(function() {
	  jQuery('.openbody').hide();
	});
    </script>
	
<h2 class="pageheader"><div id="icon-themes" class="icon32" style="margin:0;"></div>The Website Weaver - <a href="http://www.spiderwebpress.com" target="_blank">Powered by SpiderWeb Press</a></h2>

<p style="margin-bottom:25px;"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/swp_help.png" style="vertical-align:middle;margin-right:8px;" />Need help? Visit the SpiderWebPress Help site for <a href="http://help.spiderwebpress.com/" target="_blank">tutorials</a> or <a href="http://forum.spiderwebpress.com/become-a-member/" target="_blank">subscribe as a member</a> and ask for <a href="http://forum.spiderwebpress.com/forum/" target="_blank">help at the forums.</a>
<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Want to give help? <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ULU6HQ72WDRGJ" target="_blank">Make a contribution</a> to our Free Plugin Development Fund and help support The Website Weaver.</p>

<div class="topwrap">
<div style="float:right;margin-right:20px;text-align:right;"><strong><a href="http://store.spiderwebpress.com/" target="_blank">Visit the SpiderWebPress Store</a></strong><br />Themes, Graphics and Tools for Web Masters.</div>

<div class="topbtnwrap">

<div class="defaultswrap">
    <div class="submit" style="padding:0;float:left;width:216px;margin:0 20px 0 0;">
    <form method="post" name="swpoptions">
    <?php wp_nonce_field('weaver_gendef','weaver_nonce_gendef'); ?>
    <input name="gendef" type="submit" value="Generate New Theme Defaults" onClick='return confirm("SAVE FIRST!! Are you sure your want to CHANGE the theme default options? This cannot be undone!")'/>
    <input type="hidden" name="action" value="gendef" />
    <a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>SAVE FIRST - This will create NEW "reset" default options for the current theme - Warning: Cannot be undone.</span></a>
    </form></div>
    
    <div style="padding:0;float:left;width:238px;">
    <form method="post">
    <?php wp_nonce_field('weaver_reset','weaver_nonce_reset'); ?>
    <input name="reset" type="submit" value="Reset to Theme Defaults" onClick='return confirm("Are you sure your want to reset to default options? This cannot be undone!")'/>
    <input type="hidden" name="action" value="reset" />
    <a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>Reset options back to default - Warning: Cannot be undone.</span></a>
    </form></div>
</div>

<div>
<form method="post">
<?php wp_nonce_field('weaver_update','weaver_nonce_update'); ?>
<div class="submit" style="padding:0;">
<input name="update" type="submit" value="Update Theme With Current Settings" onClick='return confirm("SAVE FIRST!! Are you sure your want to UPDATE the theme with the current options? This cannot be undone!")'/>
<input type="hidden" name="action" value="update" />
<a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>SAVE FIRST - This will update the appearance of the current theme - Warning: Cannot be undone.</span></a></div>
</form>
</div>

<div class="submit" style="padding:15px 0 5px 0;">  
<form method="post">
<?php wp_nonce_field('weaver_save','weaver_nonce_save'); ?>
<input name="save" type="submit" value="Save current settings" />    
<input type="hidden" name="action" value="save" />
</div>

</div>

</div>

<?php foreach ($options as $value) { 
    
	switch ( $value['type'] ) {
		
		case "openbgoptions": ?>
                  
<div class="openbody fullscreen">
	<div class="bodywrap">
        
        <div class="bgoptionswrap">          
        <h3 class="toptrigger topright ui-widget-header">Site Background &amp; General Options<span class="floatright ui-icon ui-icon-circle-triangle-s"></span></h3>
        <div class="bgoptions">
              
                            
        <?php break;
							
		case "closebgoptions": ?>
                  
        </div><!--bgoptions-->
        </div><!--bgoptionswrap-->
                    
      	<?php break;
	
		case "widthandtopspace": ?>
        
                <code class="topleft">Top spacing height: <span id="topadheightwrap"><?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?></span><input id="topadheight" name="<?php echo $value['id']; ?>" type="hidden" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></code>
                

            <div class="topad">
            </div>
                        
        <?php break;
	
		case "openwrap": ?>
            
            <code class="topright">Site width: <span id="sitewidthwrap"><?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?></span></code><input name="<?php echo $value['id']; ?>" type="hidden" id="sitewidth" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
            <div class="openwrap">

                            
                            <?php break;
                    
							case "opendropdown": ?>
                            
                            <div class="dropdownsection">
                            	<h3 class="ui-widget-header"><?php if (isset($value['tooltip']) && $value['tooltip'] != ''){ ?><a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span><?php echo $value['tooltip']; ?></span></a>&nbsp;<?php } ?><?php echo $value['name']; ?><span id="plus" class="floatright ui-icon ui-icon-circle-triangle-w"></span></h3>
                            
                            	<div class="dropdowncontent">
                                
                                <div class="submit" style="padding:10px 0;">
                                <input name="save" type="submit" value="Save current settings" />    
								<input type="hidden" name="action" value="save" />
                                </div>
                            
                            <?php break;
							
							case "closedropdown": ?>

                            	</div>
                            
                            </div>

                            
        <?php break;
							
		case "upload": ?>
                            
        <p><?php echo $value['name']; ?></p>
         <div><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />  
         <input type="button" value="Upload" onclick="$.colorbox({width:'50%', inline:true, href:'#uploaderwindow',onOpen:function(){ document.getElementById('setoptionname').value='<?php echo $value['id']; ?>' }});"> IMPORTANT: Save other settings before uploading files!</div>
          <small>Max 500kb</small>
         <p><input type="button" value="Remove Image" onclick="document.getElementById('<?php echo $value['id']; ?>').value='noimage'" /></p>

                        
          <?php break;
							
		   case "gfont": ?>
           
           
							
                            <div style="display:block;margin-bottom:15px;padding-bottom:15px;width:600px;text-align:right;border-bottom:1px dotted #ccc;">
							<span style="font-size:28px;font-family:'<?php echo $value['name']; ?>';margin-right:10px;padding:0;"><?php echo $value['name']; ?>:</span>
                            <?php $radiocount = 1; ?>
                                <?php foreach ($value['options'] as $option) { ?>
                                <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?><?php echo $radiocount; ?>" type="radio" <?php if ( get_option( $value['id'] ) != "" && get_option( $value['id'] ) == $option) { echo ' checked'; } else if (get_option( $value['id'] ) == "" && $option == $value['std']) { echo ' checked'; } ?> value="<?php echo $option; ?>" />
                                <label style="margin-right:10px;" for="<?php echo $value['id']; ?><?php echo $radiocount; ?>"><?php echo $option; ?></label>
                            <?php $radiocount ++; } ?>
                            </div>

         
         
          <?php break;
							
			case "font": ?>
                            
             <p><strong><?php echo $value['name']; ?>:</strong></p>
             <div>
                           
             <input name="<?php echo $value['id']; ?>" id="<?php echo $value['name']; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />
             <input type="button" value="Choose a Font" onclick="$.colorbox({ width:'50%', inline:true, href:'#inlinefontpicker',onOpen:function(){ fontoptionname='<?php echo $value['name']; ?>' } });"></div>

                        
        <?php break;
							
		case "hr": ?>
                            
        <hr />
                        
        <?php break;
							
		case "bgcolor": ?>

        
        <SCRIPT LANGUAGE="JavaScript">
        jQuery(document).ready(function() {  
        jQuery('#<?php echo $value['id'];?>').ColorPicker({
			  onSubmit: function(hsb, hex, rgb) {
				jQuery('#<?php echo $value['id'];?>').val(hex);
				jQuery('#<?php echo $value['id'];?>').ColorPickerHide();
			  },
              onBeforeShow: function () {
                jQuery(this).ColorPickerSetColor(this.value);
              },
              onChange: function (hsb, hex, rgb) {
                jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
              }
           })	
           .bind('keyup', function(){
              jQuery(this).ColorPickerSetColor(this.value);
           });
       });
       </script>
       <p><strong><?php echo $value['name']; ?>:</strong></p>
       <div id="col<?php echo $value['id']; ?>">
       <div class="colorpickwrap" style="background-color:#<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>;"><input style="width:54px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></div>
       </div>
       <p style="margin-bottom:30px;"><input type="button" value="No BG Color" onclick="document.getElementById('<?php echo $value['id']; ?>').value='none';" /></p>
                        
        <?php break;
							
		case "color": ?>

        <SCRIPT LANGUAGE="JavaScript">
			jQuery(document).ready(function() {  
			jQuery('#<?php echo $value['id'];?>').ColorPicker({
			onSubmit: function(hsb, hex, rgb) {
				jQuery('#<?php echo $value['id'];?>').val(hex);
				jQuery('#<?php echo $value['id'];?>').ColorPickerHide();
			},
			onBeforeShow: function () {
				jQuery(this).ColorPickerSetColor(this.value);
			},
			onChange: function (hsb, hex, rgb) {
				jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
			}
		})	
			.bind('keyup', function(){
			jQuery(this).ColorPickerSetColor(this.value);
			});
		});
		</script>
                <div><?php echo $value['name']; ?></div>
                <div id="col<?php echo $value['id']; ?>">
                    <div class="colorpickwrap" style="background-color:#<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>;"><input style="width:54px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></div>
                </div>
                        
          <?php break;
							
		   case "status": ?>
                           
           <div style="display:block;margin-bottom:15px;padding-bottom:15px;border-bottom:1px dotted #ccc;">
           <p style="margin-right:10px;padding:0;font-weight:bold;"><?php echo $value['name']; ?>:</p>
           <?php $radiocount = 1; ?>
           <?php foreach ($value['options'] as $option) { ?>
           <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?><?php echo $radiocount; ?>" type="radio" <?php if ( get_option( $value['id'] ) != "" && get_option( $value['id'] ) == $option) { echo ' checked'; } else if (get_option( $value['id'] ) == "" && $option == $value['std']) { echo ' checked'; } ?> value="<?php echo $option; ?>" />
           <label style="margin-right:10px;" for="<?php echo $value['id']; ?><?php echo $radiocount; ?>"><?php echo $option; ?></label>
           <?php $radiocount ++; } ?>
           </div>
				
							<?php break;
                    
							case "blockcolor": ?>
							
							 <SCRIPT LANGUAGE="JavaScript">
							jQuery(document).ready(function() {  
							jQuery('#<?php echo $value['id'];?>').ColorPicker({
							onSubmit: function(hsb, hex, rgb) {
								jQuery('#<?php echo $value['id'];?>').val(hex);
								jQuery('#<?php echo $value['id'];?>').ColorPickerHide();
								jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
							},
							onBeforeShow: function () {
								jQuery(this).ColorPickerSetColor(this.value);
							},
							onChange: function (hsb, hex, rgb) {
								jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
							}
						})	
							.bind('keyup', function(){
							jQuery(this).ColorPickerSetColor(this.value);
							});
						});
						</script>
								<p><strong><?php echo $value['name']; ?>:</strong></p>
								<div id="col<?php echo $value['id']; ?>">
									<div class="colorpickwrap" style="background-color:#<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>;"><input style="width:54px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></div>
								</div>
                        
         <?php break;
							
		case "height": ?>
        <?php if ( get_option( $value['id'] ) != "") { $thisvalue = get_option( $value['id'] ); } else { $thisvalue = $value['std']; } ?>
        <h3 class="ui-widget-header"><?php echo $displayname; ?> Preview:</h3>
        <div style="position:relative;" class="previewwrap" id="<?php echo $blockstyle; ?>">
        <?php $previewfunction(); ?>
        <div class="bottomleft ui-state-default ui-corner-all"><div class="ui-icon ui-icon-arrowthick-2-n-s"></div></div>
        <code class="bottomright" style="margin:0 20px 0 0;"><?php echo $displayname; ?> height: <span id="<?php echo $currentvalue; ?>heightwrap"><?php echo $thisvalue; ?></span></code>
        <div class="bottomright ui-state-default ui-corner-all"><div class="ui-icon ui-icon-arrowthick-2-n-s"></div></div>
        </div>
        <p>Drag bottom edge of preview to resize</p>
		<input name="<?php echo $value['id']; ?>" id="<?php echo $currentvalue; ?>height" type="hidden" value="<?php echo $thisvalue; ?>" />
                        
        <?php break;
							
		case "upload": ?>
                            
        <p><?php echo $value['name']; ?></p>
         <div><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />  
         <input type="button" value="Upload" onclick="$.colorbox({width:'50%', inline:true, href:'#uploaderwindow',onOpen:function(){ document.getElementById('setoptionname').value='<?php echo $value['id']; ?>' }});"> IMPORTANT: Save other settings before uploading files!</div>
        <small>Max 500kb</small>
        <p><input type="button" value="Remove Image" onclick="document.getElementById('<?php echo $value['id']; ?>').value='noimage'" /></p>
		
		<?php break;
							
		case "text": ?>
                       <div><?php echo $value['name']; ?></div>
                       <input name="<?php echo $value['id']; ?>" style="margin-bottom:15px;" size="40" id="<?php echo $value['id']; ?>" type="textfield" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
		
		<?php break;
	
		case "opensortable": ?>
				<ul class="sortable-list">
		
		<?php break;
		
		case "sortable": ?>
        
        <?php if ( get_option( $value['id'] ) != "") { 
		$currentvalue = get_option( $value['id'] ); 
		} else { 
		$currentvalue = $value['std']; 
		} 
		switch ($currentvalue) {
			case "header":
				$options_sortable = $options_header;
				$displayname = "Header";
				break;
			case "mainmenu":
				$options_sortable = $options_mainmenu;
				$displayname = "Main Menu";
				break;
			case "featuredone":
				$options_sortable = $options_featuredone;
				$displayname = "Spotlights";
				break;
			case "secondmenu":
				$options_sortable = $options_secondmenu;
				$displayname = "Second Menu";
				break;
			case "featuredtwo":
				$options_sortable = $options_featuredtwo;
				$displayname = "Featured Strip";
				break;
			case "contentarea":
				$options_sortable = $options_contentarea;
				$displayname = "Content Area";
				break;
			case "footerone":
				$options_sortable = $options_footerone;
				$displayname = "Footer One";
				break;
			case "footertwo":
				$options_sortable = $options_footertwo;
				$displayname = "Footer Two";
				break;
		} 
		$blockstyle = $currentvalue.'style';
		$previewfunction = $currentvalue.'_preview';
		?>
        <input style="width:200px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="hidden" value="<?php echo $currentvalue; ?>" />              
					<span class="sortable-item only" id="<?php echo $currentvalue; ?>">
                    
                    <!--start section and title dropdown-->
                    <div class="">  
                        	
                            <!--start options dropdown-->
                            <div class="block">
                        
						<?php foreach ($options_sortable as $value) {
    
							switch ( $value['type'] ) {
							
							case "openoptions": ?>
                            
                            <h3 class="optionstrigger bottomright ui-widget-header"><?php echo $displayname; ?> Settings<span class="floatright ui-icon ui-icon-circle-triangle-s"></span></h3>
                            <div class="blockoptions">
                            
                            <?php break;
							
							case "closeoptions": ?>
                            
                            </div>
                            
                            
                            <?php break;
                    
							case "opendropdown": ?>
                            
                            <div class="dropdownsection">
                            	<h3 class="ui-widget-header"><?php if (isset($value['tooltip']) && $value['tooltip'] != ''){ ?><a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span><?php echo $value['tooltip']; ?></span></a>&nbsp;<?php } ?><?php echo $value['name']; ?><span id="plus" class="floatright ui-icon ui-icon-circle-triangle-w"></span></h3>
                            
                            	<div class="dropdowncontent">
                                
                                <div class="submit" style="padding:10px 0;">
                                <input name="save" type="submit" value="Save current settings" />    
								<input type="hidden" name="action" value="save" />
                                </div>
                            
                            <?php break;
							
							case "closedropdown": ?>

                            	</div>
                            
                            </div>
                            
                            <?php break;
							
							case "font": ?>
                            
                            <p><strong><?php echo $value['name']; ?>:</strong></p>
                            <div>
                           
                            <input name="<?php echo $value['id']; ?>" id="<?php echo $value['name']; ?>" type="text" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />
                            <input type="button" value="Choose a Font" onclick="$.colorbox({ width:'50%', inline:true, href:'#inlinefontpicker',onOpen:function(){ fontoptionname='<?php echo $value['name']; ?>' } });"></div>
                            	
                        
                        	<?php break;
							
							case "radio": ?>
							
                            <div style="display:block;">
							<p><strong><?php echo $value['name']; ?>:</strong></p>
                                <div style="float:left;">
								<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
							<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
							&nbsp;<label for="<?php echo $value['id']; ?>"><?php echo $option; ?></label></div>
                            <div class="clear"></div>
                            </div>
                        
                        	<?php break;
							
							case "header": ?>
							
                            <div style="display:block;">
							<h3 class="sectionheader"><?php echo $value['name']; ?>: <?php if (isset($value['tooltip']) && $value['tooltip'] != ''){ ?><a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span><?php echo $value['tooltip']; ?></span></a><?php } ?></h3>
                            </div>
                        
                        	<?php break;
							
							case "hr": ?>
                            
                            <hr />
                        
                        	<?php break;
							
							case "status": ?>
                           
                            <div style="display:block;margin-bottom:15px;padding-bottom:15px;border-bottom:1px dotted #ccc;">
                            <p style="margin-right:10px;padding:0;font-weight:bold;"><?php echo $value['name']; ?>:</p>
                            <?php $radiocount = 1; ?>
                            <?php foreach ($value['options'] as $option) { ?>
                            <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?><?php echo $radiocount; ?>" type="radio" <?php if ( get_option( $value['id'] ) != "" && get_option( $value['id'] ) == $option) { echo ' checked'; } else if (get_option( $value['id'] ) == "" && $option == $value['std']) { echo ' checked'; } ?> value="<?php echo $option; ?>" />
                            <label style="margin-right:10px;" for="<?php echo $value['id']; ?><?php echo $radiocount; ?>"><?php echo $option; ?></label>
                            <?php $radiocount ++; } ?>
                            </div>
                        
                        	<?php break;
							
							case "upload": ?>
                            
                            <p><strong><?php echo $value['name']; ?>:</strong></p>
                            <div><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />  
                            <input type="button" value="Upload" onclick="$.colorbox({width:'50%', inline:true, href:'#uploaderwindow',onOpen:function(){ document.getElementById('setoptionname').value='<?php echo $value['id']; ?>' }});"> IMPORTANT: Save other settings before uploading files!</div>
                            <small>Max 500kb</small>
                            <p style="margin-bottom:30px;"><input type="button" value="Remove Image" onclick="document.getElementById('<?php echo $value['id']; ?>').value='noimage'" /></p>
                        
                        	<?php break;
							
							case "height": ?>
                        	<?php if ( get_option( $value['id'] ) != "") { $thisvalue = get_option( $value['id'] ); } else { $thisvalue = $value['std']; } ?>
                            
                            <div style="position:relative;" class="previewwrap <?php echo $currentvalue ?>" id="<?php echo $blockstyle; ?>">
                            <h3 class="handle topright" style="z-index:69;"><div class="floatright ui-icon ui-icon-arrow-4"></div></h3>
                            <?php $previewfunction(); ?>
                            <div style="width:100%;position:absolute;top:0;left:0;z-index:5;height:0;text-align:center;"><code><?php echo $displayname; ?> height: <span id="<?php echo $currentvalue; ?>heightwrap"><?php echo $thisvalue; ?></span></code></div>
							<input name="<?php echo $value['id']; ?>" id="<?php echo $currentvalue; ?>height" type="hidden" value="<?php echo $thisvalue; ?>" />
                            </div>
                        	<?php break;
							
							case "colposition": ?>
                            <div class="contentwrap content">
                            <div id="slider"></div>
                            <div id="sidebar" class="sidebar">
                            	<div style="text-align:center;">Sidebar<p><code>Width: <span id="sidebarwidth"></span></code></p></div>
                            	<?php widget_preview(); ?>
                            </div>
                            <div class="postarea colside" id="postsarea">
                            	<div style="text-align:center;">Posts area<p><code>Width: <span id="postareawidth"></span></code></p></div>
                                <div class="postswrap">
								<?php posts_preview(); ?>
                                </div>
                            </div>
                            <script language="javascript" type="text/javascript">
							$(window).load(function(){   $('.postswrap').masonry({
								itemSelector: '.post' 
							}); });
							</script>
                            </div>
                            <input name="<?php echo $value['id']; ?>" id="updatepostwidth" type="hidden" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
						 
							<?php break;
							
							case "hidden": ?>
                            <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="hidden" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />							
							
                        	
							<?php break;
							
							case "radio": ?>
                            
                            <p><strong><?php echo $value['name']; ?>:</strong></p>
                            <p><?php foreach ($value['options'] as $option) { ?>
                            <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="radio" <?php if ( get_option( $value['id'] ) == $option) { echo ' checked'; } elseif ($option == $value['std']) { echo ' checked'; } ?> value="<?php echo $option; ?>" />&nbsp;<?php echo $option; ?><?php } ?></p>
						 
							<?php break;
							
							case "text": ?>
                            <p><strong><?php echo $value['name']; ?>:</strong></p>
                            <input name="<?php echo $value['id']; ?>" style="margin-bottom:15px;" size="40" id="<?php echo $value['id']; ?>" type="textfield" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" />
                        
							<?php break;
                                                
                            case "bgcolor": ?>
                    
                            <SCRIPT LANGUAGE="JavaScript">
                                jQuery(document).ready(function() {  
                                jQuery('#<?php echo $value['id'];?>').ColorPicker({
                                onSubmit: function(hsb, hex, rgb) {
                                    jQuery('#<?php echo $value['id'];?>').val(hex);
                                    jQuery('#<?php echo $value['id'];?>').ColorPickerHide();
                                },
                                onBeforeShow: function () {
                                    jQuery(this).ColorPickerSetColor(this.value);
                                },
                                onChange: function (hsb, hex, rgb) {
                                    jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
                                }
                            })	
                                .bind('keyup', function(){
                                jQuery(this).ColorPickerSetColor(this.value);
                                });
                            });
                            </script>
                                    <p><strong><?php echo $value['name']; ?>:</strong></p>
                                    <div id="col<?php echo $value['id']; ?>">
                                        <div class="colorpickwrap" style="background-color:#<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>;"><input style="width:54px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></div>
                                    </div>
                                    <p style="margin-bottom:30px;"><input type="button" value="No BG Color" onclick="document.getElementById('<?php echo $value['id']; ?>').value='none';" /></p>
				
							<?php break;
                    
							case "blockcolor": ?>
							
							 <SCRIPT LANGUAGE="JavaScript">
							jQuery(document).ready(function() {  
							jQuery('#<?php echo $value['id'];?>').ColorPicker({
							onSubmit: function(hsb, hex, rgb) {
								jQuery('#<?php echo $value['id'];?>').val(hex);
								jQuery('#<?php echo $value['id'];?>').ColorPickerHide();
								jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
							},
							onBeforeShow: function () {
								jQuery(this).ColorPickerSetColor(this.value);
							},
							onChange: function (hsb, hex, rgb) {
								jQuery('#col<?php echo $value['id']; ?> div').css('backgroundColor', '#' + hex);
							}
						})	
							.bind('keyup', function(){
							jQuery(this).ColorPickerSetColor(this.value);
							});
						});
						</script>
								<p><strong><?php echo $value['name']; ?>:</strong></p>
								<div id="col<?php echo $value['id']; ?>">
									<div class="colorpickwrap" style="background-color:#<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>;"><input style="width:54px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></div>
								</div>
						 
							<?php break;
							
							} 
						} ?>
                        </div>
                        <!--end options dropdown-->
                    </div>
                    <!--end section and title dropdown-->
                </span>
                        
                    
		<?php break;
		
		case "closesortable": ?>	
                </ul>
		<?php break;
		
		case "closewrap": ?>
            </div>

        <?php break;
		
		case "closebody": ?>
            
            </div><!--bodywrap-->
		</div> <!--openbody fullscreen-->
        
        <?php break;
 
	} 
} ?>



<div class="submit" style="padding:0;margin-bottom:25px;">
<input name="save" type="submit" value="Save current settings" />    
<input type="hidden" name="action" value="save" />
</form>
</div>

<div class="submit" style="padding:0;float:left;width:251px;margin:0 20px 0 0;">
<form method="post">
<?php wp_nonce_field('weaver_update_bot','weaver_nonce_update_bot'); ?>
<input name="update" type="submit" value="Update Theme With Current Settings" onClick='return confirm("SAVE FIRST!! Are you sure your want to UPDATE the theme with the current options? This cannot be undone!")'/>
<input type="hidden" name="action" value="update" />
<a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>SAVE FIRST - This will update the appearance of the current theme - Warning: Cannot be undone.</span></a>
</form></div>

<div class="submit" style="padding:0;float:left;width:216px;margin:0 20px 0 0;">
<form method="post" name="swpoptions">
<?php wp_nonce_field('weaver_gendef_bot','weaver_nonce_gendef_bot'); ?>
<input name="gendef" type="submit" value="Generate New Theme Defaults" onClick='return confirm("SAVE FIRST!! Are you sure your want to CHANGE the theme default options? This cannot be undone!")'/>
<input type="hidden" name="action" value="gendef" />
<a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>SAVE FIRST - This will create NEW "reset" default options for the current theme - Warning: Cannot be undone.</span></a>
</form></div>

<div style="padding:0;float:left;width:238px;">
<form method="post">
<?php wp_nonce_field('weaver_reset_bot','weaver_nonce_reset_bot'); ?>
<input name="reset" type="submit" value="Reset to Theme Defaults" onClick='return confirm("Are you sure your want to reset to default options? This cannot be undone!")'/>
<input type="hidden" name="action" value="reset" />
<a class="tooltip" href="#"><img src="<?php echo WP_PLUGIN_URL; ?>/the-website-weaver/images/system_question.png" /><span>Reset options back to default - Warning: Cannot be undone.</span></a>
</form></div>

<div class="clear" style="height:50px;">&nbsp;</div>


<div style='display:none'>
<div id='uploaderwindow' style='padding:10px; background:#fff;'>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
<input type="hidden" name="optionname" id="setoptionname" value="" />
<input type="hidden" name="action" value="upfile" />
<h2 class="pageheader" style="display:block;margin-top:0;">Image File Uploader</h2>
Choose a file to upload: <input name="uploadedfile" type="file" /><br />
<input name="upfile" type="submit" value="Upload File" />
</form>
</div>
</div>

<div style='display:none'>
<div id='inlinefontpicker' style='padding:10px 50px; background:#fff;'>
<script type="text/javascript">
var fontoptionname;
</script>
<h2 class="pageheader" style="display:block;margin-bottom:15px;"><span id="icon-themes" class="icon32" style="margin:0;"></span>Click a font to choose it</h2>

<div style="font-family:'Arial';"><a href="#" onclick="document.getElementById(fontoptionname).value='Arial';$.colorbox.close();">Arial</a></div>
<div style="font-family:'Verdana';"><a href="#" onclick="document.getElementById(fontoptionname).value='Verdana';$.colorbox.close();">Verdana</a></div>
<div style="font-family:'Georgia';"><a href="#" onclick="document.getElementById(fontoptionname).value='Georgia';$.colorbox.close();">Georgia</a></div>
<div style="font-family:'Tahoma';"><a href="#" onclick="document.getElementById(fontoptionname).value='Tahoma';$.colorbox.close();">Tahoma</a></div>
<div style="font-family:'Times New Roman';"><a href="#" onclick="document.getElementById(fontoptionname).value='Times New Roman';$.colorbox.close();">Times New Roman</a></div>
<div style="font-family:'Courier New';"><a href="#" onclick="document.getElementById(fontoptionname).value='Courier New';$.colorbox.close();">Courier New</a></div>
<div style="font-family:'Trebuchet MS';"><a href="#" onclick="document.getElementById(fontoptionname).value='Trebuchet MS';$.colorbox.close();">Trebuchet MS</a></div>
<div style="font-family:'Impact';"><a href="#" onclick="document.getElementById(fontoptionname).value='Impact';$.colorbox.close();">Impact</a></div>
<div style="font-family:'Lucida Sans Unicode';"><a href="#" onclick="document.getElementById(fontoptionname).value='Lucida Sans Unicode';$.colorbox.close();">Lucida Sans Unicode</a></div>
<div style="font-family:'Palatino Linotype';"><a href="#" onclick="document.getElementById(fontoptionname).value='Palatino Linotype';$.colorbox.close();">Palatino Linotype</a></div>
<div style="font-family:'Droid Sans'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_droidsans]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Droid Sans';$.colorbox.close();">Droid Sans</a></div>
<div style="font-family:'Lobster'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_lobster]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Lobster';$.colorbox.close();">Lobster</a></div>
<div style="font-family:'PT Sans'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_ptsans]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='PT Sans';$.colorbox.close();">PT Sans</a></div>
<div style="font-family:'Droid Serif'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_droidserif]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Droid Serif';$.colorbox.close();">Droid Serif</a></div>
<div style="font-family:'Ubuntu'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_ubuntu]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Ubuntu';$.colorbox.close();">Ubuntu</a></div>
<div style="font-family:'Yanone Kaffeesatz'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_yanonekaffeesatz]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Yanone Kaffeesatz';$.colorbox.close();">Yanone Kaffeesatz</a></div>
<div style="font-family:'Reenie Beanie'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_reeniebeanie]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Reenie Beanie';$.colorbox.close();">Reenie Beanie</a></div>
<div style="font-family:'Molengo'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_molengo]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Molengo';$.colorbox.close();">Molengo</a></div>
<div style="font-family:'Arvo'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_arvo]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Arvo';$.colorbox.close();">Arvo</a></div>
<div style="font-family:'Coming Soon'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_comingsoon]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Coming Soon';$.colorbox.close();">Coming Soon</a></div>
<div style="font-family:'Crafty Girls'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_craftygirls]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Crafty Girls';$.colorbox.close();">Crafty Girls</a></div>
<div style="font-family:'Calligraffitti'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_calligraffitti]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Calligraffitti';$.colorbox.close();">Calligraffitti</a></div>
<div style="font-family:'Tangerine'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_tangerine]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Tangerine';$.colorbox.close();">Tangerine</a></div>
<div style="font-family:'Cherry Cream Soda'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_cherrycreamsoda]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Cherry Cream Soda';$.colorbox.close();">Cherry Cream Soda</a></div>
<div style="font-family:'OFL Sorts Mill Goudy TT'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_oflsortsmillgoudytt]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='OFL Sorts Mill Goudy TT';$.colorbox.close();">OFL Sorts Mill Goudy TT</a></div>
<div style="font-family:'Cantarell'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_cantarell]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Cantarell';$.colorbox.close();">Cantarell</a></div>
<div style="font-family:'Rock Salt'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_rocksalt]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Rock Salt';$.colorbox.close();">Rock Salt</a></div>
<div style="font-family:'Vollkorn'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_vollkorn]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Vollkorn';$.colorbox.close();">Vollkorn</a></div>
<div style="font-family:'Covered By Your Grace'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_coveredbyyourgrace]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Covered By Your Grace';$.colorbox.close();">Covered By Your Grace</a></div>
<div style="font-family:'Chewy'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_chewy]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Chewy';$.colorbox.close();">Chewy</a></div>
<div style="font-family:'Luckiest Guy'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_luckiestguy]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Luckiest Guy';$.colorbox.close();">Luckiest Guy</a></div>
<div style="font-family:'Dancing Script'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_dancingscript]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Dancing Script';$.colorbox.close();">Dancing Script</a></div>
<div style="font-family:'Bangers'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_bangers]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Bangers';$.colorbox.close();">Bangers</a></div>
<div style="font-family:'Philosopher'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_philosopher]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Philosopher';$.colorbox.close();">Philosopher</a></div>
<div style="font-family:'Fontdiner Swanky'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_fontdinerswanky]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Fontdiner Swanky';$.colorbox.close();">Fontdiner Swanky</a></div>
<div style="font-family:'Slackey'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_slackey]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Slackey';$.colorbox.close();">Slackey</a></div>
<div style="font-family:'Permanent Marker'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_permanentmarker]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Permanent Marker';$.colorbox.close();">Permanent Marker</a></div>
<div style="font-family:'Cabin Sketch'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_cabinsketch]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Cabin Sketch';$.colorbox.close();">Cabin Sketch</a></div>
<div style="font-family:'Michroma'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_michroma]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Michroma';$.colorbox.close();">Michroma</a></div>
<div style="font-family:'Unkempt'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_unkempt]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Unkempt';$.colorbox.close();">Unkempt</a></div>
<div style="font-family:'Allan'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_allan]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Allan';$.colorbox.close();">Allan</a></div>
<div style="font-family:'Corben'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_corben]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Corben';$.colorbox.close();">Corben</a></div>
<div style="font-family:'Mountains of Christmas'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_mountainsofchristmas]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Mountains of Christmas';$.colorbox.close();">Mountains of Christmas</a></div>
<div style="font-family:'Maiden Orange'"><a href="#" onclick="$('input[name=<?php echo $cat=get_template(); ?>_maidenorange]:eq(0)').attr('checked', 'checked');document.getElementById(fontoptionname).value='Maiden Orange';$.colorbox.close();">Maiden Orange</a></div>
</div>
</div>

    
<script type="text/javascript">

	var colpos = <?php global $saveoptions;foreach ($saveoptions as $value) {if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); }}
	$cat=get_template(); 
	if(${$cat."_sidebar_float"}=="Right"){
	echo (${$cat."_bodywidth"}-${$cat."_sidebar_area_width"});
	}else if(${$cat."_sidebar_float"}=="Left"){
	echo ${$cat."_sidebar_area_width"};
	}?>;
	
	var thistheme = '<?php echo $cat; ?>';
	
	var sitewidth = <?php echo ${$cat."_bodywidth"}; ?>;
	
	$(document).ready(function() {
			
			jQuery('.bgoptions').hide();  
	  
			jQuery('h3.toptrigger span').click(function(){  
				if(jQuery(this).parent().next('.bgoptions').css('display')==='none')  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-s inactive').addClass('ui-icon-circle-triangle-n active').children('img').removeClass('ui-icon-circle-triangle-s inactive').addClass('ui-icon-circle-triangle-n active');  
	  
					}  
				else  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-n active').addClass('ui-icon-circle-triangle-s inactive').children('img').removeClass('ui-icon-circle-triangle-n active').addClass('ui-icon-circle-triangle-s inactive');  
					}  
	  
				jQuery(this).parent().next('.bgoptions').toggle();  
			});
			
			
			jQuery('.blockoptions').hide();  
	  
			jQuery('.block h3 span').click(function(){  
				if(jQuery(this).parent().next('.blockoptions').css('display')==='none')  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-s inactive').addClass('ui-icon-circle-triangle-n active').children('img').removeClass('ui-icon-circle-triangle-s inactive').addClass('ui-icon-circle-triangle-n active');  
	  
					}  
				else  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-n active').addClass('ui-icon-circle-triangle-s inactive').children('img').removeClass('ui-icon-circle-triangle-n active').addClass('ui-icon-circle-triangle-s inactive');  
					}  
	  
				jQuery(this).parent().next('.blockoptions').toggle();  
			});
			
			
			jQuery('.dropdowncontent').hide();  
	  
			jQuery('.dropdownsection h3 span').click(function(){
				if(jQuery(this).parent().next('.dropdowncontent').css('display')==='none')  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-w inactive').addClass('ui-icon-circle-triangle-s active').children('img').removeClass('ui-icon-circle-triangle-w inactive').addClass('ui-icon-circle-triangle-s active');
	  
					}
				else  
					{   jQuery(this).removeClass('ui-icon-circle-triangle-s active').addClass('ui-icon-circle-triangle-w inactive').children('img').removeClass('ui-icon-circle-triangle-s active').addClass('ui-icon-circle-triangle-w inactive');  
					}  
	  
				jQuery(this).parent().next('.dropdowncontent').toggle();  
			});			
			
			$(".uploader").colorbox({iframe:true, innerWidth:425, innerHeight:344});

			$( ".topad" ).resizable({
				handles: 's',
				grid: [1, 1],
				minHeight: 0,
				resize: function(event, ui) {
					if (ui.size.height<0)
					  {
					  ui.size.height=0;
					  }
					h=Math.round(ui.size.height);
					document.getElementById('topadheight').value=h;
					document.getElementById('topadheightwrap').innerHTML=h;
				}
			});
			$( ".openwrap" ).resizable({
				handles: 'e',
				resize: function(event, ui) {
					w=Math.round(ui.size.width);
					document.getElementById('sitewidth').value=w;
					document.getElementById('sitewidthwrap').innerHTML=w;
				}
			});
			$( "#headerstyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('headerheight').value=h;
					document.getElementById('headerheightwrap').innerHTML=h;
				}
			});
			$( "#mainmenustyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('mainmenuheight').value=h;
					document.getElementById('mainmenuheightwrap').innerHTML=h;
				}
			});
			$( "#featuredonestyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('featuredoneheight').value=h;
					document.getElementById('featuredoneheightwrap').innerHTML=h;
				}
			});
			$( "#secondmenustyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('secondmenuheight').value=h;
					document.getElementById('secondmenuheightwrap').innerHTML=h;
				}
			});
			$( "#featuredtwostyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('featuredtwoheight').value=h;
					document.getElementById('featuredtwoheightwrap').innerHTML=h;
				}
			});
			$( "#footeronestyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('footeroneheight').value=h;
					document.getElementById('footeroneheightwrap').innerHTML=h;
				}
			});
			$( "#footertwostyle" ).resizable({
				handles: 's',
				resize: function(event, ui) {
					h=Math.round(ui.size.height);
					document.getElementById('footertwoheight').value=h;
					document.getElementById('footertwoheightwrap').innerHTML=h;
				}
			});
			$( "#slider" ).slider({
				min:0,
				max:sitewidth,
				step:10,
				value: colpos,
				create: function(event, ui) {
				<?php if(${$cat."_sidebar_float"}=="Right"){?>
					sw=sitewidth-colpos;
					pw=sitewidth-sw;
				<?php }else if(${$cat."_sidebar_float"}=="Left"){ ?>
					pw=sitewidth-colpos;
					sw=sitewidth-pw;
				<?php } ?>
					document.getElementById('sidebarwidth').innerHTML=sw+10;
					document.getElementById('postareawidth').innerHTML=pw-10;
				},
				slide: function(event, ui) {
				<?php if(${$cat."_sidebar_float"}=="Right"){?>
					sw=sitewidth-ui.value;
					pw=sitewidth-sw;
				<?php }else if(${$cat."_sidebar_float"}=="Left"){ ?>
					pw=sitewidth-ui.value;
					sw=sitewidth-pw;
				<?php } ?>
					document.getElementById('sidebar').style.width=(sw)+"px";
					document.getElementById('sidebarwidth').innerHTML=sw+10;
					document.getElementById('updatepostwidth').value=sw;
					document.getElementById('postsarea').style.width=(pw-10)+"px";
					document.getElementById('postareawidth').innerHTML=pw-10;
				}
			});
			
			$(".sortable-list").sortable({
				items: 'span.only',
				handle: 'h3.handle',
				opacity: 0.9,
				placeholder: 'placeholder',
				stop: function(event, ui) {
					var row_ord = $(".sortable-list").sortable('toArray');
					var i=0;
					while (i<=7)
					  {
					  x=row_ord[i];
					  rownum=i+1;
					  rowname=thistheme + "_row_0" + rownum;
					  document.getElementById(rowname).value=x;
					  i++;
					  }
				}
			});
			
			$(".logopos").draggable({
				drag: function(event, ui) {
					document.getElementById('<?php echo $cat."_logopos_top";?>').value=ui.position.top;
					document.getElementById('<?php echo $cat."_logopos_left";?>').value=ui.position.left;
				}
			});

			$(".titlepos").draggable({
				drag: function(event, ui) {
					document.getElementById('<?php echo $cat."_sitetitlepos_top";?>').value=ui.position.top;
					document.getElementById('<?php echo $cat."_sitetitlepos_left";?>').value=ui.position.left;
				}
			});

			$(".taglinepos").draggable({
				drag: function(event, ui) {
					document.getElementById('<?php echo $cat."_taglinepos_top";?>').value=ui.position.top;
					document.getElementById('<?php echo $cat."_taglinepos_left";?>').value=ui.position.left;
				}
			});

			$(".rssiconpos").draggable({
				drag: function(event, ui) {
					document.getElementById('<?php echo $cat."_rssiconpos_top";?>').value=ui.position.top;
					document.getElementById('<?php echo $cat."_rssiconpos_left";?>').value=ui.position.left;
				}
			});

			$(".extraiconpos").draggable({
				drag: function(event, ui) {
					document.getElementById('<?php echo $cat."_extraiconpos_top";?>').value=ui.position.top;
					document.getElementById('<?php echo $cat."_extraiconpos_left";?>').value=ui.position.left;
				}
			});
	
			jQuery('.openbody').show();
		}
	);

	</script>
<?php 
/* end admin page if theme compatible */
	
	} else if (!file_exists($filename)) {
	echo '<div id="message" class="updated fade"><p><strong>Your current theme is not Website Weaver compatible. Please download and activate the free <a href="http://www.spiderwebpress.com/wordpress-theme-generator/the-aaa-starter-professional-wordpress-theme/" target="_blank">AAA Starter Theme</a> or another compatible theme and try again.</strong></div>';
	}
/* end theme compatible check */
} ?>
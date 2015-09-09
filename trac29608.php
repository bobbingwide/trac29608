<?php // (C) Copyright Bobbing Wide 2014,2015
/*
Plugin Name: TRAC 29608    
Plugin URI: http://www.oik-plugins.com/oik-plugins/oik
Description: Test TRAC #29608 incl. shortcode test function clone
Version: 0.0.1 
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2014,2015 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html
*/

add_shortcode( "dumptag", "trac_29608_shortcode_dumptag" );
add_shortcode( "paragraph", "trac_29608_shortcode_paragraph" );
add_shortcode( "noautop", "trac_29608_noautop" );
add_shortcode( "29608",  "trac_29608" );

add_action( "init", "trac_29608_init" );
//add_action( "admin_bar_menu", "wpmark", 9);


/**
 * Implement [noautop] shortcode.
 *
 * With wpautop running after shortcode expansion
 * we can get confused if the source contains a load of paragraphs
 * and we want to see what the expected output actually looks like in a browser
 * 
 * noautop will turn autop processing off by removing this filter.
 * 
 * add_filter( 'the_content', 'wpautop_nobr', 100            );
 * 
 * Note: I'm not sure if this process is reversible. 
 * Need to check what I do in oik-css.
 * 
 */ 
function trac_29608_noautop( $atts ) {
  remove_filter( "the_content", "wpautop_nobr", 100 );
  remove_filter( "the_content", "wpautop" );
  
  return( "<!--noautop-->" );
}  

/**
 * Duplicate of  Tests_Post_Output::_shortcode_dumptag
 *
 * This expects all the parameters that a shortcode might get
 * Note that none are optional
 * 
 * There should be a test for no parameters passed
 * and also one for "<" and ">=" 
 * 
 * Another for $content
 * And another for when the shortcode to invoke the function 
 * isn't the shortcode first thought of
 */
function trac_29608_shortcode_dumptag( $atts, $content, $tag ) {
  $out = '';
  if ( is_array( $atts ) ) {
    foreach ($atts as $k=>$v) {
      $out .= "$k = $v\n";
    } 
  }
  //if ( $content ) {
  //  $out .= $content;
  //}
  if ( $tag !== "dumptag" ) {
    $out .=  "shortcode: $tag ";
  }
	return $out;
}

/**
 * Duplicate of Tests_Post_Output::_shortcode_paragraph
 */
function trac_29608_shortcode_paragraph( $atts, $content ) {
  extract(shortcode_atts(array(
			'class' => 'graf',
		), $atts));
  return "<p class='$class'>$content</p>\n";
}

function trac_29608_get_field( $field ) {
  $value = bw_array_get( $_REQUEST, $field, true );
  return( $value );

}

 
/**
 * Display the form to accept the input
 * 
 * to compare filter logic output
 */ 
function trac_29608_form( $value ) {
  oik_require( "bobbforms.inc" );
  e( "<!--notext:-->" );
  bw_form();
  stag( "table" );
  bw_textarea( "input_29608", 100, "Input", $value );
  bw_checkbox( "autop", "Perform wpautop processing", trac_29608_get_field( "autop" ) );
  bw_textfield( "limit", 6, "Performance test iterations", trac_29608_get_field( "limit" ) );
  etag( "table" );
  e( wp_nonce_field( "_29608_form", "_29608_nonce", false ) );
  p( isubmit( "_filter_29608", __( "Filter" ) ) );
  etag( "form" );
  e( "<!--dotext:-->" );
}


/**
 * Implement [29608] shortcode.
 *
 * Displays a form which you can use to enter content 
 * and see the results of 'the_content' filtering before and
 * after the implementation of a fix for TRAC 29608
 *
 * Output
 *
 * - input area
 * - information area for shortcode expansion options
 * - output area showing pre 29608 output - i.e. the original 'expected' output
 * - output area displaying post 29608 output - the new 'expected' output
 * - output area showing how pre 29608 output formats
 * - output area showing how post 29608 output format
 * 
 * If there are differences 
 * - output area showing the hexadecimal dump of the original output
 * - output area showing the hexadecimal dump of the new output
 * 
 * Notes: Since the intended logic for TRAC 29608 is to perform autop processing after shortcode expansion
 * rather than before, and since this code is being run as a shortcode
 * then the whole of this output would normally be affected by the filters that run subsequently.
 * This makes a mockery of the processing. 
 * We call trac_29608_disable_remaining_filters() to prevent this from happening.
 *  
 */
function trac_29608( $atts=null, $content=null, $tag=null ) {
  $value = bw_array_get( $_REQUEST, "input_29608", "Code is poetry" );
  
  define( 'WP_INSTALLING', true );
  
  
  $pre_29608_result = trac_pre_29608( $value );
  
  $post_29608_result = trac_post_29608( $value );
  h3( "Results" );
  
  $match = trac_29608_compare( $pre_29608_result, $post_29608_result );
  e( bw_do_shortcode( "[div class=w50p5]" ));
  
  $version = bw_wp( array( "v" ) );
  
  h3( $version );
 
  trac_29608_input( $pre_29608_result ); 
  ediv();
  //sdiv( "w50p0" );
  
  e( bw_do_shortcode( "[div class=w50p0]" ));
   
  h3( "autopia" ); 
  trac_29608_input( $post_29608_result );
  ediv();
  
  
  sediv( "cleared" );
  e( "<hr />" );
  e( bw_do_shortcode( "[div class=w50p0]" ));
  trac_29608_display( $pre_29608_result );
  ediv();
  
  e( bw_do_shortcode( "[div class=w50p0]" ));
  trac_29608_display( $post_29608_result );
  ediv();
  
  sediv( "cleared" );
  
  
  
  
  //if ( !$match ) {
    h3( "Hex dump" );
    trac_29608_dump( $pre_29608_result );
    trac_29608_dump( $post_29608_result );
  //} 
 
  
  h3( "Performance comparison" );
  trac_29608_perf( $value );
  
  $bw_ret = bw_ret();
  
  
  trac_29608_form( $value );
  $bw_ret_form = bw_ret();
  
  $bw_ret = $bw_ret_form . $bw_ret;
  
  trac_29608_disable_remaining_filters();
  
  bw_trace2( $bw_ret, "bw_ret", false );
  return( $bw_ret );
}

/** 
 * Display the escaped HTML.
 *
 * Display the HTML produced by filtering the content
 * 
 *
 * @param string $content 
 */ 
function trac_29608_input( $content ) {
  stag( "pre" );
  e( esc_html( $content ) );
  etag( "pre" );
}

/**
 * Display the unescaped HTML.
 *
 * Question: Will this always be safe to do so?
 * If not, what sort of problems can this cause?
 * 
 * @param string $content
 */
function trac_29608_display( $content ) {
  sdiv( "cleared" );
  ediv();
  e( $content );
}

/**
 * Produce a hex dump of the generated HTML
 * 
 * @param string $content
 */
function trac_29608_dump( $content ) {
  oik_require( "includes/hexdump.php", "trac29608" );
  $dump = hexdump( $content );
  stag( "code" );
  e( $dump );
  etag( "code" );
}

/**
 * Compare performance of the two methods
 *
 * Do this regardless of the comparison
 * As it may help decide which is the better solution
 * Especially when the output doesn't actually make much sense regardless of the method chosen.
 */
function trac_29608_perf( $value ) {
  $limit = trac_29608_get_field( "limit" );
  $limit = absint( $limit );
  if ( !$limit ) {
    $limit = 1000;
  }
  bw_push();
  $start = microtime();
  for ( $loop = 0; $loop < $limit; $loop++ ) {
    $result = apply_filters( "the_content_pre_29608", $value );
  }
  $middle = microtime();
  for ( $loop = 0; $loop < $limit; $loop++ ) {
    $result = apply_filters( "the_content_post_29608", $value );
  }
  $end = microtime();
  bw_pop();
  $pre = bw_get_elapsed( $start, $middle );
  $post = bw_get_elapsed( $middle, $end );
  //e( "Start: $start ");
  //e( "Middle: $middle ");
  //e( "End: $end " );
  p( "Pre: $pre ");
  p( "Post: $post" );
  if ( $post > $pre ) {
    p( "Post is slower" );
  } elseif ( $post == $pre ) {
    p( "Times match!" );
  } else {
    p( "Post is faster" );
  }
  p( "Loops: $limit " );
   

}

/* e 1.3
   s 0.9
*/   
function bw_get_elapsed( $tods, $tode=null ) {
  if ( !$tode ) {
    $tode = microtime( );
  }
  //e( $tode );
  list( $su, $ss ) = explode(" ", $tods );
  list( $eu, $es ) = explode(" ", $tode );
  $elapsedsec  = $es - $ss;
  $elapsedu = $eu - $su;
  
  $elapsed = $elapsedsec + $elapsedu;
  
  //e( $elapsedsec );
  //e( $elapsedu );
  //e( $elapsed );
  return( $elapsed );  
}


/** 
 * Disable remaining filters for the current filter
 *
 * We're expanding a shortcode that's being used to compare the results of running
 * different filters against the "current" installation.
 * The current installation may be WordPress 4.0, 4.1, 4.2 or higher
 * Any filters that are run after shortcode expansion could affect our output.
 * So we need to disable all the filters that follow.
 * We must not disable filters that precede this one
 * First we have to find out what this one is
 * We can look up the call stack and find a match for the filter function
 * in the global filters array.
 * 
 * 
 0. bw_lazy_backtrace C:\apache\htdocs\wordpress\wp-content\plugins\oik\bwtrace.inc:60 0
1. bw_backtrace C:\apache\htdocs\wordpress\wp-content\plugins\trac29608\trac29608.php:256 0
2. trac_29608_disable_remaining_filters C:\apache\htdocs\wordpress\wp-content\plugins\trac29608\trac29608.php:190 0
3. trac_29608(,,29608) C:\apache\htdocs\wordpress\wp-content\plugins\trac29608\trac29608.php:0 3
4. call_user_func(trac_29608,,,29608) C:\apache\htdocs\wordpress\wp-includes\shortcodes.php:286 4
5. do_shortcode_tag(array) C:\apache\htdocs\wordpress\wp-content\plugins\oik-css\includes\shortcodes-earlier.php:100 1
6. do_shortcode_tag_earlier(array) C:\apache\htdocs\wordpress\wp-content\plugins\oik-css\includes\shortcodes-earlier.php:0 1
7. preg_replace_callback(/\[(\[?)(embed|wp_caption|caption|gallery|playlist|audio|video|purchase_link|download_history|purchase_history|download_checkout|download_cart|edd_login|edd_register|download_discounts|purchase_collection|downloads|edd_price|edd_receipt|edd_profile_editor|dumptag|paragraph|noautop|29608|year|rss|ad|top|login_link|blog_title|xhtml|css|rss_url|rss_title|template_url|search|product|product_page|product_category|product_categories|add_to_cart|add_to_cart_url|products|recent_products|sale_products|best_selling_products|top_rated_products|featured_products|product_attribute|related_products|shop_messages|woocommerce_order_tracking|woocommerce_cart|woocommerce_checkout|woocommerce_my_account|woocommerce_messages|bw_otd|bw_field|bw_fields|bw_new|bw_related|oik_edd_apikey|oikp_download|bw_squeeze|bw_testimonials|oikth_download|bw_mshot|nivo|bugger|fbh|latestnews|getoik|lazy|loikp|TODO|apiref|themeref|smart|lssc|diy|bob|fob|bing|bong|wide|hide|wow|WoW|WOW|oik|loik|OIK|lbw|bw_page|bw_post|bw_plug|bp|lwp|lbp|wpms|lwpms|drupal|ldrupal|artisteer|lartisteer|wp|bw_csv|bw_search|bw_dash|bw_action|bw_rpt|bw_graphviz|bw_crumbs|bw_option|bw_text|bw_css|bw_geshi|bw_background|bw_autop|bw_blog|bw_blogs|bw_popup|bw_more|bw_rwd|bw_codes|bw_code|bw_user|bw_users|bw_wtf|bw_directions|bw|bw_address|bw_mailto|bw_email|bw_geo|bw_telephone|bw_fax|bw_mobile|bw_skype|bw_tel|bw_mob|bw_wpadmin|bw_domain|bw_show_googlemap|bw_contact|bw_company|bw_business|bw_formal|bw_slogan|bw_alt_slogan|bw_admin|bw_twitter|bw_facebook|bw_linkedin|bw_youtube|bw_flickr|bw_picasa|bw_googleplus|bw_google_plus|bw_google\-plus|bw_google|bw_instagram|bw_pinterest|bw_follow_me|clear|bw_logo|bw_qrcode|div|sdiv|ediv|sediv|bw_emergency|bw_abbr|bw_acronym|bw_blockquote|bw_cite|bw_copyright|stag|etag|bw_tree|bw_posts|bw_pages|bw_list|bw_bookmarks|bw_attachments|bw_pdf|bw_images|bw_portfolio|bw_thumbs|bw_button|bw_contact_button|bw_block|bw_eblock|paypal|ngslideshow|gpslides|bwtron|bwtroff|bwtrace|bw_power|bw_editcss|bw_table|bw_parent|bw_iframe|bw_jq|bw_accordion|bw_tabs|bw_login|bw_loginout|bw_register|bw_link|bw_contact_form|bw_countdown|bw_cycle|bw_count|bw_navi|bw_tides|bw_api|api|apis|hooks|codes|file|files|classes|hook|md)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s,do_shortcode_tag_earlier,[noautop][29608]

   
  global $wp_filter;
  $fields = array();
  $filters = bw_array_get( $wp_filter, "show_user_profile", null );
  foreach ( $filters as $priority => $hooks ) {
    foreach ( $hooks as $key => $hook ) {
      bw_trace2( $hook, "hook", false );
      $function = oiku_get_function_as_string( $hook['function'] ); 
      $fields["$priority $function"] = $hook['function'];
    }
  } 
 * 
 */

function trac_29608_disable_remaining_filters() {
  $cf = current_filter();
  e( "Disabling remaining filters after $cf" );
  //bw_backtrace();
  global $wp_filter;
  
  $filters = bw_array_get( $wp_filter, $cf, null );
  bw_trace2( $filters, "filters", null );
  // Make use of TRAC 17817 ! - didn't appear to work
  
  $current = current( $wp_filter[ $cf ] );
  bw_trace2( $current, "current", false );
  $key = key( $wp_filter[ $cf ] );
  bw_trace2( $key, "key", false );
  end( $wp_filter[$cf] );
  next( $wp_filter[$cf] );

  
  //do_action( $cf, "Disabling filters" );
  
}


/**
 * Filter 'the_content' using pre-29608 filters
 * 
 */
function trac_pre_29608( $value ) {
  add_filter( 'the_content_pre_29608', 'wptexturize'     );
  add_filter( 'the_content_pre_29608', 'convert_smilies'    );
  add_filter( 'the_content_pre_29608', 'convert_chars'      );
  add_filter( 'the_content_pre_29608', 'wpautop'      );
  add_filter( 'the_content_pre_29608', 'shortcode_unautop'  );
  add_filter( 'the_content_pre_29608', 'prepend_attachment' );
	add_filter( 'the_content_pre_29608', 'capital_P_dangit', 11 );
  add_filter( 'the_content_pre_29608', 'do_shortcode', 11 );
  
  $result = apply_filters( "the_content_pre_29608", $value );
  return( $result );
}

/**
 * Convert new lines to <br /> regardess
 * 
 */
function wpautop_nlbr( $pee ) {
  $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', '_autop_newline_preservation_helper', $pee);
  $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
  $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
  return( $pee );
}

/**
 * Filter 'the_content' using post-29608 filters
 *
 * Notes: 
 * - the filters are not invoked in the order coded below, you have to pay attention to the priorities as well
 * - wptexturize_blocks is intended to be run after shortcode expansion
 * - 
 * 
 * - Capital_P_dangit needs to be run after autop otherwise "Wordpress" on its own won't be processed.
 * 
 */
function trac_post_29608( $value ) {
  if ( !function_exists( "do_shortcode_earlier" ) ) {
    oik_require2( "includes/shortcodes-earlier.php", "trac29608", "oik-css" );
  }
  if ( !function_exists( "wptexturize_blocks" ) ) {
    oik_require2( "includes/formatting-later.php", "trac29608", "oik-css" );
  }  

  add_filter( 'the_content_post_29608', 'convert_smilies'    );
  add_filter( 'the_content_post_29608', 'convert_chars'      );
  add_filter( 'the_content_post_29608', 'do_shortcode_earlier', 11 );
  add_filter( 'the_content_post_29608', 'wptexturize_blocks', 98     );
  if ( trac_29608_get_field( "autop" ) ) {
    add_filter( 'the_content_post_29608', 'wpautop_nobr', 100      );
  }
  // There is no need for shortcode_unautop since we perform autop processing AFTER shortcode expansion
  //add_filter( 'the_content_post_29608', 'shortcode_unautop'  );
  
  add_filter( 'the_content_post_29608', 'prepend_attachment' );
  
	add_filter( 'the_content_post_29608', 'capital_P_dangit', 101 );
  //add_filter( 'the_content_post_29608', 'wpautop_nlbr', 102 );
  $result = apply_filters( "the_content_post_29608", $value );
  return( $result );
}

/**
 * Compare the results 
 *
 * @param string $result1 
 * @param string $result2
 * @return bool - true if they match, false otherwise
 */
function trac_29608_compare( $result1, $result2 ) {
  $match = ( $result1 == $result2 );
  if ( $match ) {
    p( "Results match exactly." );
  } else {
    p( "Results are different." );
  }
  return( $match ); 
}

/**
 * The output of shortcode 29608 should not be texturized
 */
function trac_29608_no_texturize_shortcodes( $shortcodes ) {
  $shortcodes[] = "29608";
  return( $shortcodes );
}


/**
 * 
 * We can't remove the wptexturize filter processing from normal stuff
 * but we need to prevent the output from the [29608] shortcode from being
 * texturized since this can totally mess up the generated form code.
 * So the shortcode needs to implement the no texturize filter
 * 
 *  
 */
function trac_29608_init() {
  add_filter( "no_texturize_shortcodes", "trac_29608_no_texturize_shortcodes" );
} 

//function wpmark( &$wp_admin_bar ) { $wp_admin_bar->add_menu( array('id'=>'wpmark','title'=>"@wpmark" ) ); }
   // 'href'   => "t29608",
    //'meta'   => array( 'tabindex' => -1, ),
 // ) );

//} 
  

    


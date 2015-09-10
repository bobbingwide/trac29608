=== trac29608 ===
Contributors: bobbingwide
Donate link: http://www.oik-plugins.com/oik/oik-donate/
Tags: shortcodes, s
Requires at least: 4.3
Tested up to: 4.3
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Plugin to test solutions for TRAC #29608

It's purpose is to help you visualise how shortcodes are expanded using
the current WordPress code and the 'new' solution in which the sequence in which filters
are invoked has been changed.

This solution considers the output of shortcodes to be 'perfect' already.

= Current vs new sequence =

For 'the_content' filter

Current logic invokes:

* 'wptexturize'     
* 'convert_smilies'    
* 'convert_chars'      
* 'wpautop'      
* 'shortcode_unautop'  
* 'prepend_attachment' 
* 'capital_P_dangit', 11
* 'do_shortcode', 11


New logic invokes:

* 'convert_smilies'    
* 'convert_chars'      
* 'prepend_attachment' 
* 'do_shortcode_earlier', 11 	- NEW FILTER FUNCTION
* 'wptexturize_blocks', 98   - NEW FILTER FUNCTION
* 'wpautop_nobr', 100      - NEW FILTER FUNCTION
* 'capital_P_dangit', 101 

Similar changes would be made to the sequence of filter hooks for 'the_title', 'the_excerpt', etcetera.

= Background =

This has been a long time in coming...

I first raised TRAC #29608 ( Sep 9, 2014), 
soon after WordPress 4.0 was released ( Benny, Sep 4, 2014 ).

I had a problem with shortcode's attributes being texturized.
I proposed a solution which changed the sequence in which filters were invoked.
So that texturizing was performed after shortcode expansion.


I started developing this solution with a plan to discuss it with others 
at WordCamp London Contributor day in Nov 2014.

Subsequently I developed a shortcode called [29608] 
which allows you to enter some content and see the results
of processing through the current WordPress method and the
alternative implemented by this logic.

I recently released an update to the oik-css plugin (v0.8.0) that use this new logic to deliver a solution for [bw_geshi lang=none].

Note: The trac29608 plugin is dependent upon oik for some of its logic
and oik-css for the new filter functions.

TRAC 29608 has now been closed. A new TRAC has been raised.
I'll need to change this number.

= shortcode test function clones =

This plugin also includes clones of the shortcodes used in the unit test functions.

* [dumptag]	- Duplicate of  Tests_Post_Output::_shortcode_dumptag
* [paragraph]	- Duplicate of Tests_Post_Output::_shortcode_paragraph


It also delivers a very flaky shortcode [noautop]	which will probably be removed

= Usage =

1. Create a private post (or page or other CPT) containing the [29608] shortcode.
1. Display the post.
1. Fill in the form. Don't be too silly with the input values.
1. See what you get.
1. Pretend you're an optician and ask yourself "Is that better or worse?"
1. Repeat until happy.

== Installation ==
1. Upload the contents of the trac29608 plugin to the `/wp-content/plugins/trac29608' directory
1. Activate the trac29608 plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

== Screenshots ==
1. trac29608 in action

== Upgrade Notice ==
= 0.0.1 =
This plugin depends upon oik and oik-css. Please install these plugins first. 


== Changelog == 
= 0.0.1 =
* Added: New plugin

== Further reading ==
If you want to read more about the oik plugins then please visit the
[oik plugin](http://www.oik-plugins.com/oik) 
**"the oik plugin - for often included key-information"**


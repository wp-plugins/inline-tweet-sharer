<?php
/*
Plugin Name:  Inline Tweet Sharer
Plugin URI: http://winwar.co.uk/plugins/inline-tweet-sharer/
Description:  Create twitter links on your site that tweet the anchor text - for memorable quotes to help increase social media views, similar to the New York Times.
Version:      1.0.5
Author:       Rhys Wynne
Author URI:   http://winwar.co.uk/

*/

define("ITS_PLUGIN_NAME","Inline Tweet Sharer");
define("ITS_PLUGIN_TAGLINE","Create twitter links on your site that tweet the anchor text - for memorable quotes to help increase social media views, similar to the New York Times.");
define("ITS_PLUGIN_URL","http://winwar.co.uk/plugins/inline-tweet-sharer/");
define("ITS_EXTEND_URL","http://wordpress.org/plugins/inline-tweet-sharer/");
define("ITS_AUTHOR_TWITTER","rhyswynne");
define("ITS_DONATE_LINK","https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SBVM5663CHYN4");

function inline_tweet_sharer_create_tweet($prefix = "", $tweeter = "", $suffix = "", $content) {

    $tweetlinkstring = "";
    
    if ("" != $prefix)
    {
        $tweetlinkstring .= $prefix . ' ';
    } 
    elseif ("" != $tweeter) 
    {
        $tweetlinkstring .= "RT @" . $tweeter . ": ";
    } 
    elseif ("" != get_option('inline-tweet-sharer-default'))
    {
        $tweeter = get_option('inline-tweet-sharer-default');
        $tweetlinkstring .= "RT @" . $tweeter . ": ";
    }
    
    $tweetlinkstring .= '' . $content . ' ';
    
    if ("" != $suffix)
    {
        $tweetlinkstring .= $suffix;
    }

    if (strlen($tweetlinkstring) > 116)
    {
        $tweetlinkstring = substr($tweetlinkstring,0,116);
        $tweetlinkstring = preg_replace('/ [^ ]*$/', ' ...', $tweetlinkstring); 
    }
    
    if ("1" == get_option('inline-tweet-sharer-capitalise'))
    {
        $tweetlinkstring = ucfirst($tweetlinkstring);                
    }
    
    $tweetlinkstring = urlencode($tweetlinkstring);
    

    $link = '<a '; 
    
    if ("1" == get_option('inline-tweet-sharer-marker'))
    {
        $link .= 'class="inline-twitter-link"';
    }
    
    $url = 'https://twitter.com/intent/tweet?url=' . urlencode(get_permalink()) . '&text=' . $tweetlinkstring;
    $url = str_replace(array("\n","\r"), "", $url);
    $url = str_replace(array("/"), "\/", $url);
    $link .= ' href="#" onclick="inline_tweet_sharer_open_win(\''.$url.'\');"';
    //$link .= ' href="#" onclick="window.open(\''.$url.'\',\'tweetwindow\',\'width=566,height=592,location=yes,directories=no,channelmode=no,menubar=no,resizable=no,scrollbars=no,status=no,toolbar=no\')"';
    $link .= ' title="'. __('Tweet This!','inline-tweet-sharer').'">' . $content; 

    if ("1" == get_option('inline-tweet-sharer-marker'))
    {
        $link .= ' <span> </span>';
    }
    
    $link .= "</a>";
    
    return $link;

}


/* ==== ADMIN FUNCTIONS ==== */


/* THESE ARE THE ACTIONS THAT ARE CALLED WHENEVER THE ADMIN IS RUN */
if ( is_admin() ){ // admin actions

  add_action('admin_menu', 'inline_tweet_sharer_menus');
  add_action( 'admin_init', 'inline_tweet_sharer_process' );
  add_action( 'admin_init', 'inline_tweet_sharer_add_admin_stylesheet' );
  
} else {
    wp_register_style( 'inline-tweet-sharer-style', plugins_url('inline-tweet-sharer.css', __FILE__) );
    wp_enqueue_style( 'inline-tweet-sharer-style' );
    wp_enqueue_script('inline-tweet-sharer-js', plugins_url('inline-tweet-sharer.js', __FILE__));
}

function inline_tweet_sharer_add_admin_stylesheet() {
        wp_register_style( 'inline-tweet-sharer-admin-style', plugins_url('inline-tweet-sharer-admin.css', __FILE__) );
        wp_enqueue_style( 'inline-tweet-sharer-admin-style' );
}


/* THIS FUNCTION CREATES THE MENU IN THE "SETTINGS" SECTION OF WORDPRESS */
function inline_tweet_sharer_menus() {

  add_options_page('Inline Tweet Sharer', 'Inline Tweet Sharer', 8, 'inlinetweetshareroptions', 'inline_tweet_sharer_options');

}

/* THIS FUNCTION CREATES THE OPTIONS PAGE WITH ALL OPTIONS */
function inline_tweet_sharer_options() {
?>
        <div class="pea_admin_wrap">
                <div class="pea_admin_top">
                    <h1><?php echo ITS_PLUGIN_NAME?> <small> - <?php echo ITS_PLUGIN_TAGLINE?></small></h1>
                </div>
        
                <div class="pea_admin_main_wrap">
                    <div class="pea_admin_main_left">
                        <div class="pea_admin_signup">
                            Want to know about updates to this plugin without having to log into your site every time? Want to know about other cool plugins we've made? Add your email and we'll add you to our very rare mail outs.
        
                            <!-- Begin MailChimp Signup Form -->
                            <div id="mc_embed_signup">
                            <form action="http://peadig.us5.list-manage2.com/subscribe/post?u=e16b7a214b2d8a69e134e5b70&amp;id=eb50326bdf" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                            <div class="mc-field-group">
                                <label for="mce-EMAIL">Email Address
                            </label>
                                <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"><button type="submit" name="subscribe" id="mc-embedded-subscribe" class="pea_admin_green">Sign Up!</button>
                            </div>
                                <div id="mce-responses" class="clear">
                                    <div class="response" id="mce-error-response" style="display:none"></div>
                                    <div class="response" id="mce-success-response" style="display:none"></div>
                                </div>	<div class="clear"></div>
                            </form>
                            </div>
                            <!--End mc_embed_signup-->

                        </div>
  
                    <form method="post" action="options.php" id="options">
                    
                    <?php wp_nonce_field('update-options'); ?>
                    <?php settings_fields( 'inline-tweet-sharer-group' ); ?>
  
                    <table class="form-table">
                        <tbody>

                        <tr valign="top">
                            <th scope="row" style="width:400px"><label for="inline-tweet-sharer-default"><?php _e('Default Twitter Handle (leave blank for none)','inline-tweet-sharer'); ?>:</label></th>
                            <td><input type="text" name="inline-tweet-sharer-default" id="inline-tweet-sharer-default" class="regular-text code" value="<?php echo get_option('inline-tweet-sharer-default'); ?>" />
                            <br /><?php _e('This is the "RT @______: section for tweets before the quoted text, leave blank for no quoted text','inline-tweet-sharer'); ?>
                            <br /><?php _e('Just place the twitter username, no @, no http://twitter.com/','inline-tweet-sharer'); ?>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row" style="width:400px"><label for="inline-tweet-sharer-marker"><?php _e('Mark Twitter Links','inline-tweet-sharer'); ?>:</label></th>
                            <td><input type="checkbox" name="inline-tweet-sharer-marker" id="inline-tweet-sharer-marker" value="1" <?php if (get_option('inline-tweet-sharer-marker') == 1) { echo "checked"; } ?> /></td>
                        </tr>

                        <tr valign="top">
                            <th scope="row" style="width:400px"><label for="inline-tweet-sharer-capitalise"><?php _e('Capitalise first letter of Tweet?','inline-tweet-sharer'); ?>:</label></th>
                            <td><input type="checkbox" name="inline-tweet-sharer-capitalise" id="inline-tweet-sharer-capitalise" value="1" <?php if (get_option('inline-tweet-sharer-capitalise') == 1) { echo "checked"; } ?> /></td>
                        </tr>

                        </tbody>
                    </table>

                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="inline-tweet-sharer-default" />

                    <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>

                    </form>

                    </div>
                </div>
                
                <div class="pea_admin_main_right">
                    <div class="pea_admin_box">
                    
                        <h2>Like this Plugin?</h2>
                        <a href="<?php echo ITS_EXTEND_URL; ?>" target="_blank"><button type="submit" class="pea_admin_green">Rate this plugin	&#9733;	&#9733;	&#9733;	&#9733;	&#9733;</button></a><br><br>
                        
                        <div id="fb-root"></div>
                        
                        <script>(function(d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s); js.id = id;
                                js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=181590835206577";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));
                        </script>
                        
                        <div class="fb-like" data-href="<?php echo ITS_PLUGIN_URL; ?>" data-send="true" data-layout="button_count" data-width="250" data-show-faces="true"></div>
                        <br>
                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo ITS_PLUGIN_URL; ?>" data-text="Just been using <?php echo ITS_PLUGIN_NAME; ?> #WordPress plugin" data-via="<?php echo ITS_AUTHOR_TWITTER; ?>" data-related="WPBrewers">Tweet</a>
                        
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                        
                        <br>
                        <a href="http://bufferapp.com/add" class="buffer-add-button" data-text="Just been using <?php echo ITS_PLUGIN_NAME; ?> #WordPress plugin" data-url="<?php echo ITS_PLUGIN_URL; ?>" data-count="horizontal" data-via="<?php echo ITS_AUTHOR_TWITTER; ?>">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
                        
                        <br>
                        <div class="g-plusone" data-size="medium" data-href="<?php echo ITS_PLUGIN_URL; ?>"></div>
                        
                        <script type="text/javascript">
                            window.___gcfg = {lang: 'en-GB'};

                            (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/plusone.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                            })();
                        </script>

                        <br>
 
                        <su:badge layout="3" location="<?php echo ITS_PLUGIN_URL?>"></su:badge>

                        <script type="text/javascript">
                          (function() {
                            var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
                            li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
                          })();
                        </script>
                    </div>

                    <center><a href="<?php echo ITS_DONATE_LINK; ?>" target="_blank"><img class="paypal" src="<?php echo plugins_url( 'paypal.gif' , __FILE__ ); ?>" width="147" height="47" title="Please Donate - it helps support this plugin!"></a></center>

                <div class="pea_admin_box">
                    <h2>About the Author</h2>

                    <?php
                    $default = "http://reviews.evanscycles.com/static/0924-en_gb/noAvatar.gif";
                    $size = 70;
                    $rhys_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( "rhys@rhyswynne.co.uk" ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
                    ?>

                    <p class="pea_admin_clear"><img class="pea_admin_fl" src="<?php echo $rhys_url; ?>" alt="Rhys Wynne" /> <h3>Rhys Wynne</h3><br><a href="https://twitter.com/rhyswynne" class="twitter-follow-button" data-show-count="false">Follow @rhyswynne</a>
                    <div class="fb-subscribe" data-href="https://www.facebook.com/rhysywynne" data-layout="button_count" data-show-faces="false" data-width="220"></div>
                    </p>
                    
                    <p class="pea_admin_clear">Rhys Wynne is a Digital Marketing Consultant currently at 3 Door Digital and a freelance WordPress developer and blogger. His plugins have had a total of 100,000 downloads, and his premium plugins have generated four figure sums in terms of sales. Rhys likes rubbish football (supporting Colwyn Bay FC) and Professional Wrestling.</p>
                
                </div>

            </div>
    </div>
    
<?php

}

/* THIS FUNCTION SAVES THE OPTIONS FROM THE PREVIOUS FUNCTION */
function inline_tweet_sharer_process() { // whitelist options

  register_setting( 'inline-tweet-sharer-group', 'inline-tweet-sharer-default' );
  register_setting( 'inline-tweet-sharer-group', 'inline-tweet-sharer-marker' );
  register_setting( 'inline-tweet-sharer-group', 'inline-tweet-sharer-capitalise' );
}

/* THIS FUNCTION ADDS A BUTTON TO WORDPRESS' TINYMCE TO SHOW THE TWITTER BUTTON AND ALLOWERS USERS TO CLICK TO ADD THE SHORTCODE */
function inline_tweet_sharer_addbuttons() {
    // Don't bother doing this stuff if the current user lacks permissions
    if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
        return;
 
    // Add only in Rich Editor mode
    if ( get_user_option('rich_editing') == 'true') {

        add_filter("mce_external_plugins", "inline_tweet_sharer_tinymce_plugin");
        add_filter('mce_buttons', 'inline_tweet_sharer_button');
    }
}

/* THIS FUNCTION ACTUALLY ADDS THE BUTTON */
function inline_tweet_sharer_button($buttons) {
   array_push($buttons, "separator", "inlinetweetsharer");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function inline_tweet_sharer_tinymce_plugin($plugin_array) {
    $url = plugins_url( '/editor_plugin.js' , __FILE__ );
    $plugin_array['inlinetweetsharer'] = $url;
    return $plugin_array;
}
 
// init process for button control
add_action('init', 'inline_tweet_sharer_addbuttons');

/* NOW LETS ADD THE SHROTCODE "INLINE_TWEET" THAT HANDLES THE INLINE TWEET */
add_shortcode( 'inlinetweet', 'inline_tweet_sharer_shortcode' );

function inline_tweet_sharer_shortcode( $atts, $content = null ) {
	$tweeter = get_option('inline-tweet-sharer-default');

   extract( shortcode_atts( array(
      'prefix' => $prefix,
      'tweeter' => $tweeter,
      'suffix' => $suffix,
      ), $atts ) );
 
 	$tweetlink = inline_tweet_sharer_create_tweet(esc_attr($prefix), esc_attr($tweeter), esc_attr($suffix), $content);
 
   return $tweetlink;
}

?>
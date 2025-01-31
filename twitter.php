<?php

/*

Plugin Name: WP Lauro-Twitter Feed

Version: 1.0

Plugin URI: http://www.dianysmedia.info/

Description: WP Lauro-Twitter Feed: List your latest tweets in the sidebar of your wordpress blog easy. It`s very easy to use and after upload in plugins folder and activate the plugin, activate widget and start showing your latest tweets in minutes.

Author: Dianys Stan

Author URI: http://www.dianysmedia.info/

*/



/*  Copyright 2012  Dianys Stan (



    This program is free software; you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation; either version 2 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program; if not, write to the Free Software

    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/



define('MAGPIE_CACHE_ON', 1); //1.7 Cache Bug

define('MAGPIE_CACHE_AGE', 180);

define('MAGPIE_INPUT_ENCODING', 'UTF-8');

define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');



$twitter_options['widget_fields']['title'] = array('label'=>'Sidebar Title:', 'type'=>'text', 'default'=>'');

$twitter_options['widget_fields']['username'] = array('label'=>'Enter Twitter Username:', 'type'=>'text', 'default'=>'');

$twitter_options['widget_fields']['num'] = array('label'=>'No of links:', 'type'=>'text', 'default'=>'5');

$twitter_options['widget_fields']['update'] = array('label'=>'Timestamps:', 'type'=>'checkbox', 'default'=>true);

$twitter_options['widget_fields']['linked'] = array('label'=>'Linked:', 'type'=>'text', 'default'=>'#');

$twitter_options['widget_fields']['hyperlinks'] = array('label'=>'Hyperlinks?', 'type'=>'checkbox', 'default'=>true);

$twitter_options['widget_fields']['twitter_users'] = array('label'=>'Find @replies:', 'type'=>'checkbox', 'default'=>true);

$twitter_options['widget_fields']['encode_utf8'] = array('label'=>'UTF8 Encode:', 'type'=>'checkbox', 'default'=>false);

$twitter_options['prefix'] = 'twitter';


// Display Twitter messages

function twitter_messages($username = '', $num = 1, $list = false, $update = true, $linked  = '#', $hyperlinks = true, $twitter_users = true, $encode_utf8 = false) {

	echo '<div id="twitwrapper">';

	global $twitter_options;

	include_once(ABSPATH . WPINC . '/rss.php');

	$messages = fetch_rss('http://twitter.com/statuses/user_timeline/'.$username.'.rss');

	if ($list) echo '<ul class="twitter">';

	if ($username == '') {

		if ($list) echo '<li>';

		echo 'RSS not configured';

		if ($list) echo '</li>';

		

	} else {

			if ( empty($messages->items) ) {

				if ($list) echo '<li>';

				echo 'No public Twitter messages.';

				if ($list) echo '</li>';

			} else {

        $i = 0;

				foreach ( $messages->items as $message ) {

					$msg = " ".substr(strstr($message['description'],': '), 2, strlen($message['description']))." ";

					if($encode_utf8) $msg = utf8_encode($msg);

					$link = $message['link'];

					if ($list) echo '<li class="twitter-item">'; elseif ($num != 1) echo '<p class="twitter-message">';

          if ($hyperlinks) { $msg = hyperlinks($msg); }

          if ($twitter_users)  { $msg = twitter_users($msg); }
       					
					if ($linked != '' || $linked != false) {

            if($linked == 'all')  { 
		
              $msg = '<a href="'.$link.'" class="twitter-link">'.$msg.'</a>';  // Puts a link to the status of each tweet 
			  
            } else {

              $msg = $msg . '<a href="'.$link.'" class="twitter-link">'.$linked.'</a>'; // Puts a link to the status of each tweet    
			 
            }		

          } 
		  
          echo $msg;      

        if($update) {				

          $time = strtotime($message['pubdate']);   

          if ( ( abs( time() - $time) ) < 86400 )

            $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );

          else

            $h_time = date(__('Y/m/d'), $time);

          echo sprintf( __('%s', 'twitter-for-wordpress'),' <span class="twitter-timestamp"><br /><abbr title="' . date(__('Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );

         }          

					if ($list) echo '</li>'; elseif ($num != 1) echo '</p>';

					$i++;
					
					if ( $i >= $num ) break;

				
				}

			}		

		}

		if ($list) echo '</ul>';

						echo '<div class="designed" style="font-size:8px; text-align: center;">'; //please don`t remove copyright link developer. Thank you!

                      $name = "Realizare site web"; //please don`t remove copyright link developer. Thank you!

            $url = "http://www.dianysmedia.info/realizare-site-web/"; //please don`t remove copyright link developer. Thank you!

         $output = 'Developed by:  <a href="' . $url . '" title="Realizare site web">' . $name . '</a>'; //please don`t remove copyright link developer. Thank you!

     echo $output; //please don`t remove copyright link developer. Thank you!

   echo '</div>'; //please don`t remove copyright link developer. Thank you!

   echo '</div>'; //please don`t remove copyright link developer. Thank you!

	}

// Link discover

function hyperlinks($text) {

    // match protocol://address/path/file.extension?some=variable&another=asf%

    //$text = preg_replace("/\b([a-zA-Z]+:\/\/[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)\b/i","<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);

    $text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);

    // match www.something.domain/path/file.extension?some=variable&another=asf%

    //$text = preg_replace("/\b(www\.[a-z][a-z0-9\_\.\-]*[a-z]{2,6}[a-zA-Z0-9\/\*\-\?\&\%]*)\b/i","<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);

    $text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);    

    // match address

    $text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);

        //mach #trendingtopics.

    $text = preg_replace('/([\.|\,|\:|\�|\�|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text);

    return $text;

}

function twitter_users($text) {

       $text = preg_replace('/([\.|\,|\:|\�|\�|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);

       return $text;

}     

// Twitter widget

function widget_twitter_init() {

	if ( !function_exists('register_sidebar_widget') )

		return;

	$check_options = get_option('widget_twitter');

  if ($check_options['number']=='') {

    $check_options['number'] = 1;

    update_option('widget_twitter', $check_options);

  }

	function widget_twitter($args, $number = 1) {

		global $twitter_options;		

		// $args is an array of strings

		// the active theme: before_widget, before_title, after_widget,

		// and after_title are the array keys. Default tags: li and h2.

		extract($args);

		// Each widget can store its own options.

		include_once(ABSPATH . WPINC . '/rss.php');

		$options = get_option('widget_twitter');		

		// fill options with default values

		$item = $options[$number];

		foreach($twitter_options['widget_fields'] as $key => $field) {

			if (! isset($item[$key])) {

				$item[$key] = $field['default'];

			}

		}		

		$messages = fetch_rss('http://twitter.com/statuses/user_timeline/'.$item['username'].'.rss');

		// These lines generate our output.

    echo $before_widget . $before_title . '<a href="http://twitter.com/' . $item['username'] . '" class="twitter_title_link">'. $item['title'] . '</a>' . $after_title;

		twitter_messages($item['username'], $item['num'], true, $item['update'], $item['linked'], $item['hyperlinks'], $item['twitter_users'], $item['encode_utf8']);

		echo $after_widget;

	}

	// This is the function that outputs the form to let the users edit

	// the widget's title.

	function widget_twitter_control($number) {

		global $twitter_options;

		// Get our options and see form submission.

		$options = get_option('widget_twitter');

		if ( isset($_POST['twitter-submit']) ) {

			foreach($twitter_options['widget_fields'] as $key => $field) {

				$options[$number][$key] = $field['default'];

				$field_name = sprintf('%s_%s_%s', $twitter_options['prefix'], $key, $number);

				if ($field['type'] == 'text') {

					$options[$number][$key] = strip_tags(stripslashes($_POST[$field_name]));

				} elseif ($field['type'] == 'checkbox') {

					$options[$number][$key] = isset($_POST[$field_name]);

				}

			}

			update_option('widget_twitter', $options);

		}

		foreach($twitter_options['widget_fields'] as $key => $field) {			

			$field_name = sprintf('%s_%s_%s', $twitter_options['prefix'], $key, $number);

			$field_checked = '';

			if ($field['type'] == 'text') {

				$field_value = htmlspecialchars($options[$number][$key], ENT_QUOTES);

			} elseif ($field['type'] == 'checkbox') {

				$field_value = 1;

				if (! empty($options[$number][$key])) {

					$field_checked = 'checked="checked"';

				}

			}			

			printf('<p style="text-align:right;" class="twitter_field"><label for="%s">%s <input id="%s" name="%s" type="%s" value="%s" class="%s" %s /></label></p>',

				$field_name, __($field['label']), $field_name, $field_name, $field['type'], $field_value, $field['type'], $field_checked);

		}

		echo '<input type="hidden" id="twitter-submit" name="twitter-submit" value="1" />';

	}


	function widget_twitter_setup() {

		$options = $newoptions = get_option('widget_twitter');

		

		if ( isset($_POST['twitter-number-submit']) ) {

			$number = (int) $_POST['twitter-number'];

			$newoptions['number'] = $number;

		}

		

		if ( $options != $newoptions ) {

			update_option('widget_twitter', $newoptions);

			widget_twitter_register();

		}

	}	

	function widget_twitter_page() {

		$options = $newoptions = get_option('widget_twitter');

	?>


		<div class="wrap">

			<form method="POST">

				<h2><?php _e('Twitter Widgets'); ?></h2>

				<p style="line-height: 30px;"><?php _e('How many Twitter widgets would you like?'); ?>

				<select id="twitter-number" name="twitter-number" value="<?php echo $options['number']; ?>">

	<?php for ( $i = 1; $i < 10; ++$i ) echo "<option value='$i' ".($options['number']==$i ? "selected='selected'" : '').">$i</option>"; ?></select>

				<span class="submit"><input type="submit" name="twitter-number-submit" id="twitter-number-submit" value="<?php echo attribute_escape(__('Save')); ?>" /></span></p>

	
			</form>

		</div>

	<?php

	}	

	function widget_twitter_register() {		

		$options = get_option('widget_twitter');

		$dims = array('width' => 300, 'height' => 300);

		$class = array('classname' => 'widget_twitter');

		for ($i = 1; $i <= 9; $i++) {

			$name = sprintf(__('WP Lauro-Twitter Feed'), $i);

			$id = "twitter-$i"; // Never translate an id

			wp_register_sidebar_widget($id, $name, $i <= $options['number'] ? 'widget_twitter' : /* unregister */ '', $class, $i);

			wp_register_widget_control($id, $name, $i <= $options['number'] ? 'widget_twitter_control' : /* unregister */ '', $dims, $i);

		}

		

		add_action('sidebar_admin_setup', 'widget_twitter_setup'); //setup

		add_action('sidebar_admin_page', 'widget_twitter_page'); //page

	}



	widget_twitter_register();

}

// Run our code later in case this loads prior to any other plugins.

add_action('widgets_init', 'widget_twitter_init');

?>
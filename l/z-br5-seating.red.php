<?php

register_activation_hook(__FILE__, 'db_install');

function db_install () {
   	global $wpdb;

   	$tn_seats = $wpdb->prefix . "br5seating_seats";
   	if($wpdb->get_var("SHOW TABLES LIKE '$tn_seats'") != $tn_seats) {
 		$sql = "CREATE TABLE " . $tn_seats . " (
	 	token char(8) NOT NULL PRIMARY KEY,
	  	seat char(3) NOT NULL UNIQUE
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
   	$tn_gamers = $wpdb->prefix . "br5seating_gamers";
   	if($wpdb->get_var("SHOW TABLES LIKE '$tn_gamers'") != $tn_gamers) {
 		$sql = "CREATE TABLE " . $tn_gamers . " (
	 	token char(8) NOT NULL PRIMARY KEY,
	  	fname varchar(255) NOT NULL,
	  	lname varchar(255) NOT NULL,
	  	email varchar(255) NOT NULL,
		order_date date NOT NULL,
		order_num char(8) NOT NULL,
		attendee_num char(8) NOT NULL
		);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

if (!function_exists('add_action')) {
	require_once("../../../wp-config.php");
}

if (!class_exists("br5seating")) {
	class br5seating {
		
		var $shortcode = "[br5-seating]";
		var $cur_gamer;
		var $cur_seat;
		var $is_root;
		
		function addContent($content = '')  {
			global $wpdb;
			if (strPos($content, $this->shortcode) !== FALSE) {
$this->is_root = current_user_can('level_10');

$token = strToUpper($_SERVER['REQUEST_METHOD']) == 'POST' ? $_POST['gamer'] : $_GET['gamer'];

$this->cur_gamer = $token == '' ? NULL : $wpdb->get_row(
  "SELECT * FROM {$wpdb->prefix}br5seating_gamers WHERE ty='pc' AND token='"
  . mysql_real_escape_string($token) . "'", OBJECT);
$this->cur_seat = null;
				
$error = FALSE;
if (strToUpper($_SERVER['REQUEST_METHOD']) == 'POST') {
	switch ($_POST['act']) {
		case 'select':
			$seat_selected = $wpdb->get_results(
			  "SELECT * FROM {$wpdb->prefix}br5seating_seats WHERE token='"
			  . mysql_real_escape_string($token) . "'", OBJECT);
			if ($seat_selected) {
				$error = FALSE === $wpdb->update("{$wpdb->prefix}br5seating_seats",
				  array('seat' => $_POST['seat']),
				  array('token' => $_POST['gamer']));
			} else {
				$error = FALSE === $wpdb->insert("{$wpdb->prefix}br5seating_seats",
				  array('seat' => $_POST['seat'], 'token' => $_POST['gamer']));
			}

			if ($error) {
				$output .= '<p style="background-color:#800;border:2px solid red;'
				  . 'padding:1em;"><strong>There was an error processing your '
				  . 'submission.  Please contact <a href="mailto:jeffrey&#64;'
				  . 'battleroyale.ca">Jeffrey</a> for help.</strong></p>';
			} else {
				$output .= sPrintF('<p>Thank you, you are now confirmed for seat '
				  . '%2$s.</p>'
				  . '<ul><li><a href="%3$s">Return to seating chart</a>.</li>'
				  . '<li><a href="../tournaments/">Go to tournament registration</a></li></ul>',
				  $this->cur_gamer->fname, $_POST['seat'], $_SERVER['REQUEST_URI']);
			}
	}
} else {

$output = '

<script type="text/javascript">

function validate(frm) {
	var selected = false;
	for (var i = 0; i < frm.seat.length; i++) {
		if (frm.seat[i].checked) {
			selected = true;
			break;
		}
	}
	if (!selected) {
		alert("You must select a white circle to specify your seat.");
		return false;
	}
	return true;
}

var sheet = document.createElement("style")
sheet.innerHTML = "#br5seating .submit {display : none;}";
document.body.appendChild(sheet);

</script>

';

$reserved_seats = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}br5seating_seats "
  . "INNER JOIN {$wpdb->prefix}br5seating_gamers USING (token)", OBJECT);
$res_seats = array();
$seats_rvd = 0;
$seats_occ = 0;
foreach ($reserved_seats as $seat) {
	$res_seats[$seat->seat] = $seat->fname . ' ' . $seat->lname;
	if ($res_seats[$seat->seat] == ' Reserved') {
		$seats_rvd++;
	} else {
		$seats_occ++;
	}
	if ($seat->token == $this->cur_gamer->token) {
		$this->cur_seat = $seat->seat;
	}
}
if ($this->is_root) {
	$output .= sPrintF('<p>Occupied: %1$s, Reserved: %2$s, Free: %3$s</p><p>',
	  $seats_occ, $seats_rvd, 179 - $seats_occ - $seats_rvd);
	$seats_no = array_keys($res_seats);
	natSort($seats_no);
	foreach ($seats_no as $seat) {
		$output .= $seat . ', ';
	}
	$output .= '</p>';
}

if (isSet($this->cur_gamer)) {
	$output .= sPrintF('<p style="background-color:#080;border:2px solid lime;padding:1em;">'
	  . '<strong>Gamer: %1$s %2$s, Seat Status: %5$s</strong></p>'
	  . '<p>Hello %1$s, you %3$s.  Click on a white circle below to %4$s.</p>'
	  . '<p>Red seats are occupied and you can hover over them to see who has chosen that seat.'
	  . '  Gray seats are reserved and will be released closer to the event.</p>',
		$this->cur_gamer->fname, $this->cur_gamer->lname,
		isSet($this->cur_seat) ? 'are confirmed for seat ' . $this->cur_seat
		  . ', indicated in lime green' : 'have not yet chosen your seat',
		isSet($this->cur_seat) ? '<strong>change</strong> your seat' : 'select your seat',
		isSet($this->cur_seat) ? 'Confirmed in seat ' . $this->cur_seat : 'Unconfirmed');
} else {
	$output .= '<p style="background-color:#808000;border:2px solid yellow;padding:1em;">'
	  . '<strong>Note:</strong> Seat registration can only be done after a ticket has'
	  . ' been purchased.  If you have purchased a ticket, please use the unique link we sent '
	  . 'you to select your seat.  Please allow up to 12 hours for us to send you your personzalized '
	  . 'link.  Please contact <a href="mailto:jeffrey&#64;'
	  . 'battleroyale.ca">Jeffrey</a> if you need help.</p><p>Green seats are '
	  . 'currently vacant while red seats are occupied and gray seast are reserved.</p>';
}

$output .= $this->genSeatChart($res_seats);

}

$content = str_replace($this->shortcode, '</p>' . $output . '<p>', $content);
			}
			return $content;
		}
	}
}

$wptr = new br5seating();
add_filter('the_content', array(&$wptr, 'addContent')); 

?>

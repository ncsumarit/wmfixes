/*
 * Plugin Name:       Wernick Method Fixes
 * Plugin URI:        https://maritdigital.com
 * Description:       Plugin for custom fixes across themes
 * Version:           0.1
 * Author:            Jen McFarland
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wmfixes
 */


/* Anchor jump on Class Registration submissions */
add_filter( 'gform_confirmation_anchor_22', '__return_true' );

/* Rewrite date output on GF */
add_filter( 'gform_merge_tag_filter', function ( $value, $merge_tag, $options, $field, $raw_value, $format ) {
    if ( $field->type == 'date' ) {
 
        return date_i18n( 'd M Y ', strtotime( $raw_value ) );
    }
 
    return $value;
}, 10, 6 );

/* Redirect on login based on role */ 

function my_login_redirect( $url, $request, $user ) {
	//is there a user to check?
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for contribs
		if ( in_array( 'contributor', $user->roles ) ) {
			// redirect them to the default place
			$url = 'https://wernickmethod.org/welcome-teacher';
		} else {
			$url = home_url();
		}
	} else {
		$url = home_url();
	}
    return $url;
}

add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );

/* Change charge description for Stripe entries */

add_filter( 'gform_stripe_charge_description', 'wm_stripe_desc', 10, 3 );

function wm_stripe_desc( $description,$strings,$entry ) {
  return $description . ' - ' . rgar( $entry, '1.3' ) . ' ' . rgar( $entry, '1.6' );
}
/* Enable check on conditional logic before sending notifications 
   read more: https://gravitywiz.com/documentation/gpns_evaluate_conditional_logic_on_send/
*/
add_filter( 'gpns_evaluate_conditional_logic_on_send', '__return_true' );

/* Alternate 'Other' text in Gravity Forms for Teacher Supply Order Form */

add_filter( 'gform_other_choice_value', 'set_other_choice_value', 10, 2 );
function set_other_choice_value( $value, $field ) {
    if ( is_object( $field ) && 43 === $field->id && 7 === $field->formId ) {
        $value = 'Enter Qty';
    }
    return $value;
}

/* teacher username output (menu URL) */
function teacherusername_output_wp_menu( $menu_items ) {
    global $current_user;
    foreach ( $menu_items as $menu_item ) {
        if ( strpos( $menu_item->url, 'teacherUsername' ) !== false ) {
            // Get username, otherwise set it to blank.
            if ( $current_user->user_login ) {
                $username = $current_user->user_login;
            } else {
                $username = '';
            }
                $menu_item->url =  str_replace( 'teacherUsername',  $username, $menu_item->url );
        }
    }
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'teacherusername_output_wp_menu' );

/* teacher name output (menu title) */
function teachername_output_wp_menu( $menu_items ) {
    global $current_user;
    foreach ( $menu_items as $menu_item ) {
        if ( strpos( $menu_item->title, 'teacherName' ) !== false ) {
            // Get username, otherwise set it to blank.
            if ( $current_user->display_name ) {
                $username = $current_user->display_name;
            } else {
                $username = '';
            }
                $menu_item->title =  str_replace( 'teacherName',  $username, $menu_item->title );
        }
    }
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'teachername_output_wp_menu' );

/**
 * Gravity Wiz // Gravity Forms // Dynamically Populating User Role
 * https://gravitywiz.com/dynamically-populating-user-role/
 *
 * Use this snippet in conjunction with Gravity Forms dynamic population
 * functionality to populate the current user’s role into any form field.
 *
 * @version  1.0
 * @author   David Smith <david@gravitywiz.com>
 * @license  GPL-2.0+
 * @link     http://gravitywiz.com/
 */
add_filter( 'gform_field_value_user_role', 'gform_populate_user_role' );
function gform_populate_user_role( $value ) {
	$user = wp_get_current_user();
	$role = $user->roles;
	return reset( $role );
}

?>

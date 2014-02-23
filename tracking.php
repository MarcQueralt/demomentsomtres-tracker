<?php
/**
 * Fitxer del web service pel tracking 
 */
include_once 'wp-load.php';
$dades=$_POST['body'];
$titol = $dades['site']['system'] . '-' . $dades['site']['hash'] . '-' . date('U') . '-' . $dades['site']['event'];
echo print_r($titol,true)."\n";
$post = array(
  //'ID'             => [ <post id> ] // Are you updating an existing post?
  'post_content'   => json_encode($dades),
  //'post_name'      => [ <string> ] // The name (slug) for your post
  'post_title'     => $titol,
  'post_status'    => 'private',
  'post_type'      => 'dms3_tracking',
  //'post_author'    => [ <user ID> ] // The user ID number of the author. Default is the current user ID.
  'ping_status'    => 'closed',
  //'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
  //'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
  //'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
  //'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
  //'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
  //'guid'           => // Skip this and let Wordpress handle it, usually.
  //'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
  //'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
  'post_date'      => date('Y-m-d H:i:s'),
  //'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
  //'comment_status' => [ 'closed' | 'open' ] // Default is the option 'default_comment_status', or 'closed'.
  //'post_category'  => [ array(<category id>, ...) ] // Default empty.
  //'tags_input'     => [ '<tag>, <tag>, ...' | array ] // Default empty.
  //'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
  //'page_template'  => [ <string> ] // Default empty.
); 
wp_insert_post( $post, $wp_error ); 
exit();
?>

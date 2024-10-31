<?php
/**
 * Admin notice for link delete.
 *
 * @since    1.0.0
 */
function quick_admin_truncate_string( $string, $length = 20 , $dots = "..." ) {

    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;

}

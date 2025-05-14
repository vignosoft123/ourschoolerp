<?php
function get_subdomain() {
    $host = $_SERVER['HTTP_HOST']; // abc.ourschoolerp.com
    $parts = explode('.', $host);
    return count($parts) >= 3 ? $parts[0] : null;
}

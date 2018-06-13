<?php

if (!function_exists('dd')) {
    function dd($args)
    {
        var_dump(...$args);
        exit;
    }
}

function td( $args ) {
	tp( $args );
	exit;
}

function tp( $args ) {
	echo '<pre>';
	print_r( $args );
	echo '</pre>';
}

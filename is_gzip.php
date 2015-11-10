<?php
stream_context_set_default(
    array(
        'http' => array(
            'header'=>"Accept-Encoding: gzip"
        )
    )
);
$headers = get_headers('http://hitechanalogy.com');
var_dump($headers);
?>
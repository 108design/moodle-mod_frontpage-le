<?php
defined('MOODLE_INTERNAL') || die();
$callbacks=[];

// Product identity on native administration and upgrade surfaces.
$callbacks[] = ['hook'=>\core\hook\output\before_standard_head_html_generation::class,
    'callback'=>[\mod_frontpage\local\admin_branding::class,'head']];
$callbacks[] = ['hook'=>\core\hook\output\before_http_headers::class,
    'callback'=>[\mod_frontpage\local\admin_branding::class,'prepare']];

<?php
define('WP_INSTALLING', true);
require_once(dirname(dirname(__FILE__)) . '/flag-config.php');
require_once(dirname(dirname(__FILE__)) . '/lib/core.php');
require_once(dirname(__FILE__) . '/skin_functions.php');

if( !function_exists('wp_get_current_user')){
    require(ABSPATH . WPINC . '/formatting.php');
    require(ABSPATH . WPINC . '/capabilities.php');
    require(ABSPATH . WPINC . '/user.php');
    require(ABSPATH . WPINC . '/meta.php');
    require(ABSPATH . WPINC . '/pluggable.php');
    require(ABSPATH . WPINC . '/post.php');
    wp_cookie_constants();
}

// check for correct capability
if( !is_user_logged_in()){
    die('-1');
}

// check for correct FlAG capability
if( !current_user_can('FlAG Change skin')){
    die('-1');
}

$flag_options = get_option('flag_options');
$act_skin     = isset($_GET['skin'])? $_GET['skin'] : $flag_options['flashSkin'];
$act_skin     = sanitize_flagname($act_skin);

if(isset($_GET['show_options'])){
    ?>
    <!doctype html>
    <html>
    <head>
        <link rel='stylesheet' id='common-css' href='<?php echo get_admin_url(null, '/css/common.css'); ?>' type='text/css'/>
        <link rel='stylesheet' id='forms-css' href='<?php echo includes_url('/css/buttons.css'); ?>' type='text/css'/>
        <link rel="stylesheet" id="flagadmin-css" href="<?php echo plugins_url('/flash-album-gallery/admin/css/flagadmin.css'); ?>" type="text/css"/>
        <link rel='stylesheet' id='wp-color-picker-css' href='<?php echo get_admin_url(null, '/css/color-picker.css'); ?>' type='text/css' media='all'/>
        <script type='text/javascript' src='<?php echo includes_url('/js/jquery/jquery.js'); ?>'></script>
        <script type='text/javascript' src='<?php echo includes_url('/js/jquery/jquery-migrate.js'); ?>'></script>
    </head>
    <body>
    <?php flag_skin_options($act_skin); ?>

    <script type='text/javascript' src='<?php echo get_admin_url(null, '/js/svg-painter.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/core.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/widget.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/mouse.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/draggable.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/droppable.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/sortable.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/jquery.ui.touch-punch.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo includes_url('/js/jquery/ui/slider.min.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo get_admin_url(null, '/js/iris.min.js'); ?>'></script>
    <script type='text/javascript'>
        /* <![CDATA[ */
        var wpColorPickerL10n = {
            "clear": "Clear",
            "defaultString": "Default",
            "pick": "Select Color",
            "current": "Current Color"
        };
        /* ]]> */
    </script>
    <script type='text/javascript' src='<?php echo get_admin_url(null, '/js/color-picker.js'); ?>'></script>
    </body>
    </html>
    <?php
} ?>
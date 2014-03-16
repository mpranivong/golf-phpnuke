<?php
// Used to style the TinyMCE editor
add_editor_style('css/custom-editor-style.css');

// content width
if ( ! isset( $content_width ) ) $content_width = 460;

// translation
load_theme_textdomain( 'wappos', get_template_directory().'/languages' );

// automatic feed links
add_theme_support('automatic-feed-links');

// navigation
add_theme_support( 'menus' );
register_nav_menu('header_menu', 'Header Menu');
register_nav_menu('top_menu', 'Top Menu');

// thumbnails
add_theme_support( 'post-thumbnails' );
set_post_thumbnail_size( 135, 9999 );

// background
add_custom_background();

// widget
add_action( 'widgets_init', 'my_register_sidebars' );

function my_register_sidebars() {
	register_sidebar(
		array(
			'id' => 'left_sidebar',
			'name' => __('Left Sidebar', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s sidecolumn">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
			)
		);
	register_sidebar(
		array(
			'id' => 'right_sidebar',
			'name' => __('Right Sidebar', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s sidecolumn">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_1',
			'name' => __('Footer 1', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_2',
			'name' => __('Footer 2', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_3',
			'name' => __('Footer 3', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		)
	);
	register_sidebar(
		array(
			'id' => 'footer_4',
			'name' => __('Footer 4', 'wappos'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' =>'</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		)
	);
}


// header image
function wappos_admin_header_style() {
	$h_img = get_header_image();
	if($h_img) {
    ?><style type="text/css">
        #headimg {
            width: <?php echo HEADER_IMAGE_WIDTH; ?>px;
            height: <?php echo HEADER_IMAGE_HEIGHT; ?>px;
        }
    </style><?php
    }
}

define('HEADER_IMAGE', '%s/images/default_logo.png');
define('HEADER_IMAGE_WIDTH', 160);
define('HEADER_IMAGE_HEIGHT', 90);
define('HEADER_TEXTCOLOR', '555');
define( 'NO_HEADER_TEXT', true );

add_custom_image_header('', 'wappos_admin_header_style');

// style
function wappos_add_stylesheet() {
	wp_enqueue_style('print_style', get_template_directory_uri() . '/css/print.css', array(), '', 'print');
	wp_enqueue_style('droppy-top', get_template_directory_uri() . '/css/droppy-top.css', array());
	wp_enqueue_style('droppy-header', get_template_directory_uri() . '/css/droppy-header.css', array());
}
add_action('wp_print_styles', 'wappos_add_stylesheet');

// script
function wappos_add_scripts() {
	wp_enqueue_script('DD_belatedPNG', get_template_directory_uri() . '/js/DD_belatedPNG_0.0.8a-min.js');
	wp_enqueue_script('minmax', get_template_directory_uri() . '/js/minmax.js');
	wp_enqueue_script('droppy', get_template_directory_uri() . '/js/jquery.droppy.js', array('jquery'));
	wp_enqueue_script('commonjs', get_template_directory_uri() . '/js/common.js');
}
add_action('wp_print_scripts', 'wappos_add_scripts');

// excerpt
function wappos_excerpt_more($post) {
	global $post;
	return ' ... <a href="'. get_permalink($post->ID) . '" title="' . get_the_title($post->ID) . '">' . ' more &raquo; ' . '</a>';	
}	
add_filter('excerpt_more', 'wappos_excerpt_more');

// comment
function wappos_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>">
	<div class="comment-author vcard">
	<?php echo get_avatar($comment,$size='32',$default='<path_to_url>' ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
	<?php if ($comment->comment_approved == '0') : ?>
	<em><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
	<?php endif; ?>
	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','') ?></div>
	<?php comment_text() ?>
	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	</div>
<?php
}

?>

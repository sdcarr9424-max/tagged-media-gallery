<?php
/**
 * Plugin Name: Tagged Media Gallery
 * Description: Flexible tag-based image grid & slider. Built for a nonprofit art organization. Free forever — donations support independent artists.
 * Version: 1.0.0
 * Author: Sheila Carr
 * Author URI: https://www.weehours.studio
 * License: GPL2+
 */

if (!defined('ABSPATH')) exit;

define('TMG_VERSION', '1.0.0');

/* ======================================================
   TAXONOMY
   ====================================================== */

add_action('init', function () {
    register_taxonomy('media_tag', 'attachment', [
        'label'             => 'Media Tags',
        'public'            => true,
        'hierarchical'      => false,
        'show_admin_column' => true,
        'rewrite'           => ['slug' => 'media-tag'],
        'show_in_rest'      => true,
    ]);
});

/* ======================================================
   DEFAULT SETTINGS
   ====================================================== */

function tmg_defaults() {
    return [
        'grid_columns'      => 3,
        'grid_frame'        => 1,
        'grid_show_title'   => 1,
        'grid_show_caption' => 0,
        'grid_order'        => 'random',
        'slider_speed'      => 8,
        'slider_fade'       => 2,
        'slider_frame'      => 1,
        'slider_show_title' => 1,
        'slider_show_caption'=>0,
        'slider_autoplay'   => 1,
        'slider_order'      => 'random',
        'slider_nav'        => 0,
        'slider_keyboard'   => 0,
        /* NEW */
        'frame_color'       => 'black',
        'slider_frame_color' => 'black',
		'frame_custom_color' => '#000000',
        'slider_frame_custom_color' => '#000000',
		'grid_mat_color' => '#ffffff',
        'slider_mat_color' => '#ffffff',
		'grid_mat_mode' => 'white',
        'slider_mat_mode' => 'white',
        'grid_mat_custom_color' => '#ffffff',
        'slider_mat_custom_color' => '#ffffff',
        'slider_height' => 'medium',
		'slider_text_position' => 'overlay',
        'grid_text_position'   => 'overlay',
		'mobile_text_position' => 'below',
		'slider_overlay_always' => 0,
		'grid_overlay_always' => 0,
		'grid_mat'   => 1,
        'slider_mat' => 1,
		
    ];
}

function tmg_get_settings() {
    return wp_parse_args(get_option('tmg_settings', []), tmg_defaults());
}

/* ======================================================
   SETTINGS PAGE
   ====================================================== */

add_action('admin_menu', function () {
    add_menu_page(
        'Tagged Media Gallery',
        'Tagged Media',
        'manage_options',
        'tmg-settings',
        'tmg_settings_page',
        'dashicons-format-gallery',
        25
    );
});

add_action('admin_init', function () {
    register_setting('tmg_settings_group', 'tmg_settings', function($input){
        return [
            'grid_columns'      => intval($input['grid_columns'] ?? 3),
            'grid_frame'        => isset($input['grid_frame']) ? 1 : 0,
            'grid_show_title'   => isset($input['grid_show_title']) ? 1 : 0,
            'grid_show_caption' => isset($input['grid_show_caption']) ? 1 : 0,
            'grid_order'        => ($input['grid_order'] ?? '') === 'alpha' ? 'alpha' : 'random',

            'slider_speed'      => max(2, intval($input['slider_speed'] ?? 8)),
            'slider_fade'       => max(0.5, floatval($input['slider_fade'] ?? 2)),
            'slider_frame'      => isset($input['slider_frame']) ? 1 : 0,
            'slider_show_title' => isset($input['slider_show_title']) ? 1 : 0,
            'slider_show_caption'=>isset($input['slider_show_caption']) ? 1 : 0,
            'slider_autoplay'   => isset($input['slider_autoplay']) ? 1 : 0,
            'slider_order'      => ($input['slider_order'] ?? '') === 'alpha' ? 'alpha' : 'random',
            'slider_nav'        => isset($input['slider_nav']) ? 1 : 0,
            'slider_keyboard'   => isset($input['slider_keyboard']) ? 1 : 0,
			'grid_mat'   => isset($input['grid_mat']) ? 1 : 0,
            'slider_mat' => isset($input['slider_mat']) ? 1 : 0,
			
            'frame_color' => in_array(($input['frame_color'] ?? 'black'), ['black','brown','white','custom'])
    ? $input['frame_color']
    : 'black',
            'slider_frame_color' => in_array(($input['slider_frame_color'] ?? 'black'), ['black','brown','white','custom'])
    ? $input['slider_frame_color']
    : 'black',
			'frame_custom_color' => sanitize_hex_color($input['frame_custom_color'] ?? '#000000'),
'slider_frame_custom_color' => sanitize_hex_color($input['slider_frame_custom_color'] ?? '#000000'),
			'grid_mat_color' => sanitize_hex_color($input['grid_mat_color'] ?? '#ffffff'),
'slider_mat_color' => sanitize_hex_color($input['slider_mat_color'] ?? '#ffffff'),
			'grid_mat_mode' => in_array(($input['grid_mat_mode'] ?? 'white'), ['white','black','custom'])
    ? $input['grid_mat_mode']
    : 'white',

'slider_mat_mode' => in_array(($input['slider_mat_mode'] ?? 'white'), ['white','black','custom'])
    ? $input['slider_mat_mode']
    : 'white',

'grid_mat_custom_color' => sanitize_hex_color($input['grid_mat_custom_color'] ?? '#ffffff'),
'slider_mat_custom_color' => sanitize_hex_color($input['slider_mat_custom_color'] ?? '#ffffff'),
			
             'slider_height' => in_array(($input['slider_height'] ?? 'medium'), ['small','medium','large'])
    ? $input['slider_height']
    : 'medium',
			'slider_text_position' => in_array(($input['slider_text_position'] ?? 'overlay'), ['overlay','below'])
    ? $input['slider_text_position']
    : 'overlay',

'grid_text_position' => in_array(($input['grid_text_position'] ?? 'overlay'), ['overlay','below'])
    ? $input['grid_text_position']
    : 'overlay',
			'slider_overlay_always' => isset($input['slider_overlay_always']) ? 1 : 0,
			'grid_overlay_always' => isset($input['grid_overlay_always']) ? 1 : 0,
			'mobile_text_position' => in_array(($input['mobile_text_position'] ?? 'below'), ['overlay','below'])
    ? $input['mobile_text_position']
    : 'below',
			
        ];
    });
});

function tmg_settings_page() {
    $opts = tmg_get_settings();
?>

<div class="wrap tmg-admin">
<h1>Tagged Media Gallery</h1>

<form method="post" action="options.php">
<?php settings_fields('tmg_settings_group'); ?>

	
<div class="tmg-columns">
	
	<div class="tmg-full-width">
<h2>💛 Built for a Nonprofit</h2>
<p>
Tagged Media Gallery was built for the Gateway Art Gallery, a nonprofit art gallery in Lake City, Florida.
<br>
This plugin is free forever. If it helps you, please consider making a donation. Thank you!
</p>
<p>
<a href="https://www.paypal.com/donate/?hosted_button_id=US7CYBDSUEQDU" target="_blank" class="button button-primary">
Support this project
</a>
</p>
<p style="opacity:.7;font-size:13px;">
Developed by Sheila Carr — www.weehours.studio
</p>
	  </div>  
	
  <div class="tmg-column">
	  
<p>==========================</p>
<h2>GRID Settings</h2>
<p>==========================</p>
	
<div class="tmg-section">
<label>Columns
<input type="number" name="tmg_settings[grid_columns]" value="<?php echo esc_attr($opts['grid_columns']); ?>" min="1" max="6">
</label>

<label><input type="checkbox" name="tmg_settings[grid_frame]" value="1" <?php checked($opts['grid_frame'],1); ?>> Show Frame</label>

<label>Frame Color</label>
<div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
  <select name="tmg_settings[frame_color]" class="tmg-frame-select">
    <option value="black" <?php selected($opts['frame_color'],'black'); ?>>Black</option>
    <option value="brown" <?php selected($opts['frame_color'],'brown'); ?>>Brown</option>
    <option value="white" <?php selected($opts['frame_color'],'white'); ?>>White</option>
    <option value="custom" <?php selected($opts['frame_color'],'custom'); ?>>Custom</option>
  </select>
 or (if custom)
  <input type="text"
         class="tmg-color-field"
         name="tmg_settings[frame_custom_color]"
         value="<?php echo esc_attr($opts['frame_custom_color']); ?>">
</div>

<label>
  <input type="checkbox"
         name="tmg_settings[grid_mat]"
         value="1"
         <?php checked($opts['grid_mat'],1); ?>>
  Show Mat (if Frame enabled)
</label>	
<label>Mat Color</label>

<div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
  <select name="tmg_settings[grid_mat_mode]" class="tmg-grid-mat-select">
    <option value="white" <?php selected($opts['grid_mat_mode'],'white'); ?>>White</option>
    <option value="black" <?php selected($opts['grid_mat_mode'],'black'); ?>>Black</option>
    <option value="custom" <?php selected($opts['grid_mat_mode'],'custom'); ?>>Custom</option>
  </select>
 or (if custom)
  <input type="text"
         class="tmg-color-field"
         name="tmg_settings[grid_mat_custom_color]"
         value="<?php echo esc_attr($opts['grid_mat_custom_color']); ?>">
</div>
<label><input type="checkbox" name="tmg_settings[grid_show_title]" value="1" <?php checked($opts['grid_show_title'],1); ?>> Show Title</label>
<label><input type="checkbox" name="tmg_settings[grid_show_caption]" value="1" <?php checked($opts['grid_show_caption'],1); ?>> Show Caption</label>
<label>Text Position
<select name="tmg_settings[grid_text_position]">
  <option value="overlay" <?php selected($opts['grid_text_position'],'overlay'); ?>>
    Overlay on Hover
  </option>
  <option value="below" <?php selected($opts['grid_text_position'],'below'); ?>>
    Below Image
  </option>
</select>
</label>
	<label>
<input type="checkbox"
       name="tmg_settings[grid_overlay_always]"
       value="1"
       <?php checked($opts['grid_overlay_always'] ?? 0,1); ?>>
Always Show Overlay (disable hover)
</label>
	
<label>Order
<select name="tmg_settings[grid_order]">
<option value="random" <?php selected($opts['grid_order'],'random'); ?>>Random</option>
<option value="alpha" <?php selected($opts['grid_order'],'alpha'); ?>>Alphabetical</option>
</select>
</label>
    </div>
  </div>

  <div class="tmg-column">
<p>==========================</p>
<h2>SLIDER Settings</h2>
<p>==========================</p>
    <div class="tmg-section">

<div class="tmg-section">
<label><input type="checkbox" name="tmg_settings[slider_frame]" value="1" <?php checked($opts['slider_frame'],1); ?>> Show Frame</label>

	<label>Slider Frame Color</label>
<div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
  <select name="tmg_settings[slider_frame_color]" class="tmg-slider-frame-select">
    <option value="black" <?php selected($opts['slider_frame_color'],'black'); ?>>Black</option>
    <option value="brown" <?php selected($opts['slider_frame_color'],'brown'); ?>>Brown</option>
    <option value="white" <?php selected($opts['slider_frame_color'],'white'); ?>>White</option>
    <option value="custom" <?php selected($opts['slider_frame_color'],'custom'); ?>>Custom</option>
  </select>
	or (if custom)
  <input type="text"
         class="tmg-color-field"
         name="tmg_settings[slider_frame_custom_color]"
         value="<?php echo esc_attr($opts['slider_frame_custom_color']); ?>">
</div>

	<label>
  <input type="checkbox"
         name="tmg_settings[slider_mat]"
         value="1"
         <?php checked($opts['slider_mat'],1); ?>>
  Show Mat (if Frame enabled)
</label>
<label>Slider Mat Color</label>

<div style="display:flex; align-items:center; gap:12px; margin-bottom:6px;">
  <select name="tmg_settings[slider_mat_mode]" class="tmg-slider-mat-select">
    <option value="white" <?php selected($opts['slider_mat_mode'],'white'); ?>>White</option>
    <option value="black" <?php selected($opts['slider_mat_mode'],'black'); ?>>Black</option>
    <option value="custom" <?php selected($opts['slider_mat_mode'],'custom'); ?>>Custom</option>
  </select>
 or (if custom)
  <input type="text"
         class="tmg-color-field"
         name="tmg_settings[slider_mat_custom_color]"
         value="<?php echo esc_attr($opts['slider_mat_custom_color']); ?>">
</div>
	
<label><input type="checkbox" name="tmg_settings[slider_show_title]" value="1" <?php checked($opts['slider_show_title'],1); ?>> Show Title</label>
<label><input type="checkbox" name="tmg_settings[slider_show_caption]" value="1" <?php checked($opts['slider_show_caption'],1); ?>> Show Caption</label>
	<label>Text Position
<select name="tmg_settings[slider_text_position]">
<option value="overlay" <?php selected($opts['slider_text_position'],'overlay'); ?>>Overlay on Hover</option>
<option value="below" <?php selected($opts['slider_text_position'],'below'); ?>>Below Image</option>
</select>
</label>
	<label>
<input type="checkbox"
       name="tmg_settings[slider_overlay_always]"
       value="1"
       <?php checked($opts['slider_overlay_always'],1); ?>>
Always Show Overlay (disable hover)
</label>
	<label>Mobile Text Position
<select name="tmg_settings[mobile_text_position]">
<option value="overlay" <?php selected($opts['mobile_text_position'],'overlay'); ?>>Overlay (Always Visible)</option>
<option value="below" <?php selected($opts['mobile_text_position'],'below'); ?>>Below Image</option>
</select>
</label>
	
<label><input type="checkbox" name="tmg_settings[slider_autoplay]" value="1" <?php checked($opts['slider_autoplay'],1); ?>> Autoplay</label>
<label><input type="checkbox" name="tmg_settings[slider_nav]" value="1" <?php checked($opts['slider_nav'],1); ?>> Back / Next Buttons</label>
	<label><input type="checkbox" name="tmg_settings[slider_keyboard]" value="1" <?php checked($opts['slider_keyboard'],1); ?>> Use Arrow Keys</label>

<label>Autoplay Slide Speed (seconds)
<input type="number" name="tmg_settings[slider_speed]" value="<?php echo esc_attr($opts['slider_speed']); ?>" min="2" max="30" style="width:70px;">
</label>

<label>Fade Duration (seconds)
<input type="number" step="0.1" name="tmg_settings[slider_fade]" value="<?php echo esc_attr($opts['slider_fade']); ?>" min="0.5" max="10" style="width:70px;">
</label>
<label>Slider Height
<select name="tmg_settings[slider_height]">
<option value="small" <?php selected($opts['slider_height'],'small'); ?>>Small</option>
<option value="medium" <?php selected($opts['slider_height'],'medium'); ?>>Medium</option>
<option value="large" <?php selected($opts['slider_height'],'large'); ?>>Large</option>
</select>
</label>

<label>Order
<select name="tmg_settings[slider_order]">
<option value="random" <?php selected($opts['slider_order'],'random'); ?>>Random</option>
<option value="alpha" <?php selected($opts['slider_order'],'alpha'); ?>>Alphabetical</option>
</select>
</label>
</div>

    </div>  <!-- close .tmg-section -->
  </div>    <!-- close .tmg-column -->
</div>      <!-- close .tmg-columns -->
	
<?php submit_button(); ?>
</form>

	<hr>

<p>==========================</p>
<h2>HOW TO USE</h2>
<p>==========================</p>

<p><strong>Basic Usage</strong></p>

<p>Display a grid:</p>
<code>[tagged_gallery tags="your-tag-slug"]</code>

<p>Display a slider:</p>
<code>[tagged_gallery type="slider" tags="your-tag-slug"]</code>

<p><strong>Override Settings Per Gallery</strong></p>

<p>You can override global settings inside any shortcode:</p>

<pre>
[tagged_gallery 
  type="slider"
  tags="featured"
  frame="true"
  mat="true"
  frame_color="brown"
  frame_custom_color="#4a2f1d"
  mat_color="#f5f5f5"
  nav="true"
  keyboard="true"
  text_position="below"
  overlay_always="true"
]
</pre>

<p><strong>Available Overrides</strong></p>

<ul>
<li><code>type="grid"</code> or <code>type="slider"</code></li>
<li><code>tags="slug-name"</code></li>
<li><code>frame="true|false"</code></li>
<li><code>mat="true|false"</code></li>
<li><code>frame_color="black|brown|white|custom"</code></li>
<li><code>frame_custom_color="#hexcode"</code></li>
<li><code>mat_color="#hexcode"</code></li>
<li><code>show_title="true|false"</code></li>
<li><code>show_caption="true|false"</code></li>
<li><code>text_position="overlay|below"</code></li>
<li><code>overlay_always="true|false"</code></li>
<li><code>nav="true|false"</code> (slider only)</li>
<li><code>keyboard="true|false"</code> (slider only)</li>
<li><code>columns="1-6"</code> (grid only)</li>
<li><code>height="small|medium|large"</code> (slider only)</li>
</ul>

<p style="opacity:.7;">
If no overrides are used, global settings will apply automatically.
</p>

</div>

<style>
	.tmg-admin .tmg-full-width {
  flex: 0 0 100%;
  margin-bottom: 20px;
}
	
.tmg-admin .tmg-section label { 
  display:block; 
  margin:4px 0; 
  font-weight:600;
}

.tmg-admin h2 { 
  margin-top:20px; 
}

/* === 2 Column Layout (ADMIN ONLY) === */

.tmg-admin .tmg-columns {
  display: flex;
  flex-wrap: wrap;   /* ← THIS FIXES IT */
  gap: 40px;
  align-items: flex-start;
  margin-top: 20px;
}

.tmg-admin .tmg-column {
  flex: 1;
  min-width: 340px;
}

@media (max-width: 1000px) {
  .tmg-admin .tmg-columns {
    flex-direction: column;
  }
}
</style>

<?php
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');
?>

<script>
jQuery(document).ready(function($){

  // Initialize color pickers
  $('.tmg-color-field').wpColorPicker();

});
</script>

<?php
}


/* ======================================================
   SHORTCODE
   ====================================================== */

function tmg_gallery_shortcode($atts) {

    $opts = tmg_get_settings();

$atts = shortcode_atts([
    'tags' => '',
    'type' => 'grid',
    'count' => 12,

    /* basic overrides */
    'frame' => '',
    'mat' => '',
    'autoplay' => '',
    'nav' => '',
    'keyboard' => '',
    'show_title' => '',
    'show_caption' => '',
    'text_position' => '',
    'overlay_always' => '',
    'columns' => '',
    'height' => '',

    /* color overrides */
    'frame_color' => '',
    'frame_custom_color' => '',
    'mat_color' => '',
], $atts, 'tagged_gallery');

    $type = ($atts['type'] === 'slider') ? 'slider' : 'grid';
	

/* ==========================================
   SHORTCODE OVERRIDES
   ========================================== */

$prefix = ($type === 'slider') ? 'slider_' : 'grid_';

/* Toggle overrides */
if ($atts['frame'] !== '') {
    $opts[$prefix.'frame'] = filter_var($atts['frame'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['mat'] !== '') {
    $opts[$prefix.'mat'] = filter_var($atts['mat'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['autoplay'] !== '' && $type === 'slider') {
    $opts['slider_autoplay'] = filter_var($atts['autoplay'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['nav'] !== '' && $type === 'slider') {
    $opts['slider_nav'] = filter_var($atts['nav'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['keyboard'] !== '' && $type === 'slider') {
    $opts['slider_keyboard'] = filter_var($atts['keyboard'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['show_title'] !== '') {
    $opts[$prefix.'show_title'] = filter_var($atts['show_title'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['show_caption'] !== '') {
    $opts[$prefix.'show_caption'] = filter_var($atts['show_caption'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

if ($atts['text_position'] !== '') {
    $opts[$prefix.'text_position'] = $atts['text_position'] === 'below' ? 'below' : 'overlay';
}

if ($atts['overlay_always'] !== '') {
    $opts[$prefix.'overlay_always'] =
        filter_var($atts['overlay_always'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
}

/* Layout overrides */
if ($atts['columns'] !== '' && $type === 'grid') {
    $opts['grid_columns'] = max(1, min(6, intval($atts['columns'])));
}

if ($atts['height'] !== '' && $type === 'slider') {
    if (in_array($atts['height'], ['small','medium','large'])) {
        $opts['slider_height'] = $atts['height'];
    }
}

/* Color overrides */
if ($atts['frame_color'] !== '') {
    $opts[$prefix.'frame_color'] = $atts['frame_color'];
}

if ($atts['frame_custom_color'] !== '') {
    $opts[$prefix.'frame_custom_color'] = sanitize_hex_color($atts['frame_custom_color']);
    $opts[$prefix.'frame_color'] = 'custom';
}

if ($atts['mat_color'] !== '') {
    $opts[$prefix.'mat_custom_color'] = sanitize_hex_color($atts['mat_color']);
    $opts[$prefix.'mat_mode'] = 'custom';
}	
	

    $args = [
        'post_type'=>'attachment',
        'post_status'=>'inherit',
        'post_mime_type'=>'image',
        'posts_per_page'=>intval($atts['count']),
        'orderby'=>'rand',
        'order'=>'ASC'
    ];

    if (!empty($atts['tags'])) {
        $args['tax_query']=[[ 
            'taxonomy'=>'media_tag',
            'field'=>'slug',
            'terms'=>sanitize_title($atts['tags'])
        ]];
    }

    $q = new WP_Query($args);
    if(!$q->have_posts()) return '';

    ob_start();

    if($type === 'slider'){

        echo '<div class="tmg-slider-wrapper tmg-textmode-'.$opts['slider_text_position'].'">';
echo '<div class="tmg-slider"
        data-autoplay="'.$opts['slider_autoplay'].'"
        data-speed="'.intval($opts['slider_speed'] * 1000).'"
        data-keyboard="'.$opts['slider_keyboard'].'"
		data-textmode="'.$opts['slider_text_position'].'"
		data-overlayalways="'.$opts['slider_overlay_always'].'"
		data-mobiletext="'.$opts['mobile_text_position'].'"
		>';

while($q->have_posts()){
    $q->the_post();

    echo '<div class="tmg-slide"
    data-title="'.esc_attr(get_the_title()).'"
    data-caption="'.esc_attr(wp_get_attachment_caption()).'">';

    // FRAME OR IMAGE
if ($opts['slider_frame']) {

if ($opts['slider_frame_color'] === 'custom') {

    echo '<div class="tmg-frame" style="background:'.$opts['slider_frame_custom_color'].';">';

} else {

    echo '<div class="tmg-frame tmg-frame-'.$opts['slider_frame_color'].'">';

}
    $slider_mat_color = '#ffffff';

if ($opts['slider_mat_mode'] === 'black') {
    $slider_mat_color = '#000000';
} elseif ($opts['slider_mat_mode'] === 'custom') {
    $slider_mat_color = $opts['slider_mat_custom_color'];
}

if ($opts['slider_mat']) {

    echo '<div class="tmg-mat" style="background:'.$slider_mat_color.';">';
    echo wp_get_attachment_image(get_the_ID(),'large', false, ['class'=>'tmg-image']);
    echo '</div>';

} else {

    echo wp_get_attachment_image(get_the_ID(),'large', false, ['class'=>'tmg-image']);

}

    // OVERLAY NOW LIVES HERE
    if ($opts['slider_text_position'] === 'overlay') {

        echo '<div class="tmg-overlay">';

        if ($opts['slider_show_title']) {
            echo '<div class="tmg-title">'.esc_html(get_the_title()).'</div>';
        }

        if ($opts['slider_show_caption']) {
            echo '<div class="tmg-caption">'.esc_html(wp_get_attachment_caption()).'</div>';
        }

        echo '</div>';
    }

echo '</div>'; // close .tmg-frame

} else {

    echo '<div class="tmg-image-wrapper">';
    echo wp_get_attachment_image(get_the_ID(),'large', false, ['class'=>'tmg-image']);

    // OVERLAY NOW LIVES HERE
    if ($opts['slider_text_position'] === 'overlay') {

        echo '<div class="tmg-overlay">';

        if ($opts['slider_show_title']) {
            echo '<div class="tmg-title">'.esc_html(get_the_title()).'</div>';
        }

        if ($opts['slider_show_caption']) {
            echo '<div class="tmg-caption">'.esc_html(wp_get_attachment_caption()).'</div>';
        }

        echo '</div>';
    }

    echo '</div>';
}


    echo '</div>'; // CLOSE .tmg-slide

}

echo '</div>'; // CLOSE .tmg-slider		
		
				        if($opts['slider_nav']){
            echo '<button class="tmg-prev">‹</button>';
            echo '<button class="tmg-next">›</button>';
        }
		
echo '</div>'; // CLOSE .tmg-slider-wrapper
		
// Always render below container (CSS decides visibility)
echo '<div class="tmg-text-below">';

if ($opts['slider_show_title']) {
    echo '<div class="tmg-title"></div>';
}

if ($opts['slider_show_caption']) {
    echo '<div class="tmg-caption"></div>';
}

echo '</div>';
    }

    else {

        echo '<div class="tmg-grid columns-'.$opts['grid_columns'].'"
      data-overlayalways="'.$opts['grid_overlay_always'].'">';

        while($q->have_posts()){
            $q->the_post();

echo '<div class="tmg-grid-item">';
echo '<div class="tmg-grid-square">';

if ($opts['grid_frame']) {

    // FRAME COLOR
    if ($opts['frame_color'] === 'custom') {
        echo '<div class="tmg-frame" style="background:'.$opts['frame_custom_color'].';">';
    } else {
        echo '<div class="tmg-frame tmg-frame-'.$opts['frame_color'].'">';
    }

    // MAT COLOR
    $grid_mat_color = '#ffffff';

    if ($opts['grid_mat_mode'] === 'black') {
        $grid_mat_color = '#000000';
    } elseif ($opts['grid_mat_mode'] === 'custom') {
        $grid_mat_color = $opts['grid_mat_custom_color'];
    }

if ($opts['grid_mat']) {

    echo '<div class="tmg-mat" style="background:'.$grid_mat_color.';">';
    echo wp_get_attachment_image(get_the_ID(),'medium_large', false, ['class'=>'tmg-image']);
    echo '</div>';

} else {

    echo wp_get_attachment_image(get_the_ID(),'medium_large', false, ['class'=>'tmg-image']);

}

echo '</div>'; // close frame

} else {

    // NO FRAME
    echo wp_get_attachment_image(get_the_ID(),'medium_large', false, ['class'=>'tmg-image']);

}
echo '</div>'; // close .tmg-grid-square
			
/* TEXT POSITION */

if ($opts['grid_text_position'] === 'overlay') {

    echo '<div class="tmg-overlay">';

    if ($opts['grid_show_title']) {
        echo '<div class="tmg-title">'.esc_html(get_the_title()).'</div>';
    }

    if ($opts['grid_show_caption']) {
        echo '<div class="tmg-caption">'.esc_html(wp_get_attachment_caption()).'</div>';
    }

    echo '</div>';

} else {

    echo '<div class="tmg-text-below-grid">';

    if ($opts['grid_show_title']) {
        echo '<div class="tmg-title">'.esc_html(get_the_title()).'</div>';
    }

    if ($opts['grid_show_caption']) {
        echo '<div class="tmg-caption">'.esc_html(wp_get_attachment_caption()).'</div>';
    }

    echo '</div>';
}

            echo '</div>';
        }

        echo '</div>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('tagged_gallery','tmg_gallery_shortcode');


/* ======================================================
   JS + CSS (NO FLICKER VERSION)
   ====================================================== */

add_action('wp_footer',function(){
$opts = tmg_get_settings();
?>

<script>
let _tmgLightboxOpen = false;	
document.addEventListener("DOMContentLoaded",function(){
document.querySelectorAll(".tmg-slider").forEach(function(slider){

let slides = slider.querySelectorAll(".tmg-slide");
if(!slides.length) return;

let index = 0;
slides[0].classList.add("active");
// Initialize below caption
const below = slider.closest(".tmg-slider-wrapper")
    .nextElementSibling;
if(below){
    const title = slides[0].dataset.title || "";
    const caption = slides[0].dataset.caption || "";

    const titleEl = below.querySelector(".tmg-title");
    const captionEl = below.querySelector(".tmg-caption");

    if(titleEl) titleEl.textContent = title;
    if(captionEl) captionEl.textContent = caption;
}
	
function showSlide(i){

  slides[index].classList.remove("active");
  index = (i + slides.length) % slides.length;
  slides[index].classList.add("active");

  // Update below caption
const below = slider.closest(".tmg-slider-wrapper")
    .nextElementSibling;
  if(below){
      const title = slides[index].dataset.title || "";
      const caption = slides[index].dataset.caption || "";

      const titleEl = below.querySelector(".tmg-title");
      const captionEl = below.querySelector(".tmg-caption");

      if(titleEl) titleEl.textContent = title;
      if(captionEl) captionEl.textContent = caption;
  }
}

let wrapper = slider.closest(".tmg-slider-wrapper");

let prev = wrapper.querySelector(".tmg-prev");
let next = wrapper.querySelector(".tmg-next");

if(prev) prev.addEventListener("click",()=>showSlide(index-1));
if(next) next.addEventListener("click",()=>showSlide(index+1));

let speed = parseInt(slider.dataset.speed) || 8000;
let autoplayEnabled = slider.dataset.autoplay === "1";

/* Store interval directly on slider */
slider._tmgInterval = null;

function startAutoplay(){
  if(!autoplayEnabled) return;
  if(_tmgLightboxOpen) return;   // ← ADD THIS LINE
  if(slider._tmgInterval) clearInterval(slider._tmgInterval);
  slider._tmgInterval = setInterval(function(){
    showSlide(index+1);
  }, speed);
}

function stopAutoplay(){
  if(slider._tmgInterval){
    clearInterval(slider._tmgInterval);
    slider._tmgInterval = null;
  }
}

if(autoplayEnabled){
  startAutoplay();
}

/* Pause on hover */
slider.addEventListener("mouseenter", stopAutoplay);
slider.addEventListener("mouseleave", startAutoplay);

/* Expose to lightbox */
slider._tmgStart = startAutoplay;
slider._tmgStop = stopAutoplay;
	
	/* Swipe Support */
let startX = 0;
let endX = 0;

slider.addEventListener("touchstart", function(e){
  startX = e.changedTouches[0].screenX;
});

slider.addEventListener("touchend", function(e){
  endX = e.changedTouches[0].screenX;

  let diff = startX - endX;

  if(Math.abs(diff) > 50){ // minimum swipe distance
    if(diff > 0){
      showSlide(index + 1); // swipe left
    } else {
      showSlide(index - 1); // swipe right
    }
  }
});

	/* Keyboard navigation */
let keyboardEnabled = slider.dataset.keyboard === "1";

if(keyboardEnabled){

  document.addEventListener("keydown", function(e){

    if(_tmgLightboxOpen) return;

    if(e.key === "ArrowLeft"){
      e.preventDefault();
      showSlide(index - 1);
    }

    if(e.key === "ArrowRight"){
      e.preventDefault();
      showSlide(index + 1);
    }

  });

}
	
});
});



/* Simple Lightbox */
document.addEventListener("click", function(e){
  if(!e.target.classList.contains("tmg-image")) return;
_tmgLightboxOpen = true;
	
	document.querySelectorAll(".tmg-slider").forEach(function(slider){
  if(slider._tmgInterval){
    clearInterval(slider._tmgInterval);
    slider._tmgInterval = null;
  }
});

  const src = e.target.src;

  const overlay = document.createElement("div");
  overlay.style.position="fixed";
  overlay.style.inset="0";
  overlay.style.background="rgba(0,0,0,0.85)";
  overlay.style.display="flex";
  overlay.style.alignItems="center";
  overlay.style.justifyContent="center";
  overlay.style.zIndex="9999";

  const img = document.createElement("img");
  img.src = src;
  img.style.maxWidth="90%";
  img.style.maxHeight="90%";

  const close = document.createElement("div");
  close.innerHTML="✕";
  close.style.position="absolute";
  close.style.top="20px";
  close.style.right="30px";
  close.style.color="#fff";
  close.style.fontSize="28px";
  close.style.cursor="pointer";

function closeLightbox(){
	_tmgLightboxOpen = false;
  document.body.removeChild(overlay);
  document.querySelectorAll(".tmg-slider").forEach(function(slider){
    if(slider._tmgStart){
      slider._tmgStart();
    }
  });
}

close.onclick = closeLightbox;
overlay.onclick = closeLightbox;

close.onclick = closeLightbox;
overlay.onclick = closeLightbox;

  overlay.appendChild(img);
  overlay.appendChild(close);
  document.body.appendChild(overlay);
});



</script>


<style>

  .tmg-admin .tmg-section label { 
  display:block; 
  margin:4px 0; 
  font-weight: 600 !important;
}
	
  .tmg-admin .tmg-section > label {
  font-weight: 600 !important;
}
	
/* ===============================
   SLIDER — TRUE LOCKED HEIGHT
   =============================== */

<?php
$height_map = [
  'small'  => '420px',
  'medium' => '520px',
  'large'  => '650px'
];
?>

.tmg-slider {
  position: relative;
  width: 100%;
  margin: 0 auto;
  height: <?php echo $height_map[$opts['slider_height']] ?? '520px'; ?>;
  overflow: hidden;
}

/* Slides stack */
.tmg-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  transition: opacity <?php echo esc_attr($opts['slider_fade']); ?>s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}


.tmg-slide.active {
  opacity: 1;
  z-index: 2;
}
	
/* Ensure overlay aligns with visible content */


/* Frame fills slide */
.tmg-slider .tmg-frame {
  height: 100%;
  box-sizing: border-box;
  padding: 15px;
  display: flex;
  position: relative;   /* ADD THIS */
}
	
	.tmg-slider .tmg-image-wrapper {
  height: 100%;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

/* Mat fills inside frame */
.tmg-slider .tmg-mat {
  flex: 1;
  padding: 25px;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
	box-shadow:
    inset 0 2px 3px rgba(0,0,0,0.18),
    inset 0 -1px 2px rgba(0,0,0,0.06);
}

.tmg-slider .tmg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center center;
  display: block;
  flex: 1;
}

/* ===============================
   FRAME COLORS (GRID + SLIDER)
   =============================== */

.tmg-frame-black { background: #000; }
.tmg-frame-brown { background: #5a3e2b; }
.tmg-frame-white { background: #fff; }

/* ===============================
   GRID — CLEAN STRUCTURE
   =============================== */

.tmg-grid {
  display: grid;
  gap: 14px;
  row-gap: 26px;
  max-width: 1000px;
  margin: 60px auto;
}

.tmg-grid.columns-1 { grid-template-columns: repeat(1,1fr); }
.tmg-grid.columns-2 { grid-template-columns: repeat(2,1fr); }
.tmg-grid.columns-3 { grid-template-columns: repeat(3,1fr); }
.tmg-grid.columns-4 { grid-template-columns: repeat(4,1fr); }
.tmg-grid.columns-5 { grid-template-columns: repeat(5,1fr); }
.tmg-grid.columns-6 { grid-template-columns: repeat(6,1fr); }

.tmg-grid-item {
  position: relative;
  text-align: center;
}

/* Square container — ONLY square rule */
.tmg-grid-square {
  aspect-ratio: 1 / 1;
  width: 100%;
  position: relative;
}

/* Frame fills square */
.tmg-grid-square .tmg-frame {
  width: 100%;
  height: 100%;
  padding: 6px;
  box-sizing: border-box;
}

/* Mat fills frame */
.tmg-grid-square .tmg-mat {
  width: 100%;
  height: 100%;
  padding: 8px;
  box-sizing: border-box;
  overflow: hidden;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.15);
}

/* Image fills square */
.tmg-grid-square .tmg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* If frame is OFF */
.tmg-grid-square > img.tmg-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Below text spacing */
.tmg-text-below-grid {
  margin-top: 8px;
  margin-bottom: 12px;
  text-align: center;
}

.tmg-text-below-grid .tmg-title {
  font-weight: 600;
}

.tmg-text-below-grid .tmg-caption {
  opacity: 0.8;
}

/* ===============================
   HOVER OVERLAY
   =============================== */

.tmg-overlay {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 35%;

  background: linear-gradient(
    to top,
    rgba(0,0,0,0.80),
    rgba(0,0,0,0.4),
    rgba(0,0,0,0)
  );

  color: #fff;
  opacity: 0;

  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  gap: 6px;

  padding: 32px 40px 40px 46px;
  text-align: left;
  margin-bottom: 2px;
  transition: opacity 0.3s ease;

  pointer-events: none; /* IMPORTANT */
}
	
	/* Always-visible overlay mode */
.tmg-slider[data-overlayalways="1"] .tmg-overlay {
  opacity: 1 !important;
}
	
	/* Grid Always Overlay Mode */
.tmg-grid[data-overlayalways="1"] .tmg-overlay {
  opacity: 1 !important;
}
	
/* Grid overlay hover */
.tmg-grid-item:hover .tmg-overlay {
  opacity: 1;
}
	
	.tmg-slider-wrapper {
  position: relative;
  height: <?php echo $height_map[$opts['slider_height']] ?? '520px'; ?>;
}

.tmg-slider .tmg-slide:hover .tmg-overlay {
  opacity: 1;
}
	
.tmg-slider .tmg-mat,
.tmg-slider .tmg-image-wrapper {
  position: relative;
}

/* Reserve space always */
.tmg-text-below {
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.25s ease;
}

/* Show when in below mode */
.tmg-slider-wrapper.tmg-textmode-below ~ .tmg-text-below {
  visibility: visible;
  opacity: 1;
}
	
	.tmg-grid .tmg-frame,
.tmg-grid .tmg-image {
  position: relative;
}
	
/* ===============================
   MOBILE GRID STACK
   =============================== */

@media (max-width: 768px) {
  .tmg-grid {
    grid-template-columns: 1fr !important;
  }
  .tmg-slider {
    height: 360px !important;
  }
}
	
/* ===============================
   MOBILE: text display options
   =============================== */

@media (max-width: 768px) {

  /* Mobile Overlay Mode */
  .tmg-slider[data-mobiletext="overlay"] .tmg-overlay {
    opacity: 1 !important;
    display: flex;
  }

  .tmg-slider[data-mobiletext="overlay"] + .tmg-text-below {
    visibility: hidden !important;
    opacity: 0 !important;
  }

  /* Mobile Below Mode */
  .tmg-slider[data-mobiletext="below"] .tmg-overlay {
    display: none !important;
  }

  .tmg-slider[data-mobiletext="below"] + .tmg-text-below {
    visibility: visible !important;
    opacity: 1 !important;
  }

}

/* ===============================
   NAVIGATION BUTTONS
   =============================== */

.tmg-prev,
.tmg-next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  height: 42px;

  z-index: 30;

  background: rgba(211,211,211,0.65);
  border: none;
  color: #fff;
  font-size: 24px;
  width: 42px;
  cursor: pointer;

  display: flex;
  align-items: center;
  justify-content: center;

  backdrop-filter: blur(3px);
}

.tmg-prev { left: -65px; }
.tmg-next { right: -65px; }

	
	/* ===============================
   Caption/Title styling
   =============================== */
	
	.tmg-text-below {
  margin-top: 12px;
  text-align: center;
}

.tmg-text-below .tmg-title {
  font-size: 1.4em;
  font-weight: 600;
}

.tmg-text-below .tmg-caption {
  font-size: 1.1em;
  opacity: 0.8;
}

.tmg-text-below-grid {
  margin-top: 8px;
  margin-bottom: 8px !important;  /* ADD THIS */
  text-align: center;
  padding-bottom: 12px !important;
}
	
</style>

<?php });



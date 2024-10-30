<?php if (!defined('ABSPATH')) exit; ?>
<?php
$imgturk_content_id = $imgturk_content_id;
$imgturk_content_type = $imgturk_content_type;
$imgturk_url = 'https://imgturk.com/' . $imgturk_content_id;
$imgturk_prefix = '@';

if ($imgturk_content_type == 'tag') {
    $imgturk_url = 'https://imgturk.com/tag/' . $imgturk_content_id;
    $imgturk_prefix = '#';
}
?>

<div class="imgturk-widget loading"
        data-id="<?php echo esc_attr($imgturk_content_id) ?>"
        data-type="<?php echo esc_attr($imgturk_content_type) ?>">

    <h2 class="widget-title"><?php echo esc_html($imgturk_prefix . $imgturk_content_id) ?></h2>

    <span class="loader">Loading Instagram posts for
        <a href="<?php echo esc_url($imgturk_url) ?>"><?php echo esc_html($imgturk_prefix . $imgturk_content_id) ?></a>
    </span>

    <div class="imgturk-media"></div>
</div>

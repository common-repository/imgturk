<?php if (!defined('ABSPATH')) exit; ?>
<div class="imgturk-admin">
    <input type="hidden" name="<?php echo $this->get_field_name( 'title' ); ?>"
        id="<?php echo $this->get_field_id( 'title' ); ?>" value="<?php echo esc_attr($title); ?>">

    <fieldset>
        <legend><span><?php _e( 'Type:' ); ?></span></legend>

        <div class="typevalues">

            <label>
                <input type="radio" name="<?php echo $this->get_field_name( 'content_type' ); ?>" value="user"
                    <?php if ($content_type == 'user') { echo 'checked'; } ?>>
                <span><?php _e('User'); ?></span>
            </label>

            <br />

            <label>
                <input type="radio" name="<?php echo $this->get_field_name( 'content_type' ); ?>" value="tag"
                    <?php if ($content_type == 'tag') { echo 'checked'; } ?>>
                <span><?php _e('Tag'); ?></span>
            </label>

        </div>
    </fieldset>

    <p class="">
        <label for="<?php echo $this->get_field_id( 'content_id' ); ?>"><?php _e( 'Username or tag (without # or @):' ); ?></label>
        <input class="widefat" type="text" id="<?php echo $this->get_field_id('content_id'); ?>"
            name="<?php echo $this->get_field_name( 'content_id' ); ?>"
            value="<?php echo esc_attr($content_id); ?>">


        <?php settings_errors($this->get_field_name( 'content_id' )); ?>

    </p>

</div>

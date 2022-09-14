<?php

class MandalaAdmin {
    function __construct()
    {
        // Custom Actions
        add_action('admin_menu', array($this, 'mandala_admin_menu'));
        add_action('admin_init', array($this, 'mandala_theme_settings_fields'));
    }

    public function mandala_admin_menu()
    {
        add_options_page(
            __('Mandala Kadence Theme Settings', 'textdomain'),
            __('Mandala Theme', 'textdomain'),
            'manage_options',
            'mandala_theme',
            array(
                $this,
                'mandala_theme_settings_page'
            )
        );
    }

    public function mandala_theme_settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo get_admin_page_title() ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'mandala_theme_settings' ); // settings group name
                do_settings_sections( 'mandala_theme' ); // just a page slug
                submit_button(); // "Save Changes" button
                ?>
            </form>
        </div>
        <?php

    }

    public function mandala_theme_settings_fields() {

        // Slug and option group labels
        $page_slug = 'mandala_theme';
        $option_group = 'mandala_theme_settings';

        // Add a single section (for now)
        add_settings_section(
            'mandala_theme_settings_id', // section ID
            '', // title (optional)
            '', // callback function to display the section (optional)
            $page_slug
        );

        // Register fields
        register_setting( $option_group, 'blog_homepage', 'absint' );

        // Add fields
        add_settings_field(
            'blog_homepage',
            'Blog Homepage',
            array($this, 'mandala_blog_home'), // function to print the field
            $page_slug,
            'mandala_theme_settings_id' // section ID
        );

    }

    // Custom Field for selecting blog home page for banner
    function mandala_blog_home( $args ) {
        $value = get_option( 'blog_homepage' );
        $allpages = get_pages();
        ?>
            <select name="blog_homepage">
                <option value="0">None</option>
                <?php foreach($allpages as $apage): ?>
                    <option value="<?php echo $apage->ID; ?>>"
                            <?php selected($value, $apage->ID); ?>
                    ><?php echo $apage->post_title; ?></option>
                <?php endforeach; ?>
            </select>
       <?php

        if (!empty($value)) {
            $currpg = get_post($value);
            if (!empty($currpg)) {
                ?>
                    <div class="mt">
                        <a href="/<?php echo $currpg->post_name; ?>" target="_blank">
                            Go to <?php echo $currpg->post_title; ?>
                        </a>
                    </div>
                <?php
            }
        }
    }

}

$mandala_admin = new MandalaAdmin();

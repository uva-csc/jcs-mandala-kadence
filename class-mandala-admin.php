<?php

class MandalaAdmin {
    function __construct()
    {
        error_log("In mandala admin");
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

        // I created variables to make the things clearer
        $page_slug = 'mandala_theme';
        $option_group = 'mandala_theme_settings';

        // 1. create section
        add_settings_section(
            'mandala_theme_settings_id', // section ID
            '', // title (optional)
            '', // callback function to display the section (optional)
            $page_slug
        );

        // 2. register fields
        register_setting( $option_group, 'slider_on', array($this, 'mandala_sanitize_checkbox') );
        register_setting( $option_group, 'num_of_slides', 'absint' );

        // 3. add fields
        add_settings_field(
            'slider_on',
            'Display slider',
            array($this, 'mandala_checkbox'), // function to print the field
            $page_slug,
            'mandala_theme_settings_id' // section ID
        );

        add_settings_field(
            'num_of_slides',
            'Number of slides',
            array($this, 'mandala_number'),
            $page_slug,
            'mandala_theme_settings_id',
            array(
                'label_for' => 'num_of_slides',
                'class' => 'hello', // for <tr> element
                'name' => 'num_of_slides' // pass any custom parameters
            )
        );

    }


// custom callback function to print field HTML
    function mandala_number( $args ){
        printf(
            '<input type="number" id="%s" name="%s" value="%d" />',
            $args[ 'name' ],
            $args[ 'name' ],
            get_option( $args[ 'name' ], 2 ) // 2 is the default number of slides
        );
    }
// custom callback function to print checkbox field HTML
    function mandala_checkbox( $args ) {
        $value = get_option( 'slider_on' );
        ?>
        <label>
            <input type="checkbox" name="slider_on" <?php checked( $value, 'yes' ) ?> /> Yes
        </label>
        <?php
    }

// custom sanitization function for a checkbox field
    function mandala_sanitize_checkbox( $value ) {
        return 'on' === $value ? 'yes' : 'no';
    }
}

$mandala_admin = new MandalaAdmin();

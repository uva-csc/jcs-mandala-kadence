<?php

/**
 * Mandala Kadence Theme Class : A child theme of Kadence for use with Mandala content
 *
 * This theme allows for custom styling of Kadence to work with Mandala content. The stylesheets are written in
 * SCSS but must be processed into CSS which is also stored in this repo. In the init() function the theme only
 * adds the CSS file. Only generic styles for adapting any Mandala content to the Kadence theme should be placed
 * in this theme’s css. Custom CSS for subsites or specific sites should use the subsite class and custom CSS.
 *
 * This theme also allows for subsites. This requires the use of custom metadata for pages. This is best implemented
 * through the plugin Advanced Custom Fields. The necessary fields are:
 *
 *      subsite_nav : The ID number of the custom menu to use
 *      subsite_title : The Title for the subsite to display in the header in place of the blogname
 *      subsite_class : A special class to put in the <body> element to identify pages of a subsite.
 *
 * Any page *or children of a page* that has these fields set will display the specific menu and title in the header
 * and attach the subsite class to the body along with the generic class "subsite".
 *
 * @author  Than Grove
 * @package  Mandala Kadence
 * @version  1.0
 */
class MandalaKadence {
	/**
	 * The construct function adds actions and filters for
	 */
    public $main_title = '';

	function __construct() {
		// Custom Actions
		add_action('after_setup_theme', array($this, 'init'));
        add_action('wp_head', array($this, 'mandala_update_header'));
        add_action('kadence_top_header', array($this, 'subsite_back_link'));
		add_action('kadence_header', array($this, 'add_custom_data'));
		add_action('get_template_part_template-parts/header/navigation', array($this, 'subsite_nav'));
		add_action('before_kadence_logo_output', array($this, 'subsite_logo'));
		add_action('kadence_footer_navigation', array($this, 'subsite_footer'));
        //add_action('kadence_hero_header', array($this, 'subsite_title'));

		// Custom Filters
        add_filter('pre_get_document_title', array($this, 'subsite_title_clean'));
		add_filter('body_class', array($this, 'update_body_class'));
        // Get main site title before overwriting in the following filter
        $this->main_title = get_bloginfo();
		add_filter('pre_option_blogname', array($this, 'subsite_bname'));
        add_filter('kadence_logo_url', array($this, 'subsite_url'));
		// Disable update emails
		add_filter( 'auto_core_update_send_email', array($this, 'stop_wpupdate_emails'), 10, 4);
		add_filter( 'auto_plugin_update_send_email', '__return_false' );
		add_filter( 'auto_theme_update_send_email', '__return_false' );

	}

	function init() {
		// Reigster styles and scripts for Mandala Kadence theme
		wp_enqueue_style( 'mandala-kadence-styles', get_stylesheet_directory_uri() . '/css/mandala-kadence.css',
		 	false,'1.0','all');
		wp_enqueue_script( 'mandala-kadence-scripts', get_stylesheet_directory_uri() . '/js/mandala-kadence.js',
			array ( 'jquery' ), 1.0, true);

		// Add custom styles from /css/custom folder (just files ending in *.css)
		$custom_folder = get_stylesheet_directory() . '/css/custom';
		$files = array_diff(scandir($custom_folder), array('.', '..'));
		$cssfiles = array_filter($files, function($fnm) {
			return str_ends_with($fnm, '.css');
		});

		foreach($cssfiles as $n => $cssfile) {
			// error_log(get_stylesheet_directory_uri() . "/css/custom/$cssfile");
			wp_enqueue_style( 'mandala-kadence-custom-styles-' . $n,  get_stylesheet_directory_uri() . "/css/custom/$cssfile",
				false,'1.0','all');
		}

	}

	/**
	 * This function adds custom data as a JSON object to the DOM to be used by an embedded React standalone
     * Adds script tage with JSON text in it. (Deprecated)
     * Replaced by window.mandala_wp settings in mandala plugin's enqueue_scripts function
	 */
	public function add_custom_data() {
		$options = get_option( 'mandala_plugin_options' );
        if (is_numeric($options['default_sidebar'])) {
		    $sbval = $options['default_sidebar'] * 1;
			echo '<script type="application/json" id="mandala_data">{ "sidebar": ' . $sbval . ' }</script>';
		}
	}

    /**
     * Adds a CSS script tag to the head of a subsite page that contains the subsite CSS
     * Each rule in the CSS field and each selector within each rule is appended with the
     * body class for the subsite, e.g .my-subsite div.foo, .my-subsite div.bar {...}, etc.
     */
    public function mandala_update_header() {
        $pgid = get_the_ID();
        // Import subsite info for a page
        $subinf = $this->get_subsite_info();
        if (!empty($subinf['css'])) {
            $subid = $subinf['class'] ?: 'subsite' . $pgid;
            $css_lines = preg_split("/\n*}\n*/", $subinf['css']);
            $css_lines = array_map(function($item) use ($subid) {
                if (empty($item)) { return ""; }
                $item = trim($item);
                list($selector, $rule) = explode('{', $item, 2);
                $selpts = explode(',', $selector);
                $selpts = array_map(function($sel) use ($subid) {
                    $sel = trim($sel);
                    if (empty($sel)) { return ""; }
                    return ".$subid $sel";
                }, $selpts);
                return implode(",\n", $selpts) . " {\n" . $rule;
            }, $css_lines);
            $css_lines = implode("\n}\n", $css_lines);
            // error_log("Subsite CSS ($subid):\n$css_lines");
            echo '<style id="' . $subid . '-styles">' . $css_lines . '</style>';
        }

        // Get text info if article in journal and add citation meta tags
        if ($pgid) {
            $text_id = get_field('mandala_text_id', $pgid);
            if ($text_id) {
                $url = 'https://texts.mandala.library.virginia.edu/shanti_texts/node_json/' . $text_id;
                $data = $this->get_data($url);
                $data = json_decode($data, true);
                echo "\n\n<!-- Mandala Meta Tags -->\n";
                // Title
                $title = $data['title'];
                $this->add_meta('citation_title', $title);
                // Authors
                $authors = $data['field_book_author']['und'];
                $author_map = array_map(function ($a) { return $a['value']; }, $authors);
                $alist = implode(', ', $author_map);
                $this->add_meta('citation_author', $alist);

                // Journal Title
                $options = get_option( 'mandala_plugin_options' );
                $journal = !empty($options['journal_title']) ? $options['journal_title'] : False;
                if (!empty($journal)) {
                    $this->add_meta('citation_journal', $journal);
                }

                // DOI
                $doi = $data['field_doi']['und'][0]['value'];
                $this->add_meta('citation_doi', trim($doi));

                // Abstract
                $fullbook = $data['field_book_content']['und']['0']['value'];
                if(str_contains($fullbook, 'Abstract:')) {
                    $abs = explode('Abstract:', $fullbook)[1];
                    $abs = strip_tags($abs);
                    if (!empty($abs)) {
                        $this->add_meta('citations_abstract', trim($abs));
                    }
                }

                // Comment closure
                echo "<!-- End of Mandala Meta Tags -->\n\n";
            }
        }
    }

    private function add_meta($name, $cnt) {
        echo "<meta name=\"$name\" content=\"$cnt\" /> \n";
    }

    /**
     * Displays area above header with link back to main site
     */
    public function subsite_back_link() {
        $subs = $this->get_subsite_info();
        if ($this->is_subsite()) {
            $site_logo = get_custom_logo();
            $home_url = get_home_url();
            echo '<div class="subsite-back">' . $site_logo . ' <a class="back-link" href="' . $home_url . '">' .
                    $this->main_title . '</a> </div>';
        }
    }

    /**
     * Display the subsite logo
     * CSS is defined to hide site logo if it falls after subsite logo
     */
	public function subsite_logo() {
		$sublogo = $this->get_subsite_info('logo');
		if (!empty($sublogo)) {
			// error_log( json_encode($sublogo) );
			$logo = wp_get_attachment_image( $sublogo, 'full', true, array('class'    => 'subsite-logo'));
			echo $logo;
		}
	}

    /**
     * Called as a filter for pre_get_document_title. Serves to strip tags from the title
     * that is inserted into the HTML head meta tag
     *
     * @param $title
     * @return string
     */
    public function subsite_title_clean($title) {
        $subsite_title =  $this->get_subsite_info('title');
        if ($subsite_title) {
            return strip_tags($subsite_title);
        }
    }

	/**
	 * Replaces the site title (i.e. blog name) with the subsite title
	 *
	 * @param $bname
	 *
	 * @return mixed
	 */
	public function subsite_bname($bname) {
        $subsite_title =  $this->get_subsite_info('title');
		if (!empty($subsite_title)) {
			return $subsite_title;
		}
		return $bname;
	}

    public function subsite_url($homeurl) {
        if ($this->is_subsite()) {
            $myid = get_the_ID();
            $homeurl = $this->get_subsite_home($myid);
        }
        return $homeurl;
    }

	/**
	 * Called by the filter for "body_class".
	 *
	 * @return array|string[]
	 */
	public function update_body_class() {
        $extra_classes = array('loading'); # Add loading class to hide menu initially
        // Adds specific classes to the body for subsites.
        if ($this->is_subsite()) {
            $extra_classes[] = 'subsite'; # start list with generic "subsite" class for body
            $subsite_class = $this->get_subsite_info('class') ?: 'subsite' . get_the_ID(); # add unique ss id
            $subsite_class = explode(' ', $subsite_class);  # if user has space delimited classes in field
            $extra_classes = array_merge($extra_classes, $subsite_class);
        }
        // Logged in class
        if (is_user_logged_in()) {
            $extra_classes[] = 'logged-in';
        }
        return $extra_classes;
	}

    /**
     * Show a particular navigation menu for a subsite
     */
	public function subsite_nav() {
        $subsite =  $this->get_subsite_info('menu');
		if (!empty($subsite)) {
			$menu_args = array(
				'menu'            => $subsite,
				'menu_class'      => 'menu',
				'container'       => 'div',
				'container_id'    => 'subsite-menu',
				'container_class' => 'subsite-menu primary-menu-container header-menu-container',
				'theme_location'  => 'main_center',
				'echo'            => false,
			);
			$menu      = wp_nav_menu( $menu_args );
			$prefels   = '<div id="mandala-subsite-menu"
							 class="site-header-item site-header-focus-item site-header-item-main-navigation
	                                header-navigation-layout-stretch-false header-navigation-layout-fill-stretch-false" 
	                        data-section="kadence_customizer_primary_navigation">
						<nav id="site-navigation" 
							 class="main-navigation header-navigation nav--toggle-sub 
									header-navigation-style-underline header-navigation-dropdown-animation-none" 
							role="navigation" aria-label="Primary Navigation">';
			$suffels   = '</nav></div>';
			echo "$prefels $menu $suffels";
		}
	}

    /**
     * Display a custom footer menu for a subsite
     */
	public function subsite_footer() {
		// Custom fields for subsite existence and subsite alt text.
        $sbsfmenu =  $this->get_subsite_info('footer_menu');
		if (!empty($sbsfmenu)) {
			$menu_args   = array(
				'menu'            => $sbsfmenu,
				'menu_class'      => 'menu',
				'container'       => 'div',
				'container_id'    => 'subsite-menu',
				'container_class' => 'subsite-menu primary-menu-container header-menu-container',
				'theme_location'  => 'main_center',
				'echo'            => false,
			);
			$footer_menu = wp_nav_menu( $menu_args );
			echo "<nav id=\"subsite-footer-menu\">$footer_menu</nav>";
		}
	}

    /**
     * Gets all subsite info as an associative array or gets the value for a particular field
     * @param string $field
     * @return array|mixed
     */
    public function get_subsite_info($field='all') {
        $myid = get_the_ID();
        $ssinfo = array(
            'title' => $this->get_ancestor_value($myid, 'subsite_title'),
            'logo' => $this->get_ancestor_value($myid, 'subsite_logo'),
            'class' => $this->get_ancestor_value($myid, 'subsite_class'),
            'menu' => $this->get_ancestor_value($myid, 'subsite_menu'),
            'footer_menu' => $this->get_ancestor_value($myid, 'subsite_footer_menu'),
            'css' => $this->get_ancestor_value($myid, 'subsite_css'),
        );
        if (in_array($field, array_keys($ssinfo))) {
            return $ssinfo[$field];
        }
        return $ssinfo;
    }

    /**
     * Determine if a page is part of a subsite based on whether _any_ of the subsite variables are set
     * @return bool
     */
    public function is_subsite() {
        $ssinfo = $this->get_subsite_info();
        foreach($ssinfo as $k => $v) {
            if (!empty($v)) {
                return true;  // If a single subsite value is not empty then it is considered a subsite
            }
        }
        return false;
    }

    /**
     * Returns custom setting value by name if it is set on this page or any of its ancestors
     *
     * @param $pid
     * @param $varname
     * @return false|mixed
     */
	public function get_ancestor_value($pid, $varname) {
		if (empty($pid) || !is_numeric($pid)) { return false; }
		$val = get_post_meta($pid, $varname, true);
		if (empty($val)) {
			$thepost = get_post($pid);
			if (!empty($thepost->post_parent)) {
				$val = $this->get_ancestor_value($thepost->post_parent, $varname);
			} elseif ($thepost->post_type == 'post') {
                $blog_home_id = get_option( 'blog_homepage' );
                if (!empty($blog_home_id)) {
                    $val = $this->get_ancestor_value($blog_home_id, $varname);
                }
            }
		}
		return $val;
	}

    /**
     * Get a subsite's home page url
     *
     * @param $pid
     * @return false|string
     */
    public function get_subsite_home($pid) {
        $thepost = get_post($pid);
        $blog_home_id = get_option( 'blog_homepage' );
        if ($thepost->post_type == 'page') {
            while (!empty($thepost->post_parent)) {
                $thepost = $thepost->post_parent;
            }
            return get_home_url() . '/' . get_page_uri($thepost);
        } elseif ($thepost->post_type == 'post' && !empty($blog_home_id)) {
            $thepost = get_post($blog_home_id);
            return get_home_url() . '/' . get_page_uri($thepost);
        } else {
            return get_home_url();
        }
    }

    private function get_data($url) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "x-rapidapi-host: unogsng.p.rapidapi.com",
                "x-rapidapi-key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            error_log("cURL ($url) Error #:" . $err);
        } else {
            return $response;
        }
    }

	// Disable automatic emails for wp update when they succeed
	public function stop_wpupdate_emails( $send, $type, $core_update, $result ) {
		if ( ! empty( $type ) && $type == 'success' ) {
			return false;
		}
		return true;
	}

}

$mandalakadence = new MandalaKadence();
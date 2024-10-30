<?php


class npcms_membership_Settings {

    public $npcms_membership_setting;
    /**
     * Construct me
     */
    public function __construct() {
        $this->npcms_membership_setting = get_option( 'npcms_membership_setting', '' );

        // register the checkbox
        add_action('admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Setup the settings
     *
     * Add a single checkbox setting for Active/Inactive and a text field
     * just for the sake of our demo
     *
     */
    public function register_settings() {
        register_setting( 'npcms_membership_setting', 'npcms_membership_setting', array( $this, 'npcms_membership_validate_settings' ) );

        add_settings_section(
            'npcms_membership_settings_section',         // ID used to identify this section and with which to register options
            'Enable nonprofitCMS Membership',                  // Title to be displayed on the administration page
            array($this, 'npcms_membership_settings_callback'), // Callback used to render the description of the section
            'npcms_membership_setting'                           // Page on which to add this section of options
        );

        add_settings_field(
            'npcms_login_page',                      // ID used to identify the field throughout the theme
            __( 'Login Page: ', 'npcms_membership' ),                           // The label to the left of the option interface element
            array( $this, 'npcms_login_page_callback' ),   // The name of the function responsible for rendering the option interface
            'npcms_membership_setting',                          // The page on which this option will be displayed
            'npcms_membership_settings_section'         // The name of the section to which this field belongs
        );

        add_settings_field(
            'npcms_login_type',                      // ID used to identify the field throughout the theme
            __( 'Connector: ', 'npcms_membership' ),                           // The label to the left of the option interface element
            array( $this, 'npcms_type_callback' ),   // The name of the function responsible for rendering the option interface
            'npcms_membership_setting',                          // The page on which this option will be displayed
            'npcms_membership_settings_section'         // The name of the section to which this field belongs
        );

        add_settings_field(
            'npcms_login_url',                      // ID used to identify the field throughout the theme
            __( 'API Endpoint: ', 'npcms_membership' ),                           // The label to the left of the option interface element
            array( $this, 'npcms_url_callback' ),   // The name of the function responsible for rendering the option interface
            'npcms_membership_setting',                          // The page on which this option will be displayed
            'npcms_membership_settings_section'         // The name of the section to which this field belongs
        );

        add_settings_field(
            'npcms_login_apikey',                      // ID used to identify the field throughout the theme
            __( 'API Key: ', 'npcms_membership' ),                           // The label to the left of the option interface element
            array( $this, 'npcms_apikey_callback' ),   // The name of the function responsible for rendering the option interface
            'npcms_membership_setting',                          // The page on which this option will be displayed
            'npcms_membership_settings_section'         // The name of the section to which this field belongs
        );




    }

    public function npcms_membership_settings_callback()
    {

    }

    public function npcms_type_callback() {
        $out = '';
        $val = '';

        if(! empty( $this->npcms_membership_setting ) && isset ( $this->npcms_membership_setting['npcms_login_type'] ) ) {
            $val = $this->npcms_membership_setting['npcms_login_type'];
        }

        $imisSelected = $val == 'iMIS 15' ? "selected" : "";
        $mCMSSelected = $val == 'MemberCMS' ? "selected" : "";

        $out = '<select id="npcms_login_type" name="npcms_membership_setting[npcms_login_type]">';
        $out = $out.'<option value="">-- SELECT --</option>';
        $out = $out.'<option ' . $mCMSSelected . ' value="MemberCMS">MemberCMS</option>';
        $out = $out.'<option ' . $imisSelected . ' value="iMIS 15">iMIS 15</option>';
        $out = $out.'</select>';



        $out .= '<script> jQuery(document).ready(function (){ jQuery("#npcms_login_type").change(function (){ if (jQuery("#npcms_login_type").val() == "") { jQuery(".imis").closest("tr").hide(); jQuery(".member").closest("tr").hide();  } if (jQuery("#npcms_login_type").val() == "MemberCMS") { jQuery(".imis").closest("tr").hide(); jQuery(".member").closest("tr").show();  } if (jQuery("#npcms_login_type").val() == "iMIS 15") { jQuery(".member").closest("tr").hide(); jQuery(".imis").closest("tr").show();  } }); jQuery("#npcms_login_type").change();}); </script>';

        echo $out;


    }

    public function npcms_url_callback() {
        $out = '';
        $val = '';

        if(! empty( $this->npcms_membership_setting ) && isset ( $this->npcms_membership_setting['npcms_login_url'] ) ) {
            $val = $this->npcms_membership_setting['npcms_login_url'];
        }

        $out = '<input style="width:35%" class="imis member" type="text" id="npcms_login_url" name="npcms_membership_setting[npcms_login_url]" value="' . $val . '"  />';
        echo $out;
    }

    public function npcms_apikey_callback() {
        $out = '';
        $val = '';

        if(! empty( $this->npcms_membership_setting ) && isset ( $this->npcms_membership_setting['npcms_login_apikey'] ) ) {
            $val = $this->npcms_membership_setting['npcms_login_apikey'];
        }

        $out = '<input style="width:35%" class="member" type="text" id="npcms_login_apikey" name="npcms_membership_setting[npcms_login_apikey]" value="' . $val . '"  />';

        echo $out;
    }


    public function npcms_login_page_callback() {
        $out = '';
        $val = '';

        global $wpdb;

        $sql = "SELECT p.id as post_id, u.user_nicename as author, p.post_title, p.post_name as post_slug, p.post_date as local_publish_date, p.comment_count FROM " . $wpdb->base_prefix . "posts p, " . $wpdb->base_prefix . "users u WHERE p.post_status='publish' AND p.post_type='page' AND u.id = p.post_author ORDER BY p.post_title DESC LIMIT 1000";

        global $wpdb;
        $posts = $wpdb->get_results($sql);

        $out = '<select id="npcms_login_page" name="npcms_membership_setting[npcms_login_page]">';
        $out = $out.'<option value="0">-- SELECT --</option>';
        foreach ($posts as $post)
        {
            $val = '';
            if(! empty( $this->npcms_membership_setting ) && isset ( $this->npcms_membership_setting['npcms_login_page'] ) ) {
                if ($post->post_id == $this->npcms_membership_setting['npcms_login_page']) {
                    $val = 'selected';
                }
            }
            $out = $out.'<option '.$val.' value='.$post->post_id.'>'.$post->post_title.'</option>';
        }

        $out = $out.'</select>';

        $out .= "<br/>Unauthenticated users will be redirected to this page.  Be sure to include the short tag [member-login] on this page";

        echo $out;
    }


    /**
     * Validate Settings
     *
     * Filter the submitted data as per your request and return the array
     *
     * @param array $input
     */
    public function npcms_membership_validate_settings( $input ) {

        return $input;
    }
}
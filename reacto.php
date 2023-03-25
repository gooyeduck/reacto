<?php
/*
Plugin Name: Reacto
Description: A WordPress plugin that displays three reaction icons that logged-in users can submit, modify, or delete their reactions.
Version: 1.0
Author: Satyajit Talukder
License: GPL2 or later
 */
// Creation of the
function reacto_create_table()
{
    if (get_option("reaction_db_table") == 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . "user_reactions";

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            post_id INT(11) NOT NULL,
            reaction VARCHAR(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once ABSPATH . "wp-admin/includes/upgrade.php";
        dbDelta($sql);
        update_option("reaction_db_table", 1);
    }
}

register_activation_hook(__FILE__, "reacto_create_table");

// Enqueue the necessary styles and scripts
add_action("wp_enqueue_scripts", "reacto_enqueue_reactions_assets", 10);

// Register AJAX handlers for updating reactions
add_action(
    "wp_ajax_custom_reaction_submission",
    "reacto_submit_custom_reaction"
);


// Function for rendering the shortcode
function reacto_custom_reactions_shortcode()
{
    global $wpdb;
    $current_user_id = isset(wp_get_current_user()->ID)
        ? wp_get_current_user()->ID
        : 1;
    $current_post_id = (int) get_the_ID();
    $data = $wpdb->get_results(
        "SELECT reaction, post_id, user_id from {$wpdb->prefix}user_reactions WHERE user_id = {$current_user_id} AND post_id = {$current_post_id}"
    );
    $data_reaction = isset($data[0]) ? (string) $data[0]->reaction : "";
    $data_post_id = isset($data[0]) ? (int) $data[0]->post_id : "";
    $data_post_user_id = isset($data[0]) ? (int) $data[0]->user_id : "";
    $smile_count = reacto_get_reaction_count("smile");
    $straight_count = reacto_get_reaction_count("straight");
    $sad_count = reacto_get_reaction_count("sad");
    $output = '<div class="custom-reactions">';
    $output .=
        '<span class="custom-reaction smile' .
        ($data_reaction == "smile" ? " clicked" : "") .
        '" data-reaction-type="smile" data-count="' .
        esc_attr($smile_count) .
        '">' .
        reacto_get_reaction_icon(
            "smile",
            $data_reaction,
            $current_post_id,
            $data_post_id,
            $current_user_id,
            $data_post_user_id
        ) .
        "</span>";
    $output .=
        '<span class="custom-reaction straight' .
        ($data_reaction == "straight" ? " clicked" : "") .
        '" data-reaction-type="straight" data-count="' .
        esc_attr($straight_count) .
        '">' .
        reacto_get_reaction_icon(
            "straight",
            $data_reaction,
            $current_post_id,
            $data_post_id,
            $current_user_id,
            $data_post_user_id
        ) .
        "</span>";
    $output .=
        '<span class="custom-reaction sad' .
        ($data_reaction == "sad" ? " clicked" : "") .
        '" data-reaction-type="sad" data-count="' .
        esc_attr($sad_count) .
        '">' .
        reacto_get_reaction_icon(
            "sad",
            $data_reaction,
            $current_post_id,
            $data_post_id,
            $current_user_id,
            $data_post_user_id
        ) .
        "</span>";
    $output .= "</div>";
    return $output;
}

// Function for getting the reaction icon HTML
function reacto_get_reaction_icon(
    $reaction_type,
    $db_reaction = "",
    $current_post_id,
    $db_post_id,
    $current_user_id,
    $db_user_id
) {
    $icon_html = "";
    switch ($reaction_type) {
        case "smile":
            $icon_html .=
                '<i class="fa fa-smile-o" aria-hidden="true"></i><span class="reaction-label">' .
                ($reaction_type == $db_reaction &&
                $current_post_id == $db_post_id &&
                $current_user_id == $db_user_id
                    ? "1 Vote(s)"
                    : "Smile") .
                "</span>";
            break;
        case "straight":
            $icon_html .=
                '<i class="fa fa-meh-o" aria-hidden="true"></i><span class="reaction-label">' .
                ($reaction_type == $db_reaction &&
                $current_post_id == $db_post_id &&
                $current_user_id == $db_user_id
                    ? "1 Vote(s)"
                    : "Straight") .
                "</span>";
            break;
        case "sad":
            $icon_html .=
                '<i class="fa fa-frown-o" aria-hidden="true"></i><span class="reaction-label">' .
                ($reaction_type == $db_reaction &&
                $current_post_id == $db_post_id &&
                $current_user_id == $db_user_id
                    ? "1 Vote(s)"
                    : "Sad") .
                "</span>";
            break;
    }
    return $icon_html;
}

// Function for getting the reaction count
function reacto_get_reaction_count($reaction)
{
    global $wpdb;
    $count = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}user_reactions WHERE reaction='{$reaction}'"
    );
    return $count;
}

// Function for enqueuing the stylesheet and JavaScript
function reacto_enqueue_reactions_assets()
{
    wp_enqueue_style(
        "font-awesome",
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
    );
    wp_enqueue_style(
        "custom-reactions",
        plugin_dir_url(__FILE__) . "assets/css/custom-reactions.css"
    );

    //wp_enqueue_script('jquery');

    wp_enqueue_script(
        "custom-reactions",
        plugin_dir_url(__FILE__) . "assets/scripts/custom-reactions.js",
        ["jquery"],
        null,
        true
    );

    wp_localize_script("custom-reactions", "reacto_reactions", [
        "ajax_url" => admin_url("admin-ajax.php"),
        "current_user" => wp_get_current_user()->ID,
        "current_post" => get_the_ID(),
        "nonce" => wp_create_nonce("custom_reaction"),
    ]);
    //error_log(print_r(wp_get_current_user(),1));
}

// Function for submitting a reaction
// Server-side code for the AJAX endpoint
function reacto_submit_custom_reaction()
{
    check_ajax_referer("custom_reaction", "security");
    global $wpdb;
    $current_post_id = isset($_POST["current_user"]["current_post"])
        ? (int) $_POST["current_user"]["current_post"]
        : "";
    $current_user_id = isset($_POST["current_user"]["current_user"])
        ? (int) $_POST["current_user"]["current_user"]
        : "";
    $current_reaction_type = isset($_POST["reaction_type"])
        ? (string) $_POST["reaction_type"]
        : "";
    $results = $wpdb->get_results(
        "SELECT user_id, post_id, reaction FROM {$wpdb->prefix}user_reactions WHERE user_id='{$current_user_id}' AND post_id = '{$current_post_id}'"
    );
    $db_reaction_type = isset($results[0]) ? $results[0]->reaction : "";
    $db_user_id = isset($results[0]) ? $results[0]->user_id : "";
    $db_post_id = isset($results[0]) ? $results[0]->post_id : "";

    //If the current user is new and has no reactions saved inside database, so $results will return empty
    if (empty($results)) {
        // Insert the user reaction into the database if it doesn't already exist
        $status = $wpdb->insert(
            "{$wpdb->prefix}user_reactions",
            [
                "user_id" => $current_user_id,
                "post_id" => $current_post_id,
                "reaction" => $current_reaction_type,
            ],
            ["%d", "%d", "%s"]
        );
        if ($status) {
            // Return a success response with the updated reaction count
            $response = [
                "success" => true,
                "count" => reacto_get_reaction_count($current_reaction_type),
                "message" => "Reaction Submitted",
            ];
            wp_send_json($response);
        } else {
            // Return an error response with the current reaction count
            $response = [
                "success" => false,
                "count" => reacto_get_reaction_count($current_reaction_type),
                "message" => "Reaction Not Submitted",
            ];
            wp_send_json($response);
        }
    } else {
        //If current reaction & db reaction are not same and user current user id is equal to database user id and current post id is equal to database post id
        if (
            $current_reaction_type != $db_reaction_type &&
            $current_user_id == $db_user_id &&
            $current_post_id == $db_post_id
        ) {
            $result = $wpdb->update(
                "{$wpdb->prefix}user_reactions",
                [
                    "reaction" => $current_reaction_type,
                ],
                [
                    "user_id" => $current_user_id,
                    "post_id" => $current_post_id,
                ],
                ["%s"],
                ["%d", "%d"]
            );

            if ($result != false) {
                $response = [
                    "success" => true,
                    "count" => reacto_get_reaction_count(
                        $current_reaction_type
                    ),
                    "message" => "Reaction Updated",
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Reaction Update Failed",
                ];
            }
        } else {
            //delete the current reaction if the current reaction and db reaction is same
            $result = $wpdb->delete($wpdb->prefix . "user_reactions", [
                "user_id" => $current_user_id,
                "post_id" => $current_post_id,
            ]);

            if ($result != false) {
                $response = [
                    "success" => true,
                    "count" => reacto_get_reaction_count(
                        $current_reaction_type
                    ),
                    "message" => "Reaction Deleted For Current User",
                ];
            }
        }
    }
}
add_action(
    "wp_ajax_reacto_submit_custom_reaction",
    "reacto_submit_custom_reaction"
);
add_action(
    "wp_ajax_nopriv_reacto_submit_custom_reaction",
    "reacto_submit_custom_reaction"
); // Allow non-logged-in users to access the endpoint

//Elementor addon Register
function register_reacto_widget($widgets_manager)
{
    require_once __DIR__ . "/widgets/reacto-widgets.php";

    $widgets_manager->register(new \Elementor_Reacto_Widget());
}
add_action("elementor/widgets/register", "register_reacto_widget");

//Gutenberg Block Register
function reacto_block_init()
{
    register_block_type(__DIR__ . "/build", [
        "render_callback" => "render_on_frontend",
    ]);
}
add_action("init", "reacto_block_init");

//Wordpress Custom Gutenberg Block render_callback
//Below link is the reference for the solution I get for the Gutenberg Block Render to the front-end
//https://stackoverflow.com/questions/65592336/wordpress-custom-gutenberg-block-render-callback-doesnt-render
function render_on_frontend()
{
    $shortcode = do_shortcode(shortcode_unautop("[custom_reactions]"));
    return $shortcode;
}

// Register the shortcode for the plugin
add_shortcode("custom_reactions", "reacto_custom_reactions_shortcode");

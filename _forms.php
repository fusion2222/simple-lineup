<?php

// The poorest reccomended security I ever saw. But let it be here.
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 *  Form handlers and related logic.
 */
 
function simple_lineup__create_new_showboxes($post_id, $post, $wpdb){
    if(
        !(
            // All key must be present in order to update.
            array_key_exists('simple_lineup__start', $_POST) &&
            array_key_exists('simple_lineup__duration', $_POST) &&
            array_key_exists('simple_lineup__show_on_program_page', $_POST)
        )
    ){return;}

    foreach($_POST['simple_lineup__start'] as $key => $value){

        // If any show is valid, it must have synced keys with
        // `simple_lineup__duration`, `simple_lineup__show_on_program_page.`

        if(
            !(
                array_key_exists($key, $_POST['simple_lineup__duration']) &&
                array_key_exists($key, $_POST['simple_lineup__show_on_program_page'])
            )
        ){
            error_log('Sheduling keys do not correspond when creating new show record.');
            continue;
        }

        $update_data = [
            'start' => $_POST['simple_lineup__start'][$key],
            'duration' => $_POST['simple_lineup__duration'][$key],
            'show_on_program_page' => $_POST['simple_lineup__show_on_program_page'][$key],
            'node_id' => $post_id,
            'node_type' => $post->post_type
        ];
        $wpdb->insert(simple_lineup__get_db_table_name(), $update_data);

    }
}

function simple_lineup__delete_unused_shoboxes($post_id, $post, $wpdb){

    $db_table = simple_lineup__get_db_table_name();

    $prepared_sql = $wpdb->prepare(
        "SELECT id FROM {$db_table} WHERE node_id = %d AND node_type = %d", $post_id, $post->post_type
    );

    $old_ids = $wpdb->get_col($prepared_sql);

    // If there are any old IDs, they must be also sent fron frontend.
    // If not, delete all old IDs which are not sent back from user's request.
    if(empty($old_ids)){
        return;
    }

    $ids_to_keep = $_POST['simple_lineup__id'];
    $sql_delete_statement = "DELETE FROM {$db_table} WHERE";

    if(!empty($ids_to_keep)){
        $sql_ids = implode(', ', $ids_to_keep);
        $sql_delete_statement .= " id NOT IN ({$sql_ids}) AND";
    }

    $sql_delete_statement .= " node_id = %d AND node_type = %s;";
    $prepared_sql = $wpdb->prepare($sql_delete_statement, $post_id, $post->post_type);
    $old_ids = $wpdb->query($prepared_sql);
}

function simple_lineup__set_post_scheduling($post_id, $post, $update){
    global $wpdb;

    if(empty($_POST)){
        // checking empty $_POST is a must. For some wierd reason, wordpress
        // makes double saving. This prevents for accidental show removal.
        return;
    }

    $sheduled_content_types = json_decode(get_option(SIMPLE_LINEUP__CONTENT_TYPES_FIELD));

    if((wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) || !in_array($post->post_type, $sheduled_content_types)){
        // Save sheduling only in case, content_type is configured.
    }

    simple_lineup__delete_unused_shoboxes($post_id, $post, $wpdb);
    simple_lineup__create_new_showboxes($post_id, $post, $wpdb);

}

add_action('post_updated', 'simple_lineup__set_post_scheduling', 1, 4);

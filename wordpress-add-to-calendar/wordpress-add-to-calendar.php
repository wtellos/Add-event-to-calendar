<?php
/**
 * Plugin Name: Add Event to Calendar - Shortcode
 * Description: Add to Calendar dropdown button using ACF fields and UIkit button component.
 * Version: 1.0.2
 * Author: Renos Fiouris, Deepseek
 * Author URI: https://github.com/wtellos
 * Text Domain: txt-add-event-to-calendar

 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode('add-to-calendar-button', 'rfwt_add_to_calendar_button_shortcode');

function rfwt_add_to_calendar_button_shortcode($atts) {

    if ( ! function_exists( 'get_field' ) ) {
        return '';
    }    
    
    // Define shortcode attributes (update to match ACF fields)
    // Use atts like: [add-to-calendar-button title="..."]
    $atts = shortcode_atts([
        'events_start_date' => '',
        'events_end_date'   => '',
        'title'             => '',
        'events_location'   => '',
        'acf-desc'          => '',
    ], $atts, 'add-to-calendar-button');


// Get ACF field values
    $post_id  = get_the_ID();


// POST/PRODUCT URL (Event URL)
    $event_url = esc_html__( 'Read more:' ) . ' ' . get_permalink( $post_id );
    $event_url_button = '<a href="' . esc_url( $event_url ) . '" target="_blank">' . esc_html__( 'Read more' ) . '</a>';


// POST/PRODUCT TITLE
    $title = get_the_title( $post_id );


// MULTI-LOCATION OPTIONS

    // Initialize location with a default value
    $location = "Location not specified";

    $location_value = get_field('event_location')['value'];

    if ($location_value == 'location1') {
        $location = "Replace with actual location1 address";
    }elseif ($location_value == 'location2') {
        $location = "Replace with actual location2 address"; 
    }
    
// DESCRIPTION
    $words = get_the_content();

    // Fix for the strpos error
    $words = strip_tags($words); // First strip HTML tags
    $length = 300; // Desired maximum length
    if (strlen($words) > $length) {
        // Find the last space within the 300 character limit
        $last_space = strrpos(substr($words, 0, $length), ' ');
        // If found, cut at the last space, otherwise cut at 300 characters
        // $desc = substr($words, 0, ($last_space !== false ? $last_space : $length)) . '...';
        $desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ), 40, '...' );
    } else {
        // If content is shorter than 300 characters, use it as is
        $desc = $words;
    }
    
    
    // Get DATES ACF field values
    $start_date = get_field('events_start_date');
    $start_time = get_field('start_time_pick');
    $end_date = get_field('events_end_date');
    $end_time = get_field('end_time_pick');

        

    if (!$start_date || !$end_date) {
        echo esc_html__( 'Missing event date fields.', 'txt-add-event-to-calendar' );
        return ob_get_clean();
    }

    // IF date and time are in one field, then Clean up the date string (remove the timezone part for proper parsing)
    $start_date = preg_replace('/T.*$/', '', $start_date); // Remove T00:00:00+03:00 part
    $end_date = preg_replace('/T.*$/', '', $end_date);     

    // Combine date and time
    if ($start_time) {
        $start_datetime = $start_date . ' ' . $start_time;
    } else {
        $start_datetime = $start_date . ' 09:00:00';
    }

    if ($end_time) {
        $end_datetime = $end_date . ' ' . $end_time;
    } else {
        $end_datetime = $end_date . ' 17:00:00';
    }


    // Google calendar
        // Google Calendar times will be automatically converted to the user's local timezone
        
        // Set the timezone to your local timezone from Wordpress dashboard: Settings → General
        $timezone = wp_timezone();
        $utc_timezone = new DateTimeZone('UTC');

        // Create DateTime objects with the local timezone
        $start_dt = new DateTime($start_datetime, $timezone);
        $end_dt = new DateTime($end_datetime, $timezone);

        // Convert to UTC for Google Calendar
        $start_dt->setTimezone($utc_timezone);
        $end_dt->setTimezone($utc_timezone);
        
        // Format for Google Calendar - The 'Z' suffix tells Google "this is UTC time" - Google knows where the viewer is located
        $start_utc = $start_dt->format('Ymd\THis\Z');
        $end_utc = $end_dt->format('Ymd\THis\Z');    



    // Outlook calendar
        // For Outlook Calendar - keep in local timezone instead of UTC
        // The Outlook link uses local time with explicit timezone offset (e.g., '+03:00')
        $outlook_start = (new DateTime($start_datetime, $timezone))->format('Y-m-d\TH:i:sP');
        $outlook_end = (new DateTime($end_datetime, $timezone))->format('Y-m-d\TH:i:sP');
    
    
    // Calendar URLs

    // Google Calendar
    $google_url = "https://calendar.google.com/calendar/render?action=TEMPLATE"
    // OR $google_url = "https://www.google.com/calendar/event?action=TEMPLATE"

        . "&text=" . rawurlencode($title)
        . "&dates={$start_utc}/{$end_utc}"
        . "&details=" . rawurlencode($desc . " " . $event_url)
        . "&location=" . rawurlencode($location);

    // Outlook Calendar
    $outlook_url = "https://outlook.live.com/calendar/0/deeplink/compose?"
    // OR $outlook_url = "https://outlook.live.com/owa/?path=/calendar/action/compose&"

        . "subject=" . rawurlencode($title)
        . "&body=" . rawurlencode($desc . " " . $event_url)
        . "&startdt=" . rawurlencode($outlook_start)
        . "&enddt=" . rawurlencode($outlook_end)
        . "&location=" . rawurlencode($location);


    ob_start();    
    ?>

    <!-- Add Event to Calendar UI Button-->
    <div class="uk-inline">
        <button class="uk-button uk-button-text" type="button">
            <?php esc_html_e( 'Add to Calendar', 'txt-add-event-to-calendar' ); ?>
        </button>
        <div class="uk-padding-small" uk-dropdown="mode: click">
            <ul class="uk-nav uk-dropdown-nav">
                <li>
                    <a href="<?php echo esc_url( $google_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Google Calendar', 'txt-add-event-to-calendar' ); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url( $outlook_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Outlook Calendar', 'txt-add-event-to-calendar' ); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php

    return ob_get_clean();
}

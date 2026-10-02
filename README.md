# Add Event to Calendar - Shortcode plugin
A lightweight WordPress plugin that adds an **Add to Calendar** dropdown button to your events. It reads the event dates from ACF fields and builds ready-to-use links for Google Calendar and Outlook.

## Features

- `[add-to-calendar-button]` shortcode, one line to drop it anywhere
- Google Calendar and Outlook Calendar links
- Event title, description, location and permalink included automatically
- Timezone-aware (dates converted correctly for Google, offset kept for Outlook)
- Translation ready (text domain: `txt-add-event-to-calendar`)
- Styled with UIkit's button and dropdown components

## Requirements

- WordPress
- [Advanced Custom Fields](https://www.advancedcustomfields.com/)
- [UIkit](https://getuikit.com/) (e.g. via a YOOtheme theme)

## ACF fields used

| Field name | Purpose |
|---|---|
| `events_start_date` | Event start date |
| `events_end_date` | Event end date |
| `start_time_pick` | Start time (optional, defaults to 09:00) |
| `end_time_pick` | End time (optional, defaults to 17:00) |
| `event_location` | Location key, mapped to an address in the plugin file |

## Usage

1. Upload the plugin folder to `/wp-content/plugins/` and activate it.
2. Add the shortcode to an event page or template:

   `[add-to-calendar-button]`

3. Edit the location list in the plugin file to match your own venues.

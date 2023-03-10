<?php

if (!class_exists('RationalOptionPages')) {
    require __DIR__ . '/RationalOptionPages/RationalOptionPages.php';
}

$pages = array(
    'aa_berlin_addons_options' => array(
        'page_title' => __('AA Berlin Addons', 'aa-berlin-addons'),
        'menu_slug' => 'aa_berlin_addons_options',
        // no parent -> top-level item
        // 'parent_slug' => 'options-general.php',
        'icon_url' => aa_berlin_addons_to_data_uri(__DIR__ . '/../assets/images/berlin-bear.svg'),
        'position' => 61,
        'sections' => array(

            'links' => array(
                'title' => __('Link substitution', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic substitution of anchor elements for fully qualified secure urls.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Insert links for urls and phone numbers', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'If a paragraph or list item does not have any other tags, then full urls ´https://example.com/wow/look´ and phone numbers ´+41 (0) 123 456´ will be turned into links.',
                        'id' => 'insert_links',
                    ),

                    array(
                        'title' => __('Treat links to these domains as ´online meeting´ links', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'zoom.us',
                        'text' => 'Separate multiple domains by comma, case-insensitive',
                        'id' => 'stream_domains_pattern',
                    ),

                    array(
                        'title' => __('Extract meeting id from zoom.us links and append to paragraph as well', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Finds the meeting id in each zoom link and writes it into the containing paragraph to help people with accessing the meeting via phone.',
                        'id' => 'append_zoom_meeting_id',
                    ),

// TODO: fix or remove feature; date comparison not DST compatible yet
//                    array(
//                        'title' => __('Disable online meeting links, if meeting is not currently on', 'aa-berlin-addons'),
//                        'type' => 'checkbox',
//                        'text' => 'Disables online meeting links 30min before and after the meetings scheduled start and end time.',
//                        'id' => 'disable_outside_schedule',
//                    ),

                    array(
                        'title' => __('Show the following hint text as a notice of this severity', 'aa-berlin-addons'),
                        'type' => 'select',
                        'choices' => array(
                            '' => __('Disable hint', 'aa-berlin-addons'),
                            'warning' => __('Warning', 'aa-berlin-addons'),
                            'success' => __('Success', 'aa-berlin-addons'),
                            'info' => __('Info', 'aa-berlin-addons'),
                        ),
                        'id' => 'stream_link_hint_type',
                    ),

                    array(
                        'title' => __('Common hint to be shown after each online meeting link', 'aa-berlin-addons'),
                        'type' => 'wp_editor',
                        'value' => '',
                        'text' => 'Inserted after each online meeting link.',
                        'id' => 'stream_link_hint',
                    ),

                    array(
                        'title' => __('Show the following hint text as a notice of this severity for meetings marked as ´Password-less´', 'aa-berlin-addons'),
                        'type' => 'select',
                        'choices' => array(
                            '' => __('Disable hint', 'aa-berlin-addons'),
                            'neither' => __('Disable hint, disable default', 'aa-berlin-addons'),
                            'warning' => __('Warning', 'aa-berlin-addons'),
                            'success' => __('Success', 'aa-berlin-addons'),
                            'info' => __('Info', 'aa-berlin-addons'),
                        ),
                        'text' => 'For meetings marked as ´Password-less´, overrides the default online link hint with the following hint text as a notice of this severity',
                        'id' => 'passwordless_stream_link_hint_type',
                    ),

                    array(
                        'title' => __('Common hint to be shown after each online link for meetings marked as ´Password-less´', 'aa-berlin-addons'),
                        'type' => 'wp_editor',
                        'value' => '',
                        'text' => 'Inserted after each online meeting link, if meeting is marked as ´Password-less´.',
                        'id' => 'passwordless_stream_link_hint',
                    ),
                ),
            ),

            'automatic_notices' => array(
                'title' => __('Automatic Notices', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic insertion of highlighting notices in paragraphs that do not contain any other tags; e.g. in the meeting notes on the meeting detail.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Insert notices', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'insert_notices',
                    ),

                    array(
                        'title' => __('Warning prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'IMPORTANT:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'warning_prefix',
                    ),

                    array(
                        'title' => __('Success prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'UPDATE:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'success_prefix',
                    ),

                    array(
                        'title' => __('Info prefix', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'INFO:',
                        'text' => 'Case-sensitive, no double quotes',
                        'id' => 'info_prefix',
                    ),
                ),
            ),

            'type_online' => array(
                'title' => __('Handling of meeting type Online', 'aa-berlin-addons'),
                'text' => '<p>' . __('Adds some custom behaviour of meetings with type ONLINE (ONLINEINE).', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Rename meeting type ONLINE', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'add_type_online',
                    ),

                    array(
                        'title' => __('Label of meeting type ONLINE', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'Join Online',
                        'id' => 'label_type_online',
                    ),

                    array(
                        'title' => __('Disable map on meeting detail', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Overlays the map with a bold warning, if the meeting is not also marked as "Face-to-Face".',
                        'id' => 'disable_map_if_online',
                    ),

                    array(
                        'title' => __('Text of map overlay', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'This meeting ONLY meets online until further notice.',
                        'text' => 'Should describe why the map is rendered inaccessible; no double-quotes',
                        'id' => 'disable_map_text_online',
                    ),
                ),
            ),

            'temporary_closure' => array(
                'title' => __('Handling of meeting type Temporary Closure', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Mute address in listing, if both ONLINE and TC', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'If a meeting is both Online and Temporary Closure, then show its address in faded and strike-through in the listing',
                        'id' => 'muted_address_in_listing_if_tc_and_onl',
                    ),

                    array(
                        'title' => __('Disable map on meeting detail', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Overlays the map with a bold warning, that it cannot be visited in person.',
                        'id' => 'disable_map_if_tc',
                    ),

                    array(
                        'title' => __('Text of map overlay', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'This meeting is suspended until further notice.',
                        'text' => 'Should describe why the map is rendered inaccessible; no double-quotes',
                        'id' => 'disable_map_text_tc',
                    ),
                ),
            ),

            'misc_tsml' => array(
                'title' => __('Miscellaneous: 12 Step Meeting List', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Add custom set of type flags', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'M,W,TC,ONL',
                        'text' => 'List of meeting types (keys only) that should be promoted as textual flags next to the meeting name; separated by comma',
                        'id' => 'custom_type_flags_add',
                    ),

                    array(
                        'title' => __('Remove from set of type flags', 'aa-berlin-addons'),
                        'type' => 'text',
                        'text' => 'List of meeting types (keys only) that should never be promoted as textual flags next to the meeting name; separated by comma; use this to switch certain types off',
                        'id' => 'custom_type_flags_remove',
                    ),

                    array(
                        'title' => __('Activate QR code and copy-button for sharing of meetings', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'Activate to display a QR code and text field with copy button so that meeting data can be shared more easily.',
                        'id' => 'enable_sharing',
                    ),
                ),
            ),

            'misc_wordpress' => array(
                'title' => __('Miscellaneous: WordPress', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Wrap leading link of single-post Latest Posts widgets in H2', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'wrap_single_entry_links_with_h2',
                    ),

                    array(
                        'title' => __('Change the default FROM e-mail address', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'text' => 'BE SURE to test your e-mail settings after changing these!',
                        'id' => 'change_default_email_address',
                    ),

                    array(
                        'title' => __('Default FROM e-mail address', 'aa-berlin-addons'),
                        'type' => 'text',
                        'readonly' => aa_berlin_addons_has_smtp_settings(),
                        'text' => aa_berlin_addons_has_smtp_settings() ? 'DISABLED, as you are using SMTP_* settings in wp-config.php' : 'BE SURE to test your e-mail settings after changing these!',
                        'id' => 'default_from_email_address',
                    ),

                    array(
                        'title' => __('Default FROM e-mail name', 'aa-berlin-addons'),
                        'type' => 'text',
                        'readonly' => aa_berlin_addons_has_smtp_settings(),
                        'text' => aa_berlin_addons_has_smtp_settings() ? 'DISABLED, as you are using SMTP_* settings in wp-config.php' : 'BE SURE to test your e-mail settings after changing these!',
                        'id' => 'default_from_email_name',
                    ),

                    array(
                        'title' => __('Lifetime of cookies of password-proteced pages in days', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 10,
                        'id' => 'post_password_expires',
                    ),

                    array(
                        'title' => __('Global passwords list', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => '',
                        'id' => 'post_global_passwords',
                        'text' => 'Multiple passwords separated by commas (","); usable on all pages and posts that have a tick at `Allow use of global passwords`.',
                    ),

                    array(
                        'title' => __('Global passwords may be entered inaccurately', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'post_global_passwords_may_be_inaccurate',
                        'text' => 'Check this, if users may enter a password with a typo in every few characters.',
                    ),

                    array(
                        'title' => __('Do not require password for protected pages if logged in', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'no_pw_if_logged_in',
                        'text' => 'Check this, if being logged into the backend also clears all password protections for protected pages.',
                    ),

                    array(
                        'title' => __('Activate Adminer integration', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'activate_adminer',
                        'text' => 'Check this to enable In-WordPress access to the database via Adminer. (Admin status is required anyways, so this checkbox is mainly to prevent accidental access and mishaps :)',
                    ),

                    array(
                        'title' => __('Documentation Widget on Dashboard', 'aa-berlin-addons'),
                        'type' => 'wp_editor',
                        'value' => '',
                        'text' => 'Shown on the Dashboard to an admin user; put useful hints and links to documentation here.',
                        'id' => 'dashboard_widget_docs',
                    ),
                ),
            ),

            'misc_aa' => array(
                'title' => __('Miscellaneous: Region and GSO', 'aa-berlin-addons'),

                'fields' => array(
                    array(
                        'title' => __('Render a link to and in the vein of the GSO GB Chat Now widget', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'show_gso_gb_chat',
                    ),
                ),
            ),
        )
    ),
);

$option_page = new RationalOptionPages($pages);

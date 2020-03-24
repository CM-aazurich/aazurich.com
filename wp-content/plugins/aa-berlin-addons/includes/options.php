<?php

if (!class_exists('RationalOptionPages')) {
    require __DIR__ . '/RationalOptionPages/RationalOptionPages.php';
}

$pages = array(
    'aa_berlin_addons_options' => array(
        'page_title' => __('AA Berlin Addons', 'aa-berlin-addons'),
        'menu_slug' => 'aa_berlin_addons_options',
        'parent_slug' => 'options-general.php',
        'icon_url' => 'dashicons-carrot',
        'position' => 61,
        'sections' => array(

            'links' => array(
                'title' => __('Link substitution', 'aa-berlin-addons'),
                'text' => '<p>' . __('Configures the automatic substitution of anchor elements for fully qualified secure urls.', 'aa-berlin-addons') . '</p>',

                'fields' => array(
                    array(
                        'title' => __('Insert links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'insert_links',
                    ),

                    array(
                        'title' => __('Prepend "headphones" icon to online meeting links', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'prepend_stream_icons',
                    ),

                    array(
                        'title' => __('Treat links to these domains as "online meeting" links', 'aa-berlin-addons'),
                        'type' => 'text',
                        'value' => 'zoom.us',
                        'text' => 'Separate multiple domains by comma, case-insensitive',
                        'id' => 'stream_domains_pattern',
                    ),

                    array(
                        'title' => __('Disable "online meeting" links if meeting is not scheduled', 'aa-berlin-addons'),
                        'type' => 'checkbox',
                        'id' => 'disable_outside_schedule',
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
        )
    ),
);

$option_page = new RationalOptionPages($pages);

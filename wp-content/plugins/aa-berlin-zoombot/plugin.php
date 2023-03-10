<?php

/**
 * Plugin Name: AA Berlin Zoombot
 * Author: AA Berlin <ecomm.berlin@aamail.org>
 * Description: Allows for integration with Zoom online meetings providing e.g. coin ceremony features.
 * Text Domain: aa-berlin-zoombot
 * Domain Path: /languages/
 * Version: 0.1.0
 */

define('AA_BERLIN_ZOOMBOT_VERSION', '0.1.0');

register_activation_hook(__FILE__, 'aa_berlin_zoombot_activate');
add_action('init', 'aa_berlin_zoombot_init');
add_action('rest_api_init', 'aa_berlin_zoombot_rest_api_init');
add_action('wp_router_generate_routes', 'aa_berlin_zoombot_generate_routes');

function aa_berlin_zoombot_options($key = null) {
    static $options = null;

    if ($options === null) {
        $options = get_option('aa_berlin_zoombot_options', array());
    }

    if (!$key) {
        return $options;
    }

    if (isset($options[$key])) {
        return $options[$key];
    }

    return null;
}

function aa_berlin_zoombot_activate() {
    aa_berlin_zoombot_ensure_dependencies();
}

function aa_berlin_zoombot_ensure_dependencies() {
    $plugin_tests = [
        'aa_berlin_addons_init' => 'AA Berlin Addons',
    ];

    foreach ($plugin_tests as $fn => $plugin) {
        if (!function_exists($fn)) {
            deactivate_plugins(plugin_basename(__FILE__ ));

            wp_die(
                // translators: %s is the readable plugin name
                sprintf(__('Please install and activate %s before activating AA Berlin Zoombot.', 'aa-berlin-zoombot'), $plugin),
                'Plugin dependency check',
                array('back_link' => true)
            );
        }
    }
}

function aa_berlin_zoombot_init() {
    require __DIR__ . '/includes/options.php';
}

function aa_berlin_zoombot_rest_api_init() {
    $namespace = 'aa-berlin-zoombot/v1';

    register_rest_route(
        $namespace,
        '/authorize',
        array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'aa_berlin_zoombot_route_authorize',
        )
    );

    register_rest_route(
        $namespace,
        '/deauthorize',
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'aa_berlin_zoombot_route_deauthorize',
        )
    );

    register_rest_route(
        $namespace,
        '/command',
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'aa_berlin_zoombot_route_command',
        )
    );
}

function aa_berlin_zoombot_route_authorize() {
    $url = 'https://zoom.us/launch/chat?jid=robot_' . aa_berlin_zoombot_options('zoom_bot_jid');

    $response = rest_ensure_response([
        'location' => $url,
        'status' => 302,
    ]);

    $response->set_status(302);
    $response->header('Location', $url);

    return $response;
}

function aa_berlin_zoombot_route_deauthorize(WP_REST_Request $request) {
    $response = new WP_REST_Response();
    $response->set_status(200);

    if ($request->get_header('Authorization') !== aa_berlin_zoombot_options('zoom_verification_token')) {
        $response->set_status(401);
        $response->set_data([
            'status' => 401,
            'message' => 'Authorization did not match zoom_verification_token',
        ]);

        return $response;
    }

    $compliance_url = 'https://api.zoom.us/oauth/data/compliance';

    $body = $request->get_json_params();
    $payload = $body['payload'];

    $compliance_body = [
        'client_id' => $payload['client_id'],
        'user_id' => $payload['user_id'],
        'account_id' => $payload['account_id'],
        'deauthorization_event_received' => $payload,
    ];

    $auth_header = aa_berlin_zoombot_get_client_basic_auth();

    $compliance_response = wp_remote_post(
        $compliance_url,
        [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $auth_header,
                'Cache-Control' => 'no-cache',
            ],
            'body' => json_encode($compliance_body),
        ]
    );

    $response->set_data($compliance_response);

    return $response;
}

function aa_berlin_zoombot_route_command(WP_REST_Request $request) {
    $response = new WP_REST_Response();
    $response->set_status(200);

    $body = $request->get_json_params();
    $payload = $body['payload'];

    $chat_messages_url = 'https://api.zoom.us/v2/im/chat/messages';
    $access_token = aa_berlin_zoombot_fetch_access_token();

    $reply_body = json_encode([
        'robot_jid' => aa_berlin_zoombot_options('zoom_bot_jid'),
        'to_jid' => $payload['toJid'],
        'account_id' => $payload['accountId'],
        'content' => [
            'head' => [
                'text' => 'Sober?',
            ],
            'body' => [
                [
                    'type' => 'message',
                    'text' => 'Wooh! Sober for ' . $payload['cmd'],
                ],
            ],
        ],
    ]);

    $chat_messages_response = wp_remote_post(
        $chat_messages_url,
        [
            'body' => $reply_body,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $access_token,
                'Cache-Control' => 'no-cache',
            ],
        ]
    );

    return $response;
}

function aa_berlin_zoombot_fetch_access_token() {
    $token_url = 'https://api.zoom.us/oauth/token?grant_type=client_credentials';
    $auth_header = aa_berlin_zoombot_get_client_basic_auth();

    $token_response = wp_remote_post(
        $token_url,
        [
            'headers' => [
                'Authorization' => $auth_header,
                'Cache-Control' => 'no-cache',
            ],
        ]
    );

    $token_body = json_decode($token_response['body'], true);

    return $token_body['access_token'];
}

function aa_berlin_zoombot_get_client_basic_auth() {
    $zoom_client_id = aa_berlin_zoombot_options('zoom_client_id');
    $zoom_client_secret = aa_berlin_zoombot_options('zoom_client_secret');

    return 'Basic ' . base64_encode($zoom_client_id . ':' . $zoom_client_secret);
}

function aa_berlin_zoombot_generate_routes(WP_Router $router) {
    $router->add_route(
        'aa-berlin-zoombot-zoomverify',
        [
            'path' => 'zoomverify/verifyzoom.html',
            'access_callback' => true,
            'template' => false,
            'page_callback' => 'aa_berlin_zoombot_route_zoomverify',
        ]
    );
}

function aa_berlin_zoombot_route_zoomverify() {
    return aa_berlin_zoombot_options('zoom_verification_token');
}

<?php
// Include firewall configuration (API token, firewall IP)
require_once 'firewall_config.php';

/**
 * Sends an API request to the FortiGate firewall.
 *
 * @param string $endpoint API endpoint (e.g., "/cmdb/system/dns-database/qs.local/dns-entry")
 * @param string $method HTTP method ("GET", "POST", "DELETE", etc.)
 * @param array|null $payload Data to send (null for GET requests)
 * @return array Response from the FortiGate API
 */
function firewall_api_request($endpoint, $method = "GET", $payload = null) {
    $url = "https://" . FIREWALL_IP . "/api/v2" . $endpoint;
    $headers = [
        "Authorization: Bearer " . API_TOKEN,
        "Content-Type: application/json"
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method !== "GET" && $payload !== null) {
        // Ensure proper JSON encoding and avoid double-encoding
        if (is_array($payload)) {
            $json_payload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $json_payload = $payload; // Prevent re-encoding if already JSON
        }

        // Log the exact payload for debugging
        file_put_contents('/var/www/html/test/debug_payload.log', $json_payload);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    return [
        "status" => $http_code,
        "response" => json_decode($response, true),
        "error" => $error
    ];
}
?>

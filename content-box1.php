<?php
ob_start(); // Start output buffering to prevent early output issues

// Ensure domain and zone variables exist
$selected_domain = $MOST_COMMON_DOMAIN ?? "";
$selected_zone = $MOST_COMMON_ZONE ?? "";

// Function to check if a PTR record exists for a given IP
function ptr_record_exists($zone_name, $ip_address) {
    $response = firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry", "GET");
    if (!isset($response["response"]["results"])) {
        return false;
    }

    foreach ($response["response"]["results"] as $entry) {
        if ($entry["type"] === "PTR" && $entry["ip"] === $ip_address) {
            return true; // PTR record already exists
        }
    }
    return false; // No PTR record found
}

// Handle form submission (A record creation)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_record'])) {
    $zone_name = trim($_POST['zone_name'] ?? "");
    $domain_name = trim($_POST['domain'] ?? "");
    $hostname = trim($_POST['hostname'] ?? "");
    $ip_address = trim($_POST['ip_address'] ?? "");

    // Ensure hostname is valid and remove unwanted characters
    $short_hostname = preg_replace('/[^a-zA-Z0-9\-]/', '', $hostname);

    // Construct API payload for A record
    $a_record = [
        "hostname" => (string) $short_hostname,
        "type" => "A",
        "ip" => $ip_address,
        "ipv6" => "::",
        "ttl" => 0,
        "preference" => 10,
        "status" => "enable",
        "canonical-name" => ""
    ];

    // Send A record to the firewall
    firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry", "POST", json_encode($a_record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Check if PTR record for this IP exists, if not, add it
    if (!ptr_record_exists($zone_name, $ip_address)) {
        $ptr_record = [
            "hostname" => $short_hostname, // PTR usually points to the hostname of the A record
            "type" => "PTR",
            "ip" => $ip_address,
            "ipv6" => "::",
            "ttl" => 0,
            "preference" => 10,
            "status" => "enable",
            "canonical-name" => ""
        ];

        // Send PTR record to the firewall
        firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry", "POST", json_encode($ptr_record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    // Reload the page to reflect changes
    header("Location: " . $_SERVER['REQUEST_URI']);
    ob_end_flush(); // Flush output buffer before exiting
    exit;
}
?>

<!-- Add DNS Record Form (Right Side) -->
<div class="form-container">
    <form method="POST">
        <select name="zone_name" required>
            <option value="" disabled selected>Select Zone</option>
            <?php foreach ($ALL_ZONES as $zone): ?>
                <option value="<?= htmlspecialchars($zone) ?>" <?= ($zone == $selected_zone) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($zone) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="domain" required>
            <option value="" disabled selected>Select Domain</option>
            <?php foreach ($ALL_DOMAINS as $domain): ?>
                <option value="<?= htmlspecialchars($domain) ?>" <?= ($domain == $selected_domain) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($domain) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="hostname" id="hostname" required placeholder="Enter hostname (e.g., 'a')">
        <input type="text" name="ip_address" id="ip_address" required placeholder="Enter IP Address">

        <!-- Hidden fields for defaults -->
        <input type="hidden" name="record_type" value="A">
        <input type="hidden" name="status" value="enable">

        <button type="submit" name="add_record" class="add-button">Add Record</button>
    </form>
</div>

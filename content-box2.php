<?php
ob_start(); // Start output buffering to prevent early output issues

// Ensure domain and zone variables exist
$selected_domain = $MOST_COMMON_DOMAIN ?? "";
$selected_zone = $MOST_COMMON_ZONE ?? "";

// Handle form submission (CNAME record creation)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_cname'])) {
    $zone_name = trim($_POST['zone_name'] ?? "");
    $domain_name = trim($_POST['domain'] ?? "");
    $hostname = trim($_POST['hostname'] ?? "");
    $canonical_name = trim($_POST['canonical_name'] ?? "");

    // Ensure hostname and canonical name are valid
    $short_hostname = preg_replace('/[^a-zA-Z0-9\-]/', '', $hostname);
    $short_canonical = preg_replace('/[^a-zA-Z0-9\-\.]/', '', $canonical_name);

    // Construct API payload for CNAME record
    $cname_record = [
        "hostname" => (string) $short_hostname,
        "type" => "CNAME",
        "ip" => "0.0.0.0", // FortiGate requires an IP, but it's ignored for CNAMEs
        "ipv6" => "::",
        "ttl" => 0,
        "preference" => 10,
        "status" => "enable",
        "canonical-name" => (string) $short_canonical
    ];

    // Send CNAME record to the firewall
    firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry", "POST", json_encode($cname_record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    // Reload the page to reflect changes
    header("Location: " . $_SERVER['REQUEST_URI']);
    ob_end_flush(); // Flush output buffer before exiting
    exit;
}
?>

<!-- Add CNAME Record Form (Right Side) -->
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

        <input type="text" name="hostname" id="hostname" required placeholder="Enter alias hostname (e.g., 'www')">
        <input type="text" name="canonical_name" id="canonical_name" required placeholder="Enter target hostname (e.g., 'example.com')">

        <!-- Hidden fields for defaults -->
        <input type="hidden" name="record_type" value="CNAME">
        <input type="hidden" name="status" value="enable">

        <button type="submit" name="add_cname" class="add-button">Add Record</button>
    </form>
</div>

<?php
// Query the FortiGate firewall for DNS records
$dns_records = firewall_api_request("/cmdb/system/dns-database");

// Initialize arrays for storing zone names and domain names
$zone_names = [];
$domain_names = [];

// Populate arrays with data from the API response
if (isset($dns_records['response']['results'])) {
    foreach ($dns_records['response']['results'] as $zone) {
        if (!empty($zone['name'])) {
            $zone_names[] = $zone['name'];
        }
        if (!empty($zone['domain'])) {
            $domain_names[] = $zone['domain'];
        }
    }
}

// Function to find the most common value in an array
function find_most_common($array) {
    if (empty($array)) return null;
    $counts = array_count_values($array);
    arsort($counts);
    return array_key_first($counts);
}

// Define global variables for the most common zone and domain
$MOST_COMMON_ZONE = find_most_common($zone_names);
$MOST_COMMON_DOMAIN = find_most_common($domain_names);

// Store all zones and domains in arrays
$ALL_ZONES = array_unique($zone_names);
$ALL_DOMAINS = array_unique($domain_names);

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_selected']) && !empty($_POST['selected_records'])) {
    foreach ($_POST['selected_records'] as $record) {
        list($zone_name, $record_id) = explode("|", $record);
        firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry/$record_id", "DELETE");
    }
    $dns_records = firewall_api_request("/cmdb/system/dns-database"); // Refresh data
}
?>

<form method="POST">
    <table class="dns-table">
        <thead>
            <tr>
                <th>Select</th>
                <th>Zone Name</th>
                <th>Domain</th>
                <th>Type</th>
                <th>Hostname</th>
                <th>IP Address</th>
                <th>Record Type</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dns_records['response']['results'] as $zone): ?>
                <?php foreach ($zone['dns-entry'] as $entry): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_records[]" 
                                   value="<?= htmlspecialchars($zone['name']) ?>|<?= htmlspecialchars($entry['id']) ?>">
                        </td>
                        <td><?= htmlspecialchars($zone['name']) ?></td>
                        <td><?= htmlspecialchars($zone['domain']) ?></td>
                        <td><?= htmlspecialchars($zone['type']) ?></td>
                        <td><?= htmlspecialchars($entry['hostname']) ?></td>
                        <td><?= htmlspecialchars($entry['ip']) ?></td>
                        <td><?= htmlspecialchars($entry['type']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="button-container">
        <button type="submit" name="delete_selected" class="delete-button">Delete Selected</button>
    </div>
</form>

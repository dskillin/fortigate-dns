<?php

// Handle DNS Backup (Export)
if (isset($_POST['backup_dns'])) {
    // Disable any existing output buffering
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Ensure absolutely NO output before JSON
    header_remove();  // Remove any pre-set headers
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="dns_backup_' . date("Y-m-d_H-i-s") . '.json"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Fetch DNS records
    $dns_records = firewall_api_request("/cmdb/system/dns-database", "GET");

    // Check if response contains results
    if (isset($dns_records['response']['results']) && is_array($dns_records['response']['results']) && count($dns_records['response']['results']) > 0) {
        echo json_encode($dns_records['response']['results'], JSON_PRETTY_PRINT);
    } else {
        echo json_encode(["error" => "No DNS records found!"]);
    }
    exit; // Stop execution immediately
}


// Handle DNS Restore (Import)
if (isset($_POST['restore_dns']) && isset($_FILES['dns_file'])) {
    // Ensure no previous output
    if (ob_get_length()) {
        ob_end_clean();
    }

    $file = $_FILES['dns_file']['tmp_name'];

    if ($file && file_exists($file)) {
        $import_data = json_decode(file_get_contents($file), true);

        // Validate JSON structure
        if (is_array($import_data)) {
            foreach ($import_data as $zone) {
                if (!isset($zone['name']) || !isset($zone['dns-entry'])) {
                    $restore_message = "Invalid file structure!";
                    break;
                }

                $zone_name = $zone['name'];

                foreach ($zone['dns-entry'] as $entry) {
                    if (!isset($entry['hostname'], $entry['type'], $entry['ip'])) {
                        continue; // Skip invalid records
                    }

                    // Prepare the API payload (remove unnecessary fields)
                    $record_data = [
                        "hostname" => $entry["hostname"],
                        "type" => $entry["type"],
                        "ip" => $entry["ip"],
                        "status" => $entry["status"] ?? "enable",
                        "ttl" => $entry["ttl"] ?? 0,
                    ];

                    if ($entry["type"] === "CNAME" && isset($entry["canonical-name"])) {
                        $record_data["canonical-name"] = $entry["canonical-name"];
                    }

                    // Send to Firewall API
                    firewall_api_request("/cmdb/system/dns-database/$zone_name/dns-entry", "POST", $record_data);
                }
            }

            // ✅ Ensure no HTML is printed before redirect
            if (!headers_sent()) {
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
                exit;
            }
        } else {
            $restore_message = "Invalid file format!";
        }
    } else {
        $restore_message = "No file selected!";
    }
}

?>

<div class="backup-container">
    <!-- Backup DNS Button -->
    <form method="POST">
        <button type="submit" name="backup_dns" class="backup-button">Backup DNS</button>
    </form>

    <!-- Restore DNS Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="dns_file" accept=".json" required>
        <button type="submit" name="restore_dns" class="restore-button">Restore DNS</button>
    </form>

    <!-- Display Messages -->
    <?php if (isset($backup_message)) echo "<p class='message'>$backup_message</p>"; ?>
    <?php if (isset($restore_message)) echo "<p class='message'>$restore_message</p>"; ?>
</div>

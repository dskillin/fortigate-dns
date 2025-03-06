# **Fortigate DNS Management Web Interface**  
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.txt)  
A simple, web-based interface for **managing FortiGate DNS records** via the **Fortinet API**.

## **📌 Features**
✅ **View DNS Records** – Displays current DNS records in a clean, sortable table.  
✅ **Add A Records** – Supports adding **A records** with automatic **PTR record creation**.  
✅ **Add CNAME Records** – Easily create **CNAME records** for domain aliasing.  
✅ **Delete DNS Records** – Select multiple records and delete them with one click.  
✅ **Backup & Restore** – Save all DNS records as a JSON backup and restore when needed.  
✅ **Responsive UI** – Clean, structured layout with **FortiGate-inspired styling**.  

---

## **⚠️ Security Warning: Do Not Expose Publicly**
🚨 **This application should NOT be hosted on a public-facing web server.**  
It is designed for use on a **strictly controlled internal network** only.

🔹 **Recommended Deployment:**
- **Internal Web Server Only** – Deploy within a private network behind a firewall.  
- **Restrict External Access** – Use IP restrictions or firewall rules to limit access.  
- **Use Cloudflared for Secure Access** – If remote access is needed, consider using **Cloudflared** or a similar tunnel service.  
- **Login System is in the To-Do List** – A login feature is planned but currently **not implemented**, requiring manual access control.

Failure to properly secure this application could expose **critical DNS configurations** to unauthorized users.

---

## **📌 Installation & Setup**
### **1️⃣ Requirements**
- **Web Server**: Apache, Nginx, or similar (PHP 7.4+ recommended)
- **FortiGate API Access**: Enabled for DNS management
- **PHP Extensions**: cURL and JSON enabled  

### **2️⃣ Installation**
1. **Clone the Repository**
   ```sh
   git clone https://github.com/dskillin/fortigate-dns.git
   cd fortigate-dns
   ```

2. **Configure API Settings**
   - Edit **`firewall_config.php`**:
     ```php
     define('FIREWALL_IP', 'YOUR_FIREWALL_IP');
     define('API_TOKEN', 'YOUR_FORTIGATE_API_KEY');
     ```
   - Ensure the API key has DNS management permissions.

3. **Set Up Your Web Server**
   - Move the files to your web root (`/var/www/html/fortigate-dns/`).
   - **Set file permissions**:
     ```sh
     chmod -R 755 /var/www/html/fortigate-dns/
     ```

4. **Access the Web Interface**
   - Open your browser and go to:
     ```
     http://your-server-ip/fortigate-dns/
     ```

---

## **📌 Usage**
🔹 **View and delete DNS records** in the left section.  
🔹 **Add A records** in Box 1 (PTR records are created automatically).  
🔹 **Add CNAME records** in Box 2.  
🔹 **Backup & Restore DNS records** in Box 3.  
🔹 **See licensing & credits** in Box 4.  

---

## **📌 Folder Structure**
```
📂 fortigate-dns
├── 📄 index.php            # Main web interface
├── 📄 styles.css           # UI styles
├── 📄 firewall_config.php  # API credentials (edit this file)
├── 📄 fortinet_firewall.php # API functions
├── 📄 content-left.php     # DNS records table
├── 📄 content-box1.php     # Add A record form
├── 📄 content-box2.php     # Add CNAME form
├── 📄 content-box3.php     # Backup & Restore functions
├── 📄 content-box4.php     # Signature & licensing
└── 📄 LICENSE.txt          # MIT License (Attribution Required)
```

---

## **📌 Contributing**
🔹 **Fork the repo**, create a feature branch, and submit a PR.  
🔹 **Report bugs or suggest features** via [GitHub Issues](https://github.com/dskillin/fortigate-dns/issues).  
🔹 Follow the **MIT License (Attribution Required)**.  

---

## **📌 License**
This project is licensed under the **MIT License (Attribution Required)**.  
By using or modifying this project, you must **credit the original author**.

🔹 **Fortinet Disclaimer:**  
*"FortiGate" and "Fortinet" are trademarks of Fortinet, Inc. This project is **not affiliated with, endorsed by, or sponsored by Fortinet** in any way.*

📜 **Full License Details**: [LICENSE.txt](LICENSE.txt)

---

## **📌 Author**
👨‍💻 **Developed by:** [dskillin](https://github.com/dskillin)  
🔗 **GitHub Repository:** [fortigate-dns](https://github.com/dskillin/fortigate-dns)  
📅 **Year:** 2024

---

🚀 **Now you’re ready to manage FortiGate DNS records effortlessly!**  
🔥 **Star this repo if you find it useful!** ⭐  
Let me know if you need any modifications. 😃

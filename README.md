# Hestia_Dashboard

This is a dashboard switcher for Hestia Control Panel that uses the CLI to switch dashboard themes.

## Installation
```bash
./install.sh
```
Then upload the Glass theme (or create your own).
The Theme Manager also comes with an uninstall script if needed.

Manual File Modifications
There are a few files that must be modified manually (the script does not handle these changes yet).
If you do not want to edit them yourself, I have included pre-modified versions under the HestiaMod folder.
---

### 1. Modify `/usr/local/hestia/web/list/index.php`

Change this line:

```php
header("Location: /list/user/");
```
To:
```php
header("Location: /list/dashboard/");
```

### 2. Modify `/usr/local/hestia/web/inc/main.php`
Update the default home page logic to:
```php
// Set home location URLs
if ($_SESSION["userContext"] === "admin" && empty($_SESSION["look"])) {
    // Display users list for administrators unless they are impersonating a user account
    $home_url = "/list/dashboard/";
} else {
    // Set home location URL based on available package features from account
    if ($panel[$user]["DASHBOARD"] != "0") {
        $home_url = "/list/dashboard/";
    } elseif ($panel[$user]["WEB_DOMAINS"] != "0") {
        $home_url = "/list/web/";
    } elseif ($panel[$user]["DNS_DOMAINS"] != "0") {
        $home_url = "/list/dns/";
    } elseif ($panel[$user]["MAIL_DOMAINS"] != "0") {
        $home_url = "/list/mail/";
    } elseif ($panel[$user]["DATABASES"] != "0") {
        $home_url = "/list/db/";
    } elseif ($panel[$user]["CRON_JOBS"] != "0") {
        $home_url = "/list/cron/";
    } elseif ($panel[$user]["BACKUPS"] != "0") {
        $home_url = "/list/backups/";
    } elseif ($panel[$user]["USERS"] != "0") {
        $home_url = "/list/users/";
    }
}
```
### 3. Modify `/usr/local/hestia/web/login/index.php`
Update the areas of logic that determicnes landing pages to:
```php
header("Location: /list/dashboard/");
```
and 

```php
if ($_SESSION["userContext"] === "admin") {
						header("Location: /list/dashboard/");
					} else {
						if ($data[$user]["DASHBOARD"] != "0") {
							header("Location: /list/dashboard/");
						} elseif ($data[$user]["WEB_DOMAINS"] != "0") {
							header("Location: /list/web/");
						} elseif ($data[$user]["DNS_DOMAINS"] != "0") {
							header("Location: /list/dns/");
						} elseif ($data[$user]["MAIL_DOMAINS"] != "0") {
							header("Location: /list/mail/");
						} elseif ($data[$user]["DATABASES"] != "0") {
							header("Location: /list/db/");
						} elseif ($data[$user]["CRON_JOBS"] != "0") {
							header("Location: /list/cron/");
						} elseif ($data[$user]["BACKUPS"] != "0") {
							header("Location: /list/backup/");
						} elseif ($data[$user]["USER"] != "0") {
							header("Location: /list/user/");
						} else {
							header("Location: /error/");
						}
					}
```

### 4. Create the Dashboard Folder
Run the following command to create the new dashboard folder:
```
mkdir -p /usr/local/hestia/web/list/dashboard
```

### 5. Create index.php File
Create your custom index.php inside: `/usr/local/hestia/web/list/dashboard/index.php`
```php
<?php
use function Hestiacp\quoteshellarg\quoteshellarg;
$TAB = "DASHBOARD";

// Main include
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

// Get logged-in user
$user = $_SESSION['user'] ?? null;
if (!$user) {
    die("No user logged in.");
}

// Data
exec(HESTIA_CMD . "v-list-user-stats $user json", $output, $return_var);
$panel[$user] = json_decode(implode("", $output), true);
unset($output);

// Render page
render_page($user, $template, "list_dashboard");
?>
```

### 5. Create the custom css File
under `/usr/local/hestia/web/css/themes/custom/`
create a css file that will only change the colors see example below
```css
/* ================= Colors Only ================= */

/* Sidebar background and text color */
.sidebar {
    background: #1f2937;
    color: #fff;
}

.sidebar a {
    color: #fff;
}

/* Sidebar hover background colors */
.sidebar-tabs a:hover,
.sidebar-menu a:hover {
    background: #374151;
}

/* Active tab highlight */
.sidebar-tabs a.active {
    background: #2563eb;
}

/* Submenu background and hover color */
.sidebar-submenu {
    background: #1f2937;
    border-left: 1px solid rgba(255,255,255,0.1);
}
.sidebar-submenu a {
    color: #fff;
}
.sidebar-submenu a:hover {
    background: #4b5563;
}

/* Scrollbar background */
.sidebar-tabs::-webkit-scrollbar,
.sidebar-menu::-webkit-scrollbar {
    background: transparent;
}

/* Page title underline color */
.underline {
    background-color: #000;
}

/* Color Variables */
:root {
    --hestia-primary: #2563eb;
    --hestia-secondary: #64748b;
    --hestia-success: #10b981;
    --hestia-warning: #f59e0b;
    --hestia-error: #ef4444;
    --hestia-info: #06b6d4;
    --hestia-bg: #f8fafc;
    --hestia-card-bg: #ffffff;
    --hestia-text: #1e293b;
    --hestia-text-muted: #64748b;
    --hestia-border: #e2e8f0;
}

/* Body background color */
body {
    background-color: var(--hestia-bg);
}

/* Notification badge background color */
.notification-badge {
    background: var(--hestia-error);
    color: white;
}

/* Status indicator */
.status-indicator {
    background: var(--hestia-success);
}

.card-icon, .quick-stat-icon {
    color: white;
}

/* Quick Stat Icon Gradients */
.quick-stat-icon.uptime { background: linear-gradient(45deg, var(--hestia-success), #16a34a); }
.quick-stat-icon.time { background: linear-gradient(45deg, var(--hestia-info), #0891b2); }
.quick-stat-icon.cpu { background: linear-gradient(45deg, var(--hestia-warning), #ea580c); }
.quick-stat-icon.ram { background: linear-gradient(45deg, var(--hestia-error), #dc2626); }

/* Card Icon Gradients */
.card-icon.users { background: linear-gradient(45deg, #6366f1, #8b5cf6); }
.card-icon.web { background: linear-gradient(45deg, var(--hestia-primary), var(--hestia-info)); }
.card-icon.mail { background: linear-gradient(45deg, var(--hestia-success), #16a34a); }
.card-icon.database { background: linear-gradient(45deg, var(--hestia-warning), #ea580c); }
.card-icon.cron { background: linear-gradient(45deg, var(--hestia-error), #dc2626); }
.card-icon.disk { background: linear-gradient(45deg, #64748b, #475569); }

/* Progress fill gradient */
.progress-fill {
    background: linear-gradient(45deg, var(--hestia-primary), var(--hestia-info));
}

/* Activity Icon Colors */
.activity-icon.success { background: var(--hestia-success); }
.activity-icon.info { background: var(--hestia-info); }
.activity-icon.warning { background: var(--hestia-warning); }
.activity-icon.error { background: var(--hestia-error); }

/* Status Badge Colors */
.status-badge.running {
    background: rgba(16, 185, 129, 0.1);
    color: var(--hestia-success);
}
.status-badge.warning {
    background: rgba(245, 158, 11, 0.1);
    color: var(--hestia-warning);
}
.status-badge.error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--hestia-error);
}

/* Quick Actions Hover */
.quick-action-btn:hover {
    border-color: var(--hestia-primary);
    background: rgba(37, 99, 235, 0.05);
    color: var(--hestia-primary);
}

    /* Hamburger button */
.hamburger {
    color: var(--hestia-primary);
}

@media (max-width: 768px) {
    /* Change icon color when checked */
    .menu-toggle:checked + .hamburger {
        color: red; /* Red when menu is open */
    }

    /* Sidebar background color */
    .sidebar {
        background: #222; /* Ensure background covers content */
    }
}
```

✅ After completing these steps, HestiaCP will load the new Dashboard page as the default instead of the Users page.

## UN-Install
```bash
./uninstall.sh
```

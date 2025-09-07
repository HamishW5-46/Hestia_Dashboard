<?php
/**
 * Hestia Theme Manager Plugin
 * Version: 1.0.0
 * Description: Allows switching between different UI themes for Hestia Control Panel
 * Author: Custom Plugin
 */

class HestiaThemeManager {
    
    public $plugin_path;
    private $theme_path;
    public $backup_path;
    private $hestia_path;
    private $current_theme;
    private $available_themes;
    
    // Define all files that will be replaced by themes
    private $theme_files = [
        '/usr/local/hestia/web/templates/footer.php',
        '/usr/local/hestia/web/templates/header.php',
        '/usr/local/hestia/web/templates/css/theme.css',
        '/usr/local/hestia/web/templates/includes/app-footer.php',
        '/usr/local/hestia/web/templates/includes/extra-ns-fields.php',
        '/usr/local/hestia/web/templates/includes/login-footer.php',
        '/usr/local/hestia/web/templates/includes/title.php',
        '/usr/local/hestia/web/templates/includes/css.php',
        '/usr/local/hestia/web/templates/includes/js.php',
        '/usr/local/hestia/web/templates/includes/panel.php',
        '/usr/local/hestia/web/templates/includes/email-settings-panel.php',
        '/usr/local/hestia/web/templates/includes/jump-to-top-link.php',
        '/usr/local/hestia/web/templates/includes/password-requirements.php',
        '/usr/local/hestia/web/templates/pages/add_access_key.php',
        '/usr/local/hestia/web/templates/pages/edit_server_bind9.php',
        '/usr/local/hestia/web/templates/pages/list_firewall_banlist.php',
        '/usr/local/hestia/web/templates/pages/add_cron.php',
        '/usr/local/hestia/web/templates/pages/edit_server_dovecot.php',
        '/usr/local/hestia/web/templates/pages/list_firewall_ipset.php',
        '/usr/local/hestia/web/templates/pages/add_db.php',
        '/usr/local/hestia/web/templates/pages/edit_server_httpd.php',
        '/usr/local/hestia/web/templates/pages/list_firewall.php',
        '/usr/local/hestia/web/templates/pages/add_dns.php',
        '/usr/local/hestia/web/templates/pages/edit_server_mysql.php',
        '/usr/local/hestia/web/templates/pages/list_ip.php',
        '/usr/local/hestia/web/templates/pages/add_dns_rec.php',
        '/usr/local/hestia/web/templates/pages/edit_server_nginx.php',
        '/usr/local/hestia/web/templates/pages/list_key.php',
        '/usr/local/hestia/web/templates/pages/add_firewall_banlist.php',
        '/usr/local/hestia/web/templates/pages/edit_server_pgsql.php',
        '/usr/local/hestia/web/templates/pages/list_log_auth.php',
        '/usr/local/hestia/web/templates/pages/add_firewall_ipset.php',
        '/usr/local/hestia/web/templates/pages/edit_server.php',
        '/usr/local/hestia/web/templates/pages/list_log.php',
        '/usr/local/hestia/web/templates/pages/add_firewall.php',
        '/usr/local/hestia/web/templates/pages/edit_server_php.php',
        '/usr/local/hestia/web/templates/pages/list_mail_acc.php',
        '/usr/local/hestia/web/templates/pages/add_ip.php',
        '/usr/local/hestia/web/templates/pages/edit_server_service.php',
        '/usr/local/hestia/web/templates/pages/list_mail_dns.php',
        '/usr/local/hestia/web/templates/pages/add_key.php',
        '/usr/local/hestia/web/templates/pages/edit_user.php',
        '/usr/local/hestia/web/templates/pages/list_mail.php',
        '/usr/local/hestia/web/templates/pages/add_mail_acc.php',
        '/usr/local/hestia/web/templates/pages/edit_web.php',
        '/usr/local/hestia/web/templates/pages/list_packages.php',
        '/usr/local/hestia/web/templates/pages/add_mail.php',
        '/usr/local/hestia/web/templates/pages/edit_whitelabel.php',
        '/usr/local/hestia/web/templates/pages/list_rrd.php',
        '/usr/local/hestia/web/templates/pages/add_package.php',
        '/usr/local/hestia/web/templates/pages/generate_ssl.php',
        '/usr/local/hestia/web/templates/pages/list_search.php',
        '/usr/local/hestia/web/templates/pages/add_user.php',
        '/usr/local/hestia/web/templates/pages/list_access_key.php',
        '/usr/local/hestia/web/templates/pages/list_server_info.php',
        '/usr/local/hestia/web/templates/pages/add_web.php',
        '/usr/local/hestia/web/templates/pages/list_access_keys.php',
        '/usr/local/hestia/web/templates/pages/list_server_preview.php',
        '/usr/local/hestia/web/templates/pages/debug_panel.php',
        '/usr/local/hestia/web/templates/pages/list_backup_detail_incremental.php',
        '/usr/local/hestia/web/templates/pages/list_services.php',
        '/usr/local/hestia/web/templates/pages/edit_backup_exclusions.php',
        '/usr/local/hestia/web/templates/pages/list_backup_detail.php',
        '/usr/local/hestia/web/templates/pages/list_ssl.php',
        '/usr/local/hestia/web/templates/pages/edit_cron.php',
        '/usr/local/hestia/web/templates/pages/list_backup_exclusions.php',
        '/usr/local/hestia/web/templates/pages/list_stats.php',
        '/usr/local/hestia/web/templates/pages/edit_db.php',
        '/usr/local/hestia/web/templates/pages/list_backup_incremental.php',
        '/usr/local/hestia/web/templates/pages/list_terminal.php',
        '/usr/local/hestia/web/templates/pages/edit_dns.php',
        '/usr/local/hestia/web/templates/pages/list_backup.php',
        '/usr/local/hestia/web/templates/pages/list_updates.php',
        '/usr/local/hestia/web/templates/pages/edit_dns_rec.php',
        '/usr/local/hestia/web/templates/pages/list_cron.php',
        '/usr/local/hestia/web/templates/pages/list_user.php',
        '/usr/local/hestia/web/templates/pages/edit_firewall.php',
        '/usr/local/hestia/web/templates/pages/list_db.php',
        '/usr/local/hestia/web/templates/pages/list_webapps.php',
        '/usr/local/hestia/web/templates/pages/edit_ip.php',
        '/usr/local/hestia/web/templates/pages/list_dns.php',
        '/usr/local/hestia/web/templates/pages/list_weblog.php',
        '/usr/local/hestia/web/templates/pages/edit_mail_acc.php',
        '/usr/local/hestia/web/templates/pages/list_dns_public.php',
        '/usr/local/hestia/web/templates/pages/list_web.php',
        '/usr/local/hestia/web/templates/pages/edit_mail.php',
        '/usr/local/hestia/web/templates/pages/list_dns_rec.php',
        '/usr/local/hestia/web/templates/pages/edit_package.php',
        '/usr/local/hestia/web/templates/pages/list_files_incremental.php',
        '/usr/local/hestia/web/templates/pages/list_dashboard.php',
        '/usr/local/hestia/web/templates/pages/setup_webapp.php',
        '/usr/local/hestia/web/templates/pages/login/login_1.php',
        '/usr/local/hestia/web/templates/pages/login/login_2.php',
        '/usr/local/hestia/web/templates/pages/login/login_a.php',
        '/usr/local/hestia/web/templates/pages/login/login.php',
        '/usr/local/hestia/web/templates/pages/login/reset_1.php',
        '/usr/local/hestia/web/templates/pages/login/reset2fa.php',
        '/usr/local/hestia/web/templates/pages/login/reset_2.php',
        '/usr/local/hestia/web/templates/pages/login/reset_3.php'
    ];
    
    public function __construct() {
        $this->plugin_path = '/usr/local/hestia/plugins/theme-manager';
        $this->backup_path = $this->plugin_path . '/backups';
        $this->hestia_path = '/usr/local/hestia';
        $this->theme_path  = '/usr/local/hestia/web/themes';
        $this->loadConfig();
    }
    
    /**
     * Install the theme manager plugin
     */
    public function install() {
        try {
            // Create plugin directories
            $this->createDirectories();
            
            // Create original backup
            $this->createOriginalBackup();
            
            // Create config file
            $this->createConfigFile();
            
            // Create theme management interface
            $this->createThemeInterface();
            
            $this->log("Theme Manager Plugin installed successfully");
            return true;
            
        } catch (Exception $e) {
            $this->log("Installation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Uninstall the theme manager plugin
     */
    public function uninstall() {
        try {
            // Restore original theme
            $this->restoreOriginal();
            
            // Remove plugin directory
            $this->removeDirectory($this->plugin_path);
            
            $this->log("Theme Manager Plugin uninstalled successfully");
            return true;
            
        } catch (Exception $e) {
            $this->log("Uninstallation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Apply a theme
     */
    public function applyTheme($theme_name) {
        try {
            if (!$this->isValidTheme($theme_name)) {
                throw new Exception("Invalid theme: " . $theme_name);
            }
            
            $theme_path = $this->theme_path . '/' . $theme_name;
            
            // Backup current files before applying new theme
            $this->backupCurrentFiles($theme_name . '_backup_' . date('Y-m-d_H-i-s'));
            
            // Apply theme files
            foreach ($this->theme_files as $file) {
                $relative_path = str_replace('/usr/local/hestia/web/templates/', '', $file);
                $theme_file = $theme_path . '/' . $relative_path;
                
                if (file_exists($theme_file)) {
                    // Create directory if it doesn't exist
                    $dir = dirname($file);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    copy($theme_file, $file);
                    chmod($file, 0644);
                }
            }
            
            // Update current theme in config
            $this->updateCurrentTheme($theme_name);
            
            $this->log("Theme '$theme_name' applied successfully");
            return true;
            
        } catch (Exception $e) {
            $this->log("Failed to apply theme '$theme_name': " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get list of available themes
     */
    public function getAvailableThemes() {
        $themes = [];
        $themes_dir = $this->theme_path;
        
        if (is_dir($themes_dir)) {
            $dirs = scandir($themes_dir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($themes_dir . '/' . $dir)) {
                    $themes[] = $dir;
                }
            }
        }
        
        return $themes;
    }
    
    /**
     * Get current active theme
     */
    public function getCurrentTheme() {
        return $this->current_theme;
    }
    
    /**
     * Create necessary directories
     */
    private function createDirectories() {
        $dirs = [
            $this->plugin_path,
            $this->backup_path,
            $this->plugin_path . '/config',
            $this->plugin_path . '/logs',
            $this->theme_path
        ];

        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Create backup of original Hestia files
     */
    private function createOriginalBackup() {
        $original_backup_path = $this->backup_path . '/original';
        
        if (!is_dir($original_backup_path)) {
            mkdir($original_backup_path, 0755, true);
            
            foreach ($this->theme_files as $file) {
                if (file_exists($file)) {
                    $relative_path = str_replace('/usr/local/hestia/web/templates/', '', $file);
                    $backup_file = $original_backup_path . '/' . $relative_path;
                    
                    // Create directory if it doesn't exist
                    $dir = dirname($backup_file);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    copy($file, $backup_file);
                }
            }
        }
    }
    
    /**
     * Backup current files
     */
    private function backupCurrentFiles($backup_name) {
        $backup_path = $this->backup_path . '/' . $backup_name;
        
        if (!is_dir($backup_path)) {
            mkdir($backup_path, 0755, true);
            
            foreach ($this->theme_files as $file) {
                if (file_exists($file)) {
                    $relative_path = str_replace('/usr/local/hestia/web/templates/', '', $file);
                    $backup_file = $backup_path . '/' . $relative_path;
                    
                    // Create directory if it doesn't exist
                    $dir = dirname($backup_file);
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    copy($file, $backup_file);
                }
            }
        }
    }
    
    /**
     * Restore original theme
     */
    private function restoreOriginal() {
        $original_backup_path = $this->backup_path . '/original';
        
        if (is_dir($original_backup_path)) {
            foreach ($this->theme_files as $file) {
                $relative_path = str_replace('/usr/local/hestia/web/templates/', '', $file);
                $backup_file = $original_backup_path . '/' . $relative_path;
                
                if (file_exists($backup_file)) {
                    copy($backup_file, $file);
                    chmod($file, 0644);
                }
            }
        }
    }
    
    /**
     * Create config file
     */
    private function createConfigFile() {
        $config = [
            'current_theme' => 'original',
            'installed_themes' => [],
            'installation_date' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ];
        
        file_put_contents(
            $this->plugin_path . '/config/config.json', 
            json_encode($config, JSON_PRETTY_PRINT)
        );
    }
    
    /**
     * Load configuration
     */
    private function loadConfig() {
        $config_file = $this->plugin_path . '/config/config.json';
        
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            $this->current_theme = $config['current_theme'] ?? 'original';
            $this->available_themes = $config['installed_themes'] ?? [];
        } else {
            $this->current_theme = 'original';
            $this->available_themes = [];
        }
    }
    
    /**
     * Update current theme in config
     */
    private function updateCurrentTheme($theme_name) {
        $config_file = $this->plugin_path . '/config/config.json';
        
        if (file_exists($config_file)) {
            $config = json_decode(file_get_contents($config_file), true);
            $config['current_theme'] = $theme_name;
            $config['last_updated'] = date('Y-m-d H:i:s');
            
            file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
            $this->current_theme = $theme_name;
        }
    }
    
    /**
     * Check if theme is valid
     */
    private function isValidTheme($theme_name) {
        $theme_path = $this->theme_path . '/' . $theme_name;
        return is_dir($theme_path) || $theme_name === 'original';
    }
    
    /**
     * Remove directory recursively
     */
    private function removeDirectory($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filepath = $dir . '/' . $file;
                    if (is_dir($filepath)) {
                        $this->removeDirectory($filepath);
                    } else {
                        unlink($filepath);
                    }
                }
            }
            rmdir($dir);
        }
    }
    

// CLI usage
if (php_sapi_name() === 'cli') {
    $theme_manager = new HestiaThemeManager();
    
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case 'install':
                $result = $theme_manager->install();
                echo $result ? "Installation completed successfully\n" : "Installation failed\n";
                break;
                
            case 'uninstall':
                $result = $theme_manager->uninstall();
                echo $result ? "Uninstallation completed successfully\n" : "Uninstallation failed\n";
                break;
                
            case 'apply':
                if (isset($argv[2])) {
                    $result = $theme_manager->applyTheme($argv[2]);
                    echo $result ? "Theme applied successfully\n" : "Failed to apply theme\n";
                } else {
                    echo "Usage: php hestia_theme_manager.php apply <theme_name>\n";
                }
                break;
                
            case 'list':
                $themes = $theme_manager->getAvailableThemes();
                echo "Available themes:\n";
                foreach ($themes as $theme) {
                    echo "- $theme\n";
                }
                echo "- original (default)\n";
                break;
                
            case 'current':
                echo "Current theme: " . $theme_manager->getCurrentTheme() . "\n";
                break;
                
            default:
                echo "Usage: php hestia_theme_manager.php [install|uninstall|apply|list|current]\n";
        }
    } else {
        echo "Hestia Theme Manager v1.0.0\n";
        echo "Usage: php hestia_theme_manager.php [install|uninstall|apply|list|current]\n";
    }
}

?>

#!/bin/bash

# Hestia Theme Manager Installation Script
# Version: 1.0.1

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PLUGIN_DIR="/usr/local/hestia/plugins/theme-manager"
HESTIA_WEB_DIR="/usr/local/hestia/web"
THEME_DIR="$HESTIA_WEB_DIR/themes"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

type create_web_interface >/dev/null 2>&1 || create_web_interface(){ :; }

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if script is run as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        print_error "This script must be run as root"
        exit 1
    fi
}

# Function to check if Hestia is installed
check_hestia() {
    if [ ! -d "/usr/local/hestia" ]; then
        print_error "Hestia Control Panel not found. Please install Hestia first."
        exit 1
    fi
    
    if [ ! -d "$HESTIA_WEB_DIR/templates" ]; then
        print_error "Hestia web templates directory not found."
        exit 1
    fi
    
    print_status "Hestia Control Panel detected"
}

# Function to create plugin directory structure
create_directories() {
    print_status "Creating plugin directories..."
    
    mkdir -p "$PLUGIN_DIR"
    mkdir -p "$THEME_DIR"        # themes now go under web
    mkdir -p "$PLUGIN_DIR/backups"
    mkdir -p "$PLUGIN_DIR/config"
    mkdir -p "$PLUGIN_DIR/logs"
    
    # Set permissions
    chown -R hestiaweb:hestiaweb "$PLUGIN_DIR"
    chown -R hestiaweb:hestiaweb "$THEME_DIR"
    chmod -R 755 "$PLUGIN_DIR"
    chmod -R 755 "$THEME_DIR"
    
    print_status "Plugin directories created"
}

# Function to copy plugin files
copy_plugin_files() {
    print_status "Installing plugin files..."
    
    # Copy main plugin file
    if [ -f "$SCRIPT_DIR/hestia_theme_manager.php" ]; then
        cp "$SCRIPT_DIR/hestia_theme_manager.php" "$PLUGIN_DIR/"
        chmod 755 "$PLUGIN_DIR/hestia_theme_manager.php"
    else
        print_error "Main plugin file not found"
        exit 1
    fi
    
    # Copy example theme if it exists
    if [ -d "$SCRIPT_DIR/themes" ]; then
        cp -r "$SCRIPT_DIR/themes/"* "$THEME_DIR/" 2>/dev/null || true
        print_status "Example themes copied"
    fi
    
    print_status "Plugin files installed"
}

# Function to run plugin installation
run_plugin_install() {
    print_status "Running plugin installation..."
    
    cd "$PLUGIN_DIR"
    php hestia_theme_manager.php install
    
    if [ $? -eq 0 ]; then
        print_status "Plugin installation completed successfully"
    else
        print_error "Plugin installation failed"
        exit 1
    fi
}

# Function to create CLI command symlink
create_cli_command() {
    print_status "Setting up CLI command..."
    
    # Create symlink for easy CLI access
    if [ ! -L "/usr/local/bin/hestia-theme" ]; then
        ln -s "$PLUGIN_DIR/hestia_theme_manager.php" "/usr/local/bin/hestia-theme"
        chmod +x "/usr/local/bin/hestia-theme"
        print_status "CLI command 'hestia-theme' created"
    fi
}

# Function to create example theme structure
create_example_theme() {
    print_status "Creating example theme structure..."
    
    EXAMPLE_THEME_DIR="$THEME_DIR/example-dark-theme"
    mkdir -p "$EXAMPLE_THEME_DIR/includes"
    mkdir -p "$EXAMPLE_THEME_DIR/pages"
    mkdir -p "$EXAMPLE_THEME_DIR/pages/login"
    
    # Create a simple example theme info file
    cat > "$EXAMPLE_THEME_DIR/theme_info.json" << 'EOF'
{
    "name": "Example Dark Theme",
    "version": "1.0.0",
    "description": "An example dark theme for Hestia Control Panel",
    "author": "Theme Manager Plugin",
    "created": "2024-01-01"
}
EOF
    
    # Create example README for theme developers
    cat > "$THEME_DIR/README.md" << 'EOF'
# Hestia Themes Directory

This directory contains custom themes for the Hestia Control Panel.

## Creating a New Theme

1. Create a new directory with your theme name (e.g., `my-awesome-theme`)
2. Copy the file structure from the original Hestia templates
3. Modify the files to match your theme design
4. Place your theme files in the same directory structure as Hestia templates:

```
my-awesome-theme/
├── footer.php
├── header.php
├── includes/
│   ├── app-footer.php
│   ├── css.php
│   ├── js.php
│   └── ... (other includes)
├── pages/
│   ├── add_user.php
│   ├── list_user.php
│   └── ... (other pages)
└── pages/login/
    ├── login.php
    └── ... (other login pages)
```

## Theme Structure Requirements

- Your theme must maintain the same file structure as the original Hestia templates
- PHP functionality should remain unchanged - only modify HTML/CSS/JS presentation
- Include all required files or the theme switcher will skip missing files
- Test thoroughly before deploying to production

## Installing Themes

1. Place your theme directory in `/usr/local/hestia/plugins/theme-manager/themes/`
2. Use the web interface at `/theme-manager.php` or CLI command `hestia-theme apply theme-name`
3. The plugin will automatically backup current files before applying your theme

## Backup and Restore

- Original files are automatically backed up during plugin installation
- Current theme is backed up before applying a new theme
- You can always restore the original Hestia theme
EOF
    
    print_status "Example theme structure created"
}

# Function to set up logrotate for plugin logs
setup_logrotate() {
    print_status "Setting up log rotation..."
    
    cat > "/etc/logrotate.d/hestia-theme-manager" << EOF
$PLUGIN_DIR/logs/*.log {
    weekly
    missingok
    rotate 4
    compress
    delaycompress
    notifempty
    copytruncate
}
EOF
    
    print_status "Log rotation configured"
}

# Function to display installation summary
show_summary() {
    echo
    echo "======================================"
    echo "  Hestia Theme Manager Installation"
    echo "           COMPLETED"
    echo "======================================"
    echo
    print_status "Installation directory: $PLUGIN_DIR"
    print_status "CLI command: hestia-theme [install|uninstall|apply|list|current]"
    echo
    print_status "Theme directory: $THEME_DIR"
    print_status "Backup directory: $PLUGIN_DIR/backups"
    print_status "Log directory: $PLUGIN_DIR/logs"
    echo
    print_warning "Remember to:"
    echo "  1. Place your custom themes in: $THEME_DIR/"
    echo "  2. Test themes in a development environment first"
    echo "  3. Keep backups of your custom themes"
    echo "  4. Check logs if you encounter any issues"
    echo
    print_status "Installation completed successfully!"
}

# Function to check system requirements
check_requirements() {
    print_status "Checking system requirements..."
    
    # Check PHP
    if ! command -v php &> /dev/null; then
        print_error "PHP is not installed or not in PATH"
        exit 1
    fi
    
    # Check PHP version (minimum 7.4)
    PHP_VERSION=$(php -r "echo PHP_VERSION_ID;")
    if [ "$PHP_VERSION" -lt 70400 ]; then
        print_error "PHP 7.4 or higher is required"
        exit 1
    fi
    
    print_status "System requirements met"
}

# Function to backup existing plugin if it exists
backup_existing_plugin() {
    if [ -d "$PLUGIN_DIR" ]; then
        print_warning "Existing plugin installation found"
        BACKUP_NAME="theme-manager-backup-$(date +%Y%m%d-%H%M%S)"
        print_status "Creating backup: $BACKUP_NAME"
        mv "$PLUGIN_DIR" "/tmp/$BACKUP_NAME"
        print_status "Existing plugin backed up to /tmp/$BACKUP_NAME"
    fi
}

# Main installation function
main() {
    echo "======================================"
    echo "  Hestia Theme Manager Installer"
    echo "           Version 1.0.0"
    echo "======================================"
    echo
    
    # Run all checks and installation steps
    check_root
    check_requirements
    check_hestia
    backup_existing_plugin
    create_directories
    copy_plugin_files
    run_plugin_install
    if declare -f create_web_interface >/dev/null; then create_web_interface; fi
    create_cli_command
    create_example_theme
    setup_logrotate
    
    # Show installation summary
    show_summary
}

# Handle command line arguments
case "${1:-install}" in
    "install")
        main
        ;;
    "help"|"-h"|"--help")
        echo "Hestia Theme Manager Installer"
        echo
        echo "Usage: $0 [install|help]"
        echo
        echo "Commands:"
        echo "  install    Install the theme manager plugin (default)"
        echo "  help       Show this help message"
        echo
        ;;
    *)
        print_error "Unknown command: $1"
        echo "Use '$0 help' for usage information"
        exit 1
        ;;
esac

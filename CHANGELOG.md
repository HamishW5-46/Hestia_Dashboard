# Changelog
All notable changes to this project are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and the project aims to follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.1] - 2025-09-14
### Added
- `log()` helper to standardise logging (CLI echoes; web logs via `error_log()`).
- `createThemeInterface()` to generate a minimal web UI at `plugins/theme-manager/web/index.php`
  so installs don’t faceplant when UI assets are missing.

### Fixed
- PHP parse error: close the plugin class **before** the CLI `// CLI usage` block to avoid
  `unexpected token "if", expecting "function"`.
- Switched installer UI generation from heredoc to **nowdoc** to prevent PHP 8.2 variable
  interpolation warnings during install.

### Hardening
- Added safe no-op stubs in shell scripts to avoid “command not found” when optional helpers
  aren’t defined:
  - `install.sh`: `create_web_interface(){ :; }`
  - `uninstall.sh`: `remove_web_interface(){ :; }`

## [1.0.0] - 2025-01-01
### Added
- Initial release of Hestia Theme Manager (install/uninstall scripts, CLI, and theme structure).

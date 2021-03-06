<?php
/**
 * Context functions file
 *
 * @package EBloodBank
 * @since   1.0
 */
namespace EBloodBank;

/**
 * @return bool
 * @since 1.2
 */
function isCLI()
{
    return (php_sapi_name() === 'cli');
}

/**
 * Whether the current page is the installer.
 *
 * @return bool
 * @since 1.0
 */
function isInstaller()
{
    return ('install.php' === basename($_SERVER['SCRIPT_NAME']));
}

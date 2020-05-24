<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 * 
 * This file contains the following configurations:
 * 
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 * 
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 * 
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '{{database_name}}' );

/** MySQL database username */
define( 'DB_USER', '{{database_user}}' );

/** MySQL database password */
define( 'DB_PASSWORD', '{{database_password}}' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '1#U}CfKqQWj_V/gRcD:|f[v1CZEpvx[!lC<|@z88{=k)|Eu][9!F%OQhI+#>0v|2' );
define( 'SECURE_AUTH_KEY',  'onK]{gRRLt9S %Ylu4.4|lJvivy;F>/x<#Ti>q&^<80A~]!+C+D`U}~%I6KS@jS[' );
define( 'LOGGED_IN_KEY',    'vp<EN3jZyO2*z_(%zxnaP8nNw>fQb]]V(:m6]`)c{MFo6;PP(RJfU/>43R{0>?`T' );
define( 'NONCE_KEY',        'FP08]~{tRhLysc?MO*-MD=pw{4-8~-4We>@#Vs+466ZcL 1C2@X#:22FYuM_(|)$' );
define( 'AUTH_SALT',        'fU/7)m?2O@Z>AZrKUz3Siy3uB7AK&~}q$NpKJ<uDl0 h_m<0ds,m.R]=UXM8y,P;' );
define( 'SECURE_AUTH_SALT', '$nvX}f}[0p~.)_qBiu+7dxL.Yoy<6kNsrjDlc]MARj?vF-Fa*yUT}&]=n2P^v*6a' );
define( 'LOGGED_IN_SALT',   '.,$vFl1sAbZ{$aHLunc~{/]][8PT )npKW2`3WlW0W~Yillnq|h(|`Dj:_P5g ;&' );
define( 'NONCE_SALT',       'v nh:zYkGyMg^nf9ra?Pu|fU=PLhWMwC|DbpttOLln0eF2qL9,BCT6j2F5XFM(l4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
      	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';


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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'pslifestyle');

/** MySQL database username */
define('DB_USER', 'easttvillagekiki');

/** MySQL database password */
define('DB_PASSWORD', 'beagle73');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '9{z^g@,MgikiH^,:=o$fz&3yNI54^diO1nvlHu9v<,|ca/YGt:~>,.dD`!COupv4');
define('SECURE_AUTH_KEY',  'S!*ZWOg ++ZOGCoKb,7e?5%W!C55yLrDzZMq~aSU:wN(2< TjhB>H6&~{YI55K.&');
define('LOGGED_IN_KEY',    '#m=Q,V1`+|?hwj][.5*^D*jHx/TIEgBl;J>tR0xyY&2{Uk`?b]?~2sg2!Yrh-7,V');
define('NONCE_KEY',        '}@h2E}QkaUk0lVbX<(]?)5=cekO*d6#i,E3,:/{Dh`/C#Q~?p9pLf}$ `Kc_dJWH');
define('AUTH_SALT',        'q7:UDy ZM~sIXU]2ZV#}7b,Qa4zBllnBu@cD2iM?d}/ft&{yL_VLeJQ;~de.tqf!');
define('SECURE_AUTH_SALT', 'qu.)kDyF~Q@N#uAYTv.K68X$cQvR]6EB?9}*KGB2O$g342oXV^Ymk#&~iXVDrF(1');
define('LOGGED_IN_SALT',   'FO%go-*H_o|HMc{z=JvLH^j}X1N,VNpo=}oNI!lm;&4uJRUL(K|68Ls!6yW2mU%v');
define('NONCE_SALT',       '[|AFP^?_@p([II)| P%v},U(O=vftP:=oPHEkEjT!RD3J[RC/,{LXPSo-x7%$N`7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

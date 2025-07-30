<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_cms' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'lz_2Mvvj]&t,Q?o-W[$/VSd&*{zkpN7=V*xsA^]D?dl =(Xe13j:zv>{$5[Y>?@y' );
define( 'SECURE_AUTH_KEY',  'r&4cGS!@Mb/5PFbTJ.+Cm[<B2uF4Pg )>_N=PFKTr/_N8?m-GI/n15H JIjZH9^-' );
define( 'LOGGED_IN_KEY',    'dBcu)X6-h/b|?U^!>;acj[NKJfnZZj11}xw1U|/Mwv/b!.[dt1l,Q%iY@5etW}yT' );
define( 'NONCE_KEY',        '@@9YkbR+VT<Jv0i@o/|;9[e+;/?gLB1L+L*c]8+n2||u..4_)A,Mp8J~P+E3|3[p' );
define( 'AUTH_SALT',        '($a]ogMulC&$9{fc_Qlz$Evu4bNGQ1BnR_o2T<T]jBLQ$#~gpH-hd7&pb6c<`H:#' );
define( 'SECURE_AUTH_SALT', 'Y]3g++Y*`Ft1hP`agxVM2|Agk@J=5 xM0@7biwxWDtxaODTtr.0T|N_Ua[S5Tf&m' );
define( 'LOGGED_IN_SALT',   'v?aGntbO&hI+h_[~3$whqN[-gM/zb;ct3>{5d^aBL_)M?Pa-meIiZ>O3kz>>E6HW' );
define( 'NONCE_SALT',       'A|R%W-*/-75[~_T^1{6ZSbvA~5DbbSk_8?HsJJ]dRA485ZjOUV)?gXwjBq_ih~!K' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

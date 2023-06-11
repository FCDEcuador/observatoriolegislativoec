<?php

/**

 * The base configuration for WordPress

 *

 * The wp-config.php creation script uses this file during the installation.

 * You don't have to use the web site, you can copy this file to "wp-config.php"

 * and fill in the values.

 *

 * This file contains the following configurations:

 *

 * * Database settings

 * * Secret keys

 * * Database table prefix

 * * ABSPATH

 *

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */

ini_set('max_execution_time', '300');



// ** Database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', 'obse_ob_legislativo' );



/** Database username */

define( 'DB_USER', 'obse_us' );



/** Database password */

define( 'DB_PASSWORD', 'iKq^e6lKPspK8h3#' );



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

define( 'AUTH_KEY',         'hdz+a;k00L|dRZ4m&.j@e,JWCOI>G_P_GP^{D&a|1[q-?~w[/dagne33[c;VC-<r' );

define( 'SECURE_AUTH_KEY',  '@p0,%Gx!dYp~QMEh}B{TLUcpmTEnJ[sf?usUp>L7?>OF$AKZ]J#j|n vNCb?X)/h' );

define( 'LOGGED_IN_KEY',    'a DzLA&1y>,mu$Jy@rb>;qESy)v.;Z<x4hqz;fja26;BG!#|yUa/A6la7pz7gF0`' );

define( 'NONCE_KEY',        '8gxrUE;)mfN25}oo}%NO}pso4`4:0~{w,%+OcC]M0E;t;~YD[wMK:Q<X{[Y7)8YY' );

define( 'AUTH_SALT',        '19KDDACLNJAl.PqxDZ?(>jRO0ee!l0|W&QilZ,v,OF-%c5rV*Ho+=Z&M1<{Yh.Af' );

define( 'SECURE_AUTH_SALT', ' ,|>S;5pB%;*X)-2hQo4S>+8!sk}X vUWA^9Nwg)z!YWK?*o!~,PLjL&+>f}@7Gj' );

define( 'LOGGED_IN_SALT',   '.CA]R.9k6qnCV}8n~wSE_)`;e%bjG.SXi-&m|oRoE#^@hNO[]t0#U=)?N)lYU6TT' );

define( 'NONCE_SALT',       '4e]l>|/6p$*g$4HvTEjncUY)qTejI&AH`6;^yj3,7pq=3sqt`mLpQSf_q}b`@2F6' );



/**#@-*/



/**

 * WordPress database table prefix.

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

define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );



/* Add any custom values between this line and the "stop editing" line. */







/* That's all, stop editing! Happy publishing. */



/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}



/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';


<?php
# CONFIG {{{

global $CONFIG;
$CONFIG=array();

# Default to simple mode when the page is loaded. [false]
$CONFIG['DEFAULT_SIMPLE']=false;
# Force the page to always be in simple mode (no advanced mode option). [false]
$CONFIG['FORCE_SIMPLE']=false;
# Width for the URL box when in simple mode (CSS "width" attribute). [300px]
$CONFIG['SIMPLE_MODE_URLWIDTH']='300px';
# Disables POST and COOKIES for a much leaner script, at the expense of
# functionality. [false]
$CONFIG['DISABLE_POST_COOKIES']=false;

# Header file to include for the proxy main page. []
$CONFIG['INCLUDE_MAIN_HEADER']='';
# Footer file to include for the proxy main page. []
$CONFIG['INCLUDE_MAIN_FOOTER']='';
# Header file to include for the proxy URL form page. []
$CONFIG['INCLUDE_URL_HEADER']='';

# Default value for tunnel server. []
$CONFIG['DEFAULT_TUNNEL_IP']='';
# Default value for tunnel port. []
$CONFIG['DEFAULT_TUNNEL_PORT']='';
# Force the default values of the tunnel fields, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_TUNNEL']=false;
# Default value for User-Agent. []
$CONFIG['DEFAULT_USER_AGENT']='';
# Force the default value of the user agent field, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_USER_AGENT']=false;

# Default value for "Persistent URL" checkbox. [true]
$CONFIG['DEFAULT_URL_FORM']=true;
# Force the default value of the "Persistent URL" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_URL_FORM']=false;
# Default value for "Remove Cookies" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_COOKIES']=false;
# Force the default value of the "Remove Cookies" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_COOKIES']=false;
# Default value for "Remove Referer Field" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_REFERER']=false;
# Force the default value of the "Remove Referer Field" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_REFERER']=false;
# Default value for "Remove Scripts" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_SCRIPTS']=false;
# Force the default value of the "Remove Scripts" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_SCRIPTS']=false;
# Default value for "Remove Objects" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_OBJECTS']=false;
# Force the default value of the "Remove Objects" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_OBJECTS']=false;
# Default value for "Encrypt URLs" checkbox. [false]
$CONFIG['DEFAULT_ENCRYPT_URLS']=false;
# Force the default value of the "Encrypt URLs" field, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_ENCRYPT_URLS']=false;
# Default value for "Encrypt Cookies" checkbox. [false]
$CONFIG['DEFAULT_ENCRYPT_COOKIES']=false;
# Force the default value of the "Encrypt Cookies" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_ENCRYPT_COOKIES']=false;
# Default value for "Encode HTML" checkbox. [false]
$CONFIG['DEFAULT_ENCODE_HTML']=false;
# Force the default value of the "Encode HTML" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_ENCODE_HTML']=false;

/*/ Address Blocking Notes \*\

Formats for address blocking are as follows:

1.2.3.4     - plain IP address
1.0.0.0/16  - subnet blocking
1.0/16      - subnet blocking
1/8         - subnet blocking
php.net     - domain blocking

Default Value: '10/8','172/8','192.168/16','127/8','169.254/16'

\*\ End Address Blocking Notes /*/

$CONFIG['BLOCKED_ADDRESSES']=
array('10/8','172/8','192.168/16','127/8','169.254/16');

# }}}

# ADVANCED CONFIG {{{

# The following options alter the way documents are parsed on the page, and how
# the internals of th escript actually function.
# ONLY EDIT THIS STUFF IF YOU REALLY KNOW WHAT YOU ARE DOING!

# 500 is the most reasonable number I could come up with as a maximum URL length
# limit.  I ran into a 1200+ character long URL once and it nearly melted the
# processor on my laptop trying to parse it.  Honestly, who needs this long of a
# URL anyway? [500]
$CONFIG['MAXIMUM_URL_LENGTH']=500;

# Time limit in seconds for a single request and parse. [30]
$CONFIG['TIME_LIMIT']=30;
# Time limit in minutes for a DNS entry to be kept in the cache. [10]
$CONFIG['DNS_CACHE_EXPIRE']=10;
# Maximum memory usage, as specified by memory_limit in php.ini. [16M]
$CONFIG['MEMORY_LIMIT']='16M';

# Use gzip (if possible) to compress the connection between the proxy and the
# user (less bandwidth, more CPU). [false]
$CONFIG['GZIP_PROXY_USER']=false;
# Use gzip (if possible) to compress the connection between the proxy and the
# server (less bandwidth, more CPU). [false]
$CONFIG['GZIP_PROXY_SERVER']=false;

# Protocol that proxy is running on.  Change this to a value other than false
# to manually define it.  If you leave this value as false, the code detects
# if you are running on an HTTPS connection.  If you are, then 'https' is used
# as the PROTO value, otherwise 'http' is used.
$CONFIG['PROTO']=false;

# ignored filetypes for SSL check
$CONFIG['SSL_WARNING_IGNORE_FILETYPES'] = array(
'.css', '.js', '.gif', '.jpeg', '.jpg', '.png', '.bmp'
);

# }}}

# LABEL {{{

global $LABEL;
$LABEL=array();

# TITLE: title text above form
$LABEL['TITLE']='Surrogafier';
# URL: text for URL text field
$LABEL['URL']='URL:';
# TUNNEL: text for tunnel proxy text fields
$LABEL['TUNNEL']='Tunnel Proxy:';
# USER_AGENT: text for user-agent select field
$LABEL['USER_AGENT']='User-Agent:';
# USER_AGENT_CUSTOM: text for user-agent custom text field
$LABEL['USER_AGENT_CUSTOM']='';
# URL_FORM: text for persistent URL form checkbox
$LABEL['URL_FORM']='Persistent URL Form';
# REMOVE_COOKIES: text for remove cookies checkbox
$LABEL['REMOVE_COOKIES']='Remove Cookies';
# REMOVE_REFERER: text for remove referer checkbox
$LABEL['REMOVE_REFERER']='Remove Referer Field';
# REMOVE_SCRIPTS: text for remove scripts checkbox
$LABEL['REMOVE_SCRIPTS']='Remove Scripts (JS, VBS, etc)';
# REMOVE_OBJECTS: text for remove objects checkbox
$LABEL['REMOVE_OBJECTS']='Remove Objects (Flash, Java, etc)';
# ENCRYPT_URLS: text for encrypt URLs checkbox
$LABEL['ENCRYPT_URLS']='Encrypt URLs';
# ENCRYPT_COOKIES: text for encrypt cookies checkbox
$LABEL['ENCRYPT_COOKIES']='Encrypt Cookies';
# ENCODE_HTML: text for encode HTML checkbox
$LABEL['ENCODE_HTML']='Encode HTML';
# SUBMIT_MAIN: text for the main submit button
$LABEL['SUBMIT_MAIN']='Surrogafy';
# SUBMIT_SIMPLE: text for the simple submit button
$LABEL['SUBMIT_SIMPLE']='Surrogafy';

# }}}

# PRE-JAVASCRIPT CONSTANTS  {{{
# these constants and functions must be defined before JS is output, but would
# be more readably located later.

#define('AURL_LOCK_REGEXP','(?:(?:javascript|mailto|about):|~|%7e)');
define('FRAME_LOCK_REGEXP','/^(?:(?:javascript|mailto|about):|#)/i');
define('AURL_LOCK_REGEXP','/^(?:(?:javascript|mailto|about):|#|'.str_replace(array('/','.'),array('\/','\.'),addslashes(THIS_SCRIPT)).')/i');
define('URLREG','/^'.
		'(?:([a-z]*)?(?:\:?\/\/))'.      # proto
		'(?:([^\@\/]*)\@)?'.             # userpass
		'([^\/:\?\#\&]*)'.               # servername
		'(?:\:([0-9]+))?'.               # portval
		'(\/[^\&\?\#]*?)?'.              # path
		'([^\/\?\#\&]*(?:\&[^\?\#]*)?)'. # file
		'(?:\?([\s\S]*?))?'.             # query
		'(?:\#([\s\S]*))?'.              # label
		'$/ix');

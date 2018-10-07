<?php

define('LDAP_SERVER', 'example.com');
define('LDAP_PORT', 389);

// dn: <key>=comername
// Some servers come 'cn'
define('LDAP_KEY', 'uid');

define('LDAP_SEARCH', 'ou=People,dc=example,dc=com');

// Set LDAP version, v3 is required for recent versions of OpenLDAP
define('LDAP_VERSION', 3);

//Set the comer ID to impersonate, by default admin is 1
define('LDAP_IMPERSONATE_ID', 1);

//You can limit logins to a certain group, comment to allow all LDAP comers
define('LDAP_GROUP', 'cn=wpadmin');
define('LDAP_GROUP_BASE', 'ou=Group,dc=example,dc=com');
define('LDAP_GROUP_VALUE', 'memberuid');
?>

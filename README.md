### ldapAdmin Wordpress Plugin ###

Unlike other LDAP plugins, which re-create LDAP users in the local DB, this plugin allows you to impersonate a local user (admin). 

Edit config.php to configure LDAP settings

Troubleshooting tips

- Test with ldapsearch! You may need to tweak config.php accordingly
- Use  tcpdump (tcpdump -A -i eth0 port 389)

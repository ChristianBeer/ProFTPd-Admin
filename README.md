ProFTPd Admin
=======

Graphical User Interface for ProFTPd with MySQL support

&copy; 2004 The Netherlands, Lex Brugman <lex_brugman@users.sourceforge.net>  
&copy; 2012 Christian Beer <djangofett@gmx.net>  
&copy; 2015 Ricardo Padilha <ricardo@droboports.com>
Published under the GPLv2 License (see LICENSE for details)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2,
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, download from http://www.gnu.org/licenses/gpl-2.0.txt

Information about ProFTPd Admin
-------
This GUI for ProFTPd was written to support a basic user management feature when using the MySQL module. Originally written by Lex Brugmann in 2004 it was updated by Christian Beer in 2012 to support the latest PHP version.

There is no build-in security, so you have to protect the directory with something else, like Apache Basic Authentication.

This software will be rewritten over time and will be object-oriented and equipped with unit tests at the end.

To-Do
-------

A lot ;) so help is very much appreciated

Upgrade
-------

As the software has undergone a complete rewrite of the config file you should not overwrite your old config.php and compare and change values manually.

Installation
-------

1. Install ProFTPd with MySQL support
     - Debian: apt-get install proftpd-mysql
     - Gentoo: USE="mysql" emerge proftpd
2. Create a MySQL database (use something like phpMyAdmin for this), for example: "proftpd".
3. Use tables.sql to populate the database (you can use phpMyAdmin for this).
4. Add the following to your proftpd.conf (edit to your needs):

```
 SQLBackend                      mysql
 SQLConnectInfo                  database@localhost username password
 SQLAuthenticate                 on
 SQLAuthTypes                    Crypt   Backend
 SQLUserInfo                     users userid passwd uid gid homedir shell
 SQLGroupInfo                    groups groupname gid members
 SQLUserWhereClause              "disabled != 1"
 SQLLog PASS                     updatecount
 SQLNamedQuery                   updatecount UPDATE "login_count=login_count+1, last_login=now() WHERE userid='%u'" users

 # Used to track xfer traffic per user (without invoking a quota)
 SQLLog RETR                     bytes-out-count
 SQLNamedQuery                   bytes-out-count UPDATE "bytes_out_used=bytes_out_used+%b WHERE userid='%u'" users
 SQLLog RETR                     files-out-count
 SQLNamedQuery                   files-out-count UPDATE "files_out_used=files_out_used+1 WHERE userid='%u'" users

 SQLLog STOR                     bytes-in-count
 SQLNamedQuery                   bytes-in-count UPDATE "bytes_in_used=bytes_in_used+%b WHERE userid='%u'" users
 SQLLog STOR                     files-in-count
 SQLNamedQuery                   files-in-count UPDATE "files_in_used=files_in_used+1 WHERE userid='%u'" users
```

5. Extract all files to your webspace (into a subdirectory like "proftpdadmin").
6. Secure access to this directory (for example: create a .htaccess file if using apache)
7. Edit the configs/config_example.php file to your needs and rename it to config.php.
8. Start ProFTPd.
9. Go to http://yourwebspace/proftpdadmin/ and start using it!

Thanks / Links
-------
Lex Brugman for initiating this project  
Justin Vincent for the ezSQL library
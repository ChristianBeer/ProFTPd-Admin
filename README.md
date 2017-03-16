# ProFTPd Admin

Graphical User Interface for ProFTPd with MySQL and sqlite3 support



## About ProFTPd Admin


This GUI for ProFTPd was written to support a basic user management feature
when using the SQL module. Originally written by Lex Brugmann in 2004, 
updated by [Christian Beer](https://github.com/ChristianBeer/ProFTPd-Admin)
in 2012 to support the latest PHP version.
2017 updated by Michael Keck with build-in login for the admin user.

It's possible to use either of SHA1 and pbkdf2 with either of MySQL/MariaDB 
and sqlite3. pbkdf2 is supported since ProFTPd 1.3.5.

You can look at some [screenshots](screenshots/README.md) to see if this is 
the tool you need.



## Installation


#### (A) Using MySQL and SHA1

1. Install ProFTPd with MySQL support
2. Create a MySQL database, for example: "proftpd".
3. Use the file [tables.sql](tables.sql) to populate the database.
4. Add the following to your _`proftpd.conf`_ and _`sql.conf`_ (edit to your needs):

```ini
CreateHome              on 775
AuthOrder               mod_sql.c

SQLBackend              mysql
SQLEngine               on
SQLPasswordEngine       on
SQLAuthenticate         on
SQLAuthTypes            SHA1

SQLConnectInfo          database@localhost username password
SQLUserInfo             users userid passwd uid gid homedir shell
SQLGroupInfo            groups groupname gid members
SQLUserWhereClause      "disabled != 1"
SQLLog PASS             updatecount
SQLNamedQuery           updatecount UPDATE "login_count=login_count+1, last_login=now() WHERE userid='%u'" users

# Used to track xfer traffic per user (without invoking a quota)
SQLLog RETR             bytes-out-count
SQLNamedQuery           bytes-out-count UPDATE "bytes_out_used=bytes_out_used+%b WHERE userid='%u'" users
SQLLog RETR             files-out-count
SQLNamedQuery           files-out-count UPDATE "files_out_used=files_out_used+1 WHERE userid='%u'" users

SQLLog STOR             bytes-in-count
SQLNamedQuery           bytes-in-count UPDATE "bytes_in_used=bytes_in_used+%b WHERE userid='%u'" users
SQLLog STOR             files-in-count
SQLNamedQuery           files-in-count UPDATE "files_in_used=files_in_used+1 WHERE userid='%u'" users
```

5. Extract all files to your webspace (into a subdirectory like _`proftpdadmin`_).
6. Edit the _`configs/config_example.php`_ file to your needs and rename it to _**`config.php`**_.  
**Notice:** Change the default login settings!
 ```php
/**
  * Login data
  *
  * Important: Please change this values in
  *            live systems!
  */
$cfg['login'] = array(
  /* Username. Please use any username you want */
  'username' => 'admin',
  /* Password. CHANGE IT and use secure password! */
  'password' => 'password',
  /* Blowfish secret key (22 chars). CHANGE IT! */
  'blowfish' => 'XBu5pjOTa8H7UIwYSzMZxD'
);
```
7. Start ProFTPd.
8. Go to `http://your.server.com/proftpdadmin/` and start using it!


#### (B) Using sqlite3 and pbkdf2

1. Install ProFTPd with sqlite3 support
2. Use [tables-sqlite3.sql](tables-sqlite3.sql) to create an sqlite3 database:
   `sqlite3 auth.sqlite3 < tables-sqlite3.sql`
3. Add the following to your _`proftpd.conf`_ and _`sql.conf`_ (edit to your needs):

```ini
CreateHome              on 775
AuthOrder               mod_sql.c

SQLBackend              sqlite3
SQLEngine               on
SQLPasswordEngine       on
SQLAuthenticate         on
SQLAuthTypes            pbkdf2
SQLPasswordPBKDF2       sha1 5000 20
SQLPasswordUserSalt     name Prepend
SQLPasswordEncoding     hex

SQLConnectInfo          /path/to/auth.sqlite3
SQLUserInfo             users userid passwd uid gid homedir shell
SQLGroupInfo            groups groupname gid members
SQLUserWhereClause      "disabled != 1"
SQLLog PASS             updatecount
SQLNamedQuery           updatecount UPDATE "login_count=login_count+1, last_login=now() WHERE userid='%u'" users

# Used to track xfer traffic per user (without invoking a quota)
SQLLog RETR             bytes-out-count
SQLNamedQuery           bytes-out-count UPDATE "bytes_out_used=bytes_out_used+%b WHERE userid='%u'" users
SQLLog RETR             files-out-count
SQLNamedQuery           files-out-count UPDATE "files_out_used=files_out_used+1 WHERE userid='%u'" users

SQLLog STOR             bytes-in-count
SQLNamedQuery           bytes-in-count UPDATE "bytes_in_used=bytes_in_used+%b WHERE userid='%u'" users
SQLLog STOR             files-in-count
SQLNamedQuery           files-in-count UPDATE "files_in_used=files_in_used+1 WHERE userid='%u'" users
```

5. Extract all files to your webspace (into a subdirectory like _`proftpdadmin`_).
6. Edit the _`configs/config_example.php`_ file to your needs and rename it to _**`config.php`**_.  
  **Notice:** Change the default login settings!
```php
/**
  * Login data
  *
  * Important: Please change this values in
  *            live systems!
  */
$cfg['login'] = array(
  /* Username. Please use any username you want */
  'username' => 'admin',
  /* Password. CHANGE IT and use secure password! */
  'password' => 'password',
  /* Blowfish secret key (22 chars). CHANGE IT! */
  'blowfish' => 'XBu5pjOTa8H7UIwYSzMZxD'
);
```
7. Start ProFTPd.
8. Go to `http://your.server.com/proftpdadmin/` and start using it!


### Upgrade

If you want to upgrade the hashing algorithm you have to change all passwords after
changing the configs (both ProFTPd and ProFTPd Admin).



## Plans

* Add postgresql support [#26](https://github.com/ChristianBeer/ProFTPd-Admin/issues/26)

* Add default user settings to groups so it is easier to create a new user
  with default values [#28](https://github.com/ChristianBeer/ProFTPd-Admin/issues/28)

* Email new users with password [#35](https://github.com/ChristianBeer/ProFTPd-Admin/issues/35)



## Thanks

- Lex Brugman for initiating this project 
- Justin Vincent for the ezSQL library 
- Ricardo Padilha for implementing sqlite3, pbkdf2 and bootstrap support
- Christian Beer for his update to support the latest PHP version



## Copyright / License

- © 2004 The Netherlands, Lex Brugman; lex_brugman@users.sourceforge.net
- © 2012 Christian Beer; djangofett@gmx.net
- © 2015 Ricardo Padilha; ricardo@droboports.com
- © 2017 Michael Keck; https://github.com/mkkeck

---------------------------------------------------------------------------

Published under the GPLv2 License (see [LICENSE](LICENSE) for details)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2, as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program; if not, download from 
[http://www.gnu.org/licenses/gpl-2.0.txt](http://www.gnu.org/licenses/gpl-2.0.txt)

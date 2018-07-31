<?php
/**
 * @author    Damien Martins
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @copyright Damien Martins <damien@makelofine.org>
 */

function mysql_backend($password) {
  return '*'.strtoupper(hash('sha1',pack('H*',hash('sha1', $password))));
}

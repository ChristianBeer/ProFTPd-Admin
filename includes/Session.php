<?php
/*
 * @class:  Session
 * @file:   includes/Session.php
 * @date:   2017-03-16
 * @author: Michael Keck
 */

/**
 * Class Session
 */
class Session {

  private static $host;
  private static $name = 'uid';
  private static $val;
  private static $sess;
  private static $sid = 'sid';


  /**
   * Save session and close
   */
  public static function close() {
    if (isset(self::$sess) && self::$sess) {
      self::$sess = TRUE;
      session_write_close();
    }
  }


  /**
   * Delete a value from session with key $index.
   *
   * If $index NULL the complete session will be deleted and destroyed.
   *
   * @param null|string $index
   * @return null|mixed
   */
  public static function delete($index = null) {
    if (isset(self::$sess) && self::$sess) {
      if (is_null($index)) {
        self::$sess = FALSE;
        unset($_SESSION);
        session_destroy();
      }
      if (isset($_SESSION[$index])) {
        $value = $_SESSION[$index];
        unset($_SESSION[$index]);
        return $value;
      }
    }
    return null;
  }


  /**
   * Extract a value from session with key $index.
   *
   * If $index NULL, whole session as an associative array is returned.
   * If session is empty or key $index not exists then NULL is returned.
   *
   * @param  null|string $index
   * @return null|mixed
   */
  public static function get($index = null) {
    if (self::$sess) {
      if (!$index && isset($_SESSION) && count($_SESSION)) {
        return $_SESSION;
      }
      if (isset($_SESSION[$index])) {
        return $_SESSION[$index];
      }
    }
    return null;
  }


  /**
   * Checks if session has key $index.
   *
   * Returns counted values for $index. If session has no key $index, then
   * 0 (zero) will be returned. If $index NULL, then the counted session keys
   * are returned.
   *
   * @param  null|string $index
   * @return int
   */
  public static function has($index = null) {
    if (self::$sess) {
      if (!$index && isset($_SESSION)) {
        return count($_SESSION);
      }
      if (isset($_SESSION[$index])) {
        return (is_array($_SESSION[$index]) ? count($_SESSION[$index]) : 1);
      }
    }
    return 0;
  }


  /**
   * Returns the session id / session name, if session exists.
   *
   * @param  bool $name
   * @return null|string
   */
  public static function id($name = true) {
    if (!$name) {
      return (isset($_SESSION[self::$sid]) ? $_SESSION[self::$sid] : null);
    }
    return self::$sid;
  }


  /**
   * Init a session.
   *
   * @param null|string $sid
   * @param null|string $val
   * @param null|string $name
   */
  public static function init($sid = null, $val = null, $name = null) {
    if (headers_sent()) {
      self::$sess = false;
      return;
    }
    if (isset($_SERVER['SERVER_NAME'])) {
      self::$host = $_SERVER['SERVER_NAME'];
    }
    else if (isset($_SERVER['SERVER_HOSTNAME'])) {
      self::$host = $_SERVER['SERVER_HOSTNAME'];
    }
    self::$host = str_replace('www.', '',self::$host);
    if (!is_numeric(str_replace('.', '', self::$host))) {
      self::$host = '.' . self::$host;
    }

    $ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknownIP');
    $ua = (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknownUA');

    self::$val = md5(preg_replace('/[^0-9]/', '', microtime())) . md5($ip . $ua);

    if ($sid) {
      self::$sid = $sid;
    }
    if ($val) {
      self::$val = $val;
    }
    if ($name) {
      self::$name = $name;
    }

    if (!isset($_SESSION[self::$sid]) || empty($_SESSION[self::$sid])) {
      ini_set('session.use_only_cookies', 1);
      session_set_cookie_params(0, '/', self::$host);
      session_name(self::$name);
      session_start();
      if (!isset($_SESSION[self::$sid]) || empty($_SESSION[self::$sid])) {
        $_SESSION[self::$sid] = self::$val;
      }
      self::$sess = TRUE;
    }
    else {
      self::$sess = TRUE;
    }
    return;
  }


  /**
   * Set a session value $value with the key $index.
   *
   * @param string $index
   * @param null|mixed $value
   */
  public static function set($index, $value = null) {
    if (isset(self::$sess) && self::$sess) {
      if (!is_null($value)) {
        $_SESSION[$index] = $value;
      }
      else {
        self::delete($index);
      }
    }
  }


  /**
   * User login session
   *
   * @param array        $conf;     configuration from gloabl $cfg['login']
   * @param null|string  [$action]; optional: 'login' or 'logout'
   * @param null|array   [$data];   optional: array('username' => $_POST['username'], 'password' => $_POST['password']),
   * @return bool
   */
  public static function user($conf = array(), $action = null, $data = null) {
    $action = ($action !== null ? ($action === 'login' ? 'login' : 'logout') : null);
    foreach (array('username','password','blowfish') as $key) {
      if (!isset($conf[$key]) || empty($conf[$key]) && trim('' . $conf[$key]) === '') {
        return false;
      }
      $conf[$key] = trim(urlencode($conf[$key]));
    }
    $conf['blowfish']   = '$2y$11$' . $conf['blowfish'] . '$';

    // Prepare Hash
    $hash = $conf['username'] . $conf['password'];
    if ($hash !== '') {
      $hash = crypt($hash, $conf['blowfish']);
    }
    else {
      return false;
    }

    self::init();

    // Login action
    if ($action === 'login') {
      if (is_array($data)) {
        foreach (array('username', 'password') as $key) {
          if (!array_key_exists($key, $data) || empty($data[$key])) {
            return false;
          }
        }
      }
      $data = trim(urlencode($data['username'])) . trim(urlencode($data['password']));
      $data = crypt($data, $hash);
      if ($data === $hash) {
        self::set('uid', $data);
        self::close();
        return true;
      }
    }

    // Logout action or uid invalid
    if ($action === 'logout' || self::get('uid') !== $hash) {
      self::delete();
      self::close();
      return false;
    }

    // Only check uid is valid
    if (self::get('uid') === $hash) {
      self::close();
      return true;
    }
    return false;

  }
}


/* Only accessible via SSL if required */
if (isset($cfg) && isset($cfg['force_ssl']) && $cfg['force_ssl'] === true) {
  if ($_SERVER['SERVER_PORT'] !== '443') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
  }
}


/* Login required */
if (isset($cfg) && isset($cfg['login']) && is_array($cfg['login'])) {
  $session_usage = true;
  $session_valid = false;
  if (isset($_POST['login']) && $_POST['login'] === 'login') {
    Session::user(
      $cfg['login'],
      'login',
      array(
        'username' => $_POST['username'],
        'password' => $_POST['password'],
      )
    );
    header('Location: ./');
    exit();
  }
  if (isset($_GET['logout'])) {
    Session::user(
      $cfg['login'],
      'logout'
    );
    header('Location: ./?login');
    exit();
  }
  if (!Session::user($cfg['login'])) {
    if (!isset($_GET['login'])) {
      header('Location: ./?login');
      exit();
    }
  }
  else {
    $session_valid = true;
  }
}

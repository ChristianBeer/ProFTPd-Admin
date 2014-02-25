<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 *
 */

include_once "ez_sql_core.php";
include_once "ez_sql_mysql.php";
include_once "configs/config.php";

/**
 * Provides all functions needed by the individual scripts
 *
 * @author Christian Beer
 * @package ProFTPd-Admin
 *
 * @todo streamline usage of $config and create a Class for it
 * @todo make database calls generic to the caller
 * @todo create standard user and group objects
 */
class AdminClass {
    /**
     * database layer
     * @var ezSQL_mysql
     */
    var $dbConn = false;
    /**
     * configuration store
     * @var Array
     */
    var $config = false;
    /**
     * version number
     * @access private
     * @var String
     */
    var $version = "2.0";

    /**
     * initialize the database connection via ezSQL_mysql
     * @param Array $cfg configuration array retrieved from config.php to store in the object
     */
    function AdminClass($cfg) {
        $this->config = $cfg;
        $this->dbConn = new ezSQL_mysql($this->config['db_user'], $this->config['db_pass'], $this->config['db_name'], $this->config['db_host']);
    }

    /**
     * return the version number to outside class
     * @return String
     */
    function get_version() {
        return $this->version;
    }

    /**
     * Retrieves HTML from header.html and replaces some placeholders
     * @return String retrieved and replaced html from header.html
     */
    function get_header() {
        $header_file = file_get_contents("header.html", true);
        $header_file = str_replace("[VERSION]", $this->version, $header_file);
        return $header_file;
    }

    /**
     * Retrieves HTML from footer.html and replaces some placeholders
     * @return String retrieved and replaced html from footer.html
     */
    function get_footer() {
        $footer_file = file_get_contents("footer.html", true);

        return $footer_file;
    }

    /**
     * retrieves groups for each user and populates an array of $data[userid][gid] = groupname
     * @return Array like $data[userid][gid] = groupname
     */
    function parse_groups() {
        $result = $this->dbConn->get_results("select * from " . $this->config['table_groups']);
        if (!$result) return false;

        $data = array();
        $field_members = $this->config['field_members'];
        $field_gid = $this->config['field_gid'];
        $field_groupname = $this->config['field_groupname'];
        foreach ($result as $group) {
            $names = explode(",", $group->$field_members);
            reset($names);
            while (list($key, $name) = each($names)) {
                $data[$name][$group->$field_gid] = $group->$field_groupname;
            }
        }
        return $data;
    }

    /**
     * retrieves the list of groups and populates an array of $data[gid] = groupname
     * @return Array like $data[gid] = groupname
     *
     * @todo make database fields generic from config
     */
    function get_groups() {
        $result = $this->dbConn->get_results("select * from " . $this->config['table_groups'] . " ORDER BY " . $this->config['field_gid'] . " ASC");
        if (!$result) return false;

        $data = array();
        $field_gid = $this->config['field_gid'];
        $field_groupname = $this->config['field_groupname'];
        foreach ($result as $group){
            $data[$group->$field_gid] = $group->$field_groupname;
        }
        return $data;
    }

    /**
     * Adds a user to a group using the groupid, does not check if user is already a member!
     * @param Integer $userid
     * @param Integer $gid
     * @return boolean false on error
     */
    function add_user_to_group($userid, $gid) {
        $result = $this->dbConn->get_var("SELECT ". $this->config['field_members']. " from " . $this->config['table_groups'] . " WHERE " . $this->config['field_gid'] . "='" . $gid . "'");
        if ($result != "") {
            $query = "UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=CONCAT(" . $this->config['field_members'] . ",\"," . $userid . "\") where " . $this->config['field_gid'] . "='" . $gid . "'";
        } else {
            $query = "UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=\"" . $userid . "\" where " . $this->config['field_gid'] . "='" . $gid . "'";
        }
        $result = $this->dbConn->query($query);
        if (!$result) return false;
        return true;
    }

    /**
     * Adds a user to a group using the groupid, does not check if user is already a member!
     * @param Integer $userid
     * @param String $groupname
     * @return boolean false on error
     */
    function add_user_to_group_by_name($userid, $groupname) {
        $result = $this->dbConn->get_var("SELECT ". $this->config['field_members']. " from " . $this->config['table_groups'] . " WHERE " . $this->config['field_groupname'] . "='" . $groupname . "'");
        if ($result != "") {
            $query = "UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=CONCAT(" . $this->config['field_members'] . ",\"," . $userid . "\") where " . $this->config['field_groupname'] . "='" . $groupname . "'";
        } else {
            $query = "UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=\"" . $userid . "\" WHERE " . $this->config['field_groupname'] . "='" . $groupname . "'";
        }
        $result = $this->dbConn->query($query);
        if (!$result) return false;
        return true;
    }

    /**
     * removes a user from a given group using the groupid
     * @param Integer $userid
     * @param Integer $gid
     * @return boolean false on error
     */
    function remove_user_from_group($userid, $gid) {
        $result = $this->dbConn->get_row("SELECT " . $this->config['field_members'] . " from " . $this->config['table_groups'] . " where " . $this->config['field_gid'] . "='" . $gid . "'");
        $list = explode(",", $result->members);
        $diff = array_diff($list, array("$userid", ""));

        if (is_array($diff)) {
            $members_new = implode(",", $diff);
        } else {
            $members_new = "";
        }
        $result = $this->dbConn->query("UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=\"" . $members_new . "\" where " . $this->config['field_gid'] . "='" . $gid . "'");
        if (!$result) return false;
        return true;
    }

    /**
     * removes a user from a given group using the groupname
     * @param Integer $userid
     * @param String $groupname
     * @return boolean false on error
     */
    function remove_user_from_group_by_name($userid, $groupname) {
        $result = $this->dbConn->get_row("SELECT " . $this->config['field_members'] . " from " . $this->config['table_groups'] . " where " . $this->config['field_groupname'] . "='" . $groupname . "'");
        $list = explode(",", $result->members);
        //check if $userid is member of this group
        if(in_array($userid, $list)) {
            // remove $userid and empty values from $list array
            $diff = array_diff($list, array("$userid", ""));

            if (is_array($diff)) {
                $members_new = implode(",", $diff);
            } else {
                $members_new = "";
            }
            $result = $this->dbConn->query("UPDATE " . $this->config['table_groups'] . " set " . $this->config['field_members'] . "=\"" . $members_new . "\" where " . $this->config['field_groupname'] . "='" . $groupname . "'");
            if (!$result) return false;
            return true;
        } else {
            return 2;
        }
    }

    /**
     * returns either the total number or the number of disabled users in the db
     * @param Boolean $only_disabled
     * @return Integer number or false on error
     */
    function get_user_count($only_disabled = false) {
        $where_clause = "";
        if ($only_disabled) {
            $where_clause = " WHERE ". $this->config['field_disabled'] ."=\"1\"";
        }
        $result = $this->dbConn->get_var("select COUNT(*) from " . $this->config['table_users']. $where_clause);
        return $result;
    }

    /**
     * returns either the total number or the number of empty groups in the db
     * @param Boolean $only_emtpy
     * @return Integer number or false on error
     */
    function get_group_count($only_emtpy=false) {
        $where_clause = "";
        if ($only_emtpy) {
            $where_clause = " WHERE ". $this->config['field_members'] ."=\"\"";
        }
        $result = $this->dbConn->get_var("select COUNT(*) from " . $this->config['table_groups']. $where_clause);
        return $result;
    }

    /**
     * returns the last index number of the user table
     * @return Integer
     */
    function get_last_user_index() {
        $result = $this->dbConn->get_var("select MAX(".$this->config['field_uid'].") from " . $this->config['table_users']);
        return $result;
    }

    /**
     * Checks if the given Username is already in the database
     * @param String $userid
     * @return boolean true if username exists, false if not
     */
    function check_username($userid) {
        $result = $this->dbConn->get_row("select 1 from " . $this->config['table_users'] . " where " . $this->config['field_userid'] . "='" . $userid . "'");
        if (is_object($result)) return true;
        return false;
    }

    /**
     * retrieves all users from db and populates an associativ array
     * @param String $sort column to order by (will be pass directly into SQL statement)
     * @param String $order either asc or desc (will be passed directly into SQL statement
     * @return String an array containing the users or false on failure
     *
     * @todo Check the order by variable if correct column or not
     */
    function get_users_as_array($sort, $order) {
        $result = $this->dbConn->get_results("SELECT * FROM " . $this->config['table_users'] . " ORDER BY " . $sort . " " . $order, ARRAY_A);
        if (!$result) return false;
        return $result;
    }

    /**
     * Adds a user entry into the database
     * @param Array $userdata
     * @return Boolean true on success, false on failure
     */
    function add_user($userdata) {
        $query = "INSERT INTO ".$this->config['table_users']." (".$this->config['field_userid'].",".$this->config['field_name'].",".$this->config['field_email'].",".$this->config['field_title'].",".$this->config['field_company'].",".$this->config['field_comment'].",".$this->config['field_gid'].",".$this->config['field_uid'].",".$this->config['field_passwd'].",". $this->config['field_homedir'].",".$this->config['field_shell'].",".$this->config['field_disabled'].") values ('".$userdata["userid"]."','".$userdata["name"]."','".$userdata["email"]."','".$userdata["title"]."','".$userdata["company"]."', '".$userdata["comment"]."','".$userdata["gid"]."','".$userdata["user_uid"]."',".$this->config['passwd_encryption']."('".$userdata["passwd"] . "'),'".$userdata["homedir"]."','".$userdata["shell"]."','".$userdata["disabled"] . "')";
        $result = $this->dbConn->query($query);
        return $result;
    }

    /**
     * Adds a group entry into the database
     * @param Array $groupdata
     * @return Boolean true on success, false on failure
     */
    function add_group($groupdata) {
        $query = "INSERT INTO ".$this->config['table_groups']." (".$this->config['field_groupname'].",".$this->config['field_gid'].",".$this->config['field_members'].") values ('".$groupdata["new_group_name"]."','".$groupdata["new_group_gid"]."','".$groupdata["new_group_members"]."')";
        $result = $this->dbConn->query($query);
        return $result;
    }

    /**
     * updates the group entry in the database (currently only the gid!)
     * @param Integer $gid
     * @param Integer $new_gid
     * @return Boolean true on success, false on failure
     */
    function update_group($gid, $new_gid) {
        $query = "UPDATE ".$this->config['table_groups']." SET ".$this->config['field_gid']."='".$new_gid."' WHERE ".$this->config['field_gid']."='".$gid."'";
        $result = $this->dbConn->query($query);
        return $result;
    }

    /**
     * retrieves user from database with given maingroup and populates an array of $data[id] = userid
     * @param Integer $gid
     * @return Array form is $data[id] = userid
     */
    function get_users_by_groupid($gid) {
        $result = $this->dbConn->get_results("SELECT ".$this->config['field_userid'].", ".$this->config['field_id']." FROM ".$this->config['table_users']." WHERE ".$this->config['field_gid']."='".$gid."'");
        if (!$result) return false;

        $data = array();
        $field_userid = $this->config['field_userid'];
        $field_id = $this->config['field_id'];
        foreach ($result as $user) {
            $data[$user->$field_id] = $user->$field_userid;
        }
        return $data;
    }

    /**
     * retrieve a group by gid
     * @param Integer $gid
     * @return Object
     */
    function get_group_by_gid($gid) {
        $result = $this->dbConn->get_row("SELECT * FROM " . $this->config['table_groups'] . " WHERE " . $this->config['field_gid'] . "='".$gid."'");
        if (!$result) return false;
        return $result;
    }

    /**
     * delete a group by gid
     * @param Integer $gid
     * @return Boolean true on success, false on failure
     */
    function delete_group_by_gid($gid) {
        $result = $this->dbConn->query("DELETE FROM ".$this->config['table_groups']." WHERE ".$this->config['field_gid']."='".$gid."'");
        return $result;
    }

    /**
     * retrieve a user by userid
     * @param String $userid
     * @return Array
     */
    function get_user_by_userid($userid) {
        $result = $this->dbConn->get_row("SELECT * FROM " . $this->config['table_users'] . " WHERE " . $this->config['field_userid'] . "='".$userid."'", ARRAY_A);
        if (!$result) return false;
        return $result;
    }

    /**
     * retrieve a user by id
     * @param Integer $id
     * @return Array
     */
    function get_user_by_id($id) {
        $result = $this->dbConn->get_row("SELECT * FROM " . $this->config['table_users'] . " WHERE " . $this->config['field_id'] . "='".$id."'", ARRAY_A);
        if (!$result) return false;
        return $result;
    }

    /**
     * updates the user entry in the database
     * @param Array $userdata
     * @return Boolean true on success, false on failure
     */
    function update_user($userdata) {
        $passwd = '';
        if (strlen($userdata['passwd']) > 0) $passwd = $this->config['field_passwd']."=".$this->config['passwd_encryption']."('" . $userdata["passwd"] . "'), ";

        $query = "UPDATE ".$this->config['table_users']." SET ".$this->config['field_userid']."='".$userdata["userid"]."', ".$this->config['field_name']."='".$userdata["name"]."', ".$this->config['field_email']."='".$userdata["email"]."', ".$this->config['field_title']."='".$userdata["title"]."', ".$this->config['field_company']."='".$userdata["company"]."', ".$this->config['field_comment']."='".$userdata["comment"]."', ".$this->config['field_gid']."='".$userdata["gid"]."', ".$this->config['field_uid']."='".$userdata["user_uid"]."', ".$passwd. $this->config['field_homedir']."='".$userdata["homedir"]."', ".$this->config['field_shell']."='".$userdata["shell"] . "', ".$this->config['field_disabled']."='".$userdata["disabled"] . "' WHERE ".$this->config['field_id']."='".$userdata['id']."'" ;
        $result = $this->dbConn->query($query);
        return $result;
    }

    /**
     * removes the given user from all additional groups
     * @param String $userid
     *
     * @todo This function should probably not print anything instead return a boolean status
     */
    function remove_user_from_all_groups($userid) {
        $groups = $this->get_groups();
        while (list($gid, $group) = each($groups)) {
            //print("Removing&nbsp;" . $userid . "&nbsp;from " . $group . "<br />");
            $ret = $this->remove_user_from_group_by_name($userid, $group);
            if ($ret === true) {
                print("Successfully removed " . $userid . " from " . $group . "<br />");
            } elseif($ret === false) {
                print("Failure while removing " . $userid . " from " . $group . "<br />");
            } elseif($ret === 2) {
                //print("Not removing " . $userid . " from " . $group . ". Is not a member.<br />");
            }
        }
    }

    /**
     * deletes the user entry from the database
     * @param String $userid
     * @return Boolean true on success, false on failure
     */
    function delete_user_by_userid($userid) {
        $result = $this->dbConn->query("DELETE FROM ".$this->config['table_users']." WHERE ".$this->config['field_userid']."='".$userid."'");
        return $result;
    }
}
?>

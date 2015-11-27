<?php

    if (!$no_config)
        require_once("config.php");

    define('NO_USER', -1);
    define('LOCKED_USER', -2);
    define('INVALID_CREDENTIALS', -3);


    // Database related functions

    function connect($host=true, $name=true, $user=true, $pass=true)
    {
        global $DBIP, $DBNAME, $DBUSER, $DBPASS;
        if ($host === true)
            $host = $DBIP;
        if ($name === true)
            $name = $DBNAME;
        if ($user === true)
            $user = $DBUSER;
        if ($pass === true)
            $pass = $DBPASS;
        return new TrackMePDO("mysql:host=$host;dbname=$name",
                              $user, $pass);
    }

    function connect_save($host=true, $name=true, $user=true, $pass=true)
    {
        try {
            return connect($host, $name, $user, $pass);
        } catch (PDOException $e) {
            return null;
        }
    }

    // TODO: Use PHP's implementation of password_verify and password_hash
    function password_verify($password, $hash)
    {
        return $hash === password_hash($password);
    }

    function password_hash($password)
    {
        $salt = "trackmeuser";
        return MD5($salt.$password);
    }

    class TrackMePDO extends PDO {

        function get_count($statement)
        {
            $stmt = $this->prepare("SELECT COUNT(*) FROM $statement");
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result[0][0];
        }

        function exec_sql()
        {
            $args = func_get_args();
            $statement = $args[0];
            if (count($args) == 1)
                $args = array();
            elseif (is_array($args[1]))
                $args = $args[1];
            else
                $args = array_slice($args, 1);
            $stmt = $this->prepare($statement);
            for ($i = 0; $i < count($args); $i++)
                $stmt->bindParam($i + 1, $args[$i]);
            if ($stmt->execute())
                return $stmt;
            else
                return false;
        }

        function create_login($username, $password)
        {
            $this->exec_sql("INSERT INTO users (username, password) ".
                            "VALUES (?, ?)",
                            $username, password_hash($password));
            return $this->valid_login($username, $password);
        }

        function valid_login($username, $password, $allow_disabled=false)
        {
            $user = $this->exec_sql("Select ID, password, Enabled " .
                                    "FROM users WHERE username=?",
                                    $username)->fetch();
            if (is_null($user))
            {
                return NO_USER;
            }
            elseif (password_verify($password, $user['password']))
            {
                if (!$allow_disabled && $user['Enabled'] == 0)
                    return LOCKED_USER;
                else
                    return $user['ID'];
            }
            else
            {
                return INVALID_CREDENTIALS;
            }
        }
    }
?>

<?php
function connect() {

    $num_args = func_num_args();

    if ($num_args === 0) {
        return null;
    } elseif ($num_args > 1) {
        $func_args = func_get_args();
    } else {
        $func_args = func_get_args();
        $func_args = reset($func_args);
    }
    if (count($func_args) < 4 || count($func_args) > 5) {

        file_put_contents(__DIR__ . '/PDOException' . '/connection_args.json', "Less or more arguments\n\n");
        return null;

    } else {

        file_put_contents(__DIR__ .'/PDOException' . '/connection_args.json', json_encode($func_args, JSON_PRETTY_PRINT) . "\n\n");
    }
    $host = $func_args[0];
    $dbname = $func_args[1];
    $username = $func_args[2];
    $password = $func_args[3];

    try {

        $conn = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password",
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));

        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $conn;

    } catch(PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n", FILE_APPEND);
    }

    return null;
}

function get_user($connection,$chat_id) {

    try {
        $sql = "SELECT * FROM users WHERE chat_id = :chat_id";
        $statement = $connection->prepare($sql);
        $statement->bindValue(':chat_id', $chat_id);
        $statement->execute();

        return $statement->fetch();

    } catch (PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n" ,FILE_APPEND);
    }

    return null;
}

function add_user($connection, array $user_info = []) {

    $fields = array_keys($user_info);
    $fields_part = implode(",", $fields);

    $params_part = implode(",", array_map(function ($val) {
        return ":$val";
    },$fields));

    try {
        $sql = "INSERT INTO users ($fields_part) VALUES ($params_part)";
        $statement = $connection->prepare($sql);

        foreach ($user_info as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();

    } catch (PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n", FILE_APPEND);
    }
    return false;
}

function add_user_textlog($connection, array $user_text_info = []) {

    $fields = array_keys($user_text_info);
    $fields_part = implode(",", $fields);

    $params_part = implode(",", array_map(function ($val) {
        return ":$val";
    }, $fields));

    if ($user_text_info['text'] == null) {
        return false;
    }

    try {

        $sql = "INSERT INTO textlog ($fields_part) VALUES ($params_part)";

        $statement = $connection->prepare($sql);

        foreach ($user_text_info as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();

    } catch (PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n", FILE_APPEND);
    }

    return false;
}

/*Weather functions*/


function add_weather($connection, array $text_arr = []) {

    $fields = array_keys($text_arr);
    $fields_part = implode(",", $fields);

    $params_part = implode(",", array_map(function ($val) {
        return ":$val";
    },$fields));

    try {
        $sql = "INSERT INTO weather ($fields_part) VALUES ($params_part)";
        $statement = $connection->prepare($sql);

        foreach ($text_arr as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        return $statement->execute();

    } catch (PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n", FILE_APPEND);
    }

    return false;
}

function get_weather($connection,$weather_id) {

    try {
        $sql = "SELECT * FROM weather WHERE weather_id = :weather_id";
        $statement = $connection->prepare($sql);
        $statement->bindValue(':weather_id', $weather_id);
        $statement->execute();

        return $statement->fetch();

    } catch (PDOException $e) {
        file_put_contents(__DIR__ .'/PDOException' . '/PDOException.txt' , $e->getMessage() . "\n\n" ,FILE_APPEND);
    }

    return null;
}
<?php

namespace src\Model\DatabaseManager;
use mysqli;
use \PDO as PDO;

class DatabaseManager
{
    private static $instance;
    private static $conn;

    //clone disactivated
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    //constructor disactivated
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DatabaseManager();
            self::$instance->connect();
            return self::$instance;
        }
        return self::$instance;
    }

    private function connect()
    {

            // Create connection
            $conn = new mysqli('localhost','root','root' ,'tchat');
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            self::$conn = $conn;
            return $conn;
    }

    public function query($query)
    {

    }

    public static function insertMessage($message)
    {
        $sql = "INSERT INTO message (msg, sender)
     VALUES ('$message->message', '$message->sender')";
     $conn = self::$conn;
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public function getMessageHistory()
    {
       $conn = self::connect();
        $sql = "SELECT * FROM message";
        $result = $conn->query($sql);
//
//        if ($result->num_rows > 0) {
//            echo "<table><tr><th>ID</th><th>Name</th></tr>";
//            // output data of each row
//            while($row = $result->fetch_assoc()) {
//                echo "<tr><td>" . $row["msg"]. "</td><td>" . $row["sender"]."</td></tr>";
//            }
//            echo "</table>";
//        } else {
//            echo "0 results";
//        }
$data = array();
         while($row = $result->fetch_assoc()) {
         $data[] =['msg' =>$row['msg'],
             'sender' =>$row['sender']];
         }

        return $data;
    }
}
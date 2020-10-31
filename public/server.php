<?php
namespace app\server;
use src\Model\DatabaseManager\DatabaseManager;

$dir = dirname(__DIR__);


require_once('../config/Parameters.php');
require_once('../src/Model/DatabaseManager.php');
$db = DatabaseManager::getInstance();

$parameters = new \config\Parameters\Parameters();

$SOCKET_PORT = $parameters::SOCKET_PORT;
$SOCKET_IP = $parameters::SOCKET_IP;

//Create TCP/IP sream socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//reuseable port
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
//bind socket to specified host
socket_bind($socket, 0, $SOCKET_PORT);
//listen to port
socket_listen($socket);
//create & add listning socket to the list
$clients = array($socket);
//start endless loop, so that our script doesn't stop
while (true) {
    //manage multipal connections
    $changed = $clients;
    //returns the socket resources in $changed array
    socket_select($changed, $null, $null, 0, 10);

    //check for new socket
    if (in_array($socket, $changed)) {
        $socket_new = socket_accept($socket); //accpet new socket
        $clients[] = $socket_new; //add socket to client array
        $header = socket_read($socket_new, 1024); //read data sent by the socket
        perform_handshaking($header, $socket_new, $SOCKET_IP, $SOCKET_PORT); //perform websocket handshake
        socket_getpeername($socket_new, $ip); //get ip address of connected socket
        //make room for new socket
        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
    }

    //loop through all connected sockets
    foreach ($changed as $changed_socket) {

        //check for any incomming data
        while (socket_recv($changed_socket, $buf, 1024, 0) >= 1) {
            $received_text = unmask($buf); //unmask data
            $tst_msg = json_decode($received_text); //json decode
            if ($tst_msg != null) {
//                //saving data to database
                $db::insertMessage($tst_msg);
//                $xml = simplexml_load_file("database.xml") or die("Error: Cannot open database.xml");
//                $message = $xml->addChild("Message");
//                $message->addChild("user", $tst_msg->name);
//                $message->addChild("message", $tst_msg->message);
//                $date = new DateTime('now');
//                $message->addChild("datetime", $date->format('Y-m-d H:i:s'));
//                echo $xml->saveXML('database.xml');
                var_dump( $tst_msg);
            }


            //prepare data to be sent to client
            $response_text = mask(json_encode(array('sender' => $tst_msg->sender, 'message' => $tst_msg->message)));
            send_message($response_text, $changed_socket); //send data
            break 2; //exist this loop
        }

        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if ($buf === false) { // check disconnected client
            // remove client for $clients array
            $found_socket = array_search($changed_socket, $clients);
            socket_getpeername($changed_socket, $ip);
            unset($clients[$found_socket]);

            //notify all users about disconnected connection
            $response = mask(json_encode(array('type' => 'system', 'message' => $ip . ' disconnected')));
            send_message($response);
        }
    }
}
// close the listening socket
socket_close($sock);

function send_message($msg, $ws = null)
{

    global $clients;
    foreach ($clients as $changed_socket) {
        if ($ws != null && $ws !== $changed_socket)
            @socket_write($changed_socket, $msg, strlen($msg));
    }

    return true;
}

//Unmask incoming framed message
function unmask($text)
{
    $length = ord($text[1]) & 127;
    if ($length == 126) {
        $masks = substr($text, 4, 4);
        $data = substr($text, 8);
    } elseif ($length == 127) {
        $masks = substr($text, 10, 4);
        $data = substr($text, 14);
    } else {
        $masks = substr($text, 2, 4);
        $data = substr($text, 6);
    }
    $text = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}

//Encode message for transfer to client.
function mask($text)
{
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if ($length <= 125)
        $header = pack('CC', $b1, $length);
    elseif ($length > 125 && $length < 65536)
        $header = pack('CCn', $b1, 126, $length);
    elseif ($length >= 65536)
        $header = pack('CCNN', $b1, 127, $length);
    return $header . $text;
}

//handshake new client.
function perform_handshaking($receved_header, $client_conn, $host, $port)
{
    $headers = array();
    $lines = preg_split("/\r\n/", $receved_header);
    foreach ($lines as $line) {
        $line = chop($line);
        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
            $headers[$matches[1]] = $matches[2];
        }
    }

    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    //hand shaking header
    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "WebSocket-Origin: $host\r\n" .
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
    socket_write($client_conn, $upgrade, strlen($upgrade));
}


//require_once ('../config/Parameters.php');
//
//
// $parameters = $parameters = new \config\Parameters\Parameters();
//
//error_reporting(E_ALL);
//
///* Allow the script to hang around waiting for connections. */
//    set_time_limit(0);
//
///* Turn on implicit output flushing so we see what we're getting
// * as it comes in. */
//    ob_implicit_flush();
//
//    $address = $parameters::SOCKET_IP;
//    $port = $parameters::SOCKET_PORT;
//
//if (($main_sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
//    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
//}
//
//if (socket_bind($main_sock, $address, $port) === false) {
//    echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($main_sock)) . "\n";
//}
//
//if (socket_listen($main_sock, 5) === false) {
//    echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($main_sock)) . "\n";
//}
//
////clients array
//$clients = array();
//
//do {
//    $read = array();
//    $read[] = $main_sock;
//
//    $read = array_merge($read,$clients);
//
//    $e = null ;
//    $w = null;
//    // Set up a blocking call to socket_select
//    if(socket_select($read,$w , $e , $tv_sec = 5) < 1)
//    {
//        //    SocketServer::debug("Problem blocking socket_select?");
//        continue;
//    }
//
//    // Handle new Connections
//    if (in_array($main_sock, $read)) {
//
//        if (($msgsock = socket_accept($main_sock)) === false) {
//            echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
//            break;
//        }
//        $clients[] = $msgsock;
//        $key = array_keys($clients, $msgsock);
//        /* Enviar instrucciones. */
//        $msg = "\nWelcome to the PHP Test Server. \n \n" .
//            "you're the client number: {$key[0]}\n" ;
//        socket_write($msgsock, $msg, strlen($msg));
//
//    }
//
//    // Handle Input
//    foreach ($clients as $key => $client) { // for each client
//        if (in_array($client, $read)) {
//            if (false === ($buf = socket_read($client, 2048, PHP_NORMAL_READ))) {
//                echo "socket_read() failed reason: " . socket_strerror(socket_last_error($client)) . "\n";
//                break 2;
//            }
//            if (!$buf = trim($buf)) {
//                continue;
//            }
//            $talkback = "Cliente {$key}: said '$buf'.\n";
//            socket_write($client, $talkback, strlen($talkback));
//            echo "$buf\n";
//        }
//
//    }
//} while (true);
//function perform_handshaking($receved_header, $client_conn, $host, $port)
//{
//    $headers = array();
//    $lines = preg_split("/\r\n/", $receved_header);
//    foreach ($lines as $line) {
//        $line = chop($line);
//        if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
//            $headers[$matches[1]] = $matches[2];
//        }
//    }
//
//    $secKey = $headers['Sec-WebSocket-Key'];
//    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
//    //hand shaking header
//    $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
//        "Upgrade: websocket\r\n" .
//        "Connection: Upgrade\r\n" .
//        "WebSocket-Origin: $host\r\n" .
//        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
//    socket_write($client_conn, $upgrade, strlen($upgrade));
//}
//
//socket_close($main_sock);
//?>

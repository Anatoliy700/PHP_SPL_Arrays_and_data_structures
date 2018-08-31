<?php

/**
 * Class RequestHandler
 * Обработка входящих запросов к серверу
 */

class RequestHandler
{
  private $method;
  private $params;
  private $linkChat;
  private $checkValid = [
    "valid" => null,
    "errors" => []
  ];
  
  function __construct($request = [], &$linkChat = null) {
    $valid = true;
    if (isset($request->method)) {
      $this->method = $request->method;
    } else {
      $valid = false;
      $this->checkValid['errors'][] = [
        "code" => "1001",
        "message" => "Не передан метод"
      ];
    }
    if (isset($request->params)) {
      $this->params = $request->params;
    } else {
      $valid = false;
      $this->checkValid['errors'][] = [
        "code" => "1002",
        "message" => "Не переданы параметры"
      ];
    }
    if ($linkChat) {
      $this->linkChat = $linkChat;
    } else {
      $valid = false;
      $this->checkValid['errors'][] = [
        "code" => "1003",
        "message" => "Не передан список для хранения сообщений"
      ];
    }
    $this->checkValid['valid'] = $valid;
  }
  
  public function getResponse($type = 'json') {
    $response = null;
    if ($this->isValid()) {
      if ($this->method === 'send_msg') {
        $this->sendMessage();
        $result = [
          "result" => [
            "success" => 1
          ]
        ];
      } elseif ($this->method === 'get_msg') {
        $result = $this->getMessage();
      }
      $response = $result;
      
    } else {
      $response = [
        "errors" => $this->checkValid['errors']
      ];
    }
    if ($type === 'json') {
      $response = json_encode($response);
    }
    return $response;
  }
  
  protected function sendMessage() {
    $this->linkChat->push($this->params);
  }
  
  protected function getMessage() {
    //проверка параметров получения сообщений
    $messages = [];
    $total = $this->linkChat->count();
    if ($total) {
      array_push($messages, $this->linkChat->shift());
    }
    return [
      "total" => $total,
      "messages" => $messages
    ];
  }
  
  protected function isValid() {
    if (!$this->checkValid['valid']) {
      return false;
    }
    if ($this->method === 'send_msg') {
      if (isset($this->params->name) && isset($this->params->message)
        && strlen($this->params->name) > 0 && strlen($this->params->message) > 0
      ) {
        return true;
      } else {
        $this->checkValid['errors'][] = [
          "code" => "1004",
          "message" => "Не корректные данные сообщения"
        ];
        return false;
      }
    } elseif ($this->method === 'get_msg') {
      //проверка на корректность параметров запроса сообщений
      return true;
    }
    $this->checkValid['errors'][] = [
      "code" => "1005",
      "message" => "Не корректный метод"
    ];
    return false;
  }
}

/////Список с сообщениями/////

$chat = new SplStack();
$chat->push([
  "name" => "Сергей",
  "message" => "Здравствуй, мир 1!"
]);
$chat->push([
  "name" => "Петр",
  "message" => "Здравствуй, мир 2!"
]);
$chat->push([
  "name" => "Иван",
  "message" => "Здравствуй, мир 3!"
]);

foreach ($chat as $item) {
  var_dump($item);
}

/////Сервер/////

$master = array();
$socket = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
if (!$socket) {
  echo "$errstr ($errno)<br />\n";
} else {
  $master[] = $socket;
  $read = $master;
  while (1) {
    $read = $master;
    $mod_fd = stream_select($read, $_w = NULL, $_e = NULL, 5);
    if ($mod_fd === FALSE) {
      break;
    }
    $read = array_merge([], $read);
    for ($i = 0; $i < $mod_fd; ++$i) {
      if ($read[$i] === $socket) {
        $conn = stream_socket_accept($socket);
        $master[] = $conn;
      } else {
        $sock_data = fread($read[$i], 1024);
        var_dump($sock_data);
        if (strlen($sock_data) === 0) { // connection closed
          $key_to_del = array_search($read[$i], $master, TRUE);
          fclose($read[$i]);
          unset($master[$key_to_del]);
        } else if ($sock_data === FALSE) {
          echo "Something bad happened";
          $key_to_del = array_search($read[$i], $master, TRUE);
          unset($master[$key_to_del]);
        } else {
          echo "The client has sent :";
          var_dump(json_decode($sock_data));
          var_dump($chat->count());
          $request = new RequestHandler(json_decode($sock_data), $chat);
          $response = $request->getResponse();
          var_dump($chat->count());
          var_dump($response);
          fwrite($read[$i], $response);
          fclose($read[$i]);
          unset($master[array_search($read[$i], $master)]);
        }
      }
    }
  }
}
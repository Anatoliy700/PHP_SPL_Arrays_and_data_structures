<?php
header('Cntent-type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    $response = [];
    $params = [];
    $userMessage = [];
    $eventMessage = '';
    $err_message = false;
    $send_message = false;
    $method = $_POST['method'] ?? null;
    if ($method === 'send') {
      $params = [
        "name" => $_POST['name'] ?? null,
        "message" => $_POST['message'] ?? null
      ];
    }
    if ($method === 'get') {
      $params = [];
    }
    
    /*    echo $method . '<br>';
        echo $params['name'] . '<br>';
        echo $params['message'] . '<br>';*/
    
    $response = sendRequest($method, $params);
    if (isset($response['result']['success']) && $response['result']['success'] === 1) {
      $send_message = true;
      $eventMessage = 'Сообщение успешно отправлено';
    } elseif (isset($response['total'])) {
      if ($response['total'] > 0) {
        $userMessage = array_shift($response['messages']);
      } else {
        $eventMessage = 'Нет сообщений на сервере';
        $send_message = true;
      }
    } else {
    
    }
//    var_dump($response);
//    var_dump($userMessage);
  } catch (\MongoDB\Driver\Exception\ConnectionException $e) {
//    echo 'root';
//    var_dump($e);
    $err_message = true;
//    $response = $e['message'];
    $eventMessage = $e->getMessage();
  }
}

function sendRequest($method, $params) {
  $response = [];
  if ($method === 'send') {
    if (isset($params['name']) && isset($params['message']) && strlen($params['name']) && strlen($params['message'])) {
      $request = [
        "method" => 'send_msg',
        "params" => [
          "name" => $params['name'],
          "message" => $params['message']
        ]
      ];
      $response = webSocketClient($request);
    } else {
      $response = false;
    }
  } elseif ($method === 'get') {
    $request = [
      "method" => 'get_msg',
      "params" => []
    ];
    $response = webSocketClient($request);
  }
  return $response;
}

function webSocketClient($request) {
  $response = [];
  $request_json = json_encode($request);
//  try {
  $fp = stream_socket_client("tcp://127.0.0.1:8000", $errno, $errstr, 30);
  if (!$fp) {
    $response = 'Ошибка соединения с сервером';
//    var_dump($response);
    throw new \MongoDB\Driver\Exception\ConnectionException('Ошибка соединения с сервером');
  } else {
    fwrite($fp, $request_json);
    $response = (json_decode(fread($fp, 1024), true));
    fclose($fp);
  }
  /*  }catch (\MongoDB\Driver\Exception\ConnectionException $e){
      var_dump($e);
    }*/
  return $response;
}

?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Чат</title>
  <style>
    body{
      padding-left: 10px;
    }
    #error {
      height: 70px;
      font-size: 2em;
      color: red;
    }
    
    #message {
      height: 70px;
      font-size: 2em;
      color: green;
    }
    form{
      margin-bottom: 20px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
<?php if ($err_message): ?>
  <div id="error"><?= $eventMessage ?></div>
<?php endif; ?>
<?php if ($send_message): ?>
  <div id="message"><?= $eventMessage ?></div>
<?php endif; ?>
<?php if ($userMessage): ?>
  <div>
    <h3><?= $userMessage['name'] ?></h3>
    <p><?= $userMessage['message'] ?></p>
  </div>
<?php endif; ?>
<form action="" method="post">
  <input type="hidden" name="method" value="get">
  <input type="submit" value="Получить">
</form>
<form action="" method="post">
  <input type="hidden" name="method" value="send">
  <input type="text" name="name" placeholder="Имя" required>
  <input type="text" name="message" placeholder="Сообщение" required>
  <input type="submit" value="Отправить">
</form>
</body>
</html>

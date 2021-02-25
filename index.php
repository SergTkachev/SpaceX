<?php
session_start();

if(!isset($_SESSION['todo_list']["id"])){

    $query = http_build_query(array('todo_list_id'=> true));

    $answer = get_content('http://todo.dev.com/src/FormHandler.php?' .$query);

    if($answer){
        $_SESSION['todo_list']["id"] = $answer['todo_list'];
    }
}

function get_content($URL){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $URL);
    $data = curl_exec($ch);
    $info = curl_error();
    curl_close($ch);
    return json_decode($data, true);
}
?>
<html>
<head>
    <title>Todo App</title>
    <link rel="stylesheet" href="/public/css/style.css" type="text/css" media="screen" charset="utf-8">
    <link rel="shortcut icon" href="/public/img/favicon.ico" type="image/x-icon">
    <script type="text/javascript" src="public/js/jquery-2.1.4.min.js"></script>
</head>
<body>
<div class="container" id="<?= $_SESSION['todo_list']["id"] ?: 0 ?>">
    <p>
        <label for="new-task">Add Item</label><input id="new-task" type="text"><button>Add</button>
    </p>

    <h3>Todo</h3>
    <ul id="incomplete-tasks">
    </ul>

    <h3>Completed</h3>
    <ul id="completed-tasks">
    </ul>
</div>

<script type="text/javascript" src="/public/js/app.js"></script>

</body>
</html>

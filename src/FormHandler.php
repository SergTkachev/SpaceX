<?php

ini_set('display_errors', 1);
require '../vendor/autoload.php';

use Manager\Manager;

$action = isset($_POST['action']) ? $_POST['action'] : '';
$params = isset($_POST['params']) ? $_POST['params'] : [];
$status = isset($params['status']) ? $params['status'] : 1;
$todo_list_id = isset($params['todo_list_id']) ? $params['todo_list_id'] : 0;

$databaseManager = new Manager();

switch ($action) {
    case'getCurrentTasks':
        $databaseManager->getCurrentTask($params["task_id"]);
        break;
    case 'getCompletedTasks':
        $databaseManager->getCompletedTasks($todo_list_id);
        break;
    case 'getActiveTasks':
        $databaseManager->getActiveTasks($todo_list_id);
        break;
    case 'saveTask':
        $databaseManager->saveTask($params['task_description'], $todo_list_id);
        break;
    case 'deleteTask':
        $databaseManager->deleteTask($params["task_id"], $todo_list_id);
        break;
    case 'updateTasks':
        $databaseManager->updateTasks($params['task_id'], $params['task_description'], $todo_list_id);
        break;
    case 'updateStatusTasks':
        $databaseManager->updateStatusTasks($params['task_id'], $status, $todo_list_id);
        break;
}

if(isset($_GET['todo_list_id'])){
    $id = $databaseManager->getLastIdToDoList();

    if($id){
        $databaseManager->createToDoList($id);
    }
}

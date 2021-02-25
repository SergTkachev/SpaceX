<?php

namespace Manager;

/**
 * Class Manager
 * @package Manager
 */
class Manager
{
    private $connection;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $db = Db::get_instance();
        $this->connection = $db->getConnection();
    }

    /**
     * @param $task
     * @param  int  $todo_list_id
     * @return mixed
     * @throws \Exception
     */
    public function saveTask($task, int $todo_list_id)
    {
        try {
            $sth = $this->connection->prepare('INSERT INTO tasks (task_description, todo_list_id) VALUES (:task_description, :todo_list_id)');
            $sth->execute(['task_description' => $task, "todo_list_id" => $todo_list_id]);

            echo json_encode(
                array('action' => 'newTask', 'task' => $this->connection->lastInsertId(), 'task_description' => $task, "todo_list_id"=> $todo_list_id)
            );
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

    /**
     * @param $task_id
     */
    public function getCurrentTask($task_id)
    {
        $sql = 'SELECT task_id, task_description FROM tasks WHERE task_id ='.$task_id;
        $state = $this->connection->query($sql);
        $rows = $state->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(array('action' => 'current', 'tasks' => $rows));
    }

    /**
     * The function return list of completed tasks
     * @param  int  $todo_list_id
     */
    public function getCompletedTasks(int $todo_list_id = 0)
    {
        $sql = 'SELECT task_id, task_description, date FROM tasks WHERE status = 0 AND todo_list_id =' . $todo_list_id;
        $state = $this->connection->query($sql);
        $rows = $state->fetchAll(\PDO::FETCH_ASSOC);


        echo json_encode(array('action' => 'completed', 'tasks' => $rows));
    }

    /**
     * @param  int  $todo_list_id
     */
    public function getActiveTasks(int $todo_list_id = 1)
    {
        $sql = 'SELECT task_id, task_description FROM tasks WHERE status = 1 AND todo_list_id =' . $todo_list_id;
        $state = $this->connection->query($sql);
        $rows = $state->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(array('action' => 'active', 'tasks' => $rows));
    }

    /**
     * @param $task_id
     * @param $task_description
     * @param  int  $status
     * @param  int  $todo_list_id
     * @throws \Exception
     */
    public function updateTasks($task_id, $task_description, $status = 1, int $todo_list_id = 0)
    {
        try {
            $sth = $this->connection->prepare(
                ' UPDATE `tasks` SET `task_description`= :task_description,`status`= :status WHERE task_id = :task_id AND todo_list_id = :todo_list_id'
            );
            $sth->execute(['task_description' => $task_description, 'status' => $status, 'task_id' => $task_id, "todo_list_id" => $todo_list_id]);

            echo json_encode(
                array(
                    'action' => 'updateTask',
                    'task' => $task_id,
                    'todo_list_id' => $todo_list_id,
                    'task_description' => $task_description,
                    'status' => $status
                )
            );
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

    /**
     * @param $task_id
     * @param  int  $status
     * @param  int  $todo_list_id
     * @throws \Exception
     */
    public function updateStatusTasks($task_id, $status = 1, int $todo_list_id = 0)
    {
        try {
            $sth = $this->connection->prepare(
                ' UPDATE `tasks` SET `status`= :status WHERE task_id = :task_id AND todo_list_id = :todo_list_id'
            );
            $sth->execute(['status' => $status, 'task_id' => $task_id, "todo_list_id" => $todo_list_id ]);

            echo json_encode(
                array(
                    'action' => 'updateStatusTasks',
                    'task' => $task_id,
                    'status' => $status
                )
            );
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

    /**
     * @param $task_id
     * @param  int  $todo_list_id
     * @throws \Exception
     */
    public function deleteTask($task_id, int $todo_list_id = 0)
    {
        try {
            $sth = $this->connection->prepare(' DELETE FROM `tasks` WHERE task_id = :task_id AND todo_list_id = :todo_list_id');
            $sth->execute(['task_id' => $task_id]);

            echo json_encode(array('action' => 'deleteTask', 'task' => $task_id, "todo_list_id" => $todo_list_id));
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getLastIdToDoList(){
        try {
            $q = $this->connection->query("SHOW TABLE STATUS LIKE 'todo_lists'");
            $next = $q->fetch(\PDO::FETCH_ASSOC);
            return $next['Auto_increment'];
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

    /**
     * @param $todo_list_id
     * @throws \Exception
     */
    public function createToDoList($todo_list_id)
    {
        try {
            $sth = $this->connection->prepare('INSERT INTO todo_lists (id) VALUES (:id)');
            $sth->execute(['id' => $todo_list_id]);

            echo json_encode(
                array('action' => 'newTable', 'todo_list' => $this->connection->lastInsertId())
            );
        } catch (\PDOException $e) {
            throw  new \Exception('Something going wrong '.$e->getMessage());
        }
    }

}

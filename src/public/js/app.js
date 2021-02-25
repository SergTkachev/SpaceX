//Document is the DOM can be accessed in the console with document.window.
// Tree is from the top, html, body, p etc.

//Problem: User interaction does not provide the correct results.
//Solution: Add interactivity so the user can manage daily tasks.
//Break things down into smaller steps and take each step at a time.


//Event handling, uder interaction is what starts the code execution.
$(document).ready(function () {

    var id_todo_list = +$('.container').attr('id');
    var taskInput = document.getElementById("new-task");//Add a new task.
    var addButton = document.getElementsByTagName("button")[0];//button add
    var incompleteTaskHolder = document.getElementById("incomplete-tasks");//ul of #incomplete-tasks
    var completedTasksHolder = document.getElementById("completed-tasks");//completed-tasks

    taskManager('getActiveTasks',{todo_list_id: id_todo_list}, function (id, descriptiopn) {
        var listItem = createNewTaskElement(descriptiopn, `${id}`);

        incompleteTaskHolder.appendChild(listItem);

        //Append listItem to incompleteTaskHolder
        bindTaskEvents(listItem, taskCompleted);
    });

    taskManager('getCompletedTasks',{todo_list_id: id_todo_list}, function (id, descriptiopn) {
        var listItem = createNewTaskElement(descriptiopn, `${id}`, false);

        completedTasksHolder.appendChild(listItem);

        //Append listItem to incompleteTaskHolder
        bindTaskEvents(listItem, taskIncomplete);
    });

//New task list item
    var createNewTaskElement = function (taskString, id = '', status = true) {

        var listItem = document.createElement("li");

        listItem.setAttribute("id", id);
        //input (checkbox)
        var checkBox = document.createElement("input");//checkbx
        //label
        var label = document.createElement("label");//label
        //input (text)
        var editInput = document.createElement("input");//text
        //button.edit
        var editButton = document.createElement("button");//edit button

        //button.delete
        var deleteButton = document.createElement("button");//delete button

        label.innerText = taskString;

        //Each elements, needs appending
        checkBox.type = "checkbox";

        if(!status) checkBox.setAttribute('checked', 'checked');

        editInput.type = "text";

        editButton.innerText = "Edit";//innerText encodes special characters, HTML does not.
        editButton.className = "edit";
        deleteButton.innerText = "Delete";
        deleteButton.className = "delete";


        //and appending.
        listItem.appendChild(checkBox);
        listItem.appendChild(label);
        listItem.appendChild(editInput);
        listItem.appendChild(editButton);
        listItem.appendChild(deleteButton);
        return listItem;
    }


    var addTask = function () {
        console.log("Add Task...");

        if (taskInput.value && taskInput.value.length > 5) {


            //Create a new list item with the text from the #new-task:
            taskManager('saveTask', {task_description: taskInput.value, todo_list_id: id_todo_list}, function (id, descriptiopn) {
                // debugger
                var listItem = createNewTaskElement(descriptiopn, `${id}`);

                incompleteTaskHolder.appendChild(listItem);

                //Append listItem to incompleteTaskHolder
                bindTaskEvents(listItem, taskCompleted);
            });

            taskInput.value = "";
        } else {
            alert('Put something in input please and more than 5 elements');
        }

    }

//Edit an existing task.

    var editTask = function () {
        console.log("Edit Task...");
        console.log("Change 'edit' to 'save'");

        debugger;
        var listItem = this.parentNode;

        var editInput = listItem.querySelector('input[type=text]');
        var label = listItem.querySelector("label");
        var containsClass = listItem.classList.contains("editMode");
        //If class of the parent is .editmode
        if (containsClass) {

            //switch to .editmode
            //label becomes the inputs value.
            label.innerText = editInput.value;

            taskManager('updateTasks', {task_id: listItem.id, task_description: editInput.value,todo_list_id: id_todo_list});
        } else {
            editInput.value = label.innerText;
        }

        //toggle .editmode on the parent.
        listItem.classList.toggle("editMode");
    }


//Delete task.
    var deleteTask = function () {
        console.log("Delete Task...");

        var listItem = this.parentNode;
        var ul = listItem.parentNode;

        //Remove the parent list item from the ul.
        ul.removeChild(listItem);
        taskManager('deleteTask', {task_id: listItem.id, todo_list_id: id_todo_list});

    }


//Mark task completed
    var taskCompleted = function () {
        console.log("Complete Task...");

        //Append the task list item to the #completed-tasks
        var listItem = this.parentNode;
        completedTasksHolder.appendChild(listItem);
        taskManager('updateStatusTasks', {task_id: listItem.id, status: 0, todo_list_id: id_todo_list});
        bindTaskEvents(listItem, taskIncomplete);

    }


    var taskIncomplete = function () {
        console.log("Incomplete Task...");
//Mark task as incomplete.
        //When the checkbox is unchecked
        //Append the task list item to the #incomplete-tasks.
        var listItem = this.parentNode;
        incompleteTaskHolder.appendChild(listItem);
        taskManager('updateStatusTasks', {task_id: listItem.id, status: 1, todo_list_id: id_todo_list});
        bindTaskEvents(listItem, taskCompleted);
    }


    var ajaxRequest = function () {
        console.log("AJAX Request");
    }

//The glue to hold it all together.


    //Set the click handler to the addTask function.

    addButton.addEventListener("click", addTask);
    addButton.addEventListener("click", ajaxRequest);


    var bindTaskEvents = function (taskListItem, checkBoxEventHandler) {
        console.log("bind list item events");
//select ListItems children
        var checkBox = taskListItem.querySelector("input[type=checkbox]");
        var editButton = taskListItem.querySelector("button.edit");
        var deleteButton = taskListItem.querySelector("button.delete");


        //Bind editTask to edit button.
        editButton.onclick = editTask;
        //Bind deleteTask to delete button.
        deleteButton.onclick = deleteTask;
        //Bind taskCompleted to checkBoxEventHandler.
        checkBox.onchange = checkBoxEventHandler;
    }

//cycle over incompleteTaskHolder ul list items
//for each list item
    for (var i = 0; i < incompleteTaskHolder.children.length; i++) {

        //bind events to list items chldren(tasksCompleted)
        bindTaskEvents(incompleteTaskHolder.children[i], taskCompleted);
    }


//cycle over completedTasksHolder ul list items
    for (var i = 0; i < completedTasksHolder.children.length; i++) {
        //bind events to list items chldren(tasksIncompleted)
        bindTaskEvents(completedTasksHolder.children[i], taskIncomplete);
    }


    function taskManager(action, params = [], callback = function () {}){
        $.ajax({
            type: 'POST',
            url: 'src/FormHandler.php',
            data: {
                action,
                params: params
            },
            dataType: 'json',
            success: function (data) {
                // debugger;
                // console.log(data);
                if (data['action'] === 'newTask') callback(data['task'], data['task_description']);
                if (data['action'] === 'completed') {
                    data["tasks"].forEach((item) => {
                        callback(item.task_id, item.task_description);
                    })

                }
                if (data['action'] === 'active') {
                    data["tasks"].forEach((item) => {
                        callback(item.task_id, item.task_description);
                    })

                }

            }
        });
    }
})


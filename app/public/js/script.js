;(function () {

    function htmlToElement(html) {
        var template = document.createElement('template');
        html = html.trim(); // Never return a text node of whitespace as the result
        template.innerHTML = html;
        return template.content.firstChild;
    }

    const refreshTodos = function() {
        fetch('/api/tasks')
        .then((response) => {
            return response.json();
        })
        .then((todos) => {
            let todoElements = document.querySelectorAll('.todos-container .todo');
            todoElements.forEach(todoElement => { 
                todoElement.remove();
            });
            todos.tasks.forEach(td => {
                let sp1 = htmlToElement('<div class="todo">' +
                    '    <p class="item ' + td.priority +'">' + td.name + '</p>' +
                    '    <span>' +
                    '        <a href="#" class="edit-todo" data-id="' + td.id + '"><i class="fas fa-pen"></i></a>' +
                    '        <a href="#" class="del-todo" data-id="' + td.id + '"><i class="fas fa-times fa-lg"></i></a>' +
                    '    </span>\n' +
                    '</div>');
                let sp2 = document.querySelector(".todos-container .add-todo");

                // Get the parent element
                let parentDiv = sp2.parentNode

                // Insert the new element into before sp2
                parentDiv.insertBefore(sp1, sp2)
            });
        })
        .catch((err) => console.log(err));
    };

    const removeTodo = function(id) {
        fetch('api/tasks/' + id, {
            method: 'DELETE'
        })
        .then((response) => {
            if (response.status === 204) {
                refreshTodos();
            }
        })
        .catch((err) => console.log(err));
    };

    const addTodo = function(todo, priority) {
        fetch('api/tasks/', {
            method: 'POST',
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
            body: JSON.stringify({
                name: todo,
                priority: priority
            }),
        })
        .then((response) => {
            if (response.status === 201) {
                refreshTodos();
            }
        })
        .catch((err) => console.log(err));
    };


    const editTodo = function(todo, priority, id) {
        fetch('api/tasks/' + id, {
            method: 'PUT',
            headers: {
                "Content-Type": "application/json; charset=utf-8",
            },
            body: JSON.stringify({
                name: todo,
                priority: priority
            }),
        }).then(r => {
            if (r.status === 204) {
                refreshTodos()
            }
        }).catch((err) => console.log(err));
    };

    window.addEventListener('load', (event) => {
        refreshTodos();
        let editLink;

        const container = document.querySelector('.todos-container');
        container.addEventListener('click', (event) => {
             // we'll use event bubbling and can check out the target here
            if (event.target.classList.contains('fa-times')) {
                let deleteLink = event.target.parentNode;
                removeTodo(deleteLink.dataset.id);
            }
            if (event.target.classList.contains('fa-plus-circle') || event.target.parentNode.classList.contains('add-todo')) {
                let modal = document.querySelector('#addTodoModal');
                modal.style.display = 'block';
                modal.focus();
            }
            if (event.target.classList.contains('fa-pen')) {
                let modal = document.querySelector('#editTodoModal');
                modal.style.display = 'block';
                modal.focus();
                editLink = event.target.parentNode;
                document.querySelector('#editTodoModal .modal-input').value = event.target.parentNode.parentNode.parentNode.textContent.trim();
                document.querySelector('#editTodoModal .modal-select').value = event.target.parentNode.parentNode.parentNode.firstElementChild.classList[1].trim();
            }
        });

        const containerAdd = document.querySelector('#addTodoModal');
        containerAdd.addEventListener('click', evt => {
            //close and cancel button
            if (evt.target.classList.contains('close') || evt.target.classList.contains('button-cancel')) {
                document.querySelector('#addTodoModal').style.display = 'none'
            }
            //add button
            if (evt.target.classList.contains('button-add')) {
                let todo = document.querySelector('#addTodoModal .modal-input').value;
                let prio = document.querySelector('#addTodoModal .modal-select').value;
                addTodo(todo, prio);
                document.querySelector('#addTodoModal ').style.display = 'none';
                document.querySelector('#addTodoModal .modal-input').value = '';
                document.querySelector('#addTodoModal .modal-select').value = 'low';
            }
        });
        //    and so on

        const containerEditor = document.querySelector('#editTodoModal');
        containerEditor.addEventListener('click', evt => {
            //close and cancel button
            if (evt.target.classList.contains('close') || evt.target.classList.contains('button-cancel')) {
                document.querySelector('#editTodoModal ').style.display = 'none';

            }
            //edit button
            if (evt.target.classList.contains('button-save')) {
                let todo = document.querySelector('#editTodoModal .modal-input').value;
                let prio = document.querySelector('#editTodoModal .modal-select').value;
                editTodo(todo, prio, editLink.dataset.id)
                document.querySelector('#editTodoModal ').style.display = 'none';
                document.querySelector('#editTodoModal .modal-input').value = '';
                document.querySelector('#editTodoModal .modal-select').value = 'low';

            }
        });
    });
})();
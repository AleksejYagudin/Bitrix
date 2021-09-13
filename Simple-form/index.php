<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $USER;
if (!$USER->IsAuthorized()) {
    LocalRedirect("/");
}
?>
<link rel="stylesheet" href="style.css">
<div class="form">
    <div class="form-block">
        <form id="formTask" method="post">
            <?=bitrix_sessid_post()?>
            <H2>Форма постановки задачи</H2>
            <div id="fields">
                <p><label for="task-name">Заголовок задачи</label></p>
                <p><input type="text" name="task-name" id="task-name" required></p>
                <p><label for="task-project">Проект</label></p>
                <p>
                    <select name="projects" id="project" required>
                        <option value="">--Выберите проект--</option>
                        <option value="IMS">IMS</option>
                        <option value="PM">PM</option>
                        <option value="BI">BI</option>
                    </select>
                </p>
                <p><label for="task-text1">Текст основания</label></p>
                <p><textarea name = "task-text1" cols="50" rows="10" id="task-text1" required></textarea></p>
                <p><label for="task-file">Файл основания</label></p>
                <p><input type="file" name="task-file" required></p>
                <p><label for="task-text2">Текст подробного описания</label></p>
                <p><textarea name="task-text2" cols="50" rows="10" id="task-text2" required></textarea></p>
                <p><label for="task-date">Дата</label></p>
                <p><input type="date" name="task-date" required></p>
                <p><button type="submit" id="submit" class="go">Отправить</button></p>
            </div>
        </form>
    </div>
    <div class="modal">
        <div class="modal_content">
            <p>Задача создана!</p>
            <button id="success">ОK</button>
        </div>
    </div>
    <div class="modalError">
        <div class="modal_content">
            <p>Ошибка при создании задачи!</p>
            <button id="successError">ОK</button>
        </div>
    </div>


<script>
    formTask.onsubmit = async (e) => {
        e.preventDefault();
        let response = await fetch('create-task.php', {
            method: 'POST',
            body: new FormData(formTask)
        });
        let result = await response.json(),
            modal = document.querySelector('.modal'),
            modalError = document.querySelector('.modalError'),
            success = document.querySelector('#success');
        if(result === 'ok')
        {
            modal.style.display = 'block';
        }
        if(result === 'no')
        {
            modalError.style.display = 'block';
        }
        success.addEventListener('click', function (){
            location.reload()
        })
        successError.addEventListener('click', function (){
            modalError.style.display = 'none';
        })
    };
</script>
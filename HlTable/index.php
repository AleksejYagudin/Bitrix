<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');?>
<script
        src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
        crossorigin="anonymous"></script>
<script src="script.js"></script>
<div class="main-block">
    <p>Введите название таблицы: <input type="text" id="table_name" required placeholder="Введите имя таблицы"></p>
    <p>Введите заголовок таблицы: <input type="text" id="table_title" required placeholder="Введите заголовок таблицы"></p>
    <button id="btn">Создать</button>
    <p id="result"></p>
</div>
<h2>Список таблиц</h2>
<div class="table-list">
</div>


<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');?>

<?php

?>

<form action="/site/index" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="fileInput">.php файл</label>
        <input type="file" class="form-control" id="fileInput" name="file" accept=".php">
        <small id="fileHelp" class="form-text text-muted">загрузка файл .php</small>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<?php if (!empty($fileContent)):?>
    <div class="card">
        <div class="card-body">
            <?=$fileContent?>
        </div>
    </div>
<?php endif;?>

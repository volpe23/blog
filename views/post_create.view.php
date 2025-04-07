<?php

use Core\Middleware\Csrf;
?>
<form method="POST" action="/posts_create">
    <?= Csrf::csrfInputField() ?>
    <div class="form-control">
        <label for="title">Title</label>
        <input name="title" id="title" class="form-input" placeholder="Title">
    </div>
    <div class="form-control">
        <label for="Text">Text</label>
        <textarea name="text" id="text" class="form-input" placeholder="Text for post..." rows="5">
        </textarea>
    </div>
    <div class="form-control">
        <input id="submit" type="submit" class="form-submit" value="Post">
    </div>

</form>
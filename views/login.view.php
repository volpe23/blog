<?php

use Core\Middleware\Csrf;
?>
<div id="user_login_form" class="container">
    <form method="POST" action="/login" class="border border-gray-500 p-8 max-w-96 my-6 mx-auto rounded">
        <?= Csrf::csrfInputField() ?>
        <div class="form-control">
            <label for="username">Username</label>
            <input name="username" id="username" class="form-input <?= isset($usernameError) && !empty($usernameError) ? "input-error" : "" ?>" placeholder="Username">
            <?php if (isset($usernameError)): ?>
                <span class="form-error-message"><?= $usernameError ?></span>
            <?php endif; ?>
        </div>
        <div class="form-control">
            <label for="password">Password</label>
            <input name="password" id="password" type="password" class="form-input <?= isset($passwordError) && !empty($passwordError) ? "input-error" : "" ?>" placeholder="Password">
            <?php if (isset($passwordError)): ?>
                <span class="form-error-message"><?= $passwordError ?></span>
            <?php endif; ?>
        </div>
        <div class="form-control">
            <input id="submit" type="submit" class="form-submit" value="Login">
        </div>
    </form>
</div>
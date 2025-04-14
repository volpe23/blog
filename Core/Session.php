<?php

namespace Core;

class Session
{

    /**
     * Checks if is set in Session
     * @param string $key
     * 
     * @return bool
     */
    public function check(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Sets the value in Session
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    public function set(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Set the value to be flashed
     * @param string $key
     * @param mixed $value
     * 
     * @return void
     */
    public function flash(string $key, mixed $value)
    {
        $_SESSION["_flash"][$key] = $value;
    }

    /**
     * Deletes session
     * @return void
     */
    public function destroy()
    {
        $_SESSION = [];
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    public function unflash()
    {
        unset($_SESSION["_flash"]);
    }

    /**
     * Set errors in flash
     * @param array $errors
     * 
     * @return void
     */
    public function setErrors(array $errors)
    {
        $_SESSION["_flash"]["errors"] = $errors;
    }

    /**
     * Get errors or false if not set
     * @return array|bool
     */
    public function errors()
    {
        return $_SESSION["_flash"]["errors"] ?? false;
    }

    /**
     * Get value from session
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }
}

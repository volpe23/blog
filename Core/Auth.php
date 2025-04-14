<?php

namespace Core;

use Core\Model;
use Core\Models\Users;

// TODO: refactor auth to be storec in the container
class Auth
{
    /**
     * @param class-string<Model> $userModel
     */
    private string $userModel;

    /**
     * User identifying column
     */
    private string $userIdentifier;

    public ?Model $user;

    /**
     * Session instance from conatiner
     * @var Session $session
     */
    protected $session;


    public function __construct(protected Container $app)
    {
        $this->session = $app->get("session");
    }

    /**
     * Checks if there is a user stored in session
     * @return bool
     */
    public function check(): bool
    {
        return $this->app->session()->check("user");
    }

    /**
     * @param array{username: string, password: string} $credentials
     */
    public function attempt(array $credentials): bool
    {
        if (!isset($credentials["username"], $credentials["password"])) {
            return false;
        }
        $user = $this->userModel::where([
            [
                "username",
                "=",
                $credentials["username"]
            ]
        ])->first();

        if ($user) {
            if (password_verify($credentials["password"], $user->password)) {
                self::$user = $user;
                self::login($user);
                return true;
            };
        }

        return false;
    }

    /**
     * @param Model $user
     * 
     * @return void
     */
    public function login($user)
    {
        $this->session->set("user", [
            "username" => $user->username
        ]);
        session_regenerate_id(true);
    }

    public function logout()
    {
        $this->session->destroy();
    }

    public function user(): ?Users
    {
        $res = $this->session->check("user") ?
            $this->userModel::where("username", $this->session->get("user")["username"])->first()
            : null;
        return $res;
    }

    public function setUserModel(string $userModel)
    {
        $this->userModel = $userModel;
    }
}

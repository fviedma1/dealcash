<?php
require_once '../models/UserModel.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Método para manejar el login
    public function login($username, $password) {
        return $this->userModel->verifyUser($username, $password);
    }
}

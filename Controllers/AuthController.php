<?php
require_once '../Models/User.php';
session_start();

class AuthController
{
    public function showLoginForm()
    {
        include 'login_view.php'; // Muestra el formulario de login
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = new User();

            if ($user->login($email, $password)) {
                echo json_encode(['status' => 'success', 'message' => 'Login exitoso']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas']);
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: login.php');
    }
}
?>

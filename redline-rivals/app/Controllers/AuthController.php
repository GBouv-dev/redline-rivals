<?php

require_once ROOT . '/app/Models/User.php';

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');
        $this->render('auth/login', ['title' => 'Connexion']);
    }

    public function login(): void
    {
        $email    = $this->input('email');
        $password = $this->input('password');

        if (!$email || !$password) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tous les champs sont requis.'];
            $this->redirect('/login');
        }

        $user = User::attempt($email, $password);

        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email ou mot de passe incorrect.'];
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->redirect('/dashboard');
    }

    public function registerForm(): void
    {
        if (Auth::check()) $this->redirect('/dashboard');
        $this->render('auth/register', ['title' => 'Inscription']);
    }

    public function register(): void
    {
        $username = $this->input('username');
        $email    = $this->input('email');
        $password = $this->input('password');
        $confirm  = $this->input('password_confirm');

        if (!$username || !$email || !$password || !$confirm) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tous les champs sont requis.'];
            $this->redirect('/register');
        }

        if ($password !== $confirm) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
            $this->redirect('/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Le mot de passe doit faire au moins 6 caractères.'];
            $this->redirect('/register');
        }

        if (User::findBy(['email' => $email])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Cet email est déjà utilisé.'];
            $this->redirect('/register');
        }

        if (User::findBy(['username' => $username])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Ce pseudo est déjà pris.'];
            $this->redirect('/register');
        }

        $userId = User::register($username, $email, $password);
        $user   = User::find((int) $userId);
        Auth::login($user);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Bienvenue sur Redline Rivals !'];
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }
}

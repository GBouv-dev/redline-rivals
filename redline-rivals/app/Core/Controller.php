<?php

abstract class Controller
{
    // Affiche une vue avec layout
    protected function render(string $view, array $data = [], string $layout = 'default'): void
    {
        extract($data);
        $viewFile = ROOT . "/app/Views/{$view}.php";

        if (!file_exists($viewFile)) {
            die("Vue introuvable : {$view}");
        }

        // Capture le contenu de la vue
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Affiche le layout avec le contenu
        $layoutFile = ROOT . "/app/Views/layouts/{$layout}.php";
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    // Redirige vers une URL
    protected function redirect(string $url): void
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    // Retourne une réponse JSON (pour les APIs)
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Vérifie si la requête est POST
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    // Récupère une valeur POST nettoyée
    protected function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : $default;
    }

    // Récupère une valeur GET nettoyée
    protected function query(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? htmlspecialchars(trim($_GET[$key])) : $default;
    }

    // Protège une route — redirige vers login si pas connecté
    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }
    }

    // Protège une route admin
    protected function requireAdmin(): void
    {
        if (!Auth::isAdmin()) {
            $this->redirect('/');
        }
    }
}

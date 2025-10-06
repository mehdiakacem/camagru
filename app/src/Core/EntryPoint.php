<?php

namespace Core;

class EntryPoint
{
    public function __construct(private \Core\Website $website) {}

    public function run(string $uri, string $method)
    {
        try {
            $this->checkUri($uri, $method);

            if ($uri == '') {
                // $uri = $this->website->getDefaultRoute();
                $title = 'Camagru';

                $variables = $page['variables'] ?? [];
                $output = $this->loadView('home.php', []);
            }
            else {

                
                $route = explode('/', $uri);

            $controllerName = array_shift($route);
            $action = array_shift($route);
            
            $this->website->checkLogin($controllerName . '/' . $action);
            
            if ($method === 'POST') {
                $action .= 'Submit';
            }

            $controller = $this->website->getController($controllerName);
            
            if (is_callable([$controller, $action])) {
                $page = $controller->$action(...$route);
                
                $title = $page['title'];
                
                $variables = $page['variables'] ?? [];
                $output = $this->loadView($page['template'], $variables);
            } else {
                http_response_code(404);
                $title = 'Not found';
                $output = 'Sorry, the page you are looking for could  not be found.';
            }
        }
        } catch (\PDOException $e) {
            $title = 'An error has occurred';

            $output = 'Database error: ' . $e->getMessage() . ' in ' .
                $e->getFile() . ':' . $e->getLine();
        }

        $layoutVariables = $this->website->getLayoutVariables();
        $layoutVariables['title'] = $title;
        $layoutVariables['output'] = $output;

        echo $this->loadView('/layouts/main.php', $layoutVariables);
    }

    private function loadView($viewFileName, $variables)
    {
        extract($variables);

        ob_start();
        include  __DIR__ . '/../Views/' . $viewFileName;

        return ob_get_clean();
    }

    private function checkUri($uri)
    {
        if ($uri != strtolower($uri)) {
            http_response_code(301);
            header('location: ' . strtolower($uri));
        }
    }
}

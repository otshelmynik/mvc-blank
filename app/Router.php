<?php

/**
 * Роутер — маршрутизация запросов к контроллерам.
 * Поддерживает параметризованные роуты: users/{id}
 */
class Router
{
    private static $routes = [];
    private static $currentRoute = null;

    /**
     * Загрузка роутов из файла
     */
    public static function load($file = null)
    {
        if ($file === null) {
            $file = dirname(__DIR__) . '/routes.php';
        }

        if (file_exists($file)) {
            self::$routes = require $file;
        }
    }

    /**
     * Добавить маршрут
     */
    public static function add($uri, $controller, $action = 'index')
    {
        self::$routes[$uri] = [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    /**
     * Обработать запрос — найти подходящий маршрут и вызвать контроллер
     */
    public static function dispatch()
    {
        // Получаем URI запроса
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        // Убираем ведущий слэш для сравнения с маршрутами
        $uri = ltrim($uri, '/');
        if ($uri === '') {
            $uri = '/';
        }

        // Определяем HTTP метод
        $method = $_SERVER['REQUEST_METHOD'];

        // Ищем точное совпадение
        if (isset(self::$routes[$uri])) {
            $routeConfig = self::$routes[$uri];

            // Для POST: определяем действие по паттерну маршрута
            if ($method === 'POST') {
                $routeConfig = self::$routes[$uri];
                $routeUri = $uri;

                // Если URI содержит {id} и заканчивается на /edit → update
                if (strpos($routeUri, '{') !== false && substr($routeUri, -5) === '/edit') {
                    $routeConfig['action'] = 'update';
                } elseif (strpos($routeUri, '{') !== false && substr($routeUri, -7) === '/delete') {
                    $routeConfig['action'] = 'destroy';
                } elseif (strpos($routeUri, '{') !== false) {
                    // Если URI содержит {id} → show/update/destroy
                    $routeConfig['action'] = 'show';
                } else {
                    // Простой URI → store
                    $routeConfig['action'] = 'store';
                }
            }

            return self::matchRoute($routeConfig, []);
        }

        // Ищем маршрут с параметрами
        foreach (self::$routes as $routeUri => $routeConfig) {
            if (strpos($routeUri, '{') === false) {
                continue;
            }

            $pattern = $routeUri;
            $params = [];

            // Заменяем {param} на именованные группы
            if (preg_match_all('/\{(\w+)\}/', $routeUri, $matches)) {
                $placeholders = $matches[1];

                // Создаём регулярное выражение
                // 1. Заменяем {param} на маркеры
                $temp = $routeUri;
                foreach ($placeholders as $p) {
                    $temp = str_replace('{' . $p . '}', '___P___', $temp);
                }
                // 2. Экранируем спецсимволы
                $pattern = preg_quote($temp, '#');
                // 3. Заменяем маркер на группу (используем \w+ вместо [^/]+ для простоты)
                $pattern = str_replace('___P___', '(\w+)', $pattern);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // убираем полное совпадение
                    foreach ($placeholders as $i => $name) {
                        $params[$name] = $matches[$i];
                    }

                    // Для POST: определяем действие
                    if ($method === 'POST') {
                        if (substr($routeUri, -5) === '/edit') {
                            $routeConfig['action'] = 'update';
                        } elseif (substr($routeUri, -7) === '/delete') {
                            $routeConfig['action'] = 'destroy';
                        } else {
                            $routeConfig['action'] = 'show';
                        }
                    }

                    return self::matchRoute($routeConfig, $params);
                }
            }
        }

        // Маршрут не найден — 404
        http_response_code(404);
        echo "404 — Страница не найдена";
        exit;
    }

    /**
     * Вызвать контроллер и действие
     */
    private static function matchRoute($routeConfig, $params = [])
    {
        self::$currentRoute = $routeConfig;

        $controllerName = $routeConfig['controller'];
        $action = $routeConfig['action'];

        // Добавляем суффикс Controller
        if (strpos($controllerName, 'Controller') === false) {
            $controllerName .= 'Controller';
        }

        $controllerFile = dirname(__DIR__) . '/controllers/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            http_response_code(500);
            echo "Ошибка: Контроллер {$controllerName} не найден";
            exit;
        }

        require_once $controllerFile;

        $controller = new $controllerName();

        // Передаём параметры в контроллер
        if (!empty($params)) {
            $reflection = new ReflectionMethod($controller, $action);
            $params = self::castParams($params, $reflection);
        }

        return $controller->$action(...array_values($params));
    }

    /**
     * Приведение типов параметров
     */
    private static function castParams($params, $method)
    {
        $casted = [];
        $paramsArray = array_values($params);

        foreach ($method->getParameters() as $i => $param) {
            if (isset($paramsArray[$i])) {
                $value = $paramsArray[$i];
                $type = $param->getType();

                if ($type) {
                    switch ($type->getName()) {
                        case 'int':
                            $value = (int) $value;
                            break;
                        case 'float':
                            $value = (float) $value;
                            break;
                        case 'bool':
                            $value = (bool) $value;
                            break;
                    }
                }

                $casted[] = $value;
            }
        }

        return $casted;
    }

    /**
     * Получить текущий маршрут
     */
    public static function getCurrentRoute()
    {
        return self::$currentRoute;
    }

    /**
     * Сгенерировать URL по имени маршрута
     */
    public static function url($uri, $params = [])
    {
        $url = $uri;

        // Заменяем параметры {id} => 1 и т.д.
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        // Добавляем ведущий слэш
        return '/' . ltrim($url, '/');
    }
}

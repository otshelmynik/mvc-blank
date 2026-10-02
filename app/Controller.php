<?php

/**
 * Базовый контроллер.
 * Предоставляет методы: render(), validate(), redirect(), getUrl()
 */
class Controller
{
    /**
     * Отобразить шаблон с данными
     *
     * @param string $view Имя шаблона (например, 'users/index')
     * @param array $data Данные для передачи в шаблон
     */
    protected function render($view, $data = [])
    {
        // Передаем контроллер в view для доступа к $this->input()
        $data['controller'] = $this;

        extract($data);

        $viewFile = dirname(__DIR__) . '/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Ошибка: Шаблон {$view} не найден";
            exit;
        }

        $layoutFile = dirname(__DIR__) . '/views/layouts/main.php';

        if (file_exists($layoutFile)) {
            // Используем буферизацию вывода для вставки контента в layout
            ob_start();
            include $viewFile;
            $content = ob_get_clean();

            include $layoutFile;
        } else {
            include $viewFile;
        }
    }

    /**
     * Валидация входящих данных
     *
     * @param array $data Данные для валидации
     * @param array $rules Правила: ['name' => 'required|min:3', 'email' => 'required|email']
     * @return array Массив ошибок (пустой — если всё ок)
     */
    protected function validate($data, $rules)
    {
        return Validator::validate($data, $rules);
    }

    /**
     * Перенаправление
     *
     * @param string $url URL для перенаправления
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Получить URL для роута
     *
     * @param string $route Имя роута
     * @param array $params Параметры для подстановки
     * @return string
     */
    protected function getUrl($route, $params = [])
    {
        return Router::url($route, $params);
    }

    /**
     * Получить данные POST запроса
     *
     * @param string $key Ключ (можно с dot-notation)
     * @param mixed $default Значение по умолчанию
     * @return mixed
     */
    protected function input($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        $keys = explode('.', $key);
        $value = $_POST;

        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Сохранить flash-сообщение
     *
     * @param string $key Ключ сообщения
     * @param string $message Текст сообщения
     */
    protected function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Получить flash-сообщение и удалить его
     *
     * @param string $key Ключ сообщения
     * @param string $default Значение по умолчанию
     * @return string|null
     */
    protected function getFlash($key, $default = null)
    {
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $message;
        }
        return $default;
    }
}

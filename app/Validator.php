<?php

/**
 * Валидатор входящих данных.
 * Правила задаются строкой через |: required, email, min:N, max:N, numeric, integer, url
 *
 * Пример:
 *   $rules = [
 *       'name'  => 'required|min:3|max:50',
 *       'email' => 'required|email',
 *       'age'   => 'numeric|min:18|max:120',
 *   ];
 *   $errors = Validator::validate($_POST, $rules);
 */
class Validator
{
    private static $messages = [
        'required'  => 'Поле "{field}" обязательно для заполнения.',
        'email'     => 'Поле "{field}" должно быть корректным email адресом.',
        'min'       => 'Поле "{field}" должно содержать минимум {param} символов.',
        'max'       => 'Поле "{field}" должно содержать максимум {param} символов.',
        'numeric'   => 'Поле "{field}" должно быть числом.',
        'integer'   => 'Поле "{field}" должно быть целым числом.',
        'url'       => 'Поле "{field}" должно быть корректным URL адресом.',
        'same'      => 'Поле "{field}" должно совпадать с полем {param}.',
    ];

    /**
     * Валидация данных
     *
     * @param array $data Данные
     * @param array $rules Правила
     * @return array Массив ошибок (пустой — если всё ок)
     */
    public static function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = isset($data[$field]) ? $data[$field] : null;
            $fieldLabel = ucfirst(str_replace('_', ' ', $field));

            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $ruleParam = isset($ruleParts[1]) ? $ruleParts[1] : null;

                // Пропускаем пустые значения для не required полей
                if ($ruleName !== 'required' && ($value === null || $value === '')) {
                    continue;
                }

                $passed = true;

                switch ($ruleName) {
                    case 'required':
                        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                            $errors[$field][] = str_replace('{field}', $fieldLabel, self::$messages['required']);
                            $passed = false;
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = str_replace('{field}', $fieldLabel, self::$messages['email']);
                            $passed = false;
                        }
                        break;

                    case 'min':
                        if (strlen((string) $value) < (int) $ruleParam) {
                            $msg = str_replace(['{field}', '{param}'], [$fieldLabel, $ruleParam], self::$messages['min']);
                            $errors[$field][] = $msg;
                            $passed = false;
                        }
                        break;

                    case 'max':
                        if (strlen((string) $value) > (int) $ruleParam) {
                            $msg = str_replace(['{field}', '{param}'], [$fieldLabel, $ruleParam], self::$messages['max']);
                            $errors[$field][] = $msg;
                            $passed = false;
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field][] = str_replace('{field}', $fieldLabel, self::$messages['numeric']);
                            $passed = false;
                        }
                        break;

                    case 'integer':
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field][] = str_replace('{field}', $fieldLabel, self::$messages['integer']);
                            $passed = false;
                        }
                        break;

                    case 'url':
                        if (!filter_var($value, FILTER_VALIDATE_URL)) {
                            $errors[$field][] = str_replace('{field}', $fieldLabel, self::$messages['url']);
                            $passed = false;
                        }
                        break;

                    case 'same':
                        if ($value !== $data[$ruleParam]) {
                            $paramLabel = ucfirst(str_replace('_', ' ', $ruleParam));
                            $msg = str_replace(['{field}', '{param}'], [$fieldLabel, $paramLabel], self::$messages['same']);
                            $errors[$field][] = $msg;
                            $passed = false;
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Добавить своё сообщение об ошибке
     *
     * @param string $rule Название правила
     * @param string $message Текст сообщения
     */
    public static function setMessage($rule, $message)
    {
        self::$messages[$rule] = $message;
    }
}

<?php

return [
    'required' => 'Поле :attribute обязательно для заполнения.',
    'string' => 'Поле :attribute должно быть строкой.',
    'email' => 'Введите корректный email.',
    'image' => 'Файл :attribute должен быть изображением.',
    'max' => [
        'string' => 'Поле :attribute не должно быть длиннее :max символов.',
        'numeric' => 'Поле :attribute не должно быть больше :max.',
        'file' => 'Файл :attribute не должен быть больше :max килобайт.',
        'array' => 'Поле :attribute не должно содержать больше :max элементов.',
    ],
    'min' => [
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
        'file' => 'Файл :attribute должен быть не меньше :min килобайт.',
        'array' => 'Поле :attribute должно содержать не менее :min элементов.',
    ],
    'confirmed' => 'Пароли не совпадают.',
    'unique' => 'Такой :attribute уже используется.',

    'attributes' => [
        'name' => 'имя',
        'email' => 'email',
        'password' => 'пароль',
        'password_confirmation' => 'повтор пароля',
        'avatar_file' => 'аватар',
    ],

    'custom' => [
        'password' => [
            'min' => 'Пароль должен содержать не менее :min символов.',
            'required' => 'Введите пароль.',
        ],
        'password_confirmation' => [
            'required' => 'Повторите пароль.',
        ],
        'email' => [
            'required' => 'Введите email.',
            'unique' => 'Пользователь с таким email уже зарегистрирован.',
        ],
        'name' => [
            'required' => 'Введите имя.',
        ],
        'avatar_file' => [
            'image' => 'Аватар должен быть изображением JPG, PNG или WebP.',
            'max' => 'Аватар должен быть изображением до 5 MB.',
        ],
    ],
];

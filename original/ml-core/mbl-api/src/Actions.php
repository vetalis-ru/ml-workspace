<?php

namespace Mbl\Api;

class Actions
{
    public function toArray(): array
    {
        return [
            'purchase' => __('Оплата товара', 'mbl_admin'),
            'free_item' => __('Товар с нулевой стоимостью', 'mbl_admin'),
            'free_registration_form' => 'Форма бесплатной регистрации',
            'bulk_operations_reg' => 'Массовые операции (регистрации)',
            'bulk_operations_add' => 'Массовые операции (добавление)',
            'auto_registration' => 'Авторегистрация',
            'activation_page' => 'Добавление ключа на странице активации',
            'profile_page_self' => 'Добавление ключа в профиле',
            'profile_page_admin' => 'Добавление доступа администратором',
            'after_auto_training_passed' => 'После прохождения автотренинга',
        ];
    }
}
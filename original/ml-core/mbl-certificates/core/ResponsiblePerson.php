<?php


class ResponsiblePerson
{
    public static function getResponsiblePersons(): array
    {
        return get_users([
            'role__in' => ['administrator', 'coach']
        ]);
    }
}
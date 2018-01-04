<?php

namespace AppBundle\Security;

final class Actions
{
    public const VIEW = 'view';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';
    public const EXPORT = 'export';
    public const DOWNLOAD = 'download';
    public const SUBSCRIBE = 'subscribe';

    /**
     * @return array
     */
    public static function getAllActions(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::EDIT,
            self::DELETE,
            self::EXPORT,
            self::DOWNLOAD,
            self::SUBSCRIBE
        ];
    }
}

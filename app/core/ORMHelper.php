<?php

namespace App\Core;

use Cycle\ORM\ORM;
use Cycle\ORM\Select;
use Cycle\ORM\EntityManager;
use Cycle\ORM\RepositoryInterface;

class ORMHelper
{
    private static ?ORM $orm = null;

    public static function getORM(): ORM
    {
        if (self::$orm === null) {
            self::$orm = require base_path('db/orm.php');
        }
        return self::$orm;
    }

    public static function getRepository(string $entityClass): RepositoryInterface
    {
        return self::getORM()->getRepository($entityClass);
    }

    public static function getManager(): EntityManager
    {
        return new EntityManager(self::getORM());
    }

    public static function select(string $entityClass): Select
    {
        $repository = self::getORM()->getRepository($entityClass);
        /** @var Select\Repository $repository */
        return $repository->select();
    }
}
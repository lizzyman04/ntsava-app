<?php

namespace App\Core;

use Cycle\ORM\ORM;
use Cycle\ORM\Select;
use Cycle\ORM\EntityManager;
use Cycle\ORM\Select\Repository;
use Cycle\Database\DatabaseManager;

class ORMHelper
{
    private static ?ORM $orm = null;
    private static ?DatabaseManager $dbManager = null;

    public static function getORM(): ORM
    {
        if (self::$orm === null) {
            self::$orm = require base_path('db/core/orm.php');
        }
        return self::$orm;
    }

    public static function getDatabaseManager(): DatabaseManager
    {
        if (self::$dbManager === null) {
            self::$dbManager = require base_path('db/core/connection.php');
        }
        return self::$dbManager;
    }

    public static function getRepository(string $entityClass): Repository
    {
        return self::getORM()->getRepository($entityClass);
    }

    public static function getManager(): EntityManager
    {
        return new EntityManager(self::getORM());
    }

    public static function select(string $entityClass): Select
    {
        return self::getRepository($entityClass)->select();
    }

    public static function findByPK(string $entityClass, $id): ?object
    {
        return self::getRepository($entityClass)->findByPK($id);
    }

    public static function findOne(string $entityClass, array $conditions): ?object
    {
        $repo = self::getRepository($entityClass);

        if (method_exists($repo, 'findOne')) {
            return $repo->findOne($conditions);
        }

        $all = $repo->findAll();
        foreach ($all as $entity) {
            $match = true;
            foreach ($conditions as $field => $value) {
                $getter = 'get' . ucfirst($field);
                if (method_exists($entity, $getter)) {
                    if ($entity->$getter() != $value) {
                        $match = false;
                        break;
                    }
                } elseif (property_exists($entity, $field)) {
                    if ($entity->$field != $value) {
                        $match = false;
                        break;
                    }
                } else {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                return $entity;
            }
        }

        return null;
    }

    public static function findAll(string $entityClass): array
    {
        return self::getRepository($entityClass)->findAll();
    }

    public static function findOneBy(string $entityClass, string $field, $value): ?object
    {
        $repo = self::getRepository($entityClass);

        if (method_exists($repo, 'findOneBy' . ucfirst($field))) {
            $method = 'findOneBy' . ucfirst($field);
            return $repo->$method($value);
        }

        if (method_exists($repo, 'findOne')) {
            return $repo->findOne([$field => $value]);
        }

        $all = $repo->findAll();
        foreach ($all as $entity) {
            $getter = 'get' . ucfirst($field);
            if (method_exists($entity, $getter) && $entity->$getter() == $value) {
                return $entity;
            }
        }

        return null;
    }

    public static function findAllBy(string $entityClass, string $field, $value): array
    {
        $repo = self::getRepository($entityClass);
        $result = [];

        $all = $repo->findAll();
        foreach ($all as $entity) {
            $getter = 'get' . ucfirst($field);
            if (method_exists($entity, $getter) && $entity->$getter() == $value) {
                $result[] = $entity;
            }
        }

        return $result;
    }
}
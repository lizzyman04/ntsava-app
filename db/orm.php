<?php

use Cycle\ORM;
use Cycle\Schema;
use Cycle\Database;
use Cycle\Annotated;
use Spiral\Tokenizer;

class ORMFactory
{
    private static ?ORM\ORMInterface $instance = null;

    public static function getORM(): ORM\ORMInterface
    {
        if (self::$instance === null) {
            $database = require __DIR__ . '/connection.php';
            self::$instance = self::initializeORM($database);
        }

        return self::$instance;
    }

    private static function initializeORM(Database\DatabaseManager $dbal): ORM\ORMInterface
    {
        $entitiesPath = __DIR__ . '/../src/Entities';
        
        $classLocator = (new Tokenizer\Tokenizer(new Tokenizer\Config\TokenizerConfig([
            'directories' => [$entitiesPath],
        ])))->classLocator();

        $schema = (new Schema\Compiler())->compile(new Schema\Registry($dbal), [
            new Schema\Generator\ResetTables(),
            new Annotated\Embeddings(new Annotated\Locator\TokenizerEmbeddingLocator($classLocator)),
            new Annotated\Entities(new Annotated\Locator\TokenizerEntityLocator($classLocator)),
            new Annotated\TableInheritance(),
            new Annotated\MergeColumns(),
            new Schema\Generator\GenerateRelations(),
            new Schema\Generator\GenerateModifiers(),
            new Schema\Generator\ValidateEntities(),
            new Schema\Generator\RenderTables(),
            new Schema\Generator\RenderRelations(),
            new Schema\Generator\RenderModifiers(),
            new Annotated\MergeIndexes(),
            new Schema\Generator\GenerateTypecast(),
        ]);

        return new ORM\ORM(
            new ORM\Factory($dbal),
            new ORM\Schema($schema)
        );
    }
}

return ORMFactory::getORM();
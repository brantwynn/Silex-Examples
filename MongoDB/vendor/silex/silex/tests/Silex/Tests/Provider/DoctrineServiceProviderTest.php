<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Silex\Tests\Provider;

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;

/**
 * DoctrineProvider test cases.
 *
 * Fabien Potencier <fabien@symfony.com>
 */
class DoctrineServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_dir(__DIR__.'/../../../../vendor/doctrine-common/lib') || !is_dir(__DIR__.'/../../../../vendor/doctrine-dbal/lib')) {
            $this->markTestSkipped('Doctrine Common/DBAL submodules were not installed.');
        }
    }

    public function testOptionsInitializer()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'db.common.class_path' => __DIR__.'/../../../../vendor/doctrine-common/lib',
            'db.dbal.class_path' => __DIR__.'/../../../../vendor/doctrine-dbal/lib',
        ));

        $this->assertEquals($app['db.default_options'], $app['db']->getParams());
    }

    public function testSingleConnection()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'db.common.class_path' => __DIR__.'/../../../../vendor/doctrine-common/lib',
            'db.dbal.class_path' => __DIR__.'/../../../../vendor/doctrine-dbal/lib',

            'db.options' => array('driver' => 'pdo_sqlite', 'memory' => true),
        ));

        $db = $app['db'];
        $params = $db->getParams();
        $this->assertTrue(array_key_exists('memory', $params));
        $this->assertTrue($params['memory']);
        $this->assertInstanceof('Doctrine\DBAL\Driver\PDOSqlite\Driver', $db->getDriver());
        $this->assertEquals(22, $app['db']->fetchColumn('SELECT 22'));

        $this->assertSame($app['dbs']['default'], $db);
    }

    public function testMultipleConnections()
    {
        $app = new Application();
        $app->register(new DoctrineServiceProvider(), array(
            'db.common.class_path' => __DIR__.'/../../../../vendor/doctrine-common/lib',
            'db.dbal.class_path' => __DIR__.'/../../../../vendor/doctrine-dbal/lib',

            'dbs.options' => array(
                'sqlite1' => array('driver' => 'pdo_sqlite', 'memory' => true),
                'sqlite2' => array('driver' => 'pdo_sqlite', 'path' => sys_get_temp_dir().'/silex'),
            ),
        ));

        $db = $app['db'];
        $params = $db->getParams();
        $this->assertTrue(array_key_exists('memory', $params));
        $this->assertTrue($params['memory']);
        $this->assertInstanceof('Doctrine\DBAL\Driver\PDOSqlite\Driver', $db->getDriver());
        $this->assertEquals(22, $app['db']->fetchColumn('SELECT 22'));

        $this->assertSame($app['dbs']['sqlite1'], $db);

        $db2 = $app['dbs']['sqlite2'];
        $params = $db2->getParams();
        $this->assertTrue(array_key_exists('path', $params));
        $this->assertEquals(sys_get_temp_dir().'/silex', $params['path']);
    }
}

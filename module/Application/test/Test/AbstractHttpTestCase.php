<?php
/**
 * @see http://blog.loftdigital.com/testing-with-apigility
 */

namespace ApplicationTest\Test;

use Application\Authentication\AccessControl;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * @method \ZF\ContentNegotiation\Request getRequest()
 */
abstract class AbstractHttpTestCase extends AbstractHttpControllerTestCase
{
    /**
     * @var Adapter
     */
    protected $adapter = null;

    /**
     * Lista de tabelas que serão criadas e dropadas
     *
     * @var array
     */
    protected $tables = [];

    public function setUp()
    {
        $configPath = realpath(__DIR__ . '/../../../../config/');
        if ($configPath === false) {
            $this->fail('Pasta de /config não localizada');
        }

        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [
            'module_listener_options' => [
                'config_cache_enabled' => false,
                'module_map_cache_enabled' => true,
            ],
            'db' => [
                'database' => 'not'
            ]
        ];

        $appConfig = ArrayUtils::merge(
            require $configPath . '/application.config.php',
            $configOverrides
        );

        // força não usar o local por segurança
        $appConfig['module_listener_options']['config_glob_paths'] = [$configPath . '/autoload/{,*.}{global,testing}.php'];

        $this->setApplicationConfig($appConfig);

        parent::setUp();
    }

    public function getApplication()
    {
        $application = parent::getApplication();

        if (!$application->getServiceManager()->has(AccessControl::class)) {
            $ac = new AccessControl();
            $application->getServiceManager()->setService(AccessControl::class, $ac);
        }

        $application->getServiceManager()
            ->get(AccessControl::class)
            ->setUsuario([
                'id_usuario' => 1,
                'nome' => 'Testing',
                'email' => 'email@testing.com',
                'admin' => 1,
                'webservice' => 1
            ]);

        return $application;
    }


    protected function assertResponseDetail($response, $detail, $code = null)
    {
        if ($code !== null) {
            $this->assertResponseStatusCode($code);
        }

        $this->assertInternalType('array', $response);
        $this->assertArrayHasKey('detail', $response);
        $this->assertEquals($detail, $response['detail']);
    }

    protected function assertValidationMessage($response, $key, $errorKey, $errorMessage = null)
    {
        $this->assertInternalType('array', $response, 'Invalid response');
        $this->assertArrayHasKey('validation_messages', $response, 'No validation messages found');
        $this->assertArrayHasKey(
            $key, $response['validation_messages'],
            "No validation message for $key"
        );

        if (!is_array($errorKey)) {
            $errorKey = [$errorKey];
        }

        if (!is_array($errorMessage)) {
            $errorMessage = array_fill(0, count($errorKey), $errorMessage);
        }

        $this->assertCount(count($errorKey), $errorMessage);

        $validationMessages = $response['validation_messages'][$key];
        foreach ($errorKey as $id => $k) {
            $this->assertArrayHasKey(
                $k,
                $response['validation_messages'][$key],
                "Error $k not found in $key"
            );
            if ($errorMessage[$id] !== null) {
                $this->assertEquals(
                    $errorMessage[$id],
                    $response['validation_messages'][$key][$errorKey],
                    "Message for $k not equal in $key"
                );
            }
            unset($validationMessages[$k]);
        }

        $this->assertEmpty(
            $validationMessages,
            "Some errors not reported for $key: " . implode(', ', array_keys($validationMessages))
        );
    }


    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        if (null === $this->adapter) {
            $this->adapter = $this->getApplication()->getServiceManager()->get(Adapter::class);
        }
        return $this->adapter;
    }

    /**
     * @param Adapter $adapter
     * @return $this
     */
    public function setAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     */
    public function createTables($tables = null)
    {
        if (empty($tables)) {
            $tables = $this->tables;
        }

        if (empty($tables)) {
            return $this;
        }

        // Recupera o script para criar as tabelas que estão no projeto do BobsFã
        $sqlPath = realpath(__DIR__ . '/../../../../vendor/bffc/extranet/tests/assets/sql/');
        foreach ($tables as $tbl) {
            $filePath = "$sqlPath/$tbl.sql";
            if (!file_exists($filePath)) {
                $this->fail("Arquivo sql '$filePath'");
            }

            $this->getAdapter()->query(file_get_contents($filePath), Adapter::QUERY_MODE_EXECUTE);
        }

        return $this;
    }

    /**
     * @param null $tables
     * @return $this
     */
    public function dropTables($tables = null)
    {
        if (empty($tables)) {
            $tables = array_reverse($this->tables);
        }

        if (!empty($tables)) {
            // Desabilita os indices e constrains para não dar erro
            // ao apagar uma tabela com foreign key
            // No mundo real isso é inviável, mas nos teste podemos
            // ignorar as foreign keys APÓS os testes
            $this->getAdapter()->query('SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;');
            $this->getAdapter()->query('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;');
            $this->getAdapter()->query('SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'TRADITIONAL,ALLOW_INVALID_DATES\';');

            // Recupera o script para remover as tabelas
            foreach ($tables as $tbl) {
                $this->getAdapter()->query("DROP TABLE IF EXISTS `$tbl`", Adapter::QUERY_MODE_EXECUTE);
            }

            $this->getAdapter()->query('SET SQL_MODE=@OLD_SQL_MODE;');
            $this->getAdapter()->query('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;');
            $this->getAdapter()->query('SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;');
        }

        return $this;
    }

    /**
     *
     * @param string|TableGateway $table
     * @param array $rows
     * @return $this
     */
    public function insertRows($table, $rows)
    {
        if (is_string($table)) {
            $table = new TableGateway($table, $this->getAdapter());
        } elseif (!$table instanceof TableGateway) {
            throw new \InvalidArgumentException("$table deve ser uma string ou TableGateway");
        }

        foreach ($rows as $r) {
            $table->insert($r);
        }

        return $this;
    }

    /**
     * @param string $procedureName
     */
    public function createProcedure(string $procedureName)
    {
        $sqlPath = realpath(__DIR__ . '/../../../../vendor/bffc/extranet/tests/assets/sql/');
        $filePath = $sqlPath . '/procedure_' . $procedureName . '.sql';
        if (!file_exists($filePath)) {
            $this->fail("Arquivo sql '$filePath'");
        }

        $this->getAdapter()->query("DROP PROCEDURE IF EXISTS $procedureName", Adapter::QUERY_MODE_EXECUTE);
        $this->getAdapter()->query(\file_get_contents($filePath), Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Retorna as tabelas padrões
     *
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }

    /**
     * Define as tabelas a serem usadas com padrão
     *
     * @param array $tables
     * @return $this
     */
    public function setTables($tables)
    {
        $this->tables = $tables;

        return $this;
    }

    /**
     * Apaga o conteúdo da pasta cache
     */
    public function clearApplicationCache()
    {
        // Recupera o script para criar as tabelas
        $cachePath = APPLICATION_DATA . '/cache/';
        $this->removeDirectory($cachePath, false);
    }

    private function removeDirectory($dir, $isSubDir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != ".." && $object != ".gitignore" && $object != 'zf') {
                    if (is_dir($dir . "/" . $object)) {
                        $this->removeDirectory($dir . "/" . $object, true);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            if ($isSubDir) {
                rmdir($dir);
            }
        }
        return;
    }

    public function tearDown()
    {
        $this->dropTables();
        parent::tearDown();
    }
}

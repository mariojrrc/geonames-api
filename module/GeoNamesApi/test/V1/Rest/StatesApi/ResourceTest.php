<?php

namespace GeoNamesApiTest\V1\Rest\StatesApi;

use Application\Model\ApiUserRole;
use Application\Model\ApiUserStatus;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ResourceTest extends AbstractHttpControllerTestCase
{
    public function testCreateStateSuccess()
    {
        $this->resetApplication();
        $this->dispatch('/states', 'POST');
        $this->assertControllerName('GeoNamesApi\\V1\\Rest\\States\\Controller');
        $this->assertMatchedRouteName('geo-names-api.rest.states');
        $this->assertResponseStatusCode(422);
        $response = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertEquals('Failed Validation', $response['detail']);

        // Not empty validation
        foreach ([
                     'name',
                     'shortName',
                 ] as $key) {
            $this->assertValidationMessage($response, $key, 'isEmpty');
        }
        $this->assertCount(2, $response['validation_messages']);

        $sample = [
            'name' => 'Rio de Janeiro',
            'shortName' => 'RJ',
        ];

        $this->resetApplication();
        $this->getRequest()->setContent(Json::encode($sample));
        $this->dispatch('/states', 'POST');
        $this->assertControllerName('GeoNamesApi\\V1\\Rest\\States\\Controller');
        $this->assertMatchedRouteName('geo-names-api.rest.states');
        $this->assertResponseStatusCode(415);
        $response = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertEquals('Invalid content-type specified', $response['detail']);

        $this->resetApplication();
        $this->getRequest()->setContent(Json::encode($sample));
        $this->getRequest()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $this->dispatch('/states', 'POST');
        $this->assertControllerName('GeoNamesApi\\V1\\Rest\\States\\Controller');
        $this->assertMatchedRouteName('geo-names-api.rest.states');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('', $this->getResponse()->getContent());
    }

    public function testGetStatesSuccess()
    {
        // Creates two states
        $samples = [
            [
                'name' => 'Rio de Janeiro',
                'shortName' => 'RJ',
            ],
            [
                'name' => 'São Paulo',
                'shortName' => 'SP',
            ],
        ];

        foreach ($samples as $state) {
            $this->resetApplication();
            $this->getRequest()->setContent(Json::encode($state));
            $this->getRequest()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
            $this->dispatch('/states', 'POST');
            $this->assertControllerName('GeoNamesApi\\V1\\Rest\\States\\Controller');
            $this->assertMatchedRouteName('geo-names-api.rest.states');
            $this->assertResponseStatusCode(201);
            $this->assertEquals('', $this->getResponse()->getContent());
        }

        // List all states
        $this->resetApplication();
        $this->getRequest()->getHeaders()->addHeaderLine('Content-Type', 'application/json');
        $this->dispatch('/states', 'GET');
        $this->assertControllerName('GeoNamesApi\\V1\\Rest\\States\\Controller');
        $this->assertMatchedRouteName('geo-names-api.rest.states');
        $this->assertResponseStatusCode(200);

        $response = Json::decode($this->getResponse()->getContent(), \JSON_OBJECT_AS_ARRAY);
        $this->assertArrayHasKey('_embedded', $response);
        $this->assertArrayHasKey('states', $response['_embedded']);
        $states = $response['_embedded']['states'];

        $this->assertNotEmpty($states);
        foreach ($states as $state) {
            $this->assertArrayHasKey('_id', $state);
            $this->assertNotEmpty( $state['_id']);
            $this->assertArrayHasKey('name', $state);
            $this->assertNotEmpty( $state['name']);
            $this->assertArrayHasKey('shortName', $state);
            $this->assertNotEmpty( $state['shortName']);
            $this->assertArrayHasKey('createdAt', $state);
            $this->assertNotEmpty( $state['createdAt']);
        }
    }


    public function setUp()
    {
        $configPath = realpath(__DIR__ . '/../../../../../../config/');
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

    private function resetApplication()
    {
        $this->clearApplicationCache();
        $this->reset();
        $this->getApplicationServiceLocator()->get('authentication')->setTokenConfig([
            'testing' =>
                [
                    'id_apiuser' => 666,
                    'name' => 'Test User',
                    'status' => ApiUserStatus::ACTIVE,
                    'role' => ApiUserRole::USER_DEFINED,
                    'ip' => null,
                    'permissions' => [
                        'GeoNamesApi\\V1\\Rest\\States\\Controller::createMessage' => ['POST'],
                    ],
                ],
        ]);

        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Authorization', 'Geonames testing');
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
}
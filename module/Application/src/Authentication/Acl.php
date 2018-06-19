<?php

namespace Application\Authentication;

use Realejo\Utils\Cache;
use Zend\Cache\StorageFactory;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Model para o controle de acesso no administrador do site
 *
 * @link      http://github.com/bffc-bobs/bobs-fa
 * @copyright Copyright (c) 2014 Realejo (http://realejo.com.br)
 * @license   proprietary
 */
class Acl
{
    /**
     * Array com os módulos disponíveis
     *
     * @var array
     */
    private $_acls = [];

    /**
     * Array com a descrição de cada role permissão
     *
     * @var array
     */
    private $_roles = [];

    public function __construct()
    {
        $cache = self::getCache();

        // Verifica se tem cache
        if (!$cache->getItem('acls') || !$cache->getItem('roles')) {
            self::createAcl();
        }

        $saved = [];
        // Recupera do cache
        $saved['acls'] = $cache->getItem('acls');
        $saved['roles'] = $cache->getItem('roles');

        $this->_acls = unserialize($saved['acls']);
        $this->_roles = unserialize($saved['roles']);
    }

    /**
     * Função para retornar todos os modulos e os roles disponíveis
     *
     * @param  string $modulo Use para retorna as informações de apenas um modulo
     * @return array|null
     */
    public function getModulos($modulo = null)
    {
        if (!is_null($modulo)) {
            if (array_key_exists($modulo, $this->_roles)) {
                return $this->_roles[$modulo];
            }
        } else {
            return $this->_roles;
        }

        return $this->_acls;
    }

    /**
     * Função para retornar todos os roles disponíveis em cada módulo
     *
     * @param  string $modulo Use para retorna as informações de apenas um modulo
     * @return array|\Zend_Acl|null
     */
    public function getRoles($modulo = null)
    {
        // Verifica se é para retornar apenas um
        if (!is_null($modulo)) {
            if (array_key_exists($modulo, $this->_acls)) {
                return $this->_acls[$modulo];
            }

            // Retorna todos os módulos
        } else {
            $modulos = [];
            foreach ($this->_acls as $m => $acl) {
                $modulos[$m] = $acl->getRoles();
            }
            return $modulos;
        }

        return null;
    }

    /**
     * Recupera o Acl de um módulo
     *
     * @param string $modulo
     *
     * @return \Zend_Acl|bool
     */
    public function getAcl($modulo)
    {
        // Verifica se o ACL está carregado
        if (empty($this->_acls)) {
            list($this->_acls, $this->_roles) = $this->createAcl();
        }

        // Verifica se o módulo existe no ACL
        if (!array_key_exists($modulo, $this->_acls)) {
            return false;
        }

        // Retorna o Acl
        return $this->_acls[$modulo];
    }

    /**
     * Recupera os roles em cada modulo
     *
     * @param int $idu código do usuário
     *
     * @param bool $force
     * @return array|object
     * @throws \Exception
     */
    public function getUsuarioRoles($idu, $force = false)
    {
        // Verifica se é o código do usuário
        if (!is_numeric($idu)) {
            throw new \Exception("Código do usuário $idu não válido em Acl::getUsuarioRole()");
        }

        $roleItem = $this->getCache()->getItem('api.acl.' . $idu);

        if ($force === false && $roleItem) {
            return (object)$roleItem;
        }

        // Recupera os roles utilizando o model usuário do ZF1
        $fetch = (new \Usuario())->getLoader()->getTable('usuario_acl')->fetchAll([new \Zend_Db_Expr("fk_usuario = $idu")]);

        // Verifica se localizou algum role
        if (count($fetch) == 0) {
            $roles = null;
        } else {
            $roles = [];
            foreach ($fetch as $row) {
                if (!empty($row['role'])) {
                    $roles[$row['modulo']] = $row['role'];
                }
            }
        }

        if (($roleItem == null) || $force === true) {
            $this->getCache()->setItem('api.acl.' . $idu, $roles);
        }

        // Retorna os roles
        return (object)$roles;
    }

    /**
     * Grava os roles de um usuário
     *
     * @todo verificar se o role existe para o módulo antes de gravar
     *
     * @param int $idu Código do usuário
     * @param array $roles permissões em cada módulo
     */
    public function setUsuarioRoles($idu, $roles)
    {
        // Apaga as permissões do usuário
        (new \Usuario())->getLoader()->getTable('usuario_acl')->delete(["fk_usuario" => $idu]);

        // Grava as novas permissões
        if (is_array($roles)) {
            foreach ($roles as $modulo => $role) {
                if ($role !== 0) {
                    (new \Usuario())->getLoader()->getTable('usuario_acl')->insert([
                        'fk_usuario' => $idu,
                        'modulo' => $modulo,
                        'role' => $role
                    ]);
                }
            }
        }

        // Apaga o cache das permissões do usuário
        $roleFile = $this->getRoleCahePath() . '/' . $idu . '.php';

        if (file_exists($roleFile)) {
            unlink($roleFile);
        }
    }

    /**
     * Cria o ACL dos módulos e grava o cache deles
     *
     * @return array(acls, roles)
     */
    public static function createAcl()
    {
        $acls = $roles = [];

        //Não foi preciso transformar cada tipo de acl do ZF1 para o ZF2
        /**
         * Gerenciamento de Ciclos
         */
        $acls['ciclo'] = \Bobsfa\Ciclo\Acl::getAcl();
        $roles['ciclo'] = \Bobsfa\Ciclo\Acl::getAclDescription();

        /**
         * Gerenciamento de promoções
         */
        $acls['promocao'] = \Promocao\AppAdmin\Acl::getAcl();
        $roles['promocao'] = \Promocao\AppAdmin\Acl::getAclDescription();

        /**
         * Gerenciamento de clientes
         */
        $acls['cliente'] = \Cliente\AppAdmin\Acl::getAcl();
        $roles['cliente'] = \Cliente\AppAdmin\Acl::getAclDescription();

        /**
         * Gerenciamento de lojas
         */
        $acls['pdv'] = \Pdv\AppAdmin\Acl::getAcl();
        $roles['pdv'] = \Pdv\AppAdmin\Acl::getAclDescription();

        /**
         * Gerenciamento de Pequisa
         */
        $acls['pesquisa'] = \Pesquisa\Admin\Acl::getAcl();
        $roles['pesquisa'] = \Pesquisa\Admin\Acl::getAclDescription();

        /**
         * Gerenciamento do game de Bobs Fã
         */
        $acl = new \Zend\Permissions\Acl\Acl();

        // Roles
        $acl->addRole(new GenericRole('consulta'))
            ->addRole(new GenericRole('agencia'), 'consulta')
            ->addRole(new GenericRole('marketing'), 'agencia')
            ->addRole(new GenericRole('admin'), 'marketing');

        // Resources
        $acl->addResource(new GenericResource('pontos'))
            ->addResource(new GenericResource('desafio'));

        // Access control
        $acl->allow('consulta', 'pontos', ['read'])
            ->allow('admin', 'pontos', ['create', 'read', 'update', 'delete'])
            ->allow('consulta', 'desafio', ['read'])
            ->allow('marketing', 'desafio', ['read', 'desafio-safe'])
            ->allow('admin', 'desafio', ['create', 'read', 'update', 'delete']);


        // Grava nos arrays
        $acls['bobsfa'] = $acl;
        $roles['bobsfa'] = [
            'nome' => 'Bobs Fã',
            'descricao' => 'Gerencia as permissões do game no Bobs Fã',
            'roles' => [
                'consulta' => 'Consulta',
                'agencia' => 'Agencia',
                'marketing' => 'Marketing',
                'admin' => 'Administrador'
            ],
            'rdescricao' => [
                'consulta' => 'Acesso de leitura',
                'agencia' => '(pendente)',
                'marketing' => 'Libera acesso a alterar imagens',
                'admin' => 'Todas as permissões'
            ]
        ];

        // Grava no cache
        $cache = self::getCache();
        $cache->setItem('acls', serialize($acls));
        $cache->setItem('roles', serialize($roles));

        return [$acls, $roles];
    }

    /**
     * Recupera o cache do ACL
     *
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public static function getCache()
    {
        // Configura o cache
//         $frontendOptions = array( 'automatic_serialization' => true, 'lifetime' => null);
//         $backendOptions  = array( 'cache_dir' => \Realejo\App\Model\Cache::getCachePath('acl'));
        $cache = StorageFactory::factory([
            'adapter' => 'memcached',
            'lifetime' => null,
            'options' => [
                'servers' => [
                    [
                        '127.0.0.1',
                        11211
                    ]
                ],
                'namespace' => 'api.acl',
                'liboptions' => [
                    'COMPRESSION' => true,
                    'binary_protocol' => true,
                    'no_block' => true,
                    'connect_timeout' => 100
                ]
            ]
        ]);

//         $cache->setOptions($backendOptions);

        return $cache;
    }

    public static function getRoleCahePath()
    {
        // retorna a pasta raiz do cache
        return Cache::getCachePath('acl/role');
    }

    public function canUpdate($module, $key, AccessControl $authentication = null)
    {
        if ($module == 'cliente') {
            // Verifica se há usuário
            if (empty($authentication) || !($authentication instanceof AccessControl)) {
                $authentication = new AccessControl();
            }
            if (empty($authentication) || !$authentication->hasIdentity()) {
                throw new \Exception('Não é possivel determinar o usuário ');
            }

            $access = [
                'info' => [
                    'nome',
                    'sobrenome',
                    'email',
                    'cpf',
                    'sexo',
                    'data_nascimento',
                    'celular',
                    'telefone',
                    'uf',
                    'cidade',
                    'logradouro',
                    'bairro',
                    'complemento',
                    'numero',
                    'cep',
                    'cpf_nao_possui',
                    'cpf_nao_possui_motivo',
                    'fb_userid',
                    'fb_token',
                    'senha',
                    'beta',
                    'nivel_sl',
                    'btn-alterar',
                    'aceito_celular',
                    'aceito_email',
                    'aceito_parceiro'
                ],
                'info-cupons' => [
                    'gerar',
                    'cancelar',
                    'trocar',
                    'resgate-manual'
                ],
                'info-promocao' => [
                    'gerar'
                ],
                'info-notificacoes' => [
                    'liberar',
                    'remover'
                ],
                'info-pesquisas' => [
                    'gerar'
                ],
                'alteracao-massa' => [
                    'liberar',
                    'todos_fas'
                ],
            ];

            // Verifica se tem acesso de update
            if (!$authentication->isAllowed('cliente', 'cliente', 'update')) {
                $access['info'] = false;
                $access['info-cupons'] = false;
                $access['info-notificacoes'] = false;
                $access['alteracao-massa'] = false;

                // Fica aqui pois é um subset das permissões acima
                if ($authentication->isAllowed('cliente', 'cliente', 'cliente-safe')) {
                    $access['info'] = [
                        'nome',
                        'sobrenome',
                        'email',
                        'cpf',
                        'sexo',
                        'data_nascimento',
                        'celular',
                        'telefone',
                        'uf',
                        'cidade',
                        'logradouro',
                        'bairro',
                        'complemento',
                        'numero',
                        'cep',
                        'cpf_nao_possui',
                        'cpf_nao_possui_motivo',
                        'senha',
                        'beta',
                        'nivel_sl',
                        'btn-alterar',
                    ];
                    $access['info-cupons'] = [
                        'gerar',
                        'cancelar',
                        'trocar',
                        'resgate-manual'
                    ];

                    $access['info-notificacoes'] = [
                        'liberar',
                        'remover'
                    ];
                }

                // Fica aqui pois é um subset das permissões acima
                if ($authentication->isAllowed('cliente', 'cliente', 'massa')) {
                    $access['alteracao-massa'] = [
                        'liberar',
                        'todos_fas'
                    ];
                }
            }

            if (isset($key) && is_array($access) && array_key_exists($key, $access)) {
                return $access[$key];
            } else {
                return $access;
            }
        }

        return false;
    }
}

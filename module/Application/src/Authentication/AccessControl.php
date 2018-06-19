<?php

namespace Application\Authentication;

use Application\Model\ApiUserService;
use ZF\ApiProblem\ApiProblem;
use Application\Model\ApiUserEntity;
use Application\Model\ApiUserStatus;

class AccessControl
{
    use \Realejo\MvcUtils\ServiceLocatorTrait;

    /**
     * Roles do usuário logado em cada módulo
     *
     * @var array
     */
    public $roles = [];
    /**
     * @var Acl
     */
    public $acl;

    /**
     * @var ApiUserEntity
     */
    private $user;

    /**
     * @var \Zend\Authentication\Storage\StorageInterface
     */
    private $_usuario;
    /**
     * @var array
     */
    private $tokenConfig;

    function __construct($id_usuario = null)
    {
        // Carrega os roles do usuário
        if ($id_usuario) {
            $this->roles = $this->getAcl()->getUsuarioRoles($id_usuario);
        }
    }

    /**
     * @return \Application\Authentication\Acl
     */
    public function getAcl()
    {
        return $this->getFromServiceLocator(Acl::class);
    }

    /**
     * Informa se o usuário é um funcionário da Realejo.
     *
     * @return bool
     */
    public function isRealejo()
    {
        return ($this->isAdmin()
            && strpos($this->getUsuario()->email, '@realejo.com.br') !== false);
    }

    /**
     * Retorna se o usuário é um superusuário. Ele é automaticamente administrador de todos os módulos
     *
     * @param array $usuario (OPCIONAL) Valida um usuário ao invés do usuário logado
     *
     * @return bool
     */
    public function isAdmin($usuario = null)
    {
        // Verifica se deve verificar a identidade
        $hasIdentity = (empty($usuario)) ? $this->hasIdentity() : true;

        // Verifica se foi passado um usuário
        if (empty($usuario)) {
            $usuario = $this->getUsuario();
        } elseif (is_array($usuario)) {
            $usuario = (object)$usuario;
        }

        return ($hasIdentity
            && $usuario->admin == 1);
    }

    public function hasIdentity()
    {
        return (null !== $this->_usuario || null !== $this->user);
    }

    /**
     * Retorna o usuário da sessão
     *
     * @param boolean $useArray OPCIONAL retorna o usuário como array e não como stdClass
     *
     * @return array|\stdClass|\Zend\Authentication\Storage\StorageInterface
     */
    public function getUsuario($useArray = false)
    {
        if ($useArray === true) {
            return (array)$this->_usuario;
        } else {
            return $this->_usuario;
        }
    }

    /**
     * Grava o usuário na sessão
     *
     * @param array $usuario
     * @return AccessControl|ApiProblem
     */
    public function setUsuario(array $usuario = null)
    {
        if (empty($usuario)) {
            return $this;
        }

        // Verifica se tem um usuário especificado
        if (null === $usuario && $this->hasIdentity()) {
            $usuario = $this->getUsuario()->id_usuario;
        }

        if (empty($usuario)) {
            return new ApiProblem(403, 'Token invalido.');
        }

        // Remove campos desnecessários
        unset($usuario['senha']);

        // Grava na sessão ativa
        $this->_usuario = (object)$usuario;

        // Retorna a cadeia
        return $this;
    }

    /**
     * Grava o usuário na sessão
     *
     * @param ApiUserEntity $user
     * @return AccessControl|ApiProblem
     */
    public function setApiUser(ApiUserEntity $user)
    {
        if (!$user->status->is(ApiUserStatus::ACTIVE)) {
            return new ApiProblem(403, 'Usuário não ativo.');
        }

        // Grava na sessão ativa
        $this->user = (object)$user;

        // Retorna a cadeia
        return $this;
    }

    /**
     * Retorna o usuário da sessão
     *
     * @return ApiUserEntity
     */
    public function getApiUser(): ApiUserEntity
    {
        return $this->user;
    }

    /**
     * Verifica se o usuário é um dos cargos solicitados (roles)
     *
     * @param null $role
     * @param  boolean $useAdmin (OPCIONAL) Retorna TRUE se for um administrador
     * @return bool
     * @internal param array|string $roles Role a ser verificado
     */
    public function isRoles($role = null, $useAdmin = false)
    {
        if ($this->hasIdentity()) {
            // Retorna TRUE se for admin
            if ($useAdmin && $this->isAdmin()) {
                return true;
            }

            // Transforma em array removendo os espaços se houver
            if (!is_array($role)) {
                $role = explode(',', str_replace(' ', '', $role));
            }

            // Verifica se tem o role
            if (in_array($this->getUsuario()->role, $role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário tem permissão necessária
     * Se não informado o $resource retorna verdadeiro se ele tem qualquer
     * permissão no módulo
     *
     * @param string $module Nome do módulo
     * @param string $resources OPCIONAL Nome do recurso
     * @param string $privileges OPCIONAL Nome do privilégio
     * @param boolean $useAdmin OPCIONAL indica de deve ignorar se o usuário é admin
     * @return bool
     * @internal param int $id_usuario Identificador do usuário
     * @throws \Exception
     */
    public function isAllowed($module, $resources = null, $privileges = null, $useAdmin = true)
    {
        // Verifica se tem um usuário carregado
        if (!$this->hasIdentity()) {
            return false;
        }

        if ($this->isApiUser()) {
            return true;
        }

        // Verifica se tem um admin e ignora a chamada
        if ($this->isAdmin() && $useAdmin) {
            if ($resources === null) {
                return ($this->getAcl()->getAcl($module));
            }
            return $this->getAcl()->getAcl($module)->has($resources);
        }

        // Carrega o cache das roles do usuário
        $roles = $this->getAcl()->getUsuarioRoles($this->getUsuario()->id_usuario);

        // Verifica se ele possui alguma permissão no módulo
        if (isset($roles->$module)) {
            // Verifica se é uma consulta genérica
            if ($resources === null) {
                return true;
            }

            // Verifica se possui a permissão
            return $this->getAcl()->getAcl($module)->isAllowed($roles->$module, $resources, $privileges);
        }

        return false;
    }

    /**
     * @param ApiUserEntity $user
     * @return AccessControl|ApiProblem
     * @throws \RuntimeException
     */
    public function setUsuarioForApiUser(ApiUserEntity $user)
    {
        // Carrega os dados do usuário
        $usuarioService = $this->getFromServiceLocator(ApiUserService::class);
        $usuario = $usuarioService
            ->findOne($user->idApiUser);

        if (empty($usuario)) {
            return new ApiProblem(403, 'Usuário inválido.');
        }

        // Verifica se o usuário tem permissão de acessar o webservice
        if (isset($usuario['api']) && $usuario['api'] !== 1
            || isset($usuario['webservice']) && $usuario['webservice'] !== 1) {
            new ApiProblem(403, 'Usuário inativo');
        }

        return $this->setUsuario($usuario);
    }

    /**
     * @param array $tokenConfig
     */
    public function setTokenConfig(array $tokenConfig)
    {
        $this->tokenConfig = $tokenConfig;
    }

    /**
     * @return array
     */
    public function getTokenConfig()
    {
        return $this->tokenConfig;
    }


    /**
     * Retorna o id do usuário da sessão
     *
     * @return int
     */
    public function getUsuarioId()
    {
        if ($this->hasIdentity()) {
            return (int)$this->_usuario->id_usuario;
        }
        return null;
    }

    /**
     * Retorna se deve usar o usuário API
     *
     * @return bool
     */
    public function isApiUser(): bool
    {
        return (isset($this->user));
    }
}

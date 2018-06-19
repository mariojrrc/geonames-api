<?php
declare(strict_types=1);

namespace Application\Authentication;

use Application\Authentication\Identity\IdentityInterface;
use Application\Authentication\Identity\ManagerIdentity;
use Application\Authentication\Identity\UserDefinedIdentity;
use Zend\Authentication\AuthenticationService as ZendAuthenticationService;
use Zend\Http\Header\Authorization;
use ZF\ApiProblem\ApiProblem;

/**
 * @var Identity\UserDefinedIdentity getIdentity()
 */
class AuthenticationService extends ZendAuthenticationService
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var array
     */
    private $tokenConfig;

    /**
     * @var string
     */
    private $token;

    /**
     * @param Authorization $header
     * @return AuthenticationService|ApiProblem
     */
    public function setTokenFromAuthorizationHeader(Authorization $header)
    {
        // split token
        $preg_match = preg_match('#(\w*)\s\s*(\w*)\/?(\w*)?#', $header->getFieldValue(), $matches);

        // Check for matches
        if ($preg_match !== 1) {
            return new ApiProblem(403, 'Authorization token not found.');
        }

        // Verifica o token
        if (strtolower($matches[1]) !== 'geonames'
            && strtolower($matches[1]) !== 'manager') {
            return new ApiProblem(403, 'Authorization format invalid.');
        }

        // Valida o token
        if (!isset($matches[2]) || empty(isset($matches[2]))) {
            return new ApiProblem(403, 'Token not set or invalid');
        }

        if (strtolower($matches[1]) === 'manager') {
            if ($this->registerManagerToken($matches[2]) === false) {
                return new ApiProblem(403, 'Invalid token.');
            }
            return $this;
        }

        // Validate the token
        if ($this->registerToken($matches[2]) === false) {
            return new ApiProblem(403, 'Invalid token.');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getStorage()->read()->getToken();
    }

    /**
     * @param string $token
     * @return IdentityInterface|bool|ApiProblem
     */
    public function registerToken(string $token)
    {
        // Checks if the token is valid
        $tokenConfig = $this->getTokenConfig();

        if (!isset($tokenConfig[$token])) {
            return false;
        }

        // @todo colocar em um factory?!
        $apiUser = null;
        if (isset($tokenConfig[$token]['role'])) {
            $apiUser = new UserDefinedIdentity();
            $apiUser->setUser($tokenConfig[$token], $token);
        }

        if (empty($apiUser)) {
            throw new \RuntimeException("Invalid user $token");
        }

        $this->getStorage()->write($apiUser);

        $this->token = $token;

        return $apiUser;
    }

    private function getTokenConfig(): array
    {
        if ($this->tokenConfig === null) {
            $configFile = 'data/token-config.php';
            if (!file_exists($configFile)) {
                throw new \RuntimeException("Arquivo $configFile não encontrado.");
            }
            $this->tokenConfig = require $configFile;
        }

        return $this->tokenConfig;
    }

    public function setTokenConfig(array $config)
    {
        $this->tokenConfig = $config;
    }

    private function registerManagerToken(string $token): bool
    {
        // Checks if the token is valid
        if (!isset($this->config['manager']) || $this->config['manager'] !== $token) {
            return false;
        }

        // @todo colocar em um factory?!
        $apiUser = new ManagerIdentity();
        $apiUser->setUser([], $token);

        $this->getStorage()->write($apiUser);

        $this->token = $token;

        return true;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }
}

<?php
declare(strict_types=1);

namespace Application\Authentication;

use Application\Authentication\Identity\IdentityInterface;
use ZF\MvcAuth\MvcAuthEvent;

class AbstractAuthorizationListener
{
    protected $resources = [];

    public function __invoke(MvcAuthEvent $mvcAuthEvent)
    {
        /** @var \ZF\MvcAuth\Authorization\AclAuthorization $authorization */
        $authorization = $mvcAuthEvent->getAuthorizationService();

        /** @var \Application\Authentication\AuthenticationService $authorization */
        $authentication = $mvcAuthEvent->getAuthenticationService();

        /**
         * Regardless of how our configuration is currently through via the Apigility UI,
         * we want to ensure that the default rule for the service we want to give access
         * to a particular identity has a DENY BY DEFAULT rule.  In our case, it will be
         * for our FooBar\V1\Rest\Foo\Controller's collection method GET.
         *
         * Naturally, if you have many versions, or many methods, you would want to build
         * some kind of logic to build all the possible strings, and push these into the
         * ACL. If this gets too cumbersome, writing an assertion would be the next best
         * approach.
         */
        foreach ($this->resources as $resource => $privileges) {
            $authorization->deny(null, $resource, $privileges);
        }

        /**
         * Next, assign the particular privilege that this identity needs.
         */
        $identity = $authentication->getIdentity();
        if (!empty($identity) && $identity instanceof IdentityInterface) {
            foreach (array_keys($this->resources) as $resource) {
                foreach ($authentication->getIdentity()->getResource($resource) as $privilege) {
                    $authorization->allow(null, $resource, $privilege);
                }
            }
        }
    }
}

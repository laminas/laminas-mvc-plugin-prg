<?php

namespace Laminas\Mvc\Plugin\Prg;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Exception\RuntimeException;
use Laminas\Session\Container;
use Laminas\Stdlib\DispatchableInterface;

/**
 * Plugin to help facilitate Post/Redirect/Get (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 */
class PostRedirectGet extends AbstractPlugin
{
    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     * Perform PRG logic
     *
     * If a null value is present for the $redirect, the current route is
     * retrieved and use to generate the URL for redirect.
     *
     * If the request method is POST, creates a session container set to expire
     * after 1 hop containing the values of the POST. It then redirects to the
     * specified URL using a status 303.
     *
     * If the request method is GET, checks to see if we have values in the
     * session container, and, if so, returns them; otherwise, it returns a
     * boolean false.
     *
     * @param  null|string $redirect
     * @param  bool        $redirectToUrl
     * @return \Laminas\Http\Response|array|\Traversable|false
     */
    public function __invoke($redirect = null, $redirectToUrl = false)
    {
        $controller = $this->getController();
        $request    = $controller->getRequest();
        $container  = $this->getSessionContainer();

        if ($request->isPost()) {
            $container->setExpirationHops(1, 'post');
            $container->post = $request->getPost()->toArray();
            return $this->redirect($redirect, $redirectToUrl);
        }

        if (null !== $container->post) {
            $post = $container->post;
            unset($container->post);
            return $post;
        }

        return false;
    }

    /**
     *
     * @return string
     */
    public function getContainerIdentifier()
    {
        $controller = $this->getController();
        $request    = $controller->getRequest();

        return md5($request->getUri());
    }

    /**
     * @return Container
     */
    public function getSessionContainer()
    {
        if (! $this->sessionContainer) {
            $this->sessionContainer = new Container($this->getContainerIdentifier());
        }
        return $this->sessionContainer;
    }

    /**
     * @param  Container $container
     * @return PostRedirectGet
     */
    public function setSessionContainer(Container $container)
    {
        $this->sessionContainer = $container;
        return $this;
    }

    /**
     * TODO: Good candidate for traits method in PHP 5.4 with FilePostRedirectGet plugin
     *
     * @param  string  $redirect
     * @param  bool    $redirectToUrl
     * @return \Laminas\Http\Response
     * @throws RuntimeException if route-based redirection is requested, but no
     *     plugin manager is composed in the controller.
     */
    protected function redirect($redirect, $redirectToUrl)
    {
        $controller         = $this->getController();
        $params             = [];
        $options            = ['query' => $controller->params()->fromQuery()];
        $reuseMatchedParams = false;

        if (null === $redirect) {
            $routeMatch = $controller->getEvent()->getRouteMatch();

            $redirect = $routeMatch->getMatchedRouteName();
            // null indicates to redirect to self.
            $reuseMatchedParams = true;
        }

        $redirector = $this->marshalRedirectPlugin($controller, $redirectToUrl);

        // Redirect to route-based URL
        if (false === $redirectToUrl) {
            $response = $redirector->toRoute($redirect, $params, $options, $reuseMatchedParams);
            $response->setStatusCode(303);
            return $response;
        }

        // Redirect to specific URL
        $response = $redirector->toUrl($redirect);
        $response->setStatusCode(303);

        return $response;
    }

    /**
     * Marshal a redirect plugin instance.
     *
     * @param DispatchableInterface $controller
     * @param bool $redirectToUrl
     * @return Redirect
     * @throws RuntimeException if route-based redirection is requested, but no
     *     plugin manager is composed in the controller.
     */
    private function marshalRedirectPlugin(DispatchableInterface $controller, $redirectToUrl)
    {
        if (method_exists($controller, 'getPluginManager')) {
            // get the redirect plugin from the plugin manager
            return $controller->getPluginManager()->get('Redirect');
        }

        // If the user wants to redirect to a route, the redirector has to come
        // from the plugin manager; otherwise no router will be injected
        if (false === $redirectToUrl) {
            throw new RuntimeException('Could not redirect to a route without a router');
        }

        return new Redirect();
    }
}

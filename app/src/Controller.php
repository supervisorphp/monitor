<?php

/*
 * This file is part of the Supervisor Monitor project.
 *
 * (c) Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Supervisor\Monitor;

use Proton\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Main controller
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Controller
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Index action displaying the main site
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function index(Request $request, Response $response, array $args)
    {
        $layout = $request->query->get('layout', 'grid');
        in_array($layout, ['grid', 'list']) or $layout = 'grid';

        $template = $this->app['twig']->loadTemplate(sprintf('layout/%s.twig', $layout));
        $response->setContent($template->render(['instances' => $this->app['manager']->getAll()]));

        return $response;
    }

    /**
     * Starts all processes in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function startAll(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->startAllProcesses($this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }

    /**
     * Restarts all processes in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function restartAll(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->stopAllProcesses($this->app->getConfig('waitForSupervisor', false));
        $instance->startAllProcesses($this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }

    /**
     * Stops all processes in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function stopAll(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->stopAllProcesses($this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }

    /**
     * Starts a process in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function startProcess(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->startProcess($args['process'], $this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }

    /**
     * Restarts a process in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function restartProcess(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->stopProcess($args['process'], $this->app->getConfig('waitForSupervisor', false));
        $instance->startProcess($args['process'], $this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }

    /**
     * Stops a process in an instance
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function stopProcess(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->stopProcess($args['process'], $this->app->getConfig('waitForSupervisor', false));

        return new RedirectResponse($this->app->getConfig('dir','/'));
    }
}

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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Main controller.
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
     * Index action displaying the main site.
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
        $twigVariables = [
            'instances'     => $this->app['manager']->getAll(),
            'baseUrl'       => $this->app->getConfig('dir', '/'),
            'globalActions' => $this->app->getConfig('globalActions', false),
            ];
        $response->setContent($template->render($twigVariables));

        return $response;
    }

    /**
     * Starts all processes.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function startAllServers(Request $request, Response $response, array $args)
    {
        $instances = $this->app['manager']->getAll();

        foreach ($instances as $instance) {
            $instance->startAllProcesses($this->app->getConfig('waitForSupervisor', false));
        }

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Restarts all processes.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function restartAllServers(Request $request, Response $response, array $args)
    {
        $instances = $this->app['manager']->getAll();

        foreach ($instances as $instance) {
            $instance->stopAllProcesses($this->app->getConfig('waitForSupervisor', false));
            $instance->startAllProcesses($this->app->getConfig('waitForSupervisor', false));
        }

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Stops all processes.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function stopAllServers(Request $request, Response $response, array $args)
    {
        $instances = $this->app['manager']->getAll();

        foreach ($instances as $instance) {
            $instance->stopAllProcesses($this->app->getConfig('waitForSupervisor', false));
        }

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Clears all processes log files.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function clearAllProcessLogs(Request $request, Response $response, array $args)
    {
        $instances = $this->app['manager']->getAll();

        foreach ($instances as $instance) {
            $instance->clearAllProcessLogs();
        }

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Clears all processes log files.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function clearAllProcessLogsInstance(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->clearAllProcessLogs();

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Clears process log files.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function clearProcessLogs(Request $request, Response $response, array $args)
    {
        $instance = $this->app['manager']->get($args['instance']);

        $instance->clearProcessLogs($args['process']);

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Starts all processes in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Restarts all processes in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Stops all processes in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Starts a process in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Restarts a process in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Stops a process in an instance.
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

        return new RedirectResponse($this->app->getConfig('dir', '/'));
    }

    /**
     * Tail -f stdout a process in an instance.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function tailStdout(Request $request, Response $response, array $args)
    {
        try {
            $instance = $this->app['manager']->get($args['instance']);
            $this->getLog($response, $instance, $args['process']);
        } catch (Exception $ex) {
            $response->setContent($ex->getMessage());
        }
    }

    /**
     * Tail -f stderr a process in an instance.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function tailStderr(Request $request, Response $response, array $args)
    {
        try {
            $instance = $this->app['manager']->get($args['instance']);
            $this->getLog($response, $instance, $args['process'], 'Stderr');
        } catch (Exception $ex) {
            $response->setContent($ex->getMessage());
        }
    }

    /**
     * Tail -f for stdout or stderr for a process.
     *
     * @param Response   $response
     * @param Supervisor $instance
     * @param string     $process
     * @param string     $type
     *
     * @throws Exception
     */
    private function getLog($response, $instance, $process, $type = 'Stdout')
    {
        $template = $this->app['twig']->loadTemplate(sprintf('layout/tail.twig'));
        $response->setContent($template->render(['doNotClose' => true, 'baseUrl' => $this->app->getConfig('dir', '/')]))->sendContent();

        //flush template to browser.  Tmeplate is setup to use bigpipe so the connection doesn't close.
        ob_flush();
        flush();

        if ($type != 'Stdout' && $type != 'Stderr') {
            throw new Exception('Log type not supported');
        }

        $offset = -4096;
        while (true) {
            // Check if client is still connected.
            if (connection_aborted()) {
                return;
            }

            usleep(300000); //0.3 s
            //Get log until no more from last offset.
            $bufferOverFlow = true;

            while ($bufferOverFlow) {
                list($string, $newoffset, $bufferOverFlow) =
                        $instance->{'tailProcess'.$type.'Log'}($process, $offset, 4096);

                if (strlen($string) > 0) {
                    if ($offset > 0) {
                        $template = $this->app['twig']->loadTemplate(sprintf('layout/tailContent.twig'));
                        // Set content with <br/> for new lines and only send in offset difference.
                        // This is becuase tailProcessStdoutLog returns 4096 no matter what the offset is.
                        $twigVariables = ['string' => nl2br(substr($string, $offset - $newoffset))];
                        $response->setContent($template->render($twigVariables))->sendContent();
                    } else {
                        $template = $this->app['twig']->loadTemplate(sprintf('layout/tailContent.twig'));
                        $twigVariables = ['string' => nl2br($string)];
                        $response->setContent($template->render($twigVariables))->sendContent();
                    }
                    // Flush to browser.
                    ob_flush();
                    flush();
                }
                $offset = $newoffset;
            }
        }
    }
}

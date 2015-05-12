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

        $instances = $this->app['manager']->getAll();

        $allProcesses = [];
        $hosts = [];
        $groups = [];
        $stopped = 0;

        //Building datas for layout
        foreach ($instances as $key => $value) {
            $processes = $value->getAllProcessInfo();
                foreach ($processes as $process) {
                    $process['host'] = $key;
                    $allProcesses[] = $process;
                    $hosts[] = $process['host'];
                    $groups[] = $process['group'];
                    if ($process['state'] == 'STOPPED') {
                        $stopped++;
                    }
                }
        }
        
        $hosts = array_unique($hosts);
        $groups = array_unique($groups);
        sort($hosts);
        sort($groups);

        switch($layout) {
            case 'grid':
                break;
            case 'list':
                break;
            case 'dashboard':
                break;
            case 'global':
                $filter = $request->query->get('filter', 'none');
                $filter_value = $request->query->get('filter_value', 'none');
                $sort = $request->query->get('sort', 'none');

                switch($filter) {
                    case 'group':
                        $allProcesses = $this->filter_processes($filter, $filter_value, $allProcesses);
                        break;
                    case 'host':
                        $allProcesses = $this->filter_processes($filter, $filter_value, $allProcesses);
                        break;
                    case 'state':
                        $allProcesses = $this->filter_processes($filter, $filter_value, $allProcesses);
                        break;
                }

                switch($sort) {
                    case 'group':
                        usort($allProcesses, array($this,'group_sort'));
                        break;
                    case 'host':
                        usort($allProcesses, array($this,'host_sort'));
                        break;
                    case 'state':
                        usort($allProcesses, array($this,'state_sort'));
                        break;
                    case 'start':
                        usort($allProcesses, array($this,'start_sort'));
                        break;
                }

                break;
            default:
                $layout = 'grid';
                break;
            }

        $template = $this->app['twig']->loadTemplate(sprintf('layout/%s.twig', $layout));

        $response->setContent($template->render([
                'instances' => $instances,
                'hosts' => $hosts,
                'allProcesses' => $allProcesses,
                'stopped' => $stopped,
                'groups' => $groups
                ]));

        return $response;
    }

    private function group_sort($a, $b) {
        return strcmp($a["group"], $b["group"]);
    }

    private function host_sort($a, $b) {
        return strcmp($a["host"], $b["host"]);
    }

    private function start_sort($a, $b) {
        return $a["start"] > $b["start"];
    }

    private function state_sort($a, $b) {
        return $a["state"] > $b["state"];
    }

    private function filter_processes($key, $value, $processes) {
        $tokeep = [];
        foreach ($processes as $process) {
            //TODO key exist ?
            if ($process[$key] == $value) {
                $tokeep[] = $process;
            }
        }
        return $tokeep;
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

        $instance->startAllProcesses(false);

        return new RedirectResponse('/');
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

        $instance->stopAllProcesses(false);
        $instance->startAllProcesses(false);

        return new RedirectResponse('/');
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

        $instance->stopAllProcesses(false);

        return new RedirectResponse('/');
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

        $instance->startProcess($args['process'], false);

        return new RedirectResponse('/');
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

        $instance->stopProcess($args['process'], false);
        $instance->startProcess($args['process'], false);

        return new RedirectResponse('/');
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

        $instance->stopProcess($args['process'], false);

        return new RedirectResponse('/');
    }
}

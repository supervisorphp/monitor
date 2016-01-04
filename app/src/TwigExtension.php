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

use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;

/**
 * Custom Twig extension.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * Maps a state to a color.
     *
     * @var array
     */
    protected $stateMap = [
        'STOPPED'  => 'danger',
        'STOPPING' => 'danger',
        'EXITED'   => 'danger',
        'FATAL'    => 'danger',
        'UNKNOWN'  => 'danger',
        'STARTING' => 'success',
        'RUNNING'  => 'success',
        'BACKOFF'  => 'warning',
    ];

    /**
     * Maps a state to a timestamp.
     *
     * @var array
     */
    protected $stateAgoMap = [
        'STOPPED'  => 'stop',
        'STOPPING' => 'stop',
        'EXITED'   => 'stop',
        'FATAL'    => 'stop',
        'UNKNOWN'  => 'stop',
        'STARTING' => 'start',
        'RUNNING'  => 'start',
    ];

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'supervisor_monitor';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('state_ago', [$this, 'stateAgo']),
            new \Twig_SimpleFilter('state', [$this, 'state']),
            new \Twig_SimpleFilter('pluralize', [$this, 'pluralize']),
        ];
    }

    /**
     * Display time in x ago format based on a state.
     *
     * @param array $process
     *
     * @return string
     */
    public function stateAgo(array $process)
    {
        if (isset($this->stateAgoMap[$process['statename']])) {
            $from = Carbon::createFromTimestamp($process[$this->stateAgoMap[$process['statename']]]);

            // Ensure the remote and the local system difference is counted
            $now = Carbon::createFromTimestamp($process['now']);
            $diff = $now->diffInSeconds();
            $from->subSeconds($diff);

            return $from->diffForHumans();
        }

        return 'No uptime info';
    }

    /**
     * Returns a color based on state name.
     *
     * @param string $state
     *
     * @return string
     */
    public function state($state)
    {
        if (isset($this->stateMap[$state])) {
            return $this->stateMap[$state];
        }

        return 'default';
    }

    /**
     * Pluralize a string based on a count.
     *
     * @param int    $count
     * @param string $string
     *
     * @return string
     */
    public function pluralize($count, $string)
    {
        if ($count > 1) {
            $string = Inflector::pluralize($string);
        }

        return sprintf('%d %s', $count, $string);
    }
}

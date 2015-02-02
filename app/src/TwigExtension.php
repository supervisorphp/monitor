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
 * Custom Twig extension
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TwigExtension extends \Twig_Extension
{
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
     * Display time in x ago format based on a state
     *
     * @param array $process
     *
     * @return string
     */
    public function stateAgo(array $process)
    {
        switch ($process['statename']) {
            case 'STOPPED':
            case 'STOPPING':
            case 'EXITED':
            case 'FATAL':
            case 'UNKNOWN':
                $from = Carbon::createFromTimestamp($process['stop']);
                break;

            case 'STARTING':
            case 'RUNNING':
            default:
                $from = Carbon::createFromTimestamp($process['start']);
                break;
        }

        return $from->diffForHumans();
    }

    /**
     * Returns a color based on state name
     *
     * @param string $state
     *
     * @return string
     */
    public function state($state)
    {
        switch ($state) {
            case 'STOPPED':
            case 'STOPPING':
            case 'EXITED':
            case 'FATAL':
            case 'UNKNOWN':
                return 'danger';
                break;

            case 'STARTING':
            case 'RUNNING':
                return 'success';
                break;

            case 'BACKOFF':
                return 'warning';
                break;
                break;

            default:
                return 'default';
                break;
        }
    }

    /**
     * Pluralize a string based on a count
     *
     * @param integer $count
     * @param string  $string
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

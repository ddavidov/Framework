<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Event\Submission;

class Submission extends \Zoolanders\Framework\Event\Event
{
    /**
     * @var \Category
     */
    protected $submission;

    /**
     * Beforesave constructor.
     * @param \Submission $submission
     */
    public function __construct(\Submission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * @return \Submission
     */
    public function getSubmission()
    {
        return $this->submission;
    }
}

<?php

namespace Zoolanders\Framework\Model\Database;

trait Access
{
    /**
     * @param $query
     * @param null $user
     * @return $this
     */
    protected function filterAccessible($user = null)
    {
        if (is_null($user)) {
            $user = \JFactory::getUser();
        }

        $db = $this->container->db;
        $field = isset($this->tablePrefix) ? $this->tablePrefix . '.access' : 'access';

        $groups = implode(',', array_unique($user->getAuthorisedViewLevels()));

        $this->where($db->qn($field) . ' IN ' . $db->q($groups));

        return $this;
    }
}
<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Utils;

/**
 * Class DataBind
 * Data binding utilities
 * @package Zoolanders\Framework\Utils
 */
trait DataBind
{
    /**
     * Bind data from the source to current object
     *
     * @param   mixed
     */
    public function bindFrom($src)
    {
        if(is_object($src)){
            $src = get_object_vars($src);
        }

        $src = (array)$src;

        if(!empty($src)){
            foreach($src as $key => $value){
                $this->{$key} = $value;
            }
        }
    }
}

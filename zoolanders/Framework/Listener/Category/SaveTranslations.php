<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Listener\Category;

use Zoolanders\Framework\Container\Container;
use Zoolanders\Framework\Event\Category;
use Zoolanders\Framework\Item\Indexer;
use Zoolanders\Framework\Listener\Listener;

class SaveTranslations extends Listener
{
    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * IndexSearchValues constructor.
     */
    function __construct(Container $c)
    {
        parent::__construct($c);
    }

    /**
     * @param Category\Saved $event
     */
    public function handle(Category\Saved $event)
    {
        $category = $event->getCategory();
        $currentLanguage = \JFactory::getLanguage()->getTag();

        $values = [];

        $languages = array_keys(\JFactory::getLanguage()->getKnownLanguages(JPATH_SITE));
        foreach ($languages as $language) {
            $values[$language] = [
                'category_id' => $category->id,
                'language' => $language,
                'name' => '',
                'alias' => '',
                'enabled' => 1
            ];
        }

        $values[$currentLanguage]['name'] = $category->name;
        $values[$currentLanguage]['alias'] = $category->alias;

        $params = $category->getParams();

        $this->setTranslationFromParams($values, $params, 'content.name_translation', 'name');
        $this->setTranslationFromParams($values, $params, 'content.alias_translation', 'alias');

        // Enabled for this language?
        $enabledLanguages = $params->get('content.language', array());

        // Empty list => all enabled
        if (!empty($enabledLanguages)) {
            foreach ($languages as $language) {
                if (!in_array($language, $enabledLanguages)) {
                    $values[$language]['enabled'] = 0;
                }
            }
        }

        $db = $this->container->db;

        foreach ($values as &$value) {
            $value = implode(",", $db->q($value));
        }

        // Clean
        /** @var \JDatabaseQuery $query */
        $query = $db->getQuery(true);
        $query->delete()->from('#__zoo_zl_category_languages')->where('category_id = ' . (int)$category->id);
        $db->setQuery($query);
        $db->execute();

        // Insert
        $query->insert('#__zoo_zl_category_languages')->columns(['category_id', 'language', 'name', 'alias', 'enabled'])->values($values);
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * @param $values
     * @param $params
     * @return array
     */
    protected function setTranslationFromParams(&$values, $params, $param, $key)
    {
        $translations = $params->get($param, array());
        foreach ($translations as $language => $translation) {
            $values[$language][$key] = $translation;
        }
    }
}
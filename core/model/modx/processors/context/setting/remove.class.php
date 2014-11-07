<?php
/**
 * Removes a context setting.
 *
 * @param string $key The key of the setting
 * @param string $context_key The key of the context
 *
 * @package modx
 * @subpackage processors.context.setting
 */

class modContextSettingRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'modContextSetting';
    public $languageTopics = array('setting');
    public $permission = 'settings';
    public $objectType = 'setting';
    public $primaryKeyField = 'key';

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function initialize() {
        $key = $this->getProperty('key');
        $context_key = $this->getProperty('context_key');
        if (!$key || !$context_key) {
            return $this->modx->lexicon($this->objectType . '_err_ns');
        }

        /** @var modContext $context */
        $context = $this->modx->getContext($context_key);
        if (!$context) {
            return $this->modx->lexicon($this->objectType . '_err_nf');
        }
        if (!$context->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }

        $this->object = $this->modx->getObject($this->classKey, array(
            'key' => $key,
            'context_key' => $context_key,
        ));

        if (!$this->object) {
            return $this->modx->lexicon($this->objectType . '_err_nf');
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function beforeRemove() {
        /* remove relative lexicon strings */
        $names = array(
            'setting_'.$this->object->get('key'),
            'setting_'.$this->object->get('key').'_desc',
        );

        foreach ($names as $name) {
            $entry = $this->modx->getObject('modLexiconEntry',array(
                'namespace' => $this->object->get('namespace'),
                'name' => $name,
            ));
            if ($entry) $entry->remove();
        }

        return parent::beforeRemove();
    }

    /**
     * {@inheritDoc}
     * @return boolean
     */
    public function afterRemove() {
        $this->modx->reloadConfig();
        return parent::afterRemove();
    }
}

return 'modContextSettingRemoveProcessor';
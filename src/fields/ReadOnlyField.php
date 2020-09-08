<?php
/**
 * Read Only plugin for Craft CMS 3.x
 *
 * Adds a read only text field to Craft CMS with click-to-copy functionality.
 *
 * @link      https://kyleandrews.dev/
 * @copyright Copyright (c) 2020 Kyle Andrews
 */

namespace codewithkyle\readonly\fields;

use codewithkyle\readonly\ReadOnly;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * ReadOnlyField Field
 *
 * Whenever someone creates a new field in Craft, they must specify what
 * type of field it is. The system comes with a handful of field types baked in,
 * and we’ve made it extremely easy for plugins to add new ones.
 *
 * https://craftcms.com/docs/plugins/field-types
 *
 * @author    Kyle Andrews
 * @package   ReadOnly
 * @since     1.0.0
 */
class ReadOnlyField extends Field
{
    // Public Properties
    // =========================================================================

    /** @var bool */
    public $hidden;

    /** @var bool */
    public $adminEdits;

    /** @var bool */
    public $visibleToAdmins;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('read-only', 'Read Only');
    }

    // Public Methods
    // =========================================================================

    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            [['hidden', 'adminEdits', 'visibleToAdmins'], 'boolean'],
            [['hidden', 'adminEdits', 'visibleToAdmins'], 'default', 'value' => false],
        ]);
        return $rules;
    }

    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'read-only/_components/fields/ReadOnlyField_settings',
            [
                'field' => $this,
            ]
        );
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'read-only/_components/fields/ReadOnlyField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'hidden' => $this->hidden,
                'adminEdits' => $this->adminEdits,
                'visibleToAdmins' => $this->visibleToAdmins,
            ]
        );
    }
}

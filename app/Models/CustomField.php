<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $fillable = [
        'label', 'key', 'type', 'group_name', 'class', 'options',
        'validation', 'file_constraints', 'ref_id', 'module'
    ];

    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'file_constraints' => 'array',
    ];

    /**
     * Get all values associated with this custom field.
     */
    public function values()
    {
        return $this->hasMany(CustomFieldValue::class, 'custom_field_id');
    }

    /**
     * Get value associated with this custom field.
     */
    public static function getValueByKey(string $key, int $moduleId, string $moduleName = null)
    {
        $field = self::where('key', $key)->first();

        if (!$field) {
            return null;
        }

        $query = CustomFieldValue::where('custom_field_id', $field->id)
            ->where('module_id', $moduleId);

        if (!is_null($moduleName)) {
            $query->where('module_type', $moduleName);
        }

        return $query->value('value'); // returns only the value field
    }

    /**
     * Get values for this custom field filtered by module type and module ID (optional).
     */
    public function getValuesForModule(string $moduleType, int $moduleId = null)
    {
        $query = $this->values()->where('module_type', $moduleType);

        if (!is_null($moduleId)) {
            $query->where('module_id', $moduleId);
        }

        return $query->get();
    }    

}




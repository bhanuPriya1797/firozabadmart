<?php

namespace App\Helpers;

use App\Models\CustomField;
use App\Models\CustomFieldValue;
use Illuminate\Support\Facades\Validator;

class CustomFieldHelper
{
    /**
     * Get custom fields for a specific module and reference ID
     */
    public static function getCustomFields($module, $refId = null, $groupName = null)
    {
        $query = CustomField::where('module', $module);
        
        // For CMS module, handle ref_id filtering properly
        if ($module === 'cms') {
            if ($refId === null) {
                // For new pages, get fields with ref_id = null (global fields)
                $query->whereNull('ref_id');
            } else {
                // For existing pages, get fields for that specific page
                $query->where('ref_id', $refId);
            }
        } else {
            // For other modules, use the original logic
            if ($refId) {
                $query->where('ref_id', $refId);
            }
        }
        
        if ($groupName) {
            $query->where('group_name', $groupName);
        }
        
        return $query->orderBy('group_name')->orderBy('id')->get();
    }

    /**
     * Get custom fields grouped by group_name
     */
    public static function getCustomFieldsGrouped($module, $refId = null)
    {
        $fields = self::getCustomFields($module, $refId);
        
        // Ensure we always return a collection
        if ($fields->isEmpty()) {
            return collect();
        }
        
        return $fields->groupBy('group_name');
    }

    /**
     * Generate validation rules for custom fields
     */
    public static function generateValidationRules($module, $refId = null)
    {
        // For CMS module, get fields based on ref_id (CMS page ID)
        // If ref_id is null (new page), get fields with ref_id = null (global fields)
        // If ref_id is provided (existing page), get fields for that specific page
        if ($module === 'cms') {
            $fields = self::getCustomFields($module, $refId);
        } else {
            $fields = self::getCustomFields($module, $refId);
        }
        $rules = [];
        $messages = [];

        foreach ($fields as $field) {
            $fieldRules = [];
            $fieldKey = $field->key;

            // Basic required validation
            if (isset($field->validation['required']) && $field->validation['required']) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific validation
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    if (isset($field->validation['min_value'])) {
                        $fieldRules[] = 'min:' . $field->validation['min_value'];
                    }
                    if (isset($field->validation['max_value'])) {
                        $fieldRules[] = 'max:' . $field->validation['max_value'];
                    }
                    break;
                case 'url':
                    $fieldRules[] = 'url';
                    break;
                case 'file':
                case 'image':
                    $fieldRules[] = 'file';
                    if (isset($field->validation['max_file_size'])) {
                        $fieldRules[] = 'max:' . ($field->validation['max_file_size'] * 1024); // Convert MB to KB
                    }
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'time':
                    $fieldRules[] = 'date_format:H:i';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                default:
                    // Text-based fields
                    if (isset($field->validation['min_length'])) {
                        $fieldRules[] = 'min:' . $field->validation['min_length'];
                    }
                    if (isset($field->validation['max_length'])) {
                        $fieldRules[] = 'max:' . $field->validation['max_length'];
                    }
                    break;
            }

            // Regex validation
            if (isset($field->validation['regex_pattern']) && !empty($field->validation['regex_pattern'])) {
                $fieldRules[] = 'regex:/' . $field->validation['regex_pattern'] . '/';
            }

            // Unique validation
            if (isset($field->validation['unique']) && $field->validation['unique']) {
                $fieldRules[] = 'unique:custom_field_values,value';
            }

            $rules[$fieldKey] = $fieldRules;

            // Custom validation messages
            $messages[$fieldKey . '.required'] = 'The ' . $field->label . ' field is required.';
            $messages[$fieldKey . '.email'] = 'The ' . $field->label . ' must be a valid email address.';
            $messages[$fieldKey . '.url'] = 'The ' . $field->label . ' must be a valid URL.';
            $messages[$fieldKey . '.numeric'] = 'The ' . $field->label . ' must be a number.';
            $messages[$fieldKey . '.file'] = 'The ' . $field->label . ' must be a file.';
            $messages[$fieldKey . '.date'] = 'The ' . $field->label . ' must be a valid date.';
            $messages[$fieldKey . '.date_format'] = 'The ' . $field->label . ' must be in the correct format.';
            $messages[$fieldKey . '.min'] = 'The ' . $field->label . ' must be at least :min.';
            $messages[$fieldKey . '.max'] = 'The ' . $field->label . ' may not be greater than :max.';
            $messages[$fieldKey . '.regex'] = 'The ' . $field->label . ' format is invalid.';
            $messages[$fieldKey . '.unique'] = 'The ' . $field->label . ' has already been taken.';
        }

        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }

    /**
     * Generate HTML form fields based on custom field configuration
     */
    public static function generateFormFields($module, $refId = null, $existingValues = [])
    {
        $fields = self::getCustomFields($module, $refId);
        $html = '';

        foreach ($fields as $field) {
            $value = $existingValues[$field->key] ?? $field->default_value ?? '';
            $required = isset($field->validation['required']) && $field->validation['required'] ? 'required' : '';
            $placeholder = $field->placeholder ?? '';
            $helpText = $field->help_text ?? '';

            $html .= '<div class="form-group mb-3">';
            $html .= '<label for="' . $field->key . '" class="form-label">' . $field->label;
            if ($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';

            switch ($field->type) {
                case 'text':
                    $html .= '<input type="text" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    break;

                case 'textarea':
                    $html .= '<textarea class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    $html .= htmlspecialchars($value);
                    $html .= '</textarea>';
                    break;

                case 'editor':
                    $html .= '<textarea class="form-control ckeditor ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '_editor" name="' . $field->key . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    $html .= htmlspecialchars($value);
                    $html .= '</textarea>';
                    break;

                case 'email':
                    $html .= '<input type="email" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    break;

                case 'number':
                    $min = $field->validation['min_value'] ?? '';
                    $max = $field->validation['max_value'] ?? '';
                    $html .= '<input type="number" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ';
                    if ($min !== '') $html .= 'min="' . $min . '" ';
                    if ($max !== '') $html .= 'max="' . $max . '" ';
                    $html .= $required . '>';
                    break;

                case 'url':
                    $html .= '<input type="url" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    break;

                case 'select':
                    $multiple = isset($field->is_multiple) && $field->is_multiple ? 'multiple' : '';
                    $html .= '<select class="form-select ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . ($multiple ? '[]' : '') . '" ' . $multiple . ' ' . $required . '>';
                    $html .= '<option value="">Select ' . $field->label . '</option>';
                    
                                                    if (isset($field->options) && is_array($field->options)) {
                                    foreach ($field->options as $optionData) {
                                        // Handle both old format (string) and new format (object)
                                        if (is_string($optionData)) {
                                            $optionValue = $optionData;
                                            $optionLabel = $optionData;
                                        } else {
                                            $optionValue = $optionData['value'] ?? '';
                                            $optionLabel = $optionData['label'] ?? $optionValue;
                                        }
                                        
                                        $selected = '';
                                        
                                        if ($multiple && is_array($value)) {
                                            $selected = in_array($optionValue, $value) ? 'selected' : '';
                                        } else {
                                            $selected = ($value == $optionValue) ? 'selected' : '';
                                        }
                                        
                                        $html .= '<option value="' . htmlspecialchars($optionValue) . '" ' . $selected . '>';
                                        $html .= htmlspecialchars($optionLabel);
                                        $html .= '</option>';
                                    }
                                }
                    $html .= '</select>';
                    break;

                case 'checkbox':
                                                if (isset($field->options) && is_array($field->options)) {
                                foreach ($field->options as $index => $optionData) {
                                    // Handle both old format (string) and new format (object)
                                    if (is_string($optionData)) {
                                        $optionValue = $optionData;
                                        $optionLabel = $optionData;
                                    } else {
                                        $optionValue = $optionData['value'] ?? '';
                                        $optionLabel = $optionData['label'] ?? $optionValue;
                                    }
                                    
                                    $checked = '';
                                    
                                    if (is_array($value)) {
                                        $checked = in_array($optionValue, $value) ? 'checked' : '';
                                    } else {
                                        $checked = ($value == $optionValue) ? 'checked' : '';
                                    }
                                    
                                    $html .= '<div class="form-check">';
                                    $html .= '<input type="checkbox" class="form-check-input" ';
                                    $html .= 'id="' . $field->key . '_' . $index . '" ';
                                    $html .= 'name="' . $field->key . '[]" ';
                                    $html .= 'value="' . htmlspecialchars($optionValue) . '" ' . $checked . '>';
                                    $html .= '<label class="form-check-label" for="' . $field->key . '_' . $index . '">';
                                    $html .= htmlspecialchars($optionLabel);
                                    $html .= '</label>';
                                    $html .= '</div>';
                                }
                            }
                    break;

                case 'radio':
                                                if (isset($field->options) && is_array($field->options)) {
                                foreach ($field->options as $index => $optionData) {
                                    // Handle both old format (string) and new format (object)
                                    if (is_string($optionData)) {
                                        $optionValue = $optionData;
                                        $optionLabel = $optionData;
                                    } else {
                                        $optionValue = $optionData['value'] ?? '';
                                        $optionLabel = $optionData['label'] ?? $optionValue;
                                    }
                                    
                                    $checked = ($value == $optionValue) ? 'checked' : '';
                                    
                                    $html .= '<div class="form-check">';
                                    $html .= '<input type="radio" class="form-check-input" ';
                                    $html .= 'id="' . $field->key . '_' . $index . '" ';
                                    $html .= 'name="' . $field->key . '" ';
                                    $html .= 'value="' . htmlspecialchars($optionValue) . '" ' . $checked . ' ' . $required . '>';
                                    $html .= '<label class="form-check-label" for="' . $field->key . '_' . $index . '">';
                                    $html .= htmlspecialchars($optionLabel);
                                    $html .= '</label>';
                                    $html .= '</div>';
                                }
                            }
                    break;

                case 'date':
                    $html .= '<input type="date" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ' . $required . '>';
                    break;

                case 'time':
                    $html .= '<input type="time" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ' . $required . '>';
                    break;

                case 'datetime':
                    $html .= '<input type="datetime-local" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ' . $required . '>';
                    break;

                case 'file':
                case 'image':
                    $accept = '';
                    if (isset($field->validation['allowed_types']) && is_array($field->validation['allowed_types'])) {
                        $accept = '.' . implode(',.', $field->validation['allowed_types']);
                    }
                    
                    $html .= '<div class="image-upload-container">';
                    $html .= '<input type="file" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'accept="' . $accept . '" data-preview="' . $field->key . '-preview" ' . $required . '>';
                    
                    // Show current file if exists
                    if (!empty($value)) {
                        $html .= '<div class="current-image-preview mt-2" id="' . $field->key . '-preview">';
                        $html .= '<div class="image-preview-wrapper">';
                        
                        if ($field->type === 'image') {
                            $html .= '<img src="' . asset('storage/' . $value) . '" alt="Current ' . $field->label . '" class="img-thumbnail" style="max-height: 150px;">';
                        } else {
                            $html .= '<div class="file-preview">';
                            $html .= '<i class="ti tabler-file me-2"></i>';
                            $html .= '<span>' . basename($value) . '</span>';
                            $html .= '</div>';
                        }
                        
                        $html .= '<div class="image-actions">';
                        if ($field->type === 'image') {
                            $html .= '<button type="button" class="btn btn-sm btn-outline-primary" onclick="previewImage(\'' . asset('storage/' . $value) . '\')">';
                            $html .= '<i class="ti tabler-eye"></i> Preview</button>';
                        }
                        $html .= '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomFieldFile(\'' . $field->key . '\', \'' . $module . '\', \'' . $moduleId . '\')">';
                        $html .= '<i class="ti tabler-trash"></i> Delete</button>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</div>';
                    } else {
                        $html .= '<div class="current-image-preview mt-2" id="' . $field->key . '-preview" style="display: none;"></div>';
                    }
                    
                    $html .= '</div>';
                    break;

                default:
                    $html .= '<input type="text" class="form-control ' . ($field->class ?? '') . '" ';
                    $html .= 'id="' . $field->key . '" name="' . $field->key . '" ';
                    $html .= 'value="' . htmlspecialchars($value) . '" ';
                    $html .= 'placeholder="' . htmlspecialchars($placeholder) . '" ' . $required . '>';
                    break;
            }

            if ($helpText) {
                $html .= '<div class="form-text">' . htmlspecialchars($helpText) . '</div>';
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Save custom field values
     */
    public static function saveCustomFieldValues($module, $moduleId, $data)
    {
        // For CMS module, get fields for the specific page (ref_id = moduleId)
        if ($module === 'cms') {
            $fields = self::getCustomFields($module, $moduleId);
        } else {
            $fields = self::getCustomFields($module, $moduleId);
        }
        
        foreach ($fields as $field) {
            $value = $data[$field->key] ?? null;
            
            // Handle array values (checkboxes, multiple select)
            if (is_array($value)) {
                $value = json_encode($value);
            }
            // For checkboxes, ensure they are always saved as arrays (even if empty)
            elseif ($field->type === 'checkbox' && !isset($data[$field->key])) {
                $value = json_encode([]);
            }
            
            // Handle file uploads
            if ($field->type === 'file' || $field->type === 'image') {
                if (isset($data[$field->key]) && $data[$field->key]->isValid()) {
                    $file = $data[$field->key];
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/custom_fields', $fileName);
                    $value = 'custom_fields/' . $fileName;
                }
            }
            
            CustomFieldValue::updateOrCreate(
                [
                    'custom_field_id' => $field->id,
                    'module_type' => $module,
                    'module_id' => $moduleId,
                ],
                [
                    'value' => $value
                ]
            );
        }
    }

    /**
     * Get custom field values for a module
     */
    public static function getCustomFieldValues($module, $moduleId)
    {
        // For CMS module, get fields for the specific page (ref_id = moduleId)
        if ($module === 'cms') {
            $fields = self::getCustomFields($module, $moduleId);
        } else {
            $fields = self::getCustomFields($module, $moduleId);
        }
        
        $values = [];
        
        foreach ($fields as $field) {
            $fieldValue = CustomFieldValue::where('custom_field_id', $field->id)
                ->where('module_type', $module)
                ->where('module_id', $moduleId)
                ->first();
                
            $value = $fieldValue ? $fieldValue->value : $field->default_value;
            
            // Handle JSON values (arrays)
            if (is_string($value)) {
                // For checkboxes, always decode JSON as they are always arrays
                if ($field->type === 'checkbox') {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $value = $decoded;
                    }
                }
                // For select fields, only decode if is_multiple is true
                elseif ($field->type === 'select' && isset($field->is_multiple) && $field->is_multiple) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $value = $decoded;
                    }
                }
                // Decode HTML entities for text-based fields that might contain HTML
                elseif (in_array($field->type, ['text', 'textarea', 'editor'])) {
                    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
            
            $values[$field->key] = $value;
        }
        
        return $values;
    }
}

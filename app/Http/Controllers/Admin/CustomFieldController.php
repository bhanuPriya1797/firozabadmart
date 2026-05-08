<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomField;
use Illuminate\Support\Str;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;


class CustomFieldController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = CustomField::select('id', 'label', 'key', 'type', 'module', 'ref_id', 'group_name', 'created_at')->orderByDesc('id');

                $user = Auth::user();
                return DataTables::of($query)
                    ->addColumn('type_badge', function ($row) {
                        $badgeClass = match($row->type) {
                            'text' => 'bg-label-primary',
                            'textarea' => 'bg-label-info',
                            'editor' => 'bg-label-warning',
                            'file' => 'bg-label-success',
                            'select' => 'bg-label-secondary',
                            'checkbox' => 'bg-label-danger',
                            'radio' => 'bg-label-dark',
                            default => 'bg-label-primary'
                        };
                        return '<span class="badge ' . $badgeClass . ' me-1">' . ucfirst($row->type) . '</span>';
                    })
                    ->addColumn('module_info', function ($row) {
                        return ucfirst($row->module) . ($row->ref_id ? ' (ID: ' . $row->ref_id . ')' : '');
                    })
                    ->addColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y, h:i A') : '';
                    })
                    ->addColumn('action', function ($row) {
                        $routeName = CustomHelper::getAdminRouteName();
                        $editUrl = route($routeName . '.custom_fields.edit', ['id' => $row->id]);
                        $deleteUrl = route($routeName . '.custom_fields.destroy', ['id' => $row->id]);

                        $actions = '';
                        $hasAnyAction = false;

                        // Check if user has any permissions to show dropdown
                        if (Gate::allows('custom_fields.edit') || Gate::allows('custom_fields.delete')) {
                            
                            $actions = '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="icon-base ti tabler-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">';

                            // Edit permission
                            if (Gate::allows('custom_fields.edit')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-edit-field"
                                    href="javascript:void(0);"
                                    data-id="' . $row->id . '"
                                >
                                    <i class="icon-base ti tabler-edit me-1"></i> Edit
                                </a>';
                                $hasAnyAction = true;
                            }

                            // Delete permission
                            if (Gate::allows('custom_fields.delete')) {
                                $actions .= '<a 
                                    class="dropdown-item waves-effect btn-delete-field"
                                    href="javascript:void(0);"
                                    data-url="' . $deleteUrl . '"
                                >
                                    <i class="icon-base ti tabler-trash me-1"></i> Delete
                                </a>';
                                $hasAnyAction = true;
                            }

                            $actions .= '</div></div>';
                        }

                        // If no actions available, return empty string
                        return $hasAnyAction ? $actions : '';
                    })
                    ->rawColumns(['action', 'type_badge'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('CustomFieldController@index: ' . $e->getMessage());
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
        }

        $data['page_title'] = 'Custom Fields';
        return view('admin.custom_fields.index', $data);
    }

    public function store(Request $request)
    {
        try {
            // Step 1: Initial validation
            $validated = $request->validate([
                'label' => 'required|string|max:255',
                'key' => 'nullable|string|max:255|regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
                'group_name' => 'nullable|string|max:255',
                'type' => 'required|in:text,textarea,email,phone,number,url,editor,file,image,select,checkbox,radio,date,time,datetime',
                'class' => 'nullable|string|max:255',
                'validation' => 'nullable',
                'module' => 'required|string|max:255',
                'default_value' => 'nullable|string|max:500',
                'help_text' => 'nullable|string|max:500',
                'placeholder' => 'nullable|string|max:255',
                'options' => 'required_if:type,select,checkbox,radio',
                'option_labels' => 'required_if:type,select,checkbox,radio',
                'is_multiple' => 'nullable|boolean',
                'max_selections' => 'nullable|integer|min:1',
                'is_searchable' => 'nullable|boolean',
            ]);

            // Step 2: Auto-generate and ensure unique key
            $baseKey = $validated['key'] ?? Str::slug($validated['label'], '_');
            $baseKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $baseKey);
            if (!preg_match('/^[a-zA-Z]/', $baseKey)) {
                $baseKey = 'field_' . $baseKey;
            }

            $uniqueKey = $baseKey;
            $suffix = 1;
            while (CustomField::where('key', $uniqueKey)->exists()) {
                $uniqueKey = $baseKey . '_' . $suffix;
                $suffix++;
            }
            $validated['key'] = $uniqueKey;

            // Step 3: Add ref_id if available
            if (!empty($request->module_ref_id)) {
                $validated['ref_id'] = $request->module_ref_id;
            }

            // Step 4: Handle options
            if ($request->filled('options')) {
                $optionsInput = $request->options;
                $optionLabelsInput = $request->input('option_labels', []);
                
                if (is_string($optionsInput)) {
                    $options = json_decode($optionsInput, true);
                } elseif (is_array($optionsInput)) {
                    $options = $optionsInput;
                } else {
                    $options = [];
                }
                
                if (is_string($optionLabelsInput)) {
                    $optionLabels = json_decode($optionLabelsInput, true);
                } elseif (is_array($optionLabelsInput)) {
                    $optionLabels = $optionLabelsInput;
                } else {
                    $optionLabels = [];
                }
                
                // Filter out empty options and create structured format
                $structuredOptions = [];
                foreach ($options as $index => $option) {
                    if (!empty(trim($option))) {
                        $label = isset($optionLabels[$index]) ? trim($optionLabels[$index]) : trim($option);
                        $structuredOptions[] = [
                            'value' => trim($option),
                            'label' => $label
                        ];
                    }
                }
                
                if (!empty($structuredOptions)) {
                    $validated['options'] = $structuredOptions;
                }
            }

            // Step 5: Handle validation
            if ($request->filled('validation')) {
                $validationInput = $request->validation;
                if (is_string($validationInput)) {
                    $validation = json_decode($validationInput, true);
                    $validated['validation'] = $validation;
                } elseif (is_array($validationInput)) {
                    $validated['validation'] = $validationInput;
                }
            }

            // Step 6: Handle boolean fields
            $validated['is_multiple'] = $request->has('is_multiple');
            $validated['is_searchable'] = $request->has('is_searchable');

            // Step 7: Create the record
            $customField = CustomField::create($validated);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'custom_fields',
                $customField->id,
                'Create Custom Field',
                'Created custom field: ' . $customField->label,
                json_encode($validated)
            );

            return response()->json([
                'status' => true,
                'message' => 'Custom field created successfully!',
                'data' => $customField,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id)
    {
        try {
            $customField = CustomField::findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $customField,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Custom field not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customField = CustomField::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'label' => 'required|string|max:255',
                'key' => 'required|string|max:255|unique:custom_fields,key,' . $customField->id,
                'type' => 'required|string|in:text,textarea,email,phone,number,url,editor,file,image,select,checkbox,radio,date,time,datetime',
                'group_name' => 'nullable|string|max:255',
                'class' => 'nullable|string|max:255',
                'validation' => 'nullable',
                'default_value' => 'nullable|string|max:500',
                'help_text' => 'nullable|string|max:500',
                'placeholder' => 'nullable|string|max:255',
                'options' => 'required_if:type,select,checkbox,radio',
                'option_labels' => 'required_if:type,select,checkbox,radio',
                'is_multiple' => 'nullable|boolean',
                'max_selections' => 'nullable|integer|min:1',
                'is_searchable' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();

            // Handle validation
            if (isset($data['validation'])) {
                if (is_string($data['validation'])) {
                    $data['validation'] = json_decode($data['validation'], true);
                } elseif (is_array($data['validation'])) {
                    // Already an array, no need to decode
                    $data['validation'] = $data['validation'];
                }
            }

            // Handle options
            if ($request->filled('options')) {
                $optionsInput = $request->options;
                $optionLabelsInput = $request->input('option_labels', []);
                
                if (is_string($optionsInput)) {
                    $options = json_decode($optionsInput, true);
                } elseif (is_array($optionsInput)) {
                    $options = $optionsInput;
                } else {
                    $options = [];
                }
                
                if (is_string($optionLabelsInput)) {
                    $optionLabels = json_decode($optionLabelsInput, true);
                } elseif (is_array($optionLabelsInput)) {
                    $optionLabels = $optionLabelsInput;
                } else {
                    $optionLabels = [];
                }
                
                // Filter out empty options and create structured format
                $structuredOptions = [];
                foreach ($options as $index => $option) {
                    if (!empty(trim($option))) {
                        $label = isset($optionLabels[$index]) ? trim($optionLabels[$index]) : trim($option);
                        $structuredOptions[] = [
                            'value' => trim($option),
                            'label' => $label
                        ];
                    }
                }
                
                if (!empty($structuredOptions)) {
                    $data['options'] = $structuredOptions;
                }
            }

            // Handle boolean fields
            $data['is_multiple'] = $request->has('is_multiple');
            $data['is_searchable'] = $request->has('is_searchable');

            // Handle ref_id
            if (!empty($request->module_ref_id)) {
                $data['ref_id'] = $request->module_ref_id;
            }

            $customField->update($data);

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'custom_fields',
                $customField->id,
                'Update Custom Field',
                'Updated custom field: ' . $customField->label,
                json_encode($data)
            );

            return response()->json([
                'status' => true,
                'message' => 'Custom field updated successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customField = CustomField::findOrFail($id);
            $customField->delete();

            // Log activity
            CustomHelper::recordActionLog(
                url()->current(),
                'custom_fields',
                $id,
                'Delete Custom Field',
                'Deleted custom field: ' . $customField->label,
                'Deleted custom field: ' . $customField->label . ' (ID: ' . $id . ')'
            );

            return response()->json([
                'status' => true,
                'message' => 'Custom field deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting custom field.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        try {
            $fieldKey = $request->input('field_key');
            $module = $request->input('module');
            $moduleId = $request->input('module_id');
            
            if (!$fieldKey || !$module || !$moduleId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Missing required parameters.',
                ], 400);
            }
            
            // Find the custom field
            $customField = CustomField::where('key', $fieldKey)
                ->where('module', $module)
                ->first();
            
            if (!$customField) {
                return response()->json([
                    'status' => false,
                    'message' => 'Custom field not found.',
                ], 404);
            }
            
            // Find the custom field value
            $fieldValue = \App\Models\CustomFieldValue::where('custom_field_id', $customField->id)
                ->where('module_type', $module)
                ->where('module_id', $moduleId)
                ->first();
            
            if ($fieldValue && $fieldValue->value) {
                // Delete the file from storage
                /*
                // STOPPING physical deletion
                if (\Storage::disk('public')->exists($fieldValue->value)) {
                    \Storage::disk('public')->delete($fieldValue->value);
                }
                */
                
                // Clear the value in database
                $fieldValue->update(['value' => null]);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'File deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error deleting file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private static function parseOptions($input)
    {
        if (empty($input)) return [];
        return array_map('trim', explode(',', $input));
    }

    private static function parseJson($input)
    {
        if (empty($input)) return [];
        $decoded = json_decode($input, true);
        return is_array($decoded) ? $decoded : [];
    }
}

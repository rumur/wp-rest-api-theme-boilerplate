<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => __('The :attribute must be accepted.', TEXT_DOMAIN),
    'active_url'           => __('The :attribute is not a valid URL.', TEXT_DOMAIN),
    'after'                => __('The :attribute must be a date after :date.', TEXT_DOMAIN),
    'after_or_equal'       => __('The :attribute must be a date after or equal to :date.', TEXT_DOMAIN),
    'alpha'                => __('The :attribute may only contain letters.', TEXT_DOMAIN),
    'alpha_dash'           => __('The :attribute may only contain letters, numbers, and dashes.', TEXT_DOMAIN),
    'alpha_num'            => __('The :attribute may only contain letters and numbers.', TEXT_DOMAIN),
    'array'                => __('The :attribute must be an array.', TEXT_DOMAIN),
    'before'               => __('The :attribute must be a date before :date.', TEXT_DOMAIN),
    'before_or_equal'      => __('The :attribute must be a date before or equal to :date.', TEXT_DOMAIN),
    'between'              => [
        'numeric' => __('The :attribute must be between :min and :max.', TEXT_DOMAIN),
        'file'    => __('The :attribute must be between :min and :max kilobytes.', TEXT_DOMAIN),
        'string'  => __('The :attribute must be between :min and :max characters.', TEXT_DOMAIN),
        'array'   => __('The :attribute must have between :min and :max items.', TEXT_DOMAIN),
    ],
    'boolean'              => __('The :attribute field must be true or false.', TEXT_DOMAIN),
    'confirmed'            => __('The :attribute confirmation does not match.', TEXT_DOMAIN),
    'date'                 => __('The :attribute is not a valid date.', TEXT_DOMAIN),
    'date_format'          => __('The :attribute does not match the format :format.', TEXT_DOMAIN),
    'different'            => __('The :attribute and :other must be different.', TEXT_DOMAIN),
    'digits'               => __('The :attribute must be :digits digits.', TEXT_DOMAIN),
    'digits_between'       => __('The :attribute must be between :min and :max digits.', TEXT_DOMAIN),
    'dimensions'           => __('The :attribute has invalid image dimensions.', TEXT_DOMAIN),
    'distinct'             => __('The :attribute field has a duplicate value.', TEXT_DOMAIN),
    'email'                => __('The :attribute must be a valid email address.', TEXT_DOMAIN),
    'exists'               => __('The selected :attribute is invalid.', TEXT_DOMAIN),
    'file'                 => __('The :attribute must be a file.', TEXT_DOMAIN),
    'filled'               => __('The :attribute field is required.', TEXT_DOMAIN),
    'image'                => __('The :attribute must be an image.', TEXT_DOMAIN),
    'in'                   => __('The selected :attribute is invalid.', TEXT_DOMAIN),
    'in_array'             => __('The :attribute field does not exist in :other.', TEXT_DOMAIN),
    'integer'              => __('The :attribute must be an integer.', TEXT_DOMAIN),
    'ip'                   => __('The :attribute must be a valid IP address.', TEXT_DOMAIN),
    'json'                 => __('The :attribute must be a valid JSON string.', TEXT_DOMAIN),
    'max'                  => [
        'numeric' => __('The :attribute may not be greater than :max.', TEXT_DOMAIN),
        'file'    => __('The :attribute may not be greater than :max kilobytes.', TEXT_DOMAIN),
        'string'  => __('The :attribute may not be greater than :max characters.', TEXT_DOMAIN),
        'array'   => __('The :attribute may not have more than :max items.', TEXT_DOMAIN),
    ],
    'mimes'                => __('The :attribute must be a file of type: :values.', TEXT_DOMAIN),
    'mimetypes'            => __('The :attribute must be a file of type: :values.', TEXT_DOMAIN),
    'min'                  => [
        'numeric' => __('The :attribute must be at least :min.', TEXT_DOMAIN),
        'file'    => __('The :attribute must be at least :min kilobytes.', TEXT_DOMAIN),
        'string'  => __('The :attribute must be at least :min characters.', TEXT_DOMAIN),
        'array'   => __('The :attribute must have at least :min items.', TEXT_DOMAIN),
    ],
    'not_in'               => __('The selected :attribute is invalid.', TEXT_DOMAIN),
    'numeric'              => __('The :attribute must be a number.', TEXT_DOMAIN),
    'present'              => __('The :attribute field must be present.', TEXT_DOMAIN),
    'regex'                => __('The :attribute format is invalid.', TEXT_DOMAIN),
    'required'             => __('The :attribute field is required.', TEXT_DOMAIN),
    'required_if'          => __('The :attribute field is required when :other is :value.', TEXT_DOMAIN),
    'required_unless'      => __('The :attribute field is required unless :other is in :values.', TEXT_DOMAIN),
    'required_with'        => __('The :attribute field is required when :values is present.', TEXT_DOMAIN),
    'required_with_all'    => __('The :attribute field is required when :values is present.', TEXT_DOMAIN),
    'required_without'     => __('The :attribute field is required when :values is not present.', TEXT_DOMAIN),
    'required_without_all' => __('The :attribute field is required when none of :values are present.', TEXT_DOMAIN),
    'same'                 => __('The :attribute and :other must match.', TEXT_DOMAIN),
    'size'                 => [
        'numeric' => __('The :attribute must be :size.', TEXT_DOMAIN),
        'file'    => __('The :attribute must be :size kilobytes.', TEXT_DOMAIN),
        'string'  => __('The :attribute must be :size characters.', TEXT_DOMAIN),
        'array'   => __('The :attribute must contain :size items.', TEXT_DOMAIN),
    ],
    'string'               => __('The :attribute must be a string.', TEXT_DOMAIN),
    'timezone'             => __('The :attribute must be a valid zone.', TEXT_DOMAIN),
    'unique'               => __('The :attribute has already been taken.', TEXT_DOMAIN),
    'uploaded'             => __('The :attribute failed to upload.', TEXT_DOMAIN),
    'url'                  => __('The :attribute format is invalid.', TEXT_DOMAIN),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

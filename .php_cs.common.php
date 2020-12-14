<?php

// Share common rules between non-test and test files
return [
    // PSR12 from https://github.com/FriendsOfPHP/PHP-CS-Fixer/pull/4943
    '@PSR2' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => [
        // Not-yet-implemented
        // 'allow_single_line_anonymous_class_with_empty_body' => true,
    ],
    'compact_nullable_typehint' => true,
    'declare_equal_normalize' => true,
    'lowercase_cast' => true,
    'lowercase_static_reference' => true,
    'new_with_braces' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_leading_import_slash' => true,
    'no_whitespace_in_blank_line' => true,
    'binary_operator_spaces' => true,
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public',
            'constant_protected',
            'constant_private',
            'property_public',
            'property_protected',
            'property_private',
        ],
    ],
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'length',
    ],
    'return_type_declaration' => true,
    'short_scalar_cast' => true,
    'single_blank_line_before_namespace' => true,
    'single_trait_insert_per_statement' => false,
    'ternary_operator_spaces' => true,
    'visibility_required' => [
        'elements' => [
            'const',
            'method',
            'property',
        ],
    ],

    // Further quality-of-life improvements
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'concat_space' => [
        'spacing' => 'one',
    ],
    'declare_strict_types' => false,
    'fully_qualified_strict_types' => true,
    'native_function_invocation' => [
        'include' => [],
        'strict' => true,
    ],
    'no_unused_imports' => true,
    'single_quote' => true,
    'space_after_semicolon' => true,
    'trailing_comma_in_multiline_array' => true,
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'whitespace_after_comma_in_array' => true,
    'class_attributes_separation' => [
        'elements' => [
            'method',
        ],
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
];
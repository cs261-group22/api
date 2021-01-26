<?php

// https://mlocati.github.io/php-cs-fixer-configurator
// https://stackoverflow.com/questions/29839194/php-cs-fixer-need-more-information-on-using-fix-level-option

// Fixers ruleset
$fixers = [
    '@PSR2' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_return' => true,
    'braces' => true,
    'cast_spaces' => true,
    'class_attributes_separation' => ['elements' => [
        'method'
    ]],
    'concat_space' => true,
    'declare_equal_normalize' => true,
    'function_typehint_space' => true,
    'hash_to_slash_comment' => true,
    'heredoc_to_nowdoc' => true,
    'include' => true,
    'increment_style' => ['style' => 'post'],
    'lowercase_cast' => true,
    'magic_constant_casing' => true,
    'method_argument_space' => true,
    'multiline_whitespace_before_semicolons' => true,
    'native_function_casing' => true,
    'no_alias_functions' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_extra_blank_lines' => ['tokens' => [
        'extra', 'throw', 'use', 'use_trait'
    ]],
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_around_offset' => ['positions' => [
        'inside'
    ]],
    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_unneeded_control_parentheses' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unused_imports' => true,
    'no_useless_return' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'normalize_index_brace' => true,
    'not_operator_with_successor_space' => true,
    'object_operator_without_whitespace' => true,
    'ordered_imports' => true,
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_indent' => true,
    'phpdoc_inline_tag' => true,
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => ['replacements' => [
        'type' => 'var'
    ]],
    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_to_comment' => true,
    'phpdoc_trim' => true,
    'phpdoc_types' => true,
    'phpdoc_var_without_name' => true,
    'psr4' => true,
    'self_accessor' => true,
    'short_scalar_cast' => true,
    'simplified_null_return' => true,
    'single_blank_line_before_namespace' => true,
    'single_quote' => true,
    'space_after_semicolon' => true,
    'standardize_not_equals' => true,
    'switch_case_semicolon_to_colon' => true,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline_array' => true,
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'whitespace_after_comma_in_array' => true,
];

// Directories to not scan
$excludeDirs = [
    'bootstrap/',
    'config/',
    'docs/',
    'public/',
    'resources/',
    'storage/',
    'vendor/',
    '.circleci/',
    '.docker/',
];

// Files to not scan
$excludeFiles = [
    'public/index.php',
    'server.php',
];

// Create a new Symfony Finder instance
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude($excludeDirs)
    ->ignoreDotFiles(true)->ignoreVCS(true)
    ->filter(function (\SplFileInfo $file) use ($excludeFiles) {
        return ! in_array($file->getRelativePathName(), $excludeFiles);
    })
;

// Create and return a PHP CS Fixer instance
return PhpCsFixer\Config::create()
    ->setRules($fixers)->setFinder($finder)
    ->setUsingCache(false)->setRiskyAllowed(true);

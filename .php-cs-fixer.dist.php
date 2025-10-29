<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/admin',
        __DIR__ . '/gestionnaire',
        __DIR__ . '/user',
        __DIR__ . '/includes',
        __DIR__ . '/classes',
    ])
    ->name('*.php')
    ->exclude('vendor')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        // ---- Standards généraux ----
        '@PSR12' => true, // norme principale PHP-FIG
        '@PhpCsFixer' => true,
        
        // ---- Espaces et indentations ----
        'indentation_type' => true,
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'single_blank_line_at_eof' => true,

        // ---- Accolades ----
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same',
            'position_after_control_structures' => 'same',
        ],

        // ---- Espaces autour des opérateurs ----
        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],

        // ---- Fonctions et parenthèses ----
        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'no_spaces_inside_parenthesis' => true,

        // ---- Nommage et style ----
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'method' => 'one',
            ],
        ],
        'visibility_required' => ['elements' => ['method', 'property']],
        'single_quote' => true,
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => ['space' => 'single'],
        'concat_space' => ['spacing' => 'one'],

        // ---- Importations ----
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'no_unused_imports' => true,

        // ---- Commentaires & PHPDoc ----
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_scalar' => true,
        'phpdoc_summary' => true,
        'phpdoc_no_package' => false,
        'phpdoc_order' => ['order' => ['param', 'return', 'throws']],
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,

        // ---- Autres bonnes pratiques ----
        'no_closing_tag' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'throw', 'try', 'if', 'for', 'foreach', 'while', 'switch'],
        ],
        'include' => true,
    ])
    ->setIndent('    ') // 4 espaces
    ->setLineEnding("\n")
    ->setFinder($finder);
?>
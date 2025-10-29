<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/public',
    ])
    ->name('*.php')
    ->exclude('vendor')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        // === Standards de base ===
        '@PSR12' => true,                 // Standard principal PHP-FIG
        '@PhpCsFixer' => true,

        // === Espaces et indentation ===
        'indentation_type' => true,       // 4 espaces (pas de tab)
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'single_blank_line_at_eof' => true,

        // === Accolades ===
        'braces' => [
            'position_after_functions_and_oop_constructs' => 'same', // accolade sur la même ligne
            'position_after_control_structures' => 'same',
            'allow_single_line_closure' => true,
            'position_after_anonymous_constructs' => 'same',
        ],


        // === Espaces autour des opérateurs ===
        'binary_operator_spaces' => [
            'default' => 'single_space',  // ex: $a = $b + $c;
        ],

        // === Parenthèses et fonctions ===
        'no_spaces_inside_parenthesis' => true,
        'function_declaration' => ['closure_function_spacing' => 'one'],
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'function_typehint_space' => true,

        // === Nommage et style ===
        'visibility_required' => ['elements' => ['method', 'property']],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'one',
                'property' => 'one',
                'method' => 'one',
            ],
        ],
        'single_quote' => true, // Utiliser ' au lieu de "
        'array_syntax' => ['syntax' => 'short'], // []
        'concat_space' => ['spacing' => 'one'], // $a . $b
        'cast_spaces' => ['space' => 'single'],

        // === Imports et use ===
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'no_unused_imports' => true,

        // === Commentaires et documentation ===
        'phpdoc_align' => ['align' => 'vertical'],       // aligner @param, @return
        'phpdoc_summary' => true,                        // description courte obligatoire
        'phpdoc_order' => ['order' => ['param', 'return', 'throws']],
        'phpdoc_trim' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_scalar' => true,

        // === Blocs de code ===
        'blank_line_before_statement' => [
            'statements' => [
                'return', 'throw', 'try', 'if', 'for', 'foreach', 'while', 'switch',
            ],
        ],
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => [
                'extra',
                'throw',
                'return',
                'continue',
                'break',
                'case',
                'default',
            ],
        ],

        // === Autres règles utiles ===
        'no_closing_tag' => true,        
        'include' => true,               // include/require uniformisés
        'line_ending' => true,
    ])
    ->setIndent('    ')  // 4 espaces
    ->setLineEnding("\n")
    ->setFinder($finder);

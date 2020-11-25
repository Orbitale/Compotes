<?php

declare(strict_types=1);

/*
 * This file is part of the Agate Apps package.
 *
 * (c) Alexandre Rock Ancelet <pierstoval@gmail.com> and Studio Agate.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$header = <<<'HEADER'
This file is part of the Compotes package.

(c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        '.php_cs',
        'public',
        'vendor',
        'var',
    ])
    ->notName('bootstrap.php')
    ->in([
        __DIR__.'/config/',
        __DIR__.'/src/',
        __DIR__.'/migrations/',
        __DIR__.'/tests/',
    ])
    ->files([
        __DIR__.'/bin/console',
    ])
;

return PhpCsFixer\Config::create()
    ->setRules([
        'header_comment' => [
            'header' => $header,
        ],
        // Enabled rules
        '@DoctrineAnnotation' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PhpCsFixer' => true,
        '@PHP56Migration' => true,
        '@PHP70Migration' => true,
        '@PHP70Migration:risky' => true,
        '@PHP71Migration' => true,
        '@PHP71Migration:risky' => true,
        '@PHP73Migration' => true,
        'compact_nullable_typehint' => true,
        'fully_qualified_strict_types' => true,
        'heredoc_to_nowdoc' => true,
        'linebreak_after_opening_tag' => true,
        'logical_operators' => true,
        'no_null_property_initialization' => true,
        'no_php4_constructor' => true,
        'no_short_echo_tag' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_imports' => true,
        'simplified_null_return' => true,
        'strict_param' => true,
        'global_namespace_import' => true,
        'native_constant_invocation' => true,
        'native_function_invocation' => true,
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'static',
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        // Overrides default doctrine rule using ":" as character
        'doctrine_annotation_array_assignment' => [
            'operator' => '=',
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls',
        ],

        // Disabled rules
        'mb_str_functions' => false, // When we know the input, it's okay to not use mb_, because it's less performant
        'increment_style' => false,         // Because "++$i" is not always necessary…
        'non_printable_character' => false, // Because I love using non breakable spaces in test methods ♥
        'php_unit_test_class_requires_covers' => false, // Because we don't use @covers
        'php_unit_internal_class' => false, // Why would this be necessary?
        'heredoc_indentation' => false, // Well, it breaks the "visual" aspects of some strings...
    ])
    ->setRiskyAllowed(true)
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setUsingCache(true)
    ->setFinder($finder)
;

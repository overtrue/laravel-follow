<?php

$header = <<<EOF
This file is part of the overtrue/laravel-follow

(c) overtrue <i@overtrue.me>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        'header_comment' => ['header' => $header],
        'blank_line_after_opening_tag' => true,
        'braces' => ['allow_single_line_closure' => true],
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'declare_equal_normalize' => ['space' => 'none'],
        'function_typehint_space' => true,
        'new_with_braces' => true,
        'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
        'no_empty_statement' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_whitespace_in_blank_line' => true,
        'return_type_declaration' => ['space_before' => 'none'],
        'single_trait_insert_per_statement' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in([__DIR__.'/src/'])
    )
;

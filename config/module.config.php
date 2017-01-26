<?php

namespace zf3SassCompiler;

return [
    'zf3_sass' => [
        'compile' => [
            'rules'  => 'empty', // Set rules of compile file (empty, always or updated)
            'format' => 'nested', // Set the output format (compact, compressed, crunched, expanded, or nested)
            'debugInfo' => true, // Annotate selectors with CSS referring to the source file and line number
            'continueOnError' => false, // Continue compilation (as best as possible) when error encountered
        ],
        'importPath' => [
            dirname(__DIR__) . '/stylesheets/sass'
        ]
    ],

    'service_manager' => [
        'factories' => [
        ],
    ],

];

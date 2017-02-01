<?php

namespace zf3SassCompiler;

return [
    'zf3_sass_compiler' => [
        'compile'    => [
            'rules'           => 'empty', // Set rules of compile file (empty, always or updated)
            'format'          => 'expanded', // Set the output format (compact, compressed, crunched, expanded, or nested)
            'debugInfo'       => true, // Annotate selectors with CSS referring to the source file and line number
            'continueOnError' => false, // Continue compilation (as best as possible) when error encountered
            'directory'       => [
                'src'  => $_SERVER['DOCUMENT_ROOT'] . '/sass',
                'dest' => $_SERVER['DOCUMENT_ROOT'] . '/css',
            ],
        ],
        'importPath' => [
            dirname(__DIR__) . '/stylesheets/sass',
        ],
    ],

    'service_manager' => [
        'factories' => [
            Service\SassCompilerService::class => Factory\Factory::class,
        ],
    ],

];

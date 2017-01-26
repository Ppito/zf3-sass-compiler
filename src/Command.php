<?php
/**
 * Created by PhpStorm.
 * User: Ppito
 * Date: 01/23/2017
 * Time: 09:12 PM
 *
 * @link      https://github.com/Ppito/zf3-sass for the canonical source repository
 * @copyright Copyright (c) 2017 Mickael TONNELIER.
 * @license   https://github.com/Ppito/zf3-whoops/blob/master/LICENSE.md The MIT License
 */

namespace zf3SassCompiler;

class Command {

    public function __invoke($argv) {

        if (file_exists($a = __DIR__ . '/../../bin/pscss')) {
            $res = exec($a . ' ' . implode(' ', $argv));
        } elseif (file_exists($a = __DIR__ . '/../bin/pscss')) {
            $res = exec($a . ' ' . implode(' ', $argv));
        } else {
            fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
            exit(1);
        }

        $file = array_slice($argv, -1);
        file_put_contents(str_replace('.scss', '.css', $file), $res);
    }
}

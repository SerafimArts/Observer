<?php

/**
 * This file is part of Observer package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Serafim\Observer\Library;

use Serafim\FFILoader\LibraryInformation;
use Serafim\FFILoader\Preprocessor;

final class Loader
{
    /**
     * @return LibraryInformation
     */
    public static function load(): LibraryInformation
    {
        $loader = new \Serafim\FFILoader\Loader();

        /** @var Preprocessor $preprocessor */
        $preprocessor = $loader->preprocessor();

        $preprocessor->minify = true;
        $preprocessor->keepComments = false;
        $preprocessor->tolerant = false;

        return $loader->load(new Library(true));
    }
}

<?php

return My\PhpCsFixerConfig::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->files()
            ->name('*.php')
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/tests')
    );
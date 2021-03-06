<?php

/*
 * This file is part of Rocketeer
 *
 * (c) Maxime Fabre <ehtnam6@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Rocketeer\Services\Connections\Shell\Modules;

use Rocketeer\TestCases\RocketeerTestCase;

class FlowTest extends RocketeerTestCase
{
    public function testCanCopyFilesFromPreviousRelease()
    {
        $this->pretend();
        $this->bash->copyFromPreviousRelease('foobar');

        $this->assertHistory([
            'cp -a {server}/releases/10000000000000/foobar {server}/releases/20000000000000/foobar',
        ]);
    }

    public function testCanCheckIfUsesStages()
    {
        $this->config->set('stages.stages', ['foobar']);
        $this->assertTrue($this->task('Deploy')->usesStages());

        $this->config->set('stages.stages', []);
        $this->assertFalse($this->task('Deploy')->usesStages());
    }

    public function testCanRunCommandsInSubdirectoryIfRequired()
    {
        $this->pretend();

        $this->swapConfig(['remote.directories.subdirectory' => 'laravel']);
        $this->bash->runForApplication('ls');
        $this->assertHistoryContains([
            [
                'cd {server}/releases/{release}/laravel',
                'ls',
            ],
        ]);

        $this->swapConfig(['remote.directories.subdirectory' => null]);
        $this->bash->runForApplication('ls');
        $this->assertHistoryContains([
            [
                'cd {server}/releases/{release}',
                'ls',
            ],
        ]);
    }

    public function testDoesNotFailIfPrimerSucceedsSilently()
    {
        $this->assertTrue($this->bash->setupIfNecessary());
    }
}

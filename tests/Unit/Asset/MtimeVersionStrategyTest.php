<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Tests\Unit\Asset;

use Digitix\FrameworkBundle\Asset\MtimeVersionStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class MtimeVersionStrategyTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/dgtx_assets_'.uniqid();
        (new Filesystem())->dumpFile($this->dir.'/bundles/theme.js', 'js');
        touch($this->dir.'/bundles/theme.js', 1700000000);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->dir);
    }

    public function testExistingFilesGetTheirMtimeAsVersion(): void
    {
        $strategy = new MtimeVersionStrategy($this->dir);

        self::assertSame('1700000000', $strategy->getVersion('bundles/theme.js'));
        self::assertSame('/bundles/theme.js?v=1700000000', $strategy->applyVersion('/bundles/theme.js'));
        self::assertSame('bundles/theme.js?a=1&v=1700000000', $strategy->applyVersion('bundles/theme.js?a=1'));
    }

    public function testUnknownFilesAreLeftUntouched(): void
    {
        $strategy = new MtimeVersionStrategy($this->dir);

        self::assertSame('', $strategy->getVersion('nope.js'));
        self::assertSame('nope.js', $strategy->applyVersion('nope.js'));
    }
}

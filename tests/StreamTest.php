<?php
declare(strict_types=1);

namespace Tests;

use Fyre\FileSystem\File;
use Fyre\FileSystem\Folder;
use Fyre\Stream\Exceptions\StreamException;
use Fyre\Stream\Stream;
use PHPUnit\Framework\TestCase;

final class StreamTest extends TestCase
{
    public function testConstructorInvalid(): void
    {
        $this->expectException(StreamException::class);

        new Stream(123);
    }

    public function testContents(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertSame(
            'This is a test.',
            $stream->contents()
        );
    }

    public function testContentsInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->contents();
    }

    public function testEnded(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertFalse(
            $stream->ended()
        );
    }

    public function testEndedEnded(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->read(16);

        $this->assertTrue(
            $stream->ended()
        );
    }

    public function testEndedInvalid(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();

        $this->assertTrue(
            $stream->ended()
        );
    }

    public function testIsReadable(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertTrue(
            $stream->isReadable()
        );
    }

    public function testIsReadableInvalid(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();

        $this->assertFalse(
            $stream->isReadable()
        );
    }

    public function testIsReadableNotReadable(): void
    {
        $stream = Stream::fromFile('tmp/test.txt', 'w');

        $stream->close();

        $this->assertFalse(
            $stream->isReadable()
        );
    }

    public function testIsSeekable(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertTrue(
            $stream->isSeekable()
        );
    }

    public function testIsSeekableInvalid(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();

        $this->assertFalse(
            $stream->isSeekable()
        );
    }

    public function testIsWritable(): void
    {
        $stream = Stream::fromFile('tmp/test.txt', 'w');

        $this->assertTrue(
            $stream->isWritable()
        );
    }

    public function testIsWritableInvalid(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();

        $this->assertFalse(
            $stream->isWritable()
        );
    }

    public function testIsWritableNotWritable(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertFalse(
            $stream->isWritable()
        );
    }

    public function testRead(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertSame(
            'This is a test.',
            $stream->read(16)
        );
    }

    public function testReadNotInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->read(16);
    }

    public function testReadNotReadable(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt', 'w');

        $stream->read(16);
    }

    public function testRewind(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->seek(5);
        $stream->rewind();

        $this->assertSame(
            0,
            $stream->tell()
        );
    }

    public function testRewindInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->rewind();
    }

    public function testSeek(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $stream->seek(5);

        $this->assertSame(
            5,
            $stream->tell()
        );
    }

    public function testSeekInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->seek(5);
    }

    public function testSize(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertSame(
            15,
            $stream->size()
        );
    }

    public function testSizeInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->size();
    }

    public function testTell(): void
    {
        $stream = Stream::fromFile('tmp/test.txt');

        $this->assertSame(
            0,
            $stream->tell()
        );
    }

    public function testTellInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->close();
        $stream->tell();
    }

    public function testWrite(): void
    {
        $stream = Stream::fromFile('tmp/test.txt', 'w+');

        $this->assertSame(
            5,
            $stream->write('Test.')
        );

        $stream->rewind();

        $this->assertSame(
            'Test.',
            (string) $stream
        );
    }

    public function testWriteInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt', 'w');

        $stream->close();
        $stream->write('Test.');
    }

    public function testWriteNotWritable(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::fromFile('tmp/test.txt');

        $stream->write('Test.');
    }

    protected function setUp(): void
    {
        (new File('tmp/test.txt', true))
            ->open('w')
            ->truncate()
            ->write('This is a test.')
            ->close();
    }

    protected function tearDown(): void
    {
        (new Folder('tmp'))
            ->delete();
    }
}

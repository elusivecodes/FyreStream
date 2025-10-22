<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Stream\Exceptions\StreamException;
use Fyre\Stream\Stream;
use Fyre\Utility\Traits\MacroTrait;
use Fyre\Utility\Traits\StaticMacroTrait;
use PHPUnit\Framework\TestCase;

use function array_diff;
use function class_uses;
use function file_put_contents;
use function mkdir;
use function rmdir;
use function unlink;

final class StreamTest extends TestCase
{
    public function testConstructorInvalid(): void
    {
        $this->expectException(StreamException::class);

        new Stream(123);
    }

    public function testEof(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertFalse(
            $stream->eof()
        );
    }

    public function testEofEnded(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->read(16);

        $this->assertTrue(
            $stream->eof()
        );
    }

    public function testEofInvalid(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->close();

        $this->assertTrue(
            $stream->eof()
        );
    }

    public function testGetContents(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            'This is a test.',
            $stream->getContents()
        );
    }

    public function testGetContentsInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createfromString('This is a test.');

        $stream->close();
        $stream->getContents();
    }

    public function testGetMetadata(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            [
                'wrapper_type' => 'PHP',
                'stream_type' => 'TEMP',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://temp',
            ],
            $stream->getMetadata()
        );
    }

    public function testGetMetadataKey(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            'php://temp',
            $stream->getMetadata('uri')
        );
    }

    public function testGetMetadataKeyInvalid(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertNull(
            $stream->getMetadata('invalid')
        );
    }

    public function testGetSize(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            15,
            $stream->getSize()
        );
    }

    public function testGetSizeInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromString('This is a test.');

        $stream->close();
        $stream->getSize();
    }

    public function testIsReadable(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertTrue(
            $stream->isReadable()
        );
    }

    public function testIsReadableInvalid(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->close();

        $this->assertFalse(
            $stream->isReadable()
        );
    }

    public function testIsReadableNotReadable(): void
    {
        $stream = Stream::createFromFile('tmp/test.txt', 'w');

        $stream->close();

        $this->assertFalse(
            $stream->isReadable()
        );
    }

    public function testIsSeekable(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertTrue(
            $stream->isSeekable()
        );
    }

    public function testIsSeekableInvalid(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->close();

        $this->assertFalse(
            $stream->isSeekable()
        );
    }

    public function testIsWritable(): void
    {
        $stream = Stream::createFromFile('tmp/test.txt', 'w');

        $this->assertTrue(
            $stream->isWritable()
        );
    }

    public function testIsWritableInvalid(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->close();

        $this->assertFalse(
            $stream->isWritable()
        );
    }

    public function testIsWritableNotWritable(): void
    {
        $stream = Stream::createFromFile('tmp/test.txt');

        $this->assertFalse(
            $stream->isWritable()
        );
    }

    public function testMacroable(): void
    {
        $this->assertEmpty(
            array_diff([MacroTrait::class, StaticMacroTrait::class], class_uses(Stream::class))
        );
    }

    public function testRead(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            'This is a test.',
            $stream->read(16)
        );
    }

    public function testReadNotInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromString('This is a test.');

        $stream->close();
        $stream->read(16);
    }

    public function testReadNotReadable(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromFile('tmp/test.txt', 'w');

        $stream->read(16);
    }

    public function testRewind(): void
    {
        $stream = Stream::createFromString('This is a test.');

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

        $stream = Stream::createFromString('This is a test.');

        $stream->close();
        $stream->rewind();
    }

    public function testSeek(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $stream->seek(5);

        $this->assertSame(
            5,
            $stream->tell()
        );
    }

    public function testSeekInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromString('This is a test.');

        $stream->close();
        $stream->seek(5);
    }

    public function testTell(): void
    {
        $stream = Stream::createFromString('This is a test.');

        $this->assertSame(
            0,
            $stream->tell()
        );
    }

    public function testTellInvalid(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromString('This is a test.');

        $stream->close();
        $stream->tell();
    }

    public function testWrite(): void
    {
        $stream = Stream::createFromFile('tmp/test.txt', 'w+');

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

        $stream = Stream::createFromFile('tmp/test.txt', 'w');

        $stream->close();
        $stream->write('Test.');
    }

    public function testWriteNotWritable(): void
    {
        $this->expectException(StreamException::class);

        $stream = Stream::createFromFile('tmp/test.txt');

        $stream->write('Test.');
    }

    public static function setUpBeforeClass(): void
    {
        mkdir('tmp');
    }

    public static function tearDownAfterClass(): void
    {
        rmdir('tmp');
    }

    protected function setUp(): void
    {
        file_put_contents('tmp/test.txt', 'This is a test.');
    }

    protected function tearDown(): void
    {
        @unlink('tmp/test.txt');
    }
}

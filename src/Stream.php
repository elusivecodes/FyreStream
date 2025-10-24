<?php
declare(strict_types=1);

namespace Fyre\Http;

use Fyre\Http\Exceptions\StreamException;
use Fyre\Utility\Traits\MacroTrait;
use Fyre\Utility\Traits\StaticMacroTrait;
use Psr\Http\Message\StreamInterface;
use Stringable;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function get_resource_type;
use function is_resource;
use function preg_match;
use function stream_get_contents;
use function stream_get_meta_data;

use const SEEK_SET;

/**
 * Stream
 */
class Stream implements StreamInterface, Stringable
{
    use MacroTrait;
    use StaticMacroTrait;

    /**
     * Create a Stream from a file path.
     *
     * @param string $filePath The file path.
     * @param string $mode The file access mode.
     * @return Stream The Stream.
     */
    public static function createFromFile(string $filePath, string $mode = 'r'): static
    {
        $resource = fopen($filePath, $mode);

        return new static($resource);
    }

    /**
     * Create a Stream from a string.
     *
     * @param string $content The string content.
     * @return Stream The Stream.
     */
    public static function createFromString(string $content = ''): static
    {
        $resource = fopen('php://temp', 'r+');

        fwrite($resource, $content);
        rewind($resource);

        return new static($resource);
    }

    /**
     * New Stream constructor.
     *
     * @param resource $resource The resource.
     *
     * @throws StreamException if the resource is not valid.
     */
    public function __construct(
        protected $resource
    ) {
        if (!is_resource($resource) || get_resource_type($resource) !== 'stream') {
            throw StreamException::forInvalidResource();
        }
    }

    /**
     * Get the entire contents of the stream.
     *
     * @return string The entire contents of the stream.
     */
    public function __toString(): string
    {
        if (!$this->isReadable()) {
            return '';
        }

        if ($this->isSeekable()) {
            $this->rewind();
        }

        return $this->getContents();
    }

    /**
     * Close the resource.
     *
     * @throws StreamException if the resource is not valid.
     */
    public function close(): void
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * Detach the resource from the stream.
     *
     * @return mixed The detached resource.
     */
    public function detach(): mixed
    {
        $resource = $this->resource;

        $this->resource = null;

        return $resource;
    }

    /**
     * Determine whether the stream has ended.
     *
     * @return bool TRUE if the stream has ended, otherwise FALSE.
     */
    public function eof(): bool
    {
        if (!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Get the contents of the stream.
     *
     * @return string The contents of the stream.
     *
     * @throws StreamException if the resource is not readable.
     */
    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw StreamException::forUnreadable();
        }

        return stream_get_contents($this->resource);
    }

    /**
     * Get the stream meta data.
     *
     * @param string|null $key The meta data key.
     * @return array The stream meta data.
     */
    public function getMetadata(string|null $key = null): mixed
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        $data = stream_get_meta_data($this->resource);

        return $key ?
            ($data[$key] ?? null) :
            $data;
    }

    /**
     * Get the size of the stream.
     *
     * @param int|null The size of the stream.
     */
    public function getSize(): int|null
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        $stats = fstat($this->resource);

        return $stats['size'] ?? null;
    }

    /**
     * Determine whether the stream is readable.
     *
     * @return bool TRUE if the stream is readable, otherwise FALSE.
     */
    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return preg_match('/[r+]/', $mode) === 1;
    }

    /**
     * Determine whether the stream is seekable.
     *
     * @return bool TRUE if the stream is seekable, otherwise FALSE.
     */
    public function isSeekable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return $meta['seekable'];
    }

    /**
     * Determine whether the stream is writable.
     *
     * @return bool TRUE if the stream is writable, otherwise FALSE.
     */
    public function isWritable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);
        $mode = $meta['mode'];

        return preg_match('/[xwca+]/', $mode) === 1;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length The number of bytes to read.
     * @return string The read data.
     *
     * @throws StreamException if the resource is not readable.
     */
    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw StreamException::forUnreadable();
        }

        $result = fread($this->resource, $length);

        if ($result === false) {
            throw StreamException::forUnreadable();
        }

        return $result;
    }

    /**
     * Rewind the stream.
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * Move the pointer in the stream.
     *
     * @param int $offset The offset.
     * @param int $whence The origin of the offset.
     *
     * @throws StreamException if the resource is not readable.
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw StreamException::forUnseekable();
        }

        $result = fseek($this->resource, $offset, $whence);

        if ($result !== 0) {
            throw StreamException::forUnseekable();
        }
    }

    /**
     * Get the offset of the pointer.
     *
     * @return int The offset of the pointer.
     *
     * @throws StreamException if the resource is not valid.
     */
    public function tell(): int
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        $result = ftell($this->resource);

        if ($result === false) {
            throw StreamException::forInvalidResource();
        }

        return $result;
    }

    /**
     * Write data to the stream.
     *
     * @param string $data The data to write.
     * @return int The number of bytes written.
     *
     * @throws StreamException if the resource is not writable.
     */
    public function write(string $data): int
    {
        if (!$this->isWritable()) {
            throw StreamException::forUnwritable();
        }

        $result = fwrite($this->resource, $data);

        if ($result === false) {
            throw StreamException::forUnwritable();
        }

        return $result;
    }
}

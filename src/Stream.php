<?php
declare(strict_types=1);

namespace Fyre\Stream;

use Fyre\Stream\Exceptions\StreamException;

use const SEEK_SET;

use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function is_resource;
use function preg_match;
use function stream_get_contents;
use function stream_get_meta_data;

/**
 * Stream
 */
class Stream
{

    protected $resource;

    protected array $metaData;

    /**
     * Create a Stream from a file path.
     * @param string $filePath The file path.
     * @param string $mode The file access mode.
     * @return Stream The Stream.
     */
    public static function fromFile(string $filePath, string $mode = 'r'): self
    {
        $resource = fopen($filePath, $mode);

        return new static($resource);
    }

    /**
     * New Stream constructor.
     * @param resource $resource The resource.
     * @throws StreamException if the resource is not valid.
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw StreamException::forInvalidResource();
        }

        $this->resource = $resource;
    }

    /**
     * Get the entire contents of the stream.
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

        return $this->contents();
    }

    /**
     * Close the resource.
     */
    public function close(): void
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        fclose($this->resource);

        $this->resource = null;
    }

    /**
     * Get the contents of the stream.
     * @return string The contents of the stream.
     */
    public function contents(): string
    {
        if (!$this->isReadable()) {
            throw StreamException::forUnreadable();
        }

        return stream_get_contents($this->resource);
    }

    /**
     * Determine if the stream has ended.
     * @return bool TRUE if the stream has ended, otherwise FALSE.
     */
    public function ended(): bool
    {
        if (!$this->resource) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * Determine if the stream is readable.
     * @return bool TRUE if the stream is readable, otherwise FALSE.
     */
    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = $this->getMetaData();

        return preg_match('/[r+]/', $meta['mode']) === 1;
    }

    /**
     * Determine if the stream is seekable.
     * @return bool TRUE if the stream is seekable, otherwise FALSE.
     */
    public function isSeekable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = $this->getMetaData();

        return $meta['seekable'];
    }

    /**
     * Determine if the stream is writable.
     * @return bool TRUE if the stream is writable, otherwise FALSE.
     */
    public function isWritable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = $this->getMetaData();

        return preg_match('/[xwca+]/', $meta['mode']) === 1;
    }

    /**
     * Read data from the stream.
     * @param int $length The number of bytes to read.
     * @return string The read data.
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
     * @return Stream The Stream;
     */
    public function rewind(): static
    {
        return $this->seek(0);
    }

    /**
     * Move the pointer in the stream.
     * @param int $offset The offset.
     * @param int $whence The origin of the offset.
     * @return Stream The Stream.
     * @throws StreamException if the resource is not readable.
     */
    public function seek(int $offset, int $whence = SEEK_SET): static
    {
        if (!$this->isSeekable()) {
            throw StreamException::forUnseekable();
        }

        $result = fseek($this->resource, $offset, $whence);

        if ($result !== 0) {
            throw StreamException::forUnseekable();
        }

        return $this;
    }

    /**
     * Get the size of the stream.
     * @param int|null The size of the stream.
     */
    public function size(): int|null
    {
        if (!$this->resource) {
            throw StreamException::forInvalidResource();
        }

        $stats = fstat($this->resource);

        return $stats['size'] ?? null;
    }

    /**
     * Get the offset of the pointer.
     * @return int The offset of the pointer.
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
     * @param string $data The data to write.
     * @return int The number of bytes written.
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

    /**
     * Get the stream meta data.
     * @return array The stream meta data.
     */
    protected function getMetaData(): array
    {
        return $this->metaData ??= stream_get_meta_data($this->resource);
    }

}

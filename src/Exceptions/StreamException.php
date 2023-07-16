<?php
declare(strict_types=1);

namespace Fyre\Stream\Exceptions;

use RuntimeException;

/**
 * StreamException
 */
class StreamException extends RuntimeException
{

    public static function forInvalidResource()
    {
        return new static('Invalid stream resource');
    }

    public static function forUnreadable()
    {
        return new static('Stream resource is not readable');
    }

    public static function forUnseekable()
    {
        return new static('Stream resource is not seekable');
    }

    public static function forUnwritable()
    {
        return new static('Stream resource is not writable');
    }

}

# FyreStream

**FyreStream** is a free, open-source stream library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Stream Creation](#stream-creation)
- [Methods](#methods)



## Installation

**Using Composer**

```
composer require fyre/stream
```

In PHP:

```php
use Fyre\Stream\Stream;
```


## Stream Creation

- `$resource` is a resource.

```php
$stream = new Stream($resource);
```

**From File**

- `$filePath` is a string representing the file path.
- `$mode` is a string representing the file access mode, and will default to "*r*".

```php
$stream = Stream::fromFile($filePath, $mode);
```


## Methods

**Close**

Close the resource.

```php
$stream->close();
```

**Contents**

Get the contents of the stream.

```php
$contents = $stream->contents();
```

**Ended**

Determine if the stream has ended.

```php
$ended = $stream->ended();
```

**Is Readable**

Determine if the stream is readable.

```php
$isReadable = $stream->isReadable();
```

**Is Seekable**

Determine if the stream is seekable.

```php
$isSeekable = $stream->isSeekable();
```

**Is Writable**

Determine if the stream is writable.

```php
$isWritable = $stream->isWritable();
```

**Read**

Read data from the stream.

- `$length` is a number representing the number of bytes to read.

```php
$data = $stream->read($length);
```

**Rewind**

Rewind the stream.

```php
$stream->rewind();
```

**Seek**

Move the pointer in the stream.

- `$offset` is a number representing the offset.
- `$whence` is a number representing the offset origin, and will default to *SEEK_SET*.

```php
$stream->seek($offset, $whence);
```

**Size**

Get the size of the stream.

```php
$size = $stream->size();
```

**Tell**

Get the offset of the pointer.

```php
$offset = $stream->tell();
```

**Write**

Write data to the stream.

- `$data` is a string representing the data to write.

```php
$written = $stream->write($data);
```
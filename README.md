# FyreStream

**FyreStream** is a free, open-source stream library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
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


## Basic Usage

- `$resource` is a resource.

```php
$stream = new Stream($resource);
```

**Create From File**

- `$filePath` is a string representing the file path.
- `$mode` is a string representing the file access mode, and will default to "*r*".

```php
$stream = Stream::createFromFile($filePath, $mode);
```

**Create From String**

- `$string` is a string representing the stream content.

```php
$stream = Stream::createFromString($string);
```

## Methods

**Close**

Close the resource.

```php
$stream->close();
```

**Detach**

Detach the resource from the stream.

```php
$stream->detach();
```

**Eof**

Determine whether the stream has ended.

```php
$ended = $stream->eof();
```

**Get Contents**

Get the contents of the stream.

```php
$contents = $stream->getContents();
```

**Get Metadata**

Get the stream meta data.

- `$key` is a string representing the meta data key.

```php
$value = $stream->getMetadata($key);
```

If the `$key` argument is omitted, this method will return an array containing all values.

```php
$data = $stream->getMetadata();
```

**Get Size**

Get the size of the stream.

```php
$size = $stream->getSize();
```

**Is Readable**

Determine whether the stream is readable.

```php
$isReadable = $stream->isReadable();
```

**Is Seekable**

Determine whether the stream is seekable.

```php
$isSeekable = $stream->isSeekable();
```

**Is Writable**

Determine whether the stream is writable.

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
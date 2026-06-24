<?php
declare(strict_types=1);

namespace ControlBit\Dto\Transformer;

use ControlBit\Dto\Contract\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @psalm-type ReverseOptions = array{
 *      chunkSize: ?int,
 *      tempDir: ?string,
 *      tempPrefix: ?string
 *  }|array{}
 */
class FileBase64Transformer implements TransformerInterface
{
    private const DEFAULT_CHUNK_SIZE = 4096;

    public function transform(mixed $value, array $options = []): mixed
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof \SplFileInfo) {
            return null;
        }

        $mimeType = \mime_content_type($value->getRealPath()) ?: 'application/octet-stream';
        $contents = \file_get_contents($value->getRealPath());

        if (false === $contents) {
            return null;
        }

        return \sprintf('data:%s;base64,%s', $mimeType, \base64_encode($contents));
    }

    /**
     * @param  mixed           $value
     * @param  ReverseOptions  $options
     *
     * @return ?\SplFileInfo
     */
    public function reverse(mixed $value, array $options = []): mixed
    {
        if (!\is_string($value)) {
            return null;
        }

        if (!\str_contains($value, ';base64,')) {
            return null;
        }

        $value = \str_replace('data:', '', $value);

        [$mimeType, $encoded] = \explode(';base64,', $value, 2);

        $path = $this->getFilePath($mimeType, $options);
        $this->saveInChunks($path, $encoded, $options['chunkSize'] ?? self::DEFAULT_CHUNK_SIZE);

        return new \SplFileInfo($path);
    }

    /**
     * @param  ReverseOptions  $options
     */
    private function getFilePath(?string $mimeType, array $options): string
    {
        $tmpPath = \tempnam(
            $options['tempDir'] ?? \sys_get_temp_dir(),
            $options['tempPrefix'] ?? '',
        );

        if (null !== $mimeType && \class_exists('\Symfony\Component\Mime\MimeTypes')) {
            $mimeTypes = new Symfony\Component\Mime\MimeTypes(); // @phpstan-ignore class.notFound

            // @phpstan-ignore-next-line
            $extension = $mimeTypes->getExtensions($mimeType)[0] ?? null;

            if (null !== $extension) {
                $newPath = $tmpPath . '.' . $extension; // @phpstan-ignore binaryOp.invalid
                \rename($tmpPath, $newPath);

                return $newPath;
            }
        }

        return $tmpPath;
    }

    private function saveInChunks(string $path, string &$encoded, int $chunkSize): void
    {
        $handle = \fopen($path, 'wb');

        if (false === $handle) {
            return;
        }

        $length = \strlen($encoded);
        $offset = 0;

        while ($offset < $length) {
            $chunk   = \substr($encoded, $offset, $chunkSize);
            $decoded = \base64_decode($chunk, true);

            if (false === $decoded) {
                \fclose($handle);
                \unlink($path);

                return;
            }

            \fwrite($handle, $decoded);
            $offset += $chunkSize;
        }

        \fclose($handle);
    }
}
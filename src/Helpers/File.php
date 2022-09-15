<?php

namespace ElaborateCode\JigsawLocalization\Helpers;

use Stringable;

class File implements Stringable
{

    /**
     * Realpath
     */
    protected string $projectRoot;

    /**
     * Realpath
     */
    protected string $path;

    protected bool $isDir;

    /**
     * 'file_name' => 'file_absolute_path'
     */
    protected ?array $directoryContent;

    public function __construct(string $rel_path = '')
    {
        $this->setProjectRoot();

        $this->setPath($rel_path);

        $this->isDir = is_dir($this->path);

        if ($this->isDir()) {
            $files = scandir($this->path);
            $files = array_splice($files, 2);

            foreach ($files as $file_name) {
                $this->directoryContent[$file_name] = $this->path . DIRECTORY_SEPARATOR . $file_name;
            }
        }
    }

    public function __toString(): string
    {
        return $this->path;
    }

    protected function setProjectRoot(): void
    {
        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);

        $this->projectRoot = realpath(dirname($reflection->getFileName(), 3));
    }

    public function getProjectRoot(): string
    {
        return $this->projectRoot;
    }

    protected function setPath(string $rel_path): void
    {
        $realpath = realpath($this->projectRoot . DIRECTORY_SEPARATOR . $rel_path);

        if (!$realpath) {
            throw new \Exception("Invalid relative path. Can't get absolute path from '$rel_path'!");
        }

        $this->path = $realpath;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function isDir(): bool
    {
        return $this->isDir;
    }

    public function getDirectoryContent(): array
    {
        if (!$this->isDir()) {
            throw new \Exception("Error Processing Request");
        }

        return $this->directoryContent;
    }
}

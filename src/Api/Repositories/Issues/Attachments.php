<?php

declare(strict_types=1);

/*
 * This file is part of Bitbucket API Client.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bitbucket\Api\Repositories\Issues;

use Http\Message\MultipartStream\MultipartStreamBuilder;

/**
 * The attachments api class.
 *
 * @author Graham Campbell <graham@alt-thre.com>
 */
class Attachments extends AbstractIssuesApi
{
    /**
     * @param array $params
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function list(array $params = [])
    {
        $path = $this->buildAttachmentsPath();

        return $this->get($path, $params);
    }

    /**
     * @param string                                            $name
     * @param string|resource|\Psr\Http\Message\StreamInterface $resource
     * @param array                                             $options
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function upload(string $name, $resource, array $options = [])
    {
        $path = $this->buildAttachmentsPath();
        $builder = (new MultipartStreamBuilder())->addResource($name, $resource, $options);
        $headers = ['Content-Type' => sprintf('multipart/form-data; boundary="%s"', $builder->getBoundary())];

        return $this->postRaw($path, $builder->build(), $headers);
    }

    /**
     * @param string $filename
     * @param array  $params
     *
     * @throws \Http\Client\Exception
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function download(string $filename, array $params = [])
    {
        $path = $this->buildAttachmentsPath($filename);

        return $this->pureGet($path, $params, ['Accept' => '*/*'])->getBody();
    }

    /**
     * @param string $filename
     * @param array  $params
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function remove(string $filename, array $params = [])
    {
        $path = $this->buildAttachmentsPath($filename);

        return $this->delete($path, $params);
    }

    /**
     * Build the attachments path from the given parts.
     *
     * @param string[] $parts
     *
     * @throws \Bitbucket\Exception\InvalidArgumentException
     *
     * @return string
     */
    protected function buildAttachmentsPath(string ...$parts)
    {
        return static::buildPath('repositories', $this->username, 'issues', $this->issue, 'attachments', ...$parts);
    }
}

<?php

namespace BEAR\QueryRepository;

use BEAR\QueryRepository\QueryRepository as Repository;
use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\FilesystemCache;
use FakeVendor\HelloWorld\Resource\Page\Index;

class ResourceRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryRepository
     */
    private $repository;

    /**
     * @var ResourceObject
     */
    private $resourceObject;

    public function setUp()
    {
        $this->repository = new Repository(new EtagSetter, new FilesystemCache($_ENV['TMP_DIR']), new AnnotationReader, new Expiry(0, 0, 0));
        /* @var $resource Resource */
        $this->resourceObject = new Index;
        $this->resourceObject->uri = new Uri('page://self/user');
    }

    public function testPutAndGet()
    {
        // put
        $this->repository->put($this->resourceObject);
        $uri = $this->resourceObject->uri;
        // get
        list($code, $headers, $body) = $this->repository->get($uri);
        $this->assertSame($code, $this->resourceObject->code);
        $this->assertSame($headers, $this->resourceObject->headers);
        $this->assertSame($body, $this->resourceObject->body);
    }

    public function testDelete()
    {
        $this->repository->put($this->resourceObject);
        $uri = $this->resourceObject->uri;
        $this->repository->purge($uri);
        $instance = $this->repository->get($uri);
        $this->assertFalse($instance);
    }
}

<?php

namespace Arcana\Bundle\ContentBundle\Test\Controller\Admin;

use Arcana\Bundle\ContentBundle\EntityManager\ContentManager;
use Arcana\Bundle\ContentBundle\Entity\Content;
use Doctrine\Common\Collections\ArrayCollection;

class ContentManagerTest extends \PHPUnit_Framework_TestCase
{
    const TEST_TEXT = 'Test text';

    public function testUpdateTexts()
    {
        $texts = array(
            '1' => '<s></s>' . self::TEST_TEXT,
            '2' => self::TEST_TEXT
        );

        $contents = array($this->createContentMock(1), $this->createContentMock(2));

        $repository = $this->createRepositoryMock();
        $repository->expects($this->once())
                ->method('findById')
                ->with(array(1, 2))
                ->will($this->returnValue($contents));

        $em = $this->createEntityManagerMock($repository);

        $manager = new ContentManager($em);

        $manager->updateTexts($texts);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createContentMock($id)
    {
        $content = $this->getMock('\Arcana\Bundle\ContentBundle\Entity\Content', array(
            'getId', 'setText'
        ));

        $content->expects($this->once())
                ->method('setText')
                ->with($this->equalTo(self::TEST_TEXT));

        $content->expects($this->any())
                ->method('getId')
                ->will($this->returnValue($id));

        return $content;
    }

    public function testGetWithMissingLocale()
    {
        $em = $this->createEntityManagerMock($this->createRepositoryMock());

        $manager = new ContentManager($em);

        $this->setExpectedException('\Arcana\Bundle\ContentBundle\Exception\MissingLocaleException');

        $manager->get('test');
    }

    public function testGetWithRequestLocale()
    {
        $content = new Content();

        $repository = $this->createRepositoryMock();
        $this->expectFindByNameAndLocale($repository, $content);

        $em = $this->createEntityManagerMock($repository);

        $requests = new \Symfony\Component\HttpFoundation\RequestStack();

        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->setLocale('ru');

        $requests->push($request);

        $manager = new ContentManager($em, $requests);

        $this->assertEquals($content, $manager->get('test'));
    }

    public function testGetWithLocale()
    {
        $content = new Content();

        $repository = $this->createRepositoryMock();
        $this->expectFindByNameAndLocale($repository, $content);

        $em = $this->createEntityManagerMock($repository);

        $manager = new ContentManager($em);

        $this->assertEquals($content, $manager->get('test', null, 'ru'));
    }

    public function testGetNew()
    {
        $content = new Content();

        $repository = $this->createRepositoryMock();
        $this->expectFindByNameAndLocale($repository, null);
        $repository->expects($this->once())
                ->method('create')
                ->with('test', 'Test content', 'ru')
                ->will($this->returnValue($content));

        $em = $this->createEntityManagerMock($repository);

        $manager = new ContentManager($em);

        $this->assertEquals($content, $manager->get('test', 'Test content', 'ru'));
    }

    public function testGetNewWithoutDefault()
    {
        $content = new Content();

        $repository = $this->createRepositoryMock();
        $this->expectFindByNameAndLocale($repository, null);
        $repository->expects($this->once())
                ->method('create')
                ->with('test', 'test', 'ru')
                ->will($this->returnValue($content));

        $em = $this->createEntityManagerMock($repository);

        $manager = new ContentManager($em);

        $this->assertEquals($content, $manager->get('test', null, 'ru'));
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $repository
     * @param Content $content
     */
    private function expectFindByNameAndLocale(\PHPUnit_Framework_MockObject_MockObject $repository, Content $content = null)
    {
        $repository->expects($this->once())
                ->method('findByNameAndLocale')
                ->with('test', 'ru')
                ->will($this->returnValue($content));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createRepositoryMock()
    {
        return $this->getMockBuilder('\Arcana\Bundle\ContentBundle\EntityRepository\ContentRepository')
                ->disableOriginalConstructor()
                ->getMock();
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $repository
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createEntityManagerMock(\PHPUnit_Framework_MockObject_MockObject $repository)
    {
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                ->disableOriginalConstructor()
                ->getMock();

        $em->expects($this->any())
                ->method('getRepository')
                ->with('ArcanaContentBundle:Content')
                ->will($this->returnValue($repository));

        return $em;
    }
}

<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\CollectionCreateStruct;
use Netgen\BlockManager\API\Values\Collection\CollectionUpdateStruct;
use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\API\Values\Collection\ItemCreateStruct;
use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\API\Values\Collection\QueryCreateStruct;
use Netgen\BlockManager\API\Values\Collection\QueryUpdateStruct;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Parameters\ParameterValue;
use Netgen\BlockManager\Tests\Collection\Stubs\QueryType;

abstract class CollectionServiceTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->collectionService = $this->createCollectionService(
            $this->createMock(CollectionValidator::class)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     */
    public function testLoadCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->assertTrue($collection->isPublished());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionThrowsNotFoundException()
    {
        $this->collectionService->loadCollection(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::__construct
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     */
    public function testLoadCollectionDraft()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->assertFalse($collection->isPublished());
        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadCollectionDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadCollectionDraftThrowsNotFoundException()
    {
        $this->collectionService->loadCollectionDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadSharedCollections
     */
    public function testLoadSharedCollections()
    {
        $collections = $this->collectionService->loadSharedCollections();

        $this->assertNotEmpty($collections);

        foreach ($collections as $collection) {
            $this->assertTrue($collection->isPublished());
            $this->assertInstanceOf(Collection::class, $collection);
            $this->assertTrue($collection->isShared());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     */
    public function testLoadItem()
    {
        $item = $this->collectionService->loadItem(7);

        $this->assertTrue($item->isPublished());
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItem
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemThrowsNotFoundException()
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     */
    public function testLoadItemDraft()
    {
        $item = $this->collectionService->loadItemDraft(7);

        $this->assertFalse($item->isPublished());
        $this->assertInstanceOf(Item::class, $item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadItemDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadItemDraftThrowsNotFoundException()
    {
        $this->collectionService->loadItem(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     */
    public function testLoadQuery()
    {
        $query = $this->collectionService->loadQuery(2);

        $this->assertTrue($query->isPublished());
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQuery
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryThrowsNotFoundException()
    {
        $this->collectionService->loadQuery(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     */
    public function testLoadQueryDraft()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $this->assertFalse($query->isPublished());
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::loadQueryDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testLoadQueryDraftThrowsNotFoundException()
    {
        $this->collectionService->loadQueryDraft(999999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     */
    public function testCreateCollection()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_MANUAL,
            'New name'
        );

        $collectionCreateStruct->itemCreateStructs = array(
            $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent'),
        );

        $collectionCreateStruct->queryCreateStructs = array(
            $this->collectionService->newQueryCreateStruct(
                new QueryType('ezcontent_search'),
                'new_query'
            ),
        );

        $createdCollection = $this->collectionService->createCollection($collectionCreateStruct);

        $this->assertFalse($createdCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $createdCollection);
        $this->assertEquals('New name', $createdCollection->getName());

        $this->assertCount(1, $createdCollection->getItems());
        $this->assertCount(1, $createdCollection->getQueries());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateCollectionThrowsBadStateException()
    {
        $collectionCreateStruct = $this->collectionService->newCollectionCreateStruct(
            Collection::TYPE_MANUAL,
            'My collection'
        );

        $this->collectionService->createCollection($collectionCreateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     */
    public function testUpdateCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $updatedCollection = $this->collectionService->updateCollection($collection, $collectionUpdateStruct);

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertEquals('Super cool collection', $updatedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateCollectionThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(4);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'Super cool collection';

        $this->collectionService->updateCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateCollectionWithExistingNameThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollectionDraft(5);

        $collectionUpdateStruct = $this->collectionService->newCollectionUpdateStruct();
        $collectionUpdateStruct->name = 'My collection';

        $this->collectionService->updateCollection(
            $collection,
            $collectionUpdateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromManualToDynamic()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('ezcontent_search'),
                'default'
            )
        );

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_DYNAMIC, $updatedCollection->getType());
        $this->assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertCount(1, $updatedCollection->getQueries());
        $this->assertEquals('ezcontent_search', $updatedCollection->getQueries()[0]->getQueryType()->getType());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     */
    public function testChangeCollectionTypeFromDynamicToManual()
    {
        $collection = $this->collectionService->loadCollectionDraft(4);

        $updatedCollection = $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );

        $this->assertFalse($updatedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $updatedCollection);
        $this->assertEquals(Collection::TYPE_MANUAL, $updatedCollection->getType());
        $this->assertEquals(count($updatedCollection->getItems()), count($collection->getItems()));
        $this->assertCount(0, $updatedCollection->getQueries());

        foreach ($updatedCollection->getItems() as $index => $item) {
            $this->assertEquals($index, $item->getPosition());
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(4);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_MANUAL
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionWithInvalidType()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->changeCollectionType(
            $collection,
            999,
            $this->collectionService->newQueryCreateStruct(
                new QueryType('ezcontent_search'),
                'default'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::changeCollectionType
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testChangeCollectionTypeThrowsBadStateExceptionOnChangingToDynamicCollectionWithoutQueryCreateStruct()
    {
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->changeCollectionType(
            $collection,
            Collection::TYPE_DYNAMIC
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection);

        $this->assertEquals($collection->isPublished(), $copiedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(7, $copiedCollection->getId());
        $this->assertNull($copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     */
    public function testCopyCollectionWithName()
    {
        $collection = $this->collectionService->loadCollection(3);
        $copiedCollection = $this->collectionService->copyCollection($collection, 'New name');

        $this->assertEquals($collection->isPublished(), $copiedCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $copiedCollection);
        $this->assertEquals(7, $copiedCollection->getId());
        $this->assertEquals('New name', $copiedCollection->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::copyCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCopyCollectionWithNameThrowsBadStateException()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->copyCollection($collection, 'My other collection');
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     */
    public function testCreateDraft()
    {
        $collection = $this->collectionService->loadCollection(2);
        $draftCollection = $this->collectionService->createDraft($collection);

        $this->assertFalse($draftCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $draftCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     */
    public function testCreateDraftWithDiscardingExistingDraft()
    {
        $collection = $this->collectionService->loadCollection(3);
        $draftCollection = $this->collectionService->createDraft($collection, true);

        $this->assertFalse($draftCollection->isPublished());
        $this->assertInstanceOf(Collection::class, $draftCollection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionWithNonPublishedCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $this->collectionService->createDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::createDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testCreateDraftThrowsBadStateExceptionIfDraftAlreadyExists()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->createDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDiscardDraft()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $this->collectionService->discardDraft($collection);

        $this->collectionService->loadCollectionDraft($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::discardDraft
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDiscardDraftThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->discardDraft($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     */
    public function testPublishCollection()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);
        $publishedCollection = $this->collectionService->publishCollection($collection);

        $this->assertInstanceOf(Collection::class, $publishedCollection);
        $this->assertTrue($publishedCollection->isPublished());

        try {
            $this->collectionService->loadCollectionDraft($collection->getId());
            self::fail('Draft collection still exists after publishing.');
        } catch (NotFoundException $e) {
            // Do nothing
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::publishCollection
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testPublishCollectionThrowsBadStateExceptionWithNonDraftCollection()
    {
        $collection = $this->collectionService->loadCollection(3);
        $this->collectionService->publishCollection($collection);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteCollection
     * @expectedException \Netgen\BlockManager\Exception\NotFoundException
     */
    public function testDeleteCollection()
    {
        $collection = $this->collectionService->loadCollection(3);

        $this->collectionService->deleteCollection($collection);

        $this->collectionService->loadCollection($collection->getId());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     */
    public function testAddItem()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollectionDraft(1);

        $createdItem = $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );

        $this->assertFalse($createdItem->isPublished());
        $this->assertInstanceOf(Item::class, $createdItem);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionWithNonDraftCollection()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollection(4);

        $this->collectionService->addItem(
            $collection,
            $itemCreateStruct,
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddItemThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $itemCreateStruct = $this->collectionService->newItemCreateStruct(Item::TYPE_MANUAL, '66', 'ezcontent');
        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addItem($collection, $itemCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     */
    public function testMoveItem()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            1
        );

        /*
        $this->assertFalse($movedItem->isPublished());
        $this->assertInstanceOf(Item::class, $movedItem);
        $this->assertEquals(1, $movedItem->getPosition());
        */

        $secondItem = $this->collectionService->loadItemDraft(2);
        $this->assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionWithNonDraftItem()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItem(4),
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveItemThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveItem(
            $this->collectionService->loadItemDraft(1),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     */
    public function testDeleteItem()
    {
        $item = $this->collectionService->loadItemDraft(1);
        $this->collectionService->deleteItem($item);

        try {
            $this->collectionService->loadItemDraft($item->getId());
            self::fail('Item still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondItem = $this->collectionService->loadItemDraft(2);
        $this->assertEquals(0, $secondItem->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteItem
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDeleteItemThrowsBadStateExceptionWithNonDraftItem()
    {
        $item = $this->collectionService->loadItem(4);
        $this->collectionService->deleteItem($item);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     */
    public function testAddQuery()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $createdQuery = $this->collectionService->addQuery(
            $collection,
            $queryCreateStruct,
            1
        );

        $this->assertFalse($createdQuery->isPublished());
        $this->assertInstanceOf(Query::class, $createdQuery);

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        $this->assertEquals(2, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionInNonDraftCollection()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $collection = $this->collectionService->loadCollection(3);

        $this->collectionService->addQuery(
            $collection,
            $queryCreateStruct,
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryInManualCollectionThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(1);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryWithExistingIdentifierThrowsBadStateException()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'default'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 1);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::addQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testAddQueryThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $collection = $this->collectionService->loadCollectionDraft(3);

        $this->collectionService->addQuery($collection, $queryCreateStruct, 9999);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     */
    public function testUpdateQuery()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameterValue('parent_location_id', 3);
        $queryUpdateStruct->setParameterValue('param', 'value');

        $updatedQuery = $this->collectionService->updateQuery($query, $queryUpdateStruct);

        $this->assertFalse($updatedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $updatedQuery);

        $this->assertEquals('new_identifier', $updatedQuery->getIdentifier());
        $this->assertEquals(
            array(
                'offset' => new ParameterValue(
                    array(
                        'name' => 'offset',
                        'parameter' => $query->getQueryType()->getParameters()['offset'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('integer'),
                        'value' => '0',
                        'isEmpty' => false,
                    )
                ),
                'param' => new ParameterValue(
                    array(
                        'name' => 'param',
                        'parameter' => $query->getQueryType()->getParameters()['param'],
                        'parameterType' => $this->parameterTypeRegistry->getParameterType('text_line'),
                        'value' => 'value',
                        'isEmpty' => false,
                    )
                ),
            ),
            $updatedQuery->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateQueryThrowsBadStateExceptionWithNonDraftQuery()
    {
        $query = $this->collectionService->loadQuery(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'new_identifier';
        $queryUpdateStruct->setParameterValue('parent_location_id', 3);
        $queryUpdateStruct->setParameterValue('param', 'value');

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::updateQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testUpdateQueryWithExistingIdentifierThrowsBadStateException()
    {
        $query = $this->collectionService->loadQueryDraft(2);

        $queryUpdateStruct = $this->collectionService->newQueryUpdateStruct();
        $queryUpdateStruct->identifier = 'featured';

        $this->collectionService->updateQuery($query, $queryUpdateStruct);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     */
    public function testMoveQuery()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft(2),
            1
        );

        /*
        $this->assertFalse($movedQuery->isPublished());
        $this->assertInstanceOf(Query::class, $movedQuery);
        $this->assertEquals(1, $movedQuery->getPosition());
        */

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        $this->assertEquals(0, $secondQuery->getPosition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionWithNonDraftQuery()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQuery(2),
            1
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::moveQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testMoveQueryThrowsBadStateExceptionWhenPositionIsTooLarge()
    {
        $this->collectionService->moveQuery(
            $this->collectionService->loadQueryDraft(2),
            9999
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     */
    public function testDeleteQuery()
    {
        $collection = $this->collectionService->loadCollectionDraft(3);

        $query = $this->collectionService->loadQueryDraft(2);
        $this->collectionService->deleteQuery($query);

        $collectionAfterDelete = $this->collectionService->loadCollectionDraft(3);

        try {
            $this->collectionService->loadQueryDraft($query->getId());
            self::fail('Query still exists after deleting.');
        } catch (NotFoundException $e) {
            // Do nothing
        }

        $secondQuery = $this->collectionService->loadQueryDraft(3);
        $this->assertEquals(0, $secondQuery->getPosition());

        $this->assertEquals(count($collection->getQueries()) - 1, count($collectionAfterDelete->getQueries()));
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::deleteQuery
     * @expectedException \Netgen\BlockManager\Exception\BadStateException
     */
    public function testDeleteQueryThrowsBadStateExceptionWithNonDraftQuery()
    {
        $query = $this->collectionService->loadQuery(2);
        $this->collectionService->deleteQuery($query);
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionCreateStruct
     */
    public function testNewCollectionCreateStruct()
    {
        $this->assertEquals(
            new CollectionCreateStruct(
                array(
                    'type' => Collection::TYPE_DYNAMIC,
                    'name' => 'New collection',
                )
            ),
            $this->collectionService->newCollectionCreateStruct(
                Collection::TYPE_DYNAMIC,
                'New collection'
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newCollectionUpdateStruct
     */
    public function testNewCollectionUpdateStruct()
    {
        $this->assertEquals(
            new CollectionUpdateStruct(),
            $this->collectionService->newCollectionUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newItemCreateStruct
     */
    public function testNewItemCreateStruct()
    {
        $this->assertEquals(
            new ItemCreateStruct(
                array(
                    'type' => Item::TYPE_OVERRIDE,
                    'valueId' => '42',
                    'valueType' => 'ezcontent',
                )
            ),
            $this->collectionService->newItemCreateStruct(Item::TYPE_OVERRIDE, '42', 'ezcontent')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryCreateStruct
     */
    public function testNewQueryCreateStruct()
    {
        $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
            new QueryType('ezcontent_search'),
            'new_query'
        );

        $this->assertEquals(
            new QueryCreateStruct(
                array(
                    'identifier' => 'new_query',
                    'queryType' => new QueryType('ezcontent_search'),
                    'parameterValues' => array(
                        'offset' => null,
                        'param' => null,
                    ),
                )
            ),
            $queryCreateStruct
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStruct()
    {
        $this->assertEquals(
            new QueryUpdateStruct(),
            $this->collectionService->newQueryUpdateStruct()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\CollectionService::newQueryUpdateStruct
     */
    public function testNewQueryUpdateStructFromQuery()
    {
        $query = $this->collectionService->loadQueryDraft(4);

        $this->assertEquals(
            new QueryUpdateStruct(
                array(
                    'identifier' => $query->getIdentifier(),
                    'parameterValues' => array(
                        'offset' => 0,
                        'param' => null,
                    ),
                )
            ),
            $this->collectionService->newQueryUpdateStruct($query)
        );
    }
}

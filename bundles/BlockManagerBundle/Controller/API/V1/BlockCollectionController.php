<?php

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\Collection\Result\ResultLoaderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator;
use Netgen\Bundle\BlockManagerBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockCollectionController extends Controller
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator
     */
    protected $validator;

    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultLoaderInterface
     */
    protected $resultLoader;

    /**
     * @var int
     */
    protected $maxLimit;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Validator\BlockCollectionValidator $validator
     * @param \Netgen\BlockManager\Collection\Result\ResultLoaderInterface $resultLoader
     * @param int $maxLimit
     */
    public function __construct(
        BlockService $blockService,
        CollectionService $collectionService,
        BlockCollectionValidator $validator,
        ResultLoaderInterface $resultLoader,
        $maxLimit
    ) {
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->validator = $validator;
        $this->resultLoader = $resultLoader;
        $this->maxLimit = $maxLimit;
    }

    /**
     * Returns the collection result.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Netgen\BlockManager\Serializer\Values\VersionedValue
     */
    public function loadCollectionResult(Block $block, $collectionIdentifier, Request $request)
    {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        $this->validator->validateOffsetAndLimit($offset, $limit);

        if (empty($limit) || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        return new VersionedValue(
            $this->resultLoader->load(
                $block->getCollectionReference($collectionIdentifier)->getCollection(),
                (int) $offset,
                (int) $limit,
                ResultSet::INCLUDE_INVISIBLE_ITEMS |
                ResultSet::INCLUDE_INVALID_ITEMS |
                ResultSet::INCLUDE_UNKNOWN_ITEMS
            ),
            Version::API_V1
        );
    }

    /**
     * Adds an item inside the collection.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addItems(Block $block, $collectionIdentifier, Request $request)
    {
        $items = $request->request->get('items');

        $this->validator->validateAddItems($block, $collectionIdentifier, $items);

        $this->collectionService->transaction(
            function () use ($block, $collectionIdentifier, $items) {
                foreach ($items as $item) {
                    $itemCreateStruct = $this->collectionService->newItemCreateStruct(
                        $item['type'],
                        $item['value_id'],
                        $item['value_type']
                    );

                    $this->collectionService->addItem(
                        $block->getCollectionReference($collectionIdentifier)->getCollection(),
                        $itemCreateStruct,
                        isset($item['position']) ? $item['position'] : null
                    );
                }
            }
        );

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Changes the collection type within the block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     * @param string $collectionIdentifier
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If new collection type is not valid
     *                                                                 If query type does not exist
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeCollectionType(Block $block, $collectionIdentifier, Request $request)
    {
        $newType = (int) $request->request->get('new_type');
        $queryType = $request->request->get('query_type');

        $this->validator->validateChangeCollectionType($block, $collectionIdentifier, $newType, $queryType);

        $collection = $block->getCollectionReference($collectionIdentifier)->getCollection();
        $queryCreateStruct = null;

        if ($newType === Collection::TYPE_MANUAL) {
            if ($collection->getType() === Collection::TYPE_MANUAL) {
                // Noop
                return new Response(null, Response::HTTP_NO_CONTENT);
            }
        } elseif ($newType === Collection::TYPE_DYNAMIC) {
            $queryCreateStruct = $this->collectionService->newQueryCreateStruct(
                $this->getQueryType($queryType)
            );
        }

        $this->collectionService->changeCollectionType($collection, $newType, $queryCreateStruct);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Performs access checks on the controller.
     */
    protected function checkPermissions()
    {
        $this->denyAccessUnlessGranted('ROLE_NGBM_API');
    }
}

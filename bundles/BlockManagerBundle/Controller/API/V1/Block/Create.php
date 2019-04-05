<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface;
use Netgen\BlockManager\Exception\BadStateException;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\Bundle\BlockManagerBundle\Controller\AbstractController;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder;
use Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class Create extends AbstractController
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructBuilder
     */
    private $createStructBuilder;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Controller\API\V1\Block\Utils\CreateStructValidator
     */
    private $createStructValidator;

    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockTypeRegistryInterface
     */
    private $blockTypeRegistry;

    public function __construct(
        BlockService $blockService,
        CreateStructBuilder $createStructBuilder,
        CreateStructValidator $createStructValidator,
        BlockTypeRegistryInterface $blockTypeRegistry
    ) {
        $this->blockService = $blockService;
        $this->createStructBuilder = $createStructBuilder;
        $this->createStructValidator = $createStructValidator;
        $this->blockTypeRegistry = $blockTypeRegistry;
    }

    /**
     * Creates the block in specified block.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If block type does not exist
     */
    public function __invoke(Block $block, Request $request): View
    {
        $requestData = $request->attributes->get('data');

        $this->createStructValidator->validateCreateBlock($requestData);

        try {
            $blockType = $this->blockTypeRegistry->getBlockType($requestData->get('block_type'));
        } catch (BlockTypeException $e) {
            throw new BadStateException('block_type', 'Block type does not exist.', $e);
        }

        $this->denyAccessUnlessGranted(
            'nglayouts:block:add',
            [
                'block_definition' => $blockType->getDefinition(),
                'layout' => $block->getLayoutId(),
            ]
        );

        $blockCreateStruct = $this->createStructBuilder->buildCreateStruct($blockType);

        $createdBlock = $this->blockService->createBlock(
            $blockCreateStruct,
            $block,
            $requestData->get('parent_placeholder'),
            $requestData->get('parent_position')
        );

        return new View($createdBlock, Version::API_V1, Response::HTTP_CREATED);
    }
}

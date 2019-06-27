<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output\Visitor;

use Generator;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\VisitorInterface;

/**
 * Zone value visitor.
 *
 * @see \Netgen\Layouts\API\Values\Layout\Zone
 */
final class ZoneVisitor implements VisitorInterface
{
    /**
     * @var \Netgen\Layouts\API\Service\BlockService
     */
    private $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    public function accept(object $value): bool
    {
        return $value instanceof Zone;
    }

    /**
     * @param \Netgen\Layouts\API\Values\Layout\Zone $value
     * @param \Netgen\Layouts\Transfer\Output\OutputVisitor $outputVisitor
     *
     * @return array
     */
    public function visit(object $value, OutputVisitor $outputVisitor): array
    {
        return [
            'identifier' => $value->getIdentifier(),
            'linked_zone' => $this->visitLinkedZone($value),
            'blocks' => iterator_to_array($this->visitBlocks($value, $outputVisitor)),
        ];
    }

    /**
     * Visit the given $zone linked zone into hash representation.
     */
    private function visitLinkedZone(Zone $zone): ?array
    {
        $linkedZone = $zone->getLinkedZone();

        if (!$linkedZone instanceof Zone) {
            return null;
        }

        return [
            'identifier' => $linkedZone->getIdentifier(),
            'layout_id' => $linkedZone->getLayoutId()->toString(),
        ];
    }

    /**
     * Visit the given $zone blocks into hash representation.
     *
     * Note: here we rely on API returning blocks already sorted by their position in the zone.
     */
    private function visitBlocks(Zone $zone, OutputVisitor $outputVisitor): Generator
    {
        foreach ($this->blockService->loadZoneBlocks($zone) as $block) {
            yield $outputVisitor->visit($block);
        }
    }
}

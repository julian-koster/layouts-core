<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

final class RuleTargetViewTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\API\Values\LayoutResolver\Target
     */
    private $target;

    /**
     * @var \Netgen\Layouts\View\View\RuleTargetViewInterface
     */
    private $view;

    protected function setUp(): void
    {
        $this->target = Target::fromArray(['id' => 42]);

        $this->view = new RuleTargetView($this->target);

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('target', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleTargetView::__construct
     * @covers \Netgen\Layouts\View\View\RuleTargetView::getTarget
     */
    public function testGetTarget(): void
    {
        self::assertSame($this->target, $this->view->getTarget());
        self::assertSame(
            [
                'target' => $this->target,
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\RuleTargetView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('rule_target', $this->view::getIdentifier());
    }
}

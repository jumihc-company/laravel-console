<?php
/**
 * User: YL
 * Date: 2020/07/01
 */

namespace Jmhc\Console\Ast;

use Jmhc\Support\Utils\Helper;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ModelUpdateVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    protected $annotation;
    /**
     * @var array
     */
    protected $fillable;

    /**
     * @var array
     */
    protected $dates;

    /**
     * @var array
     */
    protected $casts;

    /**
     * @var bool
     */
    protected $castsForce;

    public function __construct(string $annotation, string $fillable, string $dates, string $casts, bool $castsForce)
    {
        $this->annotation = $annotation;
        $this->fillable = Helper::str2array($fillable);
        $this->dates = Helper::str2array($dates);
        $this->casts = Helper::str2array($casts);
        $this->castsForce = $castsForce;
    }

    public function leaveNode(Node $node)
    {
        switch ($node) {
            case $node instanceof Node\Stmt\PropertyProperty:
                $name = (string) $node->name;
                if (in_array($name, ['fillable', 'dates', 'casts'])) {
                    $node = $this->rewritePropertyProperty($node, $this->{$name}, $name == 'casts' ? $this->castsForce : true);
                }
                break;
            case $node instanceof Node\Stmt\Class_:
                $node->setDocComment(new Doc($this->annotation));
                break;
        }

        return $node;
    }

    /**
     * 重写节点
     * @param Node\Stmt\PropertyProperty $node
     * @param array $data
     * @param bool $isForce
     * @return Node\Stmt\PropertyProperty
     */
    protected function rewritePropertyProperty(Node\Stmt\PropertyProperty $node, array $data, bool $isForce)
    {
        $items = [];
        $keys = [];

        // 不覆盖的情况下读取之前的
        if (! $isForce && $node->default instanceof Node\Expr\Array_) {
            $items = $node->default->items;

            foreach ($items as $item) {
                if (is_object($item->key)) {
                    $keys[] = $item->key->value;
                }
            }
        }

        foreach ($data as $k => $v) {
            if (in_array($k, $keys)) {
                continue;
            }

            if (is_integer($k)) {
                $items[] = new Node\Expr\ArrayItem(
                    new Node\Scalar\String_($v)
                );
            } else {
                $items[] = new Node\Expr\ArrayItem(
                    new Node\Scalar\String_($v),
                    new Node\Scalar\String_($k)
                );
            }
        }

        $node->default = new Node\Expr\Array_($items, [
            'kind' => Node\Expr\Array_::KIND_SHORT,
        ]);
        return $node;
    }
}

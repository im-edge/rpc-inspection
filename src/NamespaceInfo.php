<?php

namespace IMEdge\Web\Rpc\Inspection;

use gipfl\IcingaWeb2\Link;
use gipfl\IcingaWeb2\Url;
use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;
use ipl\Html\HtmlElement;
use stdClass;

use function implode;

class NamespaceInfo extends BaseHtmlElement
{
    protected $tag = 'ul';

    /**
     * @var array<string, object{
     *      name: string,
     *      type: string,
     *      requestType: string,
     *      title: ?string,
     *      description: ?string,
     *      parameters: array<int, object{
     *          name: string,
     *          type: string,
     *          isVariadic: ?bool,
     *          isOptional: ?bool,
     *          description: ?string,
     *      }>,
     *      returnType: ?string,
     *      resultType: ?string,
     *  }>
     */
    protected array $info;
    protected Url $baseUrl;

    public function __construct(stdClass $info, Url $baseUrl)
    {
        $this->info = (array) $info;
        ksort($this->info);
        $this->baseUrl = $baseUrl;
    }

    protected function assemble(): void
    {
        $formerNamespace = null;
        $methods = Html::tag('ul'); // unused, but makes IDE happy
        foreach ($this->info as $rpcMethod => $info) {
            list($namespace, $methodName) = explode('.', $rpcMethod, 2);
            if ($namespace !== $formerNamespace) {
                $formerNamespace = $namespace;
                $this->add($li = Html::tag('li', Html::tag('strong', $namespace)));
                $li->add($methods = Html::tag('ul'));
            }
            $methods->add($this->renderMethod($rpcMethod, MetaDataMethod::fromSerialization($info)));
        }
    }

    protected function renderMethod(string $name, MetaDataMethod $method): HtmlElement
    {
        return Html::tag('li', [
            Link::create(Html::sprintf(
                '%s(%s): %s',
                Html::tag('strong', $method->name),
                $this->renderParameters($method->parameters),
                $method->returnType
            ), $this->baseUrl->with([
                'method' => $name
            ])),
            $method->description ? [
                Html::tag('br'),
                Html::tag('i', $method->description),
            ] : null,
        ]);
    }

    /**
     * @param array<int, object{
     *     name: string,
     *     type: string,
     *     isVariadic: ?bool,
     *     isOptional: ?bool,
     *     description: ?string,
     * }> $parameters
     * @return string
     */
    protected function renderParameters(array $parameters): string
    {
        $list = [];
        foreach ($parameters as $parameter) {
            $list[] = $parameter->type . ' $' . $parameter->name;
        }

        return implode(', ', $list);
    }
}

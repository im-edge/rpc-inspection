<?php

namespace IMEdge\Web\Rpc\Inspection;

use IMEdge\Json\JsonSerialization;
use stdClass;

/**
 * IMEdge\RpcApi is php8.1+, this class can be dropped, once we raise our web requirements
 */
class MetaDataMethod implements JsonSerialization
{
    public string $name;
    // Either 'request' or 'notification'
    public string $type;
    /** @var MetaDataParameter[] */
    public array $parameters = [];
    public ?string $title = null;
    public ?string $description = null;
    public ?string $returnType = null;

    public function __construct(
        string $name,
        string $type,
        ?string $title = null,
        ?string $description = null,
        ?string $returnType = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->returnType = $returnType;
    }

    public static function fromSerialization($any): MetaDataMethod
    {
        // object(stdClass)#320 (6) { ["name"]=> string(19) "getAvailableMethods" ["requestType"]=> string(7) "request" ["resultType"]=> NULL ["parameters"]=> array(0) { } ["title"]=> NULL ["description"]=> string(0) "" }
        $self = new MetaDataMethod(
            $any->name,
            $any->type ?? $any->requestType,
            $any->title ?? null,
            $any->description ?? null,
            $any->returnType ?? $any->resultType ?? null,
        );
        foreach ($any->parameters as $key => $parameter) {
            $self->parameters[$key] = MetaDataParameter::fromSerialization($parameter);
        }

        return $self;
    }

    public function jsonSerialize(): stdClass
    {
        return (object) [
            'name'  => $this->name,
            'type'  => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'returnType'  => $this->returnType,
        ];
    }
}

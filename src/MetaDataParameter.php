<?php

namespace IMEdge\Web\Rpc\Inspection;

use IMEdge\Json\JsonSerialization;
use InvalidArgumentException;
use stdClass;

class MetaDataParameter implements JsonSerialization
{
    public string $name;
    public string $type;
    public bool $isVariadic;
    public bool $isOptional;
    public ?string $description = null;

    public function __construct(
        string $name,
        string $type,
        bool $isVariadic,
        bool $isOptional,
        ?string $description = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->isVariadic = $isVariadic;
        $this->isOptional = $isOptional;
        $this->description = $description;
    }

    public static function fromSerialization($any): MetaDataParameter
    {
        if (! is_object($any)) {
            throw new InvalidArgumentException('MetaDataParameter expects an object');
        }
        return new MetaDataParameter(
            $any->name,
            $any->type,
            $any->isVariadic ?? false, // TODO -> ???
            $any->isOptional ?? false, // TODO: stillmissing
            $any->description ?? null
        );
    }

    public function jsonSerialize(): stdClass
    {
        return (object) [
            'name' => $this->name,
            'type' => $this->type,
            'isVariadic' => $this->isVariadic,
            'isOptional' => $this->isOptional,
            'description' => $this->description,
        ];
    }
}

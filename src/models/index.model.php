<?php

namespace JSONPad;

class Index
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public string $name;
    public string $description;
    public string $pathName;
    public string $pointer;
    public string $valueType;
    public bool $alias;
    public bool $sorting;
    public bool $filtering;
    public bool $searching;
    public string $defaultOrderDirection;
    public bool $activated;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->pathName = $data['pathName'];
        $this->pointer = $data['pointer'];
        $this->valueType = $data['valueType'];
        $this->alias = $data['alias'];
        $this->sorting = $data['sorting'];
        $this->filtering = $data['filtering'];
        $this->searching = $data['searching'];
        $this->defaultOrderDirection = $data['defaultOrderDirection'];
        $this->activated = $data['activated'];
    }
}

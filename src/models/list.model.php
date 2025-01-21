<?php

namespace JSONPad;

use JSONPad\User as User;

class ListModel
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public ?User $user;
    public string $name;
    public string $description;
    public string $pathName;
    public $schema;
    public bool $pinned;
    public bool $readonly;
    public bool $realtime;
    public bool $protected;
    public bool $indexable;
    public bool $generative;
    public string $generativePrompt;
    public bool $activated;
    public int $itemCount;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->user = isset($data['user']) ? new User($data['user']) : null;
        $this->name = $data['name'];
        $this->description = $data['description'];
        $this->pathName = $data['pathName'];
        $this->schema = $data['schema'];
        $this->pinned = $data['pinned'];
        $this->readonly = $data['readonly'];
        $this->realtime = $data['realtime'];
        $this->protected = $data['protected'];
        $this->indexable = $data['indexable'];
        $this->generative = $data['generative'];
        $this->generativePrompt = $data['generativePrompt'];
        $this->activated = $data['activated'];
        $this->itemCount = $data['itemCount'];
    }
}


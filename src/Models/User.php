<?php

namespace JSONPad\Models;

use \DateTime;

class User
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public ?DateTime $lastActiveAt;
    public bool $activated;
    public string $displayName;
    public string $description;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->lastActiveAt = $data['lastActiveAt'] ? new DateTime($data['lastActiveAt']) : null;
        $this->activated = $data['activated'];
        $this->displayName = $data['displayName'];
        $this->description = $data['description'];
    }
}

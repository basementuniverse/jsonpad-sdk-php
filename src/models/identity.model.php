<?php

namespace JSONPad;

use JSONPad\User as User;

class Identity
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public ?User $user;
    public string $name;
    public string $group;
    public ?DateTime $lastLoginAt;
    public bool $activated;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->user = isset($data['user']) ? new User($data['user']) : null;
        $this->name = $data['name'];
        $this->group = $data['group'];
        $this->lastLoginAt = $data['lastLoginAt'] ? new DateTime($data['lastLoginAt']) : null;
        $this->activated = $data['activated'];
    }
}

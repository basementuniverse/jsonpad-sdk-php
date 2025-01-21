<?php

namespace JSONPad;

class Item
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public $data;
    public string $description;
    public string $version;
    public bool $readonly;
    public bool $activated;
    public int $size;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->data = $data['data'];
        $this->description = $data['description'];
        $this->version = $data['version'];
        $this->readonly = $data['readonly'];
        $this->activated = $data['activated'];
        $this->size = $data['size'];
    }
}

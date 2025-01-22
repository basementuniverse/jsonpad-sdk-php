<?php

namespace JSONPad\Models;

use \DateTime;
use \JSONPad\Models\User;

class Event
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
    public ?User $user;
    public string $modelId;
    public string $stream;
    public string $type;
    public string $version;
    public $snapshot;
    public $attachments;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->createdAt = new DateTime($data['createdAt']);
        $this->updatedAt = new DateTime($data['updatedAt']);
        $this->user = isset($data['user']) ? new User($data['user']) : null;
        $this->modelId = $data['modelId'];
        $this->stream = $data['stream'];
        $this->type = $data['type'];
        $this->version = $data['version'];
        $this->snapshot = $data['snapshot'];
        $this->attachments = $data['attachments'];
    }
}

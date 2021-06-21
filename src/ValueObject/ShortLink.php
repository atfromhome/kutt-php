<?php

declare(strict_types=1);

namespace FromHome\Kutt\ValueObject;

final class ShortLink
{
    public string $id;

    public string $address;

    public ?string $description;

    public bool $banned;

    public bool $password;

    public string $target;

    public int $visitCount;

    public string $link;

    public string $createdAt;

    public string $updatedAt;

    public static function fromArray(array $data): self
    {
        $self = new self();

        $self->id = $data['id'];
        $self->address = $data['address'];
        $self->description = $data['description'];
        $self->banned = $data['banned'];
        $self->password = $data['password'];
        $self->target = $data['target'];
        $self->visitCount = $data['visit_count'];
        $self->link = $data['link'];
        $self->createdAt = $data['created_at'];
        $self->updatedAt = $data['updated_at'];

        return $self;
    }
}

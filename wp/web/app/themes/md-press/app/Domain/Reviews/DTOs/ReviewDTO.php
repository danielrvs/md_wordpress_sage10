<?php

declare(strict_types=1);

namespace App\Domain\Reviews\DTOs;

class ReviewDTO
{
    public function __construct(
        public int $id,
        public int $doctorId,
        public string $patientName,
        public float $rating,
        public string $comment,
        public string $createdAt
    ) {}

    public static function fromComment(\WP_Comment $comment): self
    {
        return new self(
            id: (int) $comment->comment_ID,
            doctorId: (int) $comment->comment_post_ID,
            patientName: $comment->comment_author,
            rating: (float) (get_comment_meta($comment->comment_ID, 'review_rating', true) ?: 5.0),
            comment: $comment->comment_content,
            createdAt: $comment->comment_date
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'doctorId' => $this->doctorId,
            'patientName' => $this->patientName,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'createdAt' => $this->createdAt,
        ];
    }
}
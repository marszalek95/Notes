<?php

namespace App\Twig\Components;

use App\Repository\NoteRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class NoteSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    public function __construct(
        private readonly NoteRepository $noteRepository,
    ) {  
    }

    public function getNotes(): array
    {
        return $this->noteRepository->findBySearchQuery($this->query);
    }
}
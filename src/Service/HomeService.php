<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class HomeService
{
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getServices(): array
    {
        return [
            [
                'name' => 'Coupe de cheveux',
                'description' => 'Une coupe de cheveux stylée pour vous sublimer.',
                'image' => '/images/services/coupe.jpg',
                'slug' => 'coupe-de-cheveux',
            ],
            [
                'name' => 'Coloration',
                'description' => 'Des couleurs vibrantes pour vos cheveux.',
                'image' => '/images/services/coloration.jpg',
                'slug' => 'coloration',
            ],
            [
                'name' => 'Soin capillaire',
                'description' => 'Des soins pour des cheveux en pleine santé.',
                'image' => '/images/services/soin.jpg',
                'slug' => 'soin-capillaire',
            ],
        ];
    }

    public function getAvis(): array
    {
        return $this->reviewRepository->findAll();
    }
}

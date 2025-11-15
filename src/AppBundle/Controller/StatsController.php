<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Fundraiser;
use AppBundle\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Get basic stats
        $fundraiserCount = $entityManager->getRepository(Fundraiser::class)->count([]);
        $reviewCount = $entityManager->getRepository(Review::class)->count([]);
        
        // Calculate average ratings
        $totalRatings = $entityManager->createQuery(
            'SELECT SUM(r.rating) FROM AppBundle\Entity\Review r'
        )->getSingleScalarResult() ?? 0;
        
        $averageRating = $reviewCount > 0 ? $totalRatings / $reviewCount : 0;
        
        // Get fundraisers without reviews 
        $fundraisersWithoutReviews = $entityManager->createQuery(
            'SELECT COUNT(f.id) FROM AppBundle\Entity\Fundraiser f 
             WHERE f.id NOT IN (SELECT DISTINCT IDENTITY(r.fundraiser) FROM AppBundle\Entity\Review r)'
        )->getSingleScalarResult() ?? 0;
        
        // INTENTIONAL BUG: Calculate a "problematic ratio" that will cause division by zero
        // This simulates a common bug where developers forget edge cases
        $problematicRatio = 0;
        
        // Count reviews for fundraisers with ID > 10 (our new "Buggy Fundraiser" has ID 11)
        $newFundraisersReviewCount = $entityManager->createQuery(
            'SELECT COUNT(r.id) FROM AppBundle\Entity\Review r 
             JOIN r.fundraiser f WHERE f.id > 10'
        )->getSingleScalarResult() ?? 0;
        
        // INTENTIONAL BUG: This will always be division by zero for fresh DB
        // because fundraiser ID 11 has no reviews
        if ($fundraiserCount > 10) {
            // This line will crash! New fundraisers (ID > 10) have no reviews
            $problematicRatio = 100 / $newFundraisersReviewCount;
        }

        return $this->render('stats/index.html.twig', [
            'fundraiser_count' => $fundraiserCount,
            'review_count' => $reviewCount,
            'average_rating' => $averageRating,
            'fundraisers_without_reviews' => $fundraisersWithoutReviews,
            'problematic_ratio' => $problematicRatio,
        ]);
    }
    
    #[Route('/stats/trigger-error', name: 'app_stats_trigger_error')]
    public function triggerError(EntityManagerInterface $entityManager): Response
    {
        // This endpoint will definitely trigger a division by zero error
        // by temporarily removing all reviews from the calculation
        
        $fundraiserCount = $entityManager->getRepository(Fundraiser::class)->count([]);
        
        // Force division by zero
        $fakeReviewCount = 0;
        $totalRatings = 100; // Some fake total
        
        // This will always crash
        $averageRating = $totalRatings / $fakeReviewCount;
        
        return new Response('This should never be reached');
    }
}
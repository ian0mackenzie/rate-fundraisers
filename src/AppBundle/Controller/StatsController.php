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
        // Tag this request for Sentry tracking
        \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
            $scope->setTag('feature', 'stats-page');
            $scope->setTag('error-type', 'division-by-zero-error-triggered');
        });
        
        try {
            // Get basic stats - simple and safe
            $fundraiserCount = $entityManager->getRepository(Fundraiser::class)->count([]);
            $reviewCount = $entityManager->getRepository(Review::class)->count([]);
            
            // Calculate average rating - safe division
            $averageRating = 0;
            if ($reviewCount > 0) {
                $totalRatings = $entityManager->createQuery(
                    'SELECT SUM(r.rating) FROM AppBundle\Entity\Review r'
                )->getSingleScalarResult() ?? 0;
                $averageRating = $totalRatings / $reviewCount;
            }
            
            // Get fundraisers without reviews - using a simpler LEFT JOIN approach
            $fundraisersWithoutReviews = $entityManager->createQuery(
                'SELECT COUNT(f.id) FROM AppBundle\Entity\Fundraiser f 
                 LEFT JOIN f.reviews r 
                 WHERE r.id IS NULL'
            )->getSingleScalarResult() ?? 0;
            
            // Check if there are "new" fundraisers (ID > 10) without reviews
            $newFundraisersWithoutReviews = $entityManager->createQuery(
                'SELECT COUNT(f.id) FROM AppBundle\Entity\Fundraiser f 
                 LEFT JOIN f.reviews r 
                 WHERE f.id > 10 AND r.id IS NULL'
            )->getSingleScalarResult() ?? 0;
            
            // Calculate ratio of new fundraisers with reviews
            $problematicRatio = 0;
            if ($newFundraisersWithoutReviews > 0) {
                $newFundraisersReviewCount = $entityManager->createQuery(
                    'SELECT COUNT(r.id) FROM AppBundle\Entity\Review r 
                     JOIN r.fundraiser f WHERE f.id > 10'
                )->getSingleScalarResult() ?? 0;
                
                // Guard against division by zero
                if ($newFundraisersReviewCount > 0) {
                    $problematicRatio = 100 / $newFundraisersReviewCount;
                }
            }

            return $this->render('stats/index.html.twig', [
                'fundraiser_count' => $fundraiserCount,
                'review_count' => $reviewCount,
                'average_rating' => round($averageRating, 2),
                'fundraisers_without_reviews' => $fundraisersWithoutReviews,
                'problematic_ratio' => $problematicRatio,
            ]);
            
        } catch (\Exception $e) {
            // For debugging - in production, this would be caught by Sentry
            throw new \Exception('Stats calculation failed: ' . $e->getMessage(), 0, $e);
        }
    }
    
    #[Route('/stats/trigger-error', name: 'app_stats_trigger_error')]
    public function triggerError(EntityManagerInterface $entityManager): Response
    {
        // Tag this request for Sentry tracking
        \Sentry\configureScope(function (\Sentry\State\Scope $scope): void {
            $scope->setTag('feature', 'error-trigger');
            $scope->setTag('error-type', 'division-by-zero-error-triggered');
        });
        
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
    
    #[Route('/stats/debug', name: 'app_stats_debug')]
    public function debugStats(EntityManagerInterface $entityManager): Response
    {
        // Safe stats for debugging without the intentional error
        $fundraiserCount = $entityManager->getRepository(Fundraiser::class)->count([]);
        $reviewCount = $entityManager->getRepository(Review::class)->count([]);
        
        $averageRating = 0;
        if ($reviewCount > 0) {
            $totalRatings = $entityManager->createQuery(
                'SELECT SUM(r.rating) FROM AppBundle\Entity\Review r'
            )->getSingleScalarResult() ?? 0;
            $averageRating = $totalRatings / $reviewCount;
        }
        
        $fundraisersWithoutReviews = $entityManager->createQuery(
            'SELECT COUNT(f.id) FROM AppBundle\Entity\Fundraiser f 
             LEFT JOIN f.reviews r 
             WHERE r.id IS NULL'
        )->getSingleScalarResult() ?? 0;
        
        $newFundraisersReviewCount = $entityManager->createQuery(
            'SELECT COUNT(r.id) FROM AppBundle\Entity\Review r 
             JOIN r.fundraiser f WHERE f.id > 10'
        )->getSingleScalarResult() ?? 0;
        
        return new Response(json_encode([
            'fundraiser_count' => $fundraiserCount,
            'review_count' => $reviewCount,
            'average_rating' => round($averageRating, 2),
            'fundraisers_without_reviews' => $fundraisersWithoutReviews,
            'new_fundraisers_review_count' => $newFundraisersReviewCount,
            'message' => 'Debug stats - no errors here!'
        ], JSON_PRETTY_PRINT), 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/sentry-test', name: 'app_sentry_test')]
    public function sentryTest(): Response
    {
        // Test different types of Sentry reporting
        
        // 1. Manual exception
        if (isset($_GET['exception'])) {
            throw new \Exception('Test exception for Sentry monitoring!');
        }
        
        // 2. Manual message
        if (isset($_GET['message'])) {
            \Sentry\captureMessage('Test message from Rate Fundraisers app!', \Sentry\Severity::info());
            return new Response('Message sent to Sentry! Check your dashboard.');
        }
        
        // 3. Show test options
        return new Response('
            <h1>🚨 Sentry Test Page</h1>
            <p>Choose a test:</p>
            <ul>
                <li><a href="/sentry-test?exception=1">Trigger Exception</a></li>
                <li><a href="/sentry-test?message=1">Send Message</a></li>
                <li><a href="/stats/debug">Safe Stats (debug)</a></li>
                <li><a href="/stats">Dangerous Stats (division by zero error)</a></li>
                <li><a href="/stats/trigger-error">Guaranteed Error</a></li>
            </ul>
        ');
    }
}
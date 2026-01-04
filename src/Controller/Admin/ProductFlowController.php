<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\Product\Step\ProductConfirmationStepType;
use App\Form\Product\Step\ProductDetailsStepType;
use App\Form\Product\Step\ProductLicenseStepType;
use App\Form\Product\Step\ProductLogisticsStepType;
use App\Form\Product\Step\ProductTypeStepType;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/product/flow')]
class ProductFlowController extends AbstractController
{
    #[Route('/{id?}', name: 'app_admin_product_flow', methods: ['GET', 'POST'])]
    public function flow(Request $request, EntityManagerInterface $entityManager, ?Product $product = null): Response
    {
        // 1. Initialize or Retrieve Product from Session/DB
        $session = $request->getSession();
        $flowId = 'product_flow_' . ($product ? $product->getId() : 'new');
        
        // Ensure Admin Access
        if ($product) {
             $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product);
        } else {
             $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if (!$product) {
            // Check if we have one in session
            if ($session->has($flowId)) {
                $product = $session->get($flowId);
            } else {
                $product = new Product();
            }
        }
        
        // 2. Determine Current Step
        $currentStep = $request->query->getInt('step', 1);
        $totalSteps = 3; // Basic
        // Dynamic Logic
        if ($product->getType() === 'physical') {
            $formClass = match($currentStep) {
                1 => ProductTypeStepType::class,
                2 => ProductDetailsStepType::class,
                3 => ProductLogisticsStepType::class,
                4 => ProductConfirmationStepType::class, // Potential step
                default => ProductTypeStepType::class,
            };
        } else {
             $formClass = match($currentStep) {
                1 => ProductTypeStepType::class,
                2 => ProductDetailsStepType::class,
                3 => ProductLicenseStepType::class,
                4 => ProductConfirmationStepType::class,
                default => ProductTypeStepType::class,
            };
        }
        
        // Threshold Logic
        $needsConfirmation = $product->getPrice() > 100;
        if ($needsConfirmation) {
             $totalSteps = 4;
        }
        
        // Form Handling
        $form = $this->createForm($formClass, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save state to session
            $session->set($flowId, $product);
            
            // Determine Next Step or Finish
            $nextStep = $currentStep + 1;
            
            // Check loop/finish
             if ($currentStep >= 3 && !$needsConfirmation) {
                 // FINISH
                 $this->saveAndClear($entityManager, $session, $flowId, $product);
                 return $this->redirectToRoute('app_admin_product_index');
             }
             
             if ($currentStep >= 4) {
                 // FINISH
                 $this->saveAndClear($entityManager, $session, $flowId, $product);
                 return $this->redirectToRoute('app_admin_product_index');
             }

            return $this->redirectToRoute('app_admin_product_flow', ['step' => $nextStep, 'id' => $product->getId()]);
        }
        
        // Check for 'Previous' button click (handled via link in template usually, or form button)
        // If query param ?back=true...

        return $this->render('admin/product/flow.html.twig', [
            'form' => $form,
            'currentStep' => $currentStep,
            'totalSteps' => $needsConfirmation ? 4 : 3, // Approximate, purely visual
            'product' => $product,
        ]);
    }
    
    private function saveAndClear(EntityManagerInterface $em, $session, $key, $product) {
        // If user was creating new, persist. If editing, just flush.
        // Issue: if $product from session is detached, we need to merge?
        // Since we are using objects in session, they might be detached entities.
        
        // Simple hack: if ID is null, persist. If ID exists, merge.
        if (!$product->getId()) {
             $em->persist($product);
        } else {
             // Re-attach if needed, or simply flush if it was managed by Doctrine (but session serialization breaks this)
             // Better: Use $em->merge($product) - wait, merge is deprecated/removed in recent standard Doctrine?
             // Use repositories to find and update ? 
             // Ideally for Multi-step edit, we load from DB every time and apply changes.
             // For New, we store in session.
             // Let's assume 'New' flow uses Session. 'Edit' flow loads from DB and updates DB step by step? 
             // The prompt says "Les données saisies sont conservées entre les étapes." -> Session is key.
             
             // If ID exists, we should probably fetch fresh from DB and map properties.
             if ($product->getId()) {
                 $existing = $em->getRepository(Product::class)->find($product->getId());
                 $existing->setName($product->getName());
                 $existing->setDescription($product->getDescription());
                 $existing->setPrice($product->getPrice());
                 $existing->setType($product->getType());
                 $existing->setWeight($product->getWeight());
                 $existing->setDimensions($product->getDimensions());
                 $existing->setStock($product->getStock());
                 $existing->setLicenseDetails($product->getLicenseDetails());
                 $product = $existing;
             }
        }
        
        $em->flush();
        $session->remove($key);
    }
}

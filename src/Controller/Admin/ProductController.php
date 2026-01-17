<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use App\Service\CsvExporter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::VIEW);

        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAllSortedByPriceDesc(),
        ]);
    }

    #[Route('/export', name: 'app_admin_product_export', methods: ['GET'])]
    public function export(ProductRepository $productRepository, CsvExporter $exporter): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::VIEW);
        
        $products = $productRepository->findAllSortedByPriceDesc();
        return $exporter->exportProducts($products);
    }
    
    #[Route('/{id}/delete', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_product_index');
    }

    #[Route('/import', name: 'app_admin_product_import', methods: ['GET', 'POST'])]
    public function import(\Symfony\Component\HttpFoundation\Request $request, \App\Service\ProductImporter $importer): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(\App\Form\ProductImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('csv_file')->getData();

            try {
                $count = $importer->import($file->getPathname());
                $this->addFlash('success', sprintf('%d produits importés avec succès.', $count));
                return $this->redirectToRoute('app_admin_product_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'import: ' . $e->getMessage());
            }
        }

        return $this->render('admin/product/import.html.twig', [
            'form' => $form,
        ]);
    }

    // New/Edit actions will be handled by ProductFlowController (or added here if we use flow)
}

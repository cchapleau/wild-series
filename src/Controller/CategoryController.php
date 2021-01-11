<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;

/**
* @Route("/categories", name="category_")
*/

class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $category = $this->getDoctrine()
             ->getRepository(Category::class)
             ->findAll();

        if (!$category) {
            throw $this->createNotFoundException(
            'No category found in category\'s table.'
            );
        }

        return $this->render('category/index.html.twig',
            ['categories' => $category]
        );
    }

    /**
     * The controller for the category add form
     * Display the form or deal with it
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request) : Response
    {
        // Create a new Category Object
        $category = new Category();
        // Create the associated Form
        $form = $this->createForm(CategoryType::class, $category);
        // Get data from HTTP request
        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            // Deal with the submitted data
            // Get the Entity Manager
            $entityManager = $this->getDoctrine()->getManager();
            // Persist Category Object
            $entityManager->persist($category);
            // Flush the persisted object
            $entityManager->flush();
            // Finally redirect to categories list
            return $this->redirectToRoute('category_index');
        }
        // Render the form
        return $this->render('category/new.html.twig', ["form" => $form->createView()]);
    }

    /**
     * @Route("/{categoryName}", requirements={"id"="\d+"}, methods={"GET"}, name="show")
     */
    public function show(string $categoryName):Response
    {
        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No '.$categoryName.' has been sent to find a category in category\'s table.');
        }

        $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'DESC'],
                3
            );

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'programs' => $programs]);
    }
}

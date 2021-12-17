<?php

namespace App\Controller\front;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

   #[Route("front/categories/", name: "front_category_list")]
    public function typeList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render('front/categories.html.twig', ['categories' => $categories]);
    }

    #[Route("front/category/{id}",  name: "front_category_show")]
    public function categoryShow($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        return $this->render('front/category.html.twig', ['category' => $category]);
    }
}
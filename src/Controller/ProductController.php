<?php

namespace App\Controller;

use App\Model\BrandManager;
use App\Model\CategoryManager;
use App\Model\ProductManager;
use App\Model\SearchManager;
use App\Model\UniverseManager;

class ProductController extends AbstractController
{

    const PRODUCTS_BY_PAGES = 12;

    public function indexUniverse(string $universe, string $page = '1')
    {

        $productManager = new ProductManager();
        $brandManager = new BrandManager();
        $categoryManager = new CategoryManager();

        $filterPage['brand'] = $_GET['brand'] ?? null;
        $filterPage['category'] = $_GET['category'] ?? null;
        $filterPage['available'] = $_GET['available'] ?? null;
        $filterPage['universe'] = $universe;

        $pageNumber = (int)$page;
        $countProducts = $productManager->countProducts($filterPage);
        $countPages = (int)($countProducts/self::PRODUCTS_BY_PAGES+1);

        $brands = $brandManager->selectFromUniverse($filterPage['universe']);
        $categories = $categoryManager->selectFromUniverse($filterPage['universe']);
        $products = $productManager->selectUniverse($filterPage, $pageNumber, self::PRODUCTS_BY_PAGES);

        return $this->twig->render('Product/index.html.twig', ['products' => $products,
            'page' => $pageNumber,
            'countPages' => $countPages,
            'countProducts' => $countProducts,
            'brands' => $brands,
            'categories' => $categories,
            'actualFilter' => $filterPage,
            ]);
    }

    public function describe(int $id): string
    {
        $productManager = new ProductManager();
        $product = $productManager->selectOneById($id);
        return $this->twig->render('Product/describe.html.twig', ['product' => $product]);
    }

    public function search(string $page = '1')
    {

        $searchManager = new SearchManager();
        $brandManager = new BrandManager();
        $categoryManager = new CategoryManager();
        $universeManager = new UniverseManager();
        $brands = $brandManager->selectAll();
        $categories = $categoryManager->selectAll();
        $universes = $universeManager->selectAll();

        $searchTerm = trim($_GET['search']) ?? null;
        $filterPage['brand'] = $_GET['brand'] ?? null;
        $filterPage['category'] = $_GET['category'] ?? null;
        $filterPage['available'] = $_GET['available'] ?? null;
        $filterPage['universe'] = $_GET['universe'] ?? null;

        $pageNumber = (int)$page;
        $countProducts = $searchManager->countSearchedProducts($searchTerm, $filterPage);
        $countPages = (int)($countProducts/self::PRODUCTS_BY_PAGES+1);


        $products = $searchManager->searchProducts($filterPage, $searchTerm, $pageNumber, self::PRODUCTS_BY_PAGES);

        $filterPage['search'] = htmlentities($searchTerm);
        if (count($products) == 1) {
            return $this->twig->render('Product/describe.html.twig', ['product' => $products[0],
                'page' => $pageNumber,
                'countPages' => $countPages,
                'countProducts' => $countProducts,
                'brands' => $brands,
                'categories' => $categories,
                'universes' => $universes,
                'actualFilter' => $filterPage,
                'searchTerm' => htmlentities($searchTerm),
            ]);
        } else {
            return $this->twig->render('Product/search.html.twig', ['products' => $products,
                'page' => $pageNumber,
                'countPages' => $countPages,
                'countProducts' => $countProducts,
                'brands' => $brands,
                'categories' => $categories,
                'universes' => $universes,
                'actualFilter' => $filterPage,
                'searchTerm' => htmlentities($searchTerm),
            ]);
        }
    }
}
